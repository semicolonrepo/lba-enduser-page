$(".partner").change(function() {
  $("#list-partner .select-partner").css("border-color", "");
  $(this).parent().css("border-color", "#3d97ed");
});
