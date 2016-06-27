jQuery(document).ready(function($) {

    // Perform AJAX login on form submit
    $('form#subscribenow').on('submit', function(e){
        $('form#subscribenow p.status').show().text(ajax_subscribenow_object.loadingmessage);
        $('form button').text('SENDING...').prop('disabled',true);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_subscribenow_object.ajaxurl,
            data: {
                'action': 'ajax_subscribe_verify',
                'email': $('form#subscribenow [name="email"]').val(),
                'fullname': $('form#subscribenow [name="fullname"]').val(),
                'nickname': $('form#subscribenow [name="nickname"]').val(),
                'contact': $('form#subscribenow [name="contact"]').val(),
                'security': $('form#subscribenow #security').val() },
            success: function(data){
                $('form#subscribenow p.status').text(data.message);
                $('form button').text('SUBMIT').prop('disabled',false);
                if (data.sent == true){
                    $('form button').text('VERIFIED').prop('disabled',true);
                }
            }
        });
        e.preventDefault();
    });

});
