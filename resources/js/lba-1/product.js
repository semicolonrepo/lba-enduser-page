$(document).ready(function () {
  const primaryColor = $(".body-wrapper").data("primary-color");

  $(".partner").change(function() {
    const termConditionChecked = $("#check-term-condition:checked").length;

    $("#list-partner .select-partner").css("border-color", "");
    $(this).parent().css("border-color", primaryColor);

    if (termConditionChecked) {
      $("#get-voucher").prop("disabled", false);
      $("#get-voucher").css("background-color", primaryColor);
      $("#get-voucher").css("cursor", "pointer");
    } else {
      $("#get-voucher").prop("disabled", true);
      $("#get-voucher").css("background-color", "#9CA3AF");
      $("#get-voucher").css("cursor", "unset");
    }
  });

  $("#check-term-condition").change(function(event) {
    const partnerChecked = $(".partner:checked").length;
    
    if (event.currentTarget.checked && partnerChecked) {
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
