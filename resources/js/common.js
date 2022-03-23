const dayInWeek = ["日", "月", "火", "水", "木", "金", "土"];
const DAY_IN_MS = 86400000;
const DISPLAY_DAY = 0;
const DISPLAY_MONTH = 1;

/**
 * compare 2 date
 * @param {Date} date1
 * @param {Date} date2
 * @return: 0 (equal) 1(date2 > date1) -1(date1 > date2)
 */
function compareDate(date1, date2) {
  date1 = (date1.getTime() - (date1.getTime() % DAY_IN_MS)) / DAY_IN_MS;
  date2 = (date2.getTime() - (date2.getTime() % DAY_IN_MS)) / DAY_IN_MS;
  if (parseInt(date1) == parseInt(date2)) {
    return 0;
  } else if (date1 > date2) {
    return -1;
  } else {
    return 1;
  }
}

/**
 * Get YYYY-MM-01 of given date
 * @param {Date} date
 */
function getYearMonthFromDate(date) {
  return new Date(date.getFullYear(), date.getMonth(), 1);
}

/**
 * Get next month from given date
 * @param {Date} date
 */
function getNextMonth(date) {
  if (date.getMonth() == 11) {
    return new Date(date.getFullYear() + 1, 0, 1);
  } else {
    return new Date(date.getFullYear(), date.getMonth() + 1, 1);
  }
}

/**
 * Get prev month from given date
 * @param {Date} date
 */
function getPrevMonth(date) {
  if (date.getMonth() == 0) {
    return new Date(date.getFullYear() - 1, 11, 1);
  } else {
    return new Date(date.getFullYear(), date.getMonth() - 1, 1);
  }
}

/**
 * 
 * @param {Integer} $i 
 */
function getDayInWeek($i) {
  return dayInWeek[$i];
}

/**
 * Add leading zero
 * @param {Integer} num 
 * @param {Integer} size 
 */
function pad(num, size) {
  var s = num+"";
  while (s.length < size) s = "0" + s;
  return s;
}

function displayError(error) {
  console.log(error);
  let message = "エラー発生しました。";
  if (error.hasOwnProperty("responseJSON")) {
    let responseJSON = error.responseJSON;
    if (responseJSON.hasOwnProperty("message")) {
      message = error.responseJSON.message;
    }
    if (responseJSON.hasOwnProperty("errors")) {
      let errors = error.responseJSON.errors;
      for (var prop in errors) {
        message += "<li>" + errors[prop] + "</li>";
      }
    }
  }
  $("#error-message").html(message);
  $("#error-modal").modal("toggle");
}