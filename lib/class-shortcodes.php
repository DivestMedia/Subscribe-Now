<?php

function subscribenow_form($atts){
  global $post;
  $atts = shortcode_atts( array(
    'captcha' => false,
    'redirect' => false
  ), $atts );

  $pages = get_pages([
    'child_of' => $post->ID,
    'sort_order' => 'ASC' ,
    'sort_column' => 'post_name'
  ]);

  $childpages = [];
  foreach ($pages as $key => $page) {
    $childpages[$page->post_name] = $page->guid;
  }

  if(!empty($_GET['confirm']) && !empty($_GET['email'])){
    require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-member.php');
    if(md5($_GET['email'])===$_GET['confirm']){
      $member = new Member();
      $isExist = $member->checkEmailExist($_GET['email']);
      if($isExist==false){
        include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-doesnot-exist.php';
      }
      else{
        wp_enqueue_script('subscribenow-verify-ajax');
        $status = $isExist->status;
        if($status==2 && $member->checkMemberConfirmationLink($isExist)==='expired') $status = 'expired';

        switch ($status) {
          case '1':
          include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-already-listed.php';
          break;
          case '2':
          include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-verify.php';
          break;
          case 'expired':
          include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-link-expired.php';
          break;
          default:
          include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-doesnot-exist.php';
          break;
        }
      }
    }else{
      include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-doesnot-exist.php';
    }
  }else if(!empty($_GET['notice'])){
    switch ($_GET['notice']) {
      case 'thankyou':
      if(isset($childpages['thank-you'])){
        ?> <script>window.location.assign('<?=(site_url(get_option('subscribenow_landing_page')) . '/thank-you')?>');</script> <?php
      }else
      include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-confirm-sent.php';
      break;
      case 'exist':
      if(isset($childpages['already-on-mailing-list'])){
        ?> <script>window.location.assign('<?=(site_url(get_option('subscribenow_landing_page')) . '/already-on-mailing-list')?>');</script> <?php
      }else{
        wp_enqueue_script('subscribenow-ajax');
        $notice = 'Email already on mailing list. Please check your email';
        include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form.php';
      }
      break;
      case 'unverified':
        wp_enqueue_script('subscribenow-ajax');
        $resendlink = site_url(get_option('subscribenow_landing_page')) . '?' . http_build_query([
          'resend' => 1,
          'email' => $_GET['email']
        ]);
        $notice = 'Email already on mailing list but needs to be verified, please check your email. Click <a href="'.$resendlink.'">here</a> to resend a confirmation link';
        include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form.php';
        break;
        case 'failed':
        wp_enqueue_script('subscribenow-ajax');
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
