$(document).ready(function() {
  const primaryColor = $(".body-wrapper").data("primary-color");

  $(".partner").change(function() {
    $("#list-partner .select-partner").css("border-color", "");
    $(this).parent().css("border-color", primaryColor);
  });
});
