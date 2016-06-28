jQuery(document).ready(function($) {

  $('form#subscribenow').on('submit', function(e){
    $('form#subscribenow p.status').show().text(ajax_subscribenow_object.loadingmessage);
    $('form button').text('SENDING...').prop('disabled',true);
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: ajax_subscribenow_object.ajaxurl,
      data: {
        'action': 'ajax_subscribe_save',
        'email': $('form#subscribenow #email').val(),
        'g-recaptcha-response': $('form#subscribenow [name="g-recaptcha-response"]').val(),
        'security': $('form#subscribenow #security').val()
      },
      success: function(data){

        $('form#subscribenow p.status').text(data.message);
        $('form button').text('SUBSCRIBE').prop('disabled',false);
        if($('form').hasClass('force-redirect')){
          switch (data.sent) {
            case 'exist':
            window.location.assign(ajax_subscribenow_object.redirecturl + '?notice=exist&email=' + $('form#subscribenow #email').val());
            break;
            case 'false':
            window.location.assign(ajax_subscribenow_object.redirecturl + '?notice=failed&email=' + $('form#subscribenow #email').val());
            break;
            default:
            window.location.assign(ajax_subscribenow_object.redirecturl + '?notice=thankyou');
          }
        }
        if (data.sent == true){
          $('form button').text('EMAIL SENT').prop('disabled',true);
          window.location.assign(ajax_subscribenow_object.redirecturl + '?notice=thankyou');
        }
      }
    });
    e.preventDefault();
  });

});
