import InApp from 'detect-inapp';

const inapp = new InApp(navigator.userAgent || navigator.vendor || window.opera);

function getOS() {
    const userAgent = window.navigator.userAgent,
        platform = window.navigator?.userAgentData?.platform || window.navigator.platform,
        macosPlatforms = ['macOS', 'Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
        windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
        iosPlatforms = ['iPhone', 'iPad', 'iPod'];
    let os = null;

    if (macosPlatforms.indexOf(platform) !== -1) {
      os = 'Mac OS';
    } else if (iosPlatforms.indexOf(platform) !== -1) {
      os = 'iOS';
    } else if (windowsPlatforms.indexOf(platform) !== -1) {
      os = 'Windows';
    } else if (/Android/.test(userAgent)) {
      os = 'Android';
    } else if (/Linux/.test(platform)) {
      os = 'Linux';
    }

    return os;
  }

  new AutoNumeric.multiple('.autonumeric', {
    allowDecimalPadding: false,
    lZero: "keep",
    minimumValue : '0',
    decimalPlaces : 0,
    digitGroupSeparator : '',
    unformatOnSubmit: true
  });

$(document).ready(function () {
  const primaryColor = $(".body-wrapper").data("primary-color");

  let selectedPartners = $('#list-partner .select-partner[data-partner-checked="true"]');
  selectedPartners.css("border-color", primaryColor);
  $(".name-form").change(function() {
    $(".name-form-selected").val($(this).val());
  });

  $(".phone-number-form").change(function() {
    $(".phone-number-form-selected").val($(this).val());
  });

  $(".partner").change(function() {
    $(".partner-selected").val($(this).val());
    const termConditionNotChecked = $("#check-term-condition:not(:checked)").length;

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
    const termConditionNotChecked = $("#check-term-condition:not(:checked)").length;
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

  function showAlert(message) {
    $("#alert").removeClass("d-none");
    $("#alert").text(message);
    setTimeout(function() {
      $("#alert").addClass("d-none");
    }, 2000);
  }

  $("#get-voucher").click(function() {
    const brand = $(this).data("brand");
    if (brand.toUpperCase() == 'MILO' || brand.toUpperCase() == 'BEARBRAND') {
        if ($("input[name='name_form']").val() == '') {
          showAlert("Harap isi Nama Anda!")
        } else if ($("input[name='phone_number_form']").val() == '') {
          showAlert("Harap isi Nomor Handphone Anda!")
        } else {
          $("#get-voucher").submit();
        }
    }
  });

  $("#login-google").click(function() {
    const partnerChecked = $(".partner:checked").length;
    const termConditionNotChecked = $("#check-term-condition:not(:checked)").length;
    const brand = $(this).data("brand");

    if (partnerChecked == 0) {
      showAlert("Harap pilih Lokasi Penukaran Voucher!")
    } else if (termConditionNotChecked > 0) {
      showAlert("Harap ceklis semua Terms & Conditions!")
    } else {
      const partner = $(".partner:checked").val();
      const separator = $(this).data("url").indexOf('?') !== -1 ? '&' : '?';
      const urlLoginGoogle = $(this).data("url") + separator + 'partner=' + partner;

      if (inapp.isInApp() && getOS() === 'Android') {
        const action = `intent:${urlLoginGoogle}#Intent;end`;
        $("#questionare-form").attr("action", action);
      } else if(inapp.isInApp() && getOS() === 'iOS'){
        const action = urlLoginGoogle;
        $("#questionare-form").attr("action", action);
      } else {
        const action = urlLoginGoogle;
        $("#questionare-form").attr("action", action);
      }

      if (brand.toUpperCase() == 'MILO' || brand.toUpperCase() == 'BEARBRAND') {
        if ($("input[name='name_form']").val() == '') {
          showAlert("Harap isi Nama Anda!")
        } else if ($("input[name='phone_number_form']").val() == '') {
          showAlert("Harap isi Nomor Handphone Anda!")
        } else {
          $("#questionare-form").submit();
        }
      } else {
        $("#questionare-form").submit();
      }
    }
  });

  $("#form-send-otp").submit(function(e) {
    e.preventDefault();

    const partnerChecked = $(".partner:checked").length;
    const termConditionNotChecked = $("#check-term-condition:not(:checked)").length;

    if (partnerChecked == 0) {
      showAlert("Harap pilih Lokasi Penukaran Voucher!")
    } else if (termConditionNotChecked > 0) {
        showAlert("Harap ceklis semua Terms & Conditions!")
    } else {
      e.currentTarget.submit();
    }
  });
});
