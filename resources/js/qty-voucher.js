$(document).ready(function(){
  let number = 1;
  let max = $('#claim-qty').attr('max');
  $('#claim-qty').val(number);

  $('#increment').click(function(){
    if(number < max) {
      number++;
      $('#claim-qty').val(number);
    }
  });

  $('#decrement').click(function(){
    if(number > 1) {
      number--;
      $('#claim-qty').val(number);
    }
  });

  if (max == 1) {
    $('#increment, #decrement').css('display', 'none');
    $('#claim-qty').css('border', '1px solid #4e4e4e');
    $('#claim-qty').css('border-radius', '6px');
  }
});
