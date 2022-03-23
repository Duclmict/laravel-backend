//Generate edit icon
const editIcon = function(cell, formatterParams) {
  //plain text value
  return "<i class='far fa-edit'></i>";
};
const removeIcon = function(cell, formatterParams) {
  return "<i class='far fa-trash-alt text-danger'></i>";
};
const japDateFormat = function(cell, formatterParams) {
  date = moment(cell.getValue());
  return "<span>" + date.format("MM月DD日") + "</span>";
};
const CELL_TYPE_EDIT = 1;
const CELL_TYPE_REMOVE = 2;

var table;
var selectedYear = moment().year();
const displayColumns = [
  {
    title: "日付",
    field: "date",
    formatter: japDateFormat,
    align: "center",
    width: 200
  },
  {
    title: "休日名",
    field: "remark",
    align: "left"
  },
  {
    title: "編集",
    field: "",
    formatter: editIcon,
    align: "center",
    width: 100,
    cellClick: function(e, cell) {
      onCellClick(CELL_TYPE_EDIT, cell);
    }
  },
  {
    title: "削除",
    field: "",
    formatter: removeIcon,
    align: "center",
    width: 100,
    cellClick: function(e, cell) {
      onCellClick(CELL_TYPE_REMOVE, cell);
    }
  }
];
/**
 * Init table data
 */
function initTable() {
  table = new Tabulator("#holiday-table", {
    align: "center",
    layout: "fitColumns", //fit columns to width of table (optional),
    headerSort: false,
    locale: true,
    langs: {
      ja: {
        ajax: {
          loading: "読み込み中…", //ajax loader text
          error: "エラー" //ajax error text
        }
      }
    },
    columns: displayColumns,
    rowClick: function(e, row) {
      //trigger an alert message when the row is clicked
    }
  });
  table.setLocale("ja"); //set locale to french
  loadData();
}
/**
 * Load data
 */
function loadData() {
  table.setData("/holidays/getHolidayByYear?year=" + selectedYear);
  updateDisplay();
}

/**
 * Update display after load data
 */
function updateDisplay() {
  isCurrent = moment().year() == selectedYear;

  if (isCurrent) {
    $("#btn-current").addClass("btn-primary");
    $("#btn-current").removeClass("btn-secondary");
  } else {
    $("#btn-current").addClass("btn-secondary");
    $("#btn-current").removeClass("btn-primary");
  }
  $("#date-today").text(selectedYear + "年休暇設定");
}
/**
 * Event handler
 */
function eventHandler() {
  $("#btn-next-day").on("click", function(event) {
    selectedYear += 1;
    loadData();
  });

  $("#btn-pre-day").on("click", function(event) {
    selectedYear -= 1;
    loadData();
  });
  $("#btn-current").on("click", function(event) {
    selectedYear = moment().year();
    loadData();
  });

  $("#holiday-date").daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    minYear: parseInt(moment().format("YYYY"), 10),
    locale: {
      format: "YYYY-MM-DD"
    }
  });

  $("#holiday-submit").on("click", function(event) {
    if (!validateForm()) {
      return;
    }
    date = $("#holiday-date").val();
    remark = $("#holiday-remark").val();
    id = $("#holiday-id").val();
    console.log("holiday id: " + id);
    $("#holiday-modal .close").click();
    if (id) {
      updateHoliday(id, date, remark);
    } else {
      addHoliday(date, remark);
    }
  });

  $("#btn-add")
    .off("click")
    .on("click", function(event) {
      $("#holiday-remark").val("");
      $("#holiday-id").val("");
      console.log("button add");
      $("#holiday-modal").modal();
    });
}
function onCellClick(type, cell) {
  let row = cell.getRow();
  console.log(row.getData().id);

  if (type == CELL_TYPE_REMOVE) {
    removeHoliday(row.getData().id);
  } else {
    $("#holiday-date").val(row.getData().date);
    $("#holiday-remark").val(row.getData().remark);
    $("#holiday-id").val(row.getData().id);
    $("#holiday-modal").modal();
  }
}

function addHoliday(date, remark) {
  let data = {
    _token: $("[name=_token]").val(),
    date: date,
    remark: remark
  };
  $.post({
    url: "/holidays/addHoliday",
    data: data
  })
    .done(res => {
      loadData();
    })
    .fail(error => {
      displayError(error);
    });
}

function removeHoliday(id) {
  let data = {
    _token: $("[name=_token]").val(),
    id: id
  };
  $.ajax({
    url: "/holidays/deleteHoliday",
    data: data,
    type: "DELETE"
  })
    .done(res => {
      console.log(res);
      loadData();
    })
    .fail(error => {
      displayError(error);
    });
}

function updateHoliday(id, date, remark) {
  let data = {
    _token: $("[name=_token]").val(),
    id: id,
    date: date,
    remark: remark
  };
  $.ajax({
    url: "/holidays/updateHoliday",
    type: "PATCH",
    data: data
  })
    .done(res => {
      loadData();
    })
    .fail(error => {
      displayError(error);
    });
}

/**
 * validate logtime form
 */
function validateForm() {
  date = $("#holiday-date").val();
  return /^\d{4}-(0?[1-9]|1[012])-(0?[1-9]|[12][0-9]|3[01])$/.test(date);
}
