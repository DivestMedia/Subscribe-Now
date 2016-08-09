<form id="subscribenow" method="POST" action="" class="<?=( $atts['redirect'] ? 'force-redirect' : '')?> nomargin">
  <?php if($atts['minimal']){ ?>
    <fieldset class="nopadding nomargin">
      <div class="align-left input-group nopadding nomargin transparent">
        <input type="email" id="email"  name="email" placeholder="<?=$atts['placeholder']?>" class="form-control" required>
        <span class="input-group-btn">
          <button class="btn btn-default" type="submit">Subscribe</button>
        </span>
      </div>
    </fieldset>
    <?php }else{ ?>
      <h3>Stay up to date</h3>
      <p>
        Join the weekly newsletter and never miss out on new tips, tutorials, and more.
      </p>
      <label for="email">Email Address: </label>
      <input type="email" id="email" name="email" value="<?=(!empty($_GET['email']) ? $_GET['email'] : '')?>" placeholder="<?=$atts['placeholder']?>" class="form-control" style="max-width:300px;"><br>
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
      <p class="status"><?=(!empty($notice) ? $notice : '')?></p><br>
      <button class="btn btn-default" type="submit">Subscribe</button>
      <?php } ?>
      <?php
      if(isset($_GET['resend'])){
        ?>
        <input type="hidden" name="resend" value="true"/>
        <?php
      }
      ?>
      <?php wp_nonce_field( 'ajax-subscription-nonce', 'security' ); ?>

    </form>
