$(document).ready(function(){$("#check-term-condition").change(function(e){const r=$(this).data("primary-color");e.currentTarget.checked?($("#get-voucher").prop("disabled",!1),$("#get-voucher").css("background-color",r),$("#get-voucher").css("cursor","pointer")):($("#get-voucher").prop("disabled",!0),$("#get-voucher").css("background-color","#9CA3AF"),$("#get-voucher").css("cursor","unset"))})});
