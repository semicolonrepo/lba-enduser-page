$(document).ready(function(){
  let number = 1;
  let max = $('#quantity').attr('max');
  $('#quantity').val(number);

  $('#increment').click(function(){
    if(number < max) {
      number++;
      $('#quantity').val(number);
    }
  });

  $('#decrement').click(function(){
    if(number > 1) {
      number--;
      $('#quantity').val(number);
    }
  });
});
