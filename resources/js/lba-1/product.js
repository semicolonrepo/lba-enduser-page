$(document).ready(function () {
  $("#check-term-condition").change(function(event) {
    const primaryColor = $(this).data("primary-color");
    
    if (event.currentTarget.checked) {
      $("#get-voucher").prop("disabled", false);
      $("#get-voucher").css("background-color", primaryColor);
      $("#get-voucher").css("cursor", "pointer");
    } else {
      $("#get-voucher").prop("disabled", true);
      $("#get-voucher").css("background-color", "#9CA3AF");
      $("#get-voucher").css("cursor", "unset");
    }
  });
});
