$(document).ready(function () {
  const primaryColor = $(".body-wrapper").data("primary-color");

  $(".partner").change(function() {
    const termConditionNotChecked = $(".check-term-condition:not(:checked)").length;

    $("#list-partner .select-partner").css("border-color", "");
    $(this).parent().css("border-color", primaryColor);

    if (termConditionNotChecked > 0) {
      $("#get-voucher").prop("disabled", true);
      $("#get-voucher").css("background-color", "#9CA3AF");
      $("#get-voucher").css("cursor", "unset");
    } else {
      $("#get-voucher").prop("disabled", false);
      $("#get-voucher").css("background-color", primaryColor);
      $("#get-voucher").css("cursor", "pointer");
    }
  });

  $(".form-check-input").change(function(event) {
    const partnerChecked = $(".partner:checked").length;
    const termConditionNotChecked = $(".check-term-condition:not(:checked)").length;
    if (termConditionNotChecked > 0 || partnerChecked == 0) {
      $("#get-voucher").prop("disabled", true);
      $("#get-voucher").css("background-color", "#9CA3AF");
      $("#get-voucher").css("cursor", "unset");
    } else {
      $("#get-voucher").prop("disabled", false);
      $("#get-voucher").css("background-color", primaryColor);
      $("#get-voucher").css("cursor", "pointer");
    }
  });
});
