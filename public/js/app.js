$(document).ready(function(){
  $("a.delete").click(function(){
    return confirm("Do you want to delete ?");
  });
  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd',  
  });
  $('.daterangepicker').daterangepicker({
    format: 'YYYY-MM-DD',   
  });
  $('.zoom-image').fancybox();
  
  /*Js for comment module backend*/
  /*$('.select-hidden-user').hide();
  $('.select-hidden-sound').hide();
  $('.show-hidden-user').click(function(){
    if($('.text-hidden-user').is(":visible")) {
        $('.select-hidden-user').show();
        $('.text-hidden-user').hide();
    } else {
        $('.select-hidden-user').hide();
        $('.text-hidden-user').show();    
    }  
  });
  
  $('.show-hidden-sound').click(function(){
    $(this).next('ul').remove();
    if($('.text-hidden-sound').is(":visible")) {
        $('.select-hidden-sound').show();
        $('.text-hidden-sound').hide();
    } else {
        $('.text-hidden-sound').show(); 
        $('.select-hidden-sound').hide();
           
    }  
  });*/
  /*End js for comment module backend*/
  
});