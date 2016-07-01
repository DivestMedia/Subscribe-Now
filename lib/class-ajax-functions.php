<?php

function ajax_subscribe_init(){
  wp_register_script('subscribenow-ajax', SUBSCRIBE_NOW_PLUGIN_URL . 'assets/subscribenow.js', array('jquery') );
  wp_localize_script( 'subscribenow-ajax', 'ajax_subscribenow_object', array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'loadingmessage' => __('We are now sending confirmation link to your email, please wait...'),
    'redirecturl' => site_url() . '/' . get_option('subscribenow_landing_page')
  ));

  wp_register_script('subscribenow-verify-ajax', SUBSCRIBE_NOW_PLUGIN_URL . 'assets/subscribenow-verify.js', array('jquery') );
  wp_localize_script( 'subscribenow-verify-ajax', 'ajax_subscribenow_object', array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'loadingmessage' => __('Completing your subscription, please wait...'),
    'redirecturl' => site_url() . '/' . get_option('subscribenow_landing_page')
  ));

  add_action( 'wp_ajax_nopriv_ajax_subscribe_save', 'ajax_subscribe_save' );
  add_action( 'wp_ajax_nopriv_ajax_subscribe_verify', 'ajax_subscribe_verify' );
}

function ajax_subscribe_save(){

  check_ajax_referer( 'ajax-subscription-nonce', 'security' );

  $info = array();
  $email = $_POST['email'];
  if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
    $secret = get_option('subscribenow_recaptcha_site_key');
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
      $responseData = json_decode($verifyResponse);
      if(!$responseData->success){
        echo json_encode(array('sent'=>false, 'message'=>__('Invalid Captcha code.'),'response'=>$responseData));
        die();
      }
    }
    $forceupdate = isset($_POST['resend']) ? true : false;
    $newmember = addNewMember($email,$forceupdate);
    if ($newmember === 'exist'){
      echo json_encode(array('sent'=>'exist', 'message'=>__('Email already on mailing list. Please check your email')));
    } else if ( $newmember === 'unverified' ){
      $resendlink = site_url() . '/' . get_option('subscribenow_landing_page');
      echo json_encode(array('sent'=>'unverified', 'message'=>__('Email already on mailing list but needs to be verified, please check your email. Click <a href="'.$resendlink.'">here</a> to resend a confirmation link')));
    } else if ( is_wp_error($newmember) ){
      echo json_encode(array('sent'=>false, 'message'=>__('Something went wrong.')));
    } else {
      // Send Email
      if(sendConfirmationEmail($email))
      echo json_encode(array('sent'=>true, 'message'=>__('Email confirmation sent. Please check your email')));
      else
      echo json_encode(array('sent'=>false, 'message'=>__('Something went wrong. Contact Web Administrator')));
    }

    die();
  }

  function sendConfirmationEmail($email){
    $link = site_url() . '/' . get_option('subscribenow_landing_page');
    $to = $email;
    $subject = "Please verify your email - Divestmedia Newsletter Subscription";
    $linkparts = parse_url($link);
    $link = rtrim($linkparts['scheme'] . '://' . $linkparts['host'] . $linkparts['path'] , '/') . '?' . http_build_query([
      'confirm' => md5($email),
      'email' => $email
    ]);

    $data = [
      "{link}" => $link,
      '{site_name}' => 'Divestmedia.com',
      '{site_email}' => 'help@divestmedia.com',
      '{btn_confirm_txt}' => 'Confirm your Email Address'
    ];

    ob_start();
    include(SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/email-confirm.tpl');
    $ob = ob_get_clean();
    $content = str_replace(array_keys($data), array_values($data),$ob);

    $headers = 'From: wordpress@marketmasterclass.com' . "\r\n" .
    'Reply-To: wordpress@marketmasterclass.com' . "\r\n" .
    'Content-Type: text/html; charset=UTF-8' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    return mail($to, $subject, $content, $headers);

  }

  function ajax_subscribe_verify(){

    check_ajax_referer( 'ajax-subscription-nonce', 'security' );

    $info = array();
    $info['email'] = trim($_POST['email']);
    $info['fullname'] = ucwords(strtolower(trim($_POST['fullname'])));
    $info['nickname'] = strtolower(trim($_POST['fullname']));
    $info['contact'] = trim($_POST['contact']);
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
      $secret = get_option('subscribenow_recaptcha_site_key');
      $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        if(!$responseData->success){
          echo json_encode(array('sent'=>false, 'message'=>__('Invalid Captcha code.')));
          die();
        }
      }

      $newmember = addNewMemberInfo($info['email'],$info['fullname'],$info['nickname'],$info['contact']);
      if ($newmember === 'exist'){
        echo json_encode(array('sent'=>'notfound', 'message'=>__('Email not found on mailing list.')));
      } else if ( is_wp_error($newmember) ){
        echo json_encode(array('sent'=>false, 'message'=>__('Something went wrong.')));
      } else {
        echo json_encode(array('sent'=>true, 'message'=>__('Subscription Complete. You will now start receiving newsletters')));
      }

      die();
    }

    function addNewMember($email,$force = false){
      require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-member.php');
      $member = new Member();
      return $member->addMemberToList($email,$force);
    }

    function addNewMemberInfo($email,$fullname,$nickname,$contact){
      require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-member.php');
      $member = new Member();
      return $member->addMemberInfo($email,$fullname,$nickname,$contact);
    }
