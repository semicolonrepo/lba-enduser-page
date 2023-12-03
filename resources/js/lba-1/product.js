$(document).ready(function () {
  $("#check-term-condition").change(function(event) {
    if (event.currentTarget.checked) {
      $("#get-voucher").prop("disabled", false);
      $("#get-voucher").css("opacity", 1);
      $("#get-voucher").css("cursor", "pointer");
    } else {
      $("#get-voucher").prop("disabled", true);
      $("#get-voucher").css("opacity", 0.6);
      $("#get-voucher").css("cursor", "unset");
    }
  });
});
