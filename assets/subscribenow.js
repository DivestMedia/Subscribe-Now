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
        'resend': $('form#subscribenow [name="resend"]').val(),
        'g-recaptcha-response': $('form#subscribenow [name="g-recaptcha-response"]').val(),
        'security': $('form#subscribenow #security').val()
      },
      success: function(data){

        $('form#subscribenow p.status').text(data.message);
        $('form button').text('SUBSCRIBE').prop('disabled',false);
          switch (data.sent) {
            case 'exist':
            case 'false':
            case 'unverified':
            window.location.assign(ajax_subscribenow_object.redirecturl + '?notice=' + data.sent + '&email=' + $('form#subscribenow #email').val());
            break;
            default:
            $('form button').text('EMAIL SENT').prop('disabled',true);
            window.location.assign(ajax_subscribenow_object.redirecturl + '?notice=thankyou');
          }
      },
      error: function(data){
          $('form#subscribenow p.status').show().text('Something went wrong. Please try again later');
      }
    });
    e.preventDefault();
  });

});
