import InApp from 'detect-inapp';

const inapp = new InApp(navigator.userAgent || navigator.vendor || window.opera);

$(document).ready(function () {
  const primaryColor = $(".body-wrapper").data("primary-color");

  let selectedPartners = $('#list-partner .select-partner[data-partner-checked="true"]');
  selectedPartners.css("border-color", primaryColor);

  $(".partner").change(function() {
    $(".partner-selected").val($(this).val());
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

  function showAlert(message) {
    $("#alert").removeClass("d-none");
    $("#alert").text(message);
    setTimeout(function() {
      $("#alert").addClass("d-none");
    }, 2000);
  }

  $("#login-google").click(function() {
    const partnerChecked = $(".partner:checked").length;
    const termConditionChecked = $("#check-term-condition:checked").length;

    if (partnerChecked == 0) {
      showAlert("Harap pilih Lokasi Penukaran Voucher!")
    } else if (termConditionChecked == 0) {
      showAlert("Harap ceklis Terms & Conditions!")
    } else {
        const partner = $(".partner:checked").val();
        const urlLoginGoogle = $(this).data("url") + '?partner=' + partner;

        if (inapp.isInApp()) {
            window.open(`intent:${urlLoginGoogle};end`, '_blank');
        } else {
            window.location.href = urlLoginGoogle;
        }

    }
  });

  $("#form-send-otp").submit(function(e) {
    e.preventDefault();

    const partnerChecked = $(".partner:checked").length;
    const termConditionChecked = $("#check-term-condition:checked").length;

    if (partnerChecked == 0) {
      showAlert("Harap pilih Lokasi Penukaran Voucher!")
    } else if (termConditionChecked == 0) {
      showAlert("Harap ceklis Terms & Conditions!")
    } else {
      e.currentTarget.submit();
    }
  });
});
