import moment from "moment";
function formatDate(date, format = "fromNow", locale = "en") {
  if (!date) return "-";
  try {
    let momentDate;
    if (moment.isMoment(date)) {
      momentDate = date;
    } else if (typeof date === "string") {
      momentDate = moment(date, [
        "YYYY-MM-DDTHH:mm:ss.SSSZ",
        "YYYY-MM-DDTHH:mm:ssZ",
        "YYYY-MM-DDTHH:mm:ss",
        "YYYY-MM-DD HH:mm:ss",
        "YYYY-MM-DD",
        "MMM Do, YYYY",
        "MMMM Do, YYYY",
        "Do MMMM, YYYY",
        "Do MMM, YYYY",
        "DD/MM/YYYY",
        "MM/DD/YYYY",
        "DD-MM-YYYY",
        "MM-DD-YYYY"
      ], true);
      if (!momentDate.isValid()) {
        momentDate = moment(date);
      }
    } else if (date instanceof Date) {
      momentDate = moment(date);
    } else {
      momentDate = moment(String(date));
    }
    if (!momentDate.isValid()) {
      console.warn("Invalid date format:", date);
      return "-";
    }
    if (locale && locale !== "en") {
      momentDate = momentDate.locale(locale);
    }
    switch (format) {
      case "fromNow":
        return momentDate.fromNow();
      case "fromNowShort":
        return momentDate.fromNow(true);
      case "date":
        return momentDate.format("MMM DD, YYYY");
      case "datetime":
        return momentDate.format("MMM DD, YYYY HH:mm");
      case "time":
        return momentDate.format("HH:mm");
      case "iso":
        return momentDate.toISOString();
      default:
        return momentDate.format(format);
    }
  } catch (error) {
    console.error("Error formatting date:", error, "Date:", date);
    return "-";
  }
}
export {
  formatDate as f
};
