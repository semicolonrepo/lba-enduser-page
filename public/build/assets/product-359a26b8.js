$(document).ready(function(){$("#check-term-condition").change(function(e){e.currentTarget.checked?($("#get-voucher").prop("disabled",!1),$("#get-voucher").css("opacity",1),$("#get-voucher").css("cursor","pointer")):($("#get-voucher").prop("disabled",!0),$("#get-voucher").css("opacity",.6),$("#get-voucher").css("cursor","unset"))})});
