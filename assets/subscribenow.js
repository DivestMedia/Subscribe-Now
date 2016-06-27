jQuery(document).ready(function($) {

    // Perform AJAX login on form submit
    $('form#subscribenow').on('submit', function(e){
        $('form#subscribenow p.status').show().text(ajax_subscribenow_object.loadingmessage);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_subscribenow_object.ajaxurl,
            data: {
                'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                'email': $('form#subscribenow #email').val(),
                'security': $('form#subscribenow #security').val() },
            success: function(data){
                $('form#subscribenow p.status').text(data.message);
                if (data.loggedin == true){
                    // document.location.href = ajax_subscribenow_object.redirecturl;
                }
            }
        });
        e.preventDefault();
    });

});
