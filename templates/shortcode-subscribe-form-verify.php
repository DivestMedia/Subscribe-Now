<form id="subscribenow" method="POST" action="">
    <h3>Few more steps to complete your subscription</h3>
    <p>
        Please provide the following details to continue.
    </p>
    <fieldset style="max-width:500px;">
        <label>Email: </label><input type="text" name="email" value="<?=$_GET['email']?>" readonly class="form-control"><br>
        <label>Full Name *: </label><input type="text" name="fullname" maxlength="255" required="" class="form-control"><br>
        <label>Nickname: </label><input type="text" name="nickname" maxlength="255" class="form-control"><br>
        <label>Contact No: </label><input type="text" name="contact" maxlength="255" class="form-control"><br><br>
    </fieldset>
    <?php
    if($atts['captcha']){
        ?>
        <script src='https://www.google.com/recaptcha/api.js?hl=en&onload=reCaptchaCallback&render=explicit'></script>
        <script>
        var RC2KEY = '<?=get_option('subscribenow_recaptcha_client_key')?>',
        doSubmit = false;

        function reCaptchaVerify(response) {
            if (response === document.querySelector('.g-recaptcha-response').value) {
                jQuery('#subscribenow button').prop('disabled',false);
            }
        }

        function reCaptchaExpired () {
            window.location.reload();
        }

        function reCaptchaCallback () {
            jQuery('#subscribenow button').prop('disabled',true);
            grecaptcha.render('recaptcha', {
                'sitekey': RC2KEY,
                'callback': reCaptchaVerify,
                'expired-callback': reCaptchaExpired
            });
        }
        </script>
        <div id="recaptcha"></div><br>
        <?php
    }
    ?>
    <button class="btn btn-default" type="submit">Complete Subscription</button>
    <?php wp_nonce_field( 'ajax-subscription-nonce', 'security' ); ?>
    <p class="status">

    </p>
</form>
