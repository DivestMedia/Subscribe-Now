<?php

function subscribenow_form($atts){
  $atts = shortcode_atts( array(
    'captcha' => false,
    'redirect' => false
  ), $atts );

  if(!empty($_GET['confirm']) && !empty($_GET['email'])){
    require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-member.php');
    $member = new Member();
    if(md5($_GET['email'])===$_GET['confirm'] && $member->checkEmailExist($_GET['email'])){
      wp_enqueue_script('subscribenow-verify-ajax');
      include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-verify.php';
    }else{
      include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-doesnot-exist.php';
    }
  }else if(!empty($_GET['notice'])){
    switch ($_GET['notice']) {
      case 'thankyou':
      include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-confirm-sent.php';
      break;
      case 'exist':
      $notice = 'Email already on mailing list. Please check your email';
      include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form.php';
      break;
      case 'failed':
      $notice = 'Something went wrong. Please contact Web Administrator';
      include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form.php';
      break;
      case 'verified':
        include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-confirm-verified.php';
        break;
        default:
        include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-doesnot-exist.php';
        break;
      }
    }
    else{
      wp_enqueue_script('subscribenow-ajax');
      include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form.php';
    }
  }

  add_shortcode('subscribenow-form', 'subscribenow_form');
