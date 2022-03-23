//Generate edit icon
const editIcon = function(cell, formatterParams) {
  //plain text value
  return "<i class='far fa-edit icon'></i>";
};
const eraseIcon = function(cell, formatterParams) {
  //plain text value
  return "<i class='fas fa-eraser icon'></i>";
};
const japDateFormat = function(cell, formatterParams) {
  // console.log(cell.getValue());
  date = moment(cell.getValue());
  return (
    "<span>" +
    date.format("MM月DD日 (") +
    getDayInWeek(date.format("d")) +
    ")" +
    "</span>"
  );
};

const TYPE_USER = 1;
const TYPE_SELF = 2;

const displayMonthColumns = [
  {
    title: "日付",
    field: "date",
    formatter: japDateFormat,
    align: "center",
    width: 100
  },
  {
    title: "種別",
    field: "type.name",
    align: "center",
    width: 100
  },
  {
    title: "",
    field: "type.id",
    visible: false
  },
  {
    title: "出勤",
    field: "start_time",
    align: "center",
    width: 100,
    formatter: "datetime",
    formatterParams: {
      inputFormat: "HH:mm",
      outputFormat: "HH:mm",
      invalidPlaceholder: ""
    }
  },
  {
    title: "退勤",
    field: "end_time",
    align: "center",
    width: 100,
    formatter: "datetime",
    formatterParams: {
      inputFormat: "HH:mm",
      outputFormat: "HH:mm",
      invalidPlaceholder: ""
    }
  },
  {
    title: "労働時間",
    field: "working_time",
    align: "center",
    width: 100
  },
  {
    title: "最終更新",
    field: "update_time",
    formatter: "datetime",
    formatterParams: {
      inputFormat: "YYYY-MM-DD hh:mm",
      outputFormat: "YYYY-MM-DD hh:mm",
      invalidPlaceholder: ""
    },
    align: "center",
    width: 150
  },
  {
    title: "編集",
    field: "",
    formatter: editIcon,
    align: "center",
    width: 80,
    cellClick: function(e, cell) {
      onCellClick(cell);
    }
  },
  {
    title: "削除",
    field: "",
    formatter: eraseIcon,
    align: "center",
    width: 80,
    cellClick: function(e, cell) {
      onResetTime(cell);
    }
  },
  {
    title: "備考",
    field: "remark",
    align: "left"
  },
  {
    title:"",
    field:"ref_date",
    visible:false
  },
  {
    title: "",
    field: "id",
    visible: false
  }
];
var table;
let summary;
const displayMonthNoEditColumns = [
  {
    title: "日付",
    field: "date",
    formatter: japDateFormat,
    align: "center",
    width: 100
  },
  {
    title: "種別",
    field: "type.name",
    align: "center",
    width: 100
  },
  {
    title: "",
    field: "type.id",
    visible: false
  },
  {
    title: "出勤",
    field: "start_time",
    align: "center",
    width: 100,
    formatter: "datetime",
    formatterParams: {
      inputFormat: "HH:mm",
      outputFormat: "HH:mm",
      invalidPlaceholder: ""
    }
  },
  {
    title: "退勤",
    field: "end_time",
    align: "center",
    width: 100,
    formatter: "datetime",
    formatterParams: {
      inputFormat: "HH:mm",
      outputFormat: "HH:mm",
      invalidPlaceholder: ""
    }
  },
  {
    title: "労働時間",
    field: "working_time",
    align: "center",
    width: 100
  },
  {
    title: "最終更新",
    field: "update_time",
    formatter: "datetime",
    formatterParams: {
      inputFormat: "YYYY-MM-DD hh:mm",
      outputFormat: "YYYY-MM-DD hh:mm",
      invalidPlaceholder: ""
    },
    align: "center",
    width: 150
  },
  {
    title: "備考",
    field: "remark",
    align: "left"
  },
  {
    title: "",
    field: "id",
    visible: false
  }
];
/**
 * Init table data
 */
function initTable(type) {
  table = new Tabulator("#timecard-table", {
    // height: 205, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
    // data: tabledata, //assign data to table
    ajaxResponse: function(url, params, response) {
      console.log(response);
      updateCurrentDate(response);
      updateDisplay();
      return response.timecards; //return the tableData property of a response json object
    },
    align: "center",
    layout: "fitColumns", //fit columns to width of table (optional),
    headerSort: false,
    locale: true,
    index: "user_id",
    rowFormatter: function(row) {
      //row - row component
      var data = row.getData();
      if (data.offday_type == 1 || data.offday_type == 2) {
        row.getElement().style.color = "red"; //apply css change to row element
        row.getElement().style.backgroundColor = "#EFEFEF"; //apply css change to row element
      }
    },
    langs: {
      ja: {
        ajax: {
          loading: "読み込み中…", //ajax loader text
          error: "エラー" //ajax error text
        },
        pagination: {
          first: "最初", //text for the first page button
          first_title: "最初", //tooltip text for the first page button
          last: "最後",
          last_title: "最後",
          prev: "前",
          prev_title: "前",
          next: "次",
          next_title: "次",
          page_size: "サイズ"
        }
      }
    },
    columns:
      type == TYPE_SELF ? displayMonthColumns : displayMonthNoEditColumns,
    rowClick: function(e, row) {
      //trigger an alert message when the row is clicked
    }
  });
  table.setLocale("ja"); //set locale to french
  loadData();
}
/**
 * Log time
 * @param {Integer} type
 * @param {String} workDate
 * @param {String} startTime
 * @param {String} endTime
 * @param {String} remark
 */
function logtime(type, workDate, startTime, endTime, remark, ref_date) {
  let data = {
    _token: $("[name=_token]").val(),
    type: type,
    work_date: workDate,
    start_time: startTime,
    end_time: endTime,
    ref_date: ref_date,
    remark: remark
  };
  $.post({
    url: "/timecards/logtime",
    data: data
  }).done(res => {
    loadData();
  }).fail(error => {
    displayError(error);
  });
}

function resetTimecard(id) {
  let data = {
    _token: $("[name=_token]").val(),
    id: id
  };
  $.ajax({
    url: "/timecards/resetTimecard",
    data: data,
    type: "DELETE"
  }).done(res => {
    loadData();
  }).fail(error => {
    displayError(error);
  });;
}

/**
 * validate logtime form
 */
function validateLogtimeForm() {
  startTime = $("#edit-start-time").val();
  endTime = $("#edit-end-time").val();
  remark = $("#edit-remark").val();
  type = $("#edit-type option:selected").val();
  ref_date = $("#edit-ref-date option:selected").val();

  // Validate form
  let isValidStart =
    !startTime || /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])?$/.test(startTime);
  if (!isValidStart) {
    $("#edit-start-error").show();
    $("#edit-start-error").text("正しく時刻を入力してください。");
  }
  let isValidEnd =
    !endTime || /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])?$/.test(endTime);
  if (!isValidEnd) {
    $("#edit-end-error").show();
    $("#edit-end-error").text("正しく時刻を入力してください。");
  }
  let isValid = isValidStart && isValidEnd;
  if (isValid && endTime && !startTime) {
    $("#edit-start-error").show();
    $("#edit-start-error").text("出勤時間も入力してください。");
    isValid = false;
  }

  if(type == 4)
  {
    if(!ref_date){
      $("#edit-ref-error").show();
      $("#edit-ref-error").text("後日振休も入力してください。");
      isValid = false;
    }
  }

  if (
    isValid &&
    endTime &&
    moment(date.format("YYYY-MM-DD ") + startTime).isAfter(
      moment(date.format("YYYY-MM-DD ") + endTime).format("YYYY-MM-DD HH:mm"),
      "minute"
    )
  ) {
    $("#edit-end-error").show();
    $("#edit-end-error").text("退勤時間は出勤時間より入力してください。");
    isValid = false;
  }
  return isValid;
}

function onResetTime(cell) {
  let row = cell.getRow();
  let id = row.getCell("id").getValue();
  let date = moment(row.getCell('date').getValue());
  if (!id) {
    return;
  }
  $("#confirm-title").text('タイムカードリセット');
  $("#confirm-message").text(date.format("MM月DD日") + 'のタイムカードをリセットしますか？');
  $("#confirm-ok")
    .off("click")
    .click(function() {
      console.log('delete click');
      $("#confirm-modal .close").click();
      resetTimecard(id);
    });
  $("#confirm-modal").modal();
}