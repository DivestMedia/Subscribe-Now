<form id="subscribenow" method="POST" action="" class="<?=( $atts['redirect'] ? 'force-redirect' : '')?>">
  <h3>Stay up to date</h3>
  <p>
    Join the weekly newsletter and never miss out on new tips, tutorials, and more.
  </p>
  Email Address: <input type="email" id="email" name="email" value="<?=(!empty($_GET['email']) ? $_GET['email'] : '')?>"><br><br>
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
    <div id="recaptcha"></div>
    <?php
  }
  ?>
  <?php
if(isset($_GET['resend'])){
  ?>
  <input type="hidden" name="resend" value="true"/>
  <?php
}
   ?>
  <p class="status"><?=(!empty($notice) ? $notice : '')?></p><br>
  <button class="btn btn-success" type="submit">Subscribe</button>
  <?php wp_nonce_field( 'ajax-subscription-nonce', 'security' ); ?>

</form>
