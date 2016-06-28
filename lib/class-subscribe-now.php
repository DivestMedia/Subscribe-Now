<?php
if(!class_exists('SubscribeNow'))
{
  class SubscribeNow
  {
    static $instance;
    public $customers;
    private $tablename = 'subscribers';
    /**
    * Construct the plugin object
    */
    public function __construct()
    {


      add_action('admin_init', array(&$this, 'admin_init'));
      add_action('admin_menu', array(&$this, 'add_menu'));

      add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );

      add_action('init','ajax_subscribe_init');

    } // END public function __construct


    public function admin_init()
    {
      // Set up the settings for this plugin
      $this->init_settings();
      // Possibly do additional admin_init tasks
    } // END public static function activate

    /**
    * Initialize some custom settings
    */
    public function init_settings()
    {
      // register the settings for this plugin
      register_setting('subscribenow-settings-group', 'subscribenow_landing_page');

  } // END public function init_custom_settings()

  /**
  * add a menu
  */
  public function add_menu()
  {

    // add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);

    $hook = add_options_page(
    'Subscribe Now',
    'Subscribe Now',
    'manage_options',
    'wp_plugin_template',
    array(&$this, 'plugin_settings_page'));

    add_action( "load-$hook", array(&$this ,'plugin_settings_option') );
  } // END public function add_menu()

  /**
  * Menu Callback
  */
  public function plugin_settings_page()
  {

    if(!current_user_can('manage_options'))
    {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // Render the settings template
    include(sprintf("%s/templates/settings.php", SUBSCRIBE_NOW_PLUGIN_DIR));
  } // END public function plugin_settings_page()

  /**
  * Menu Options Callback
  */
  public function plugin_settings_option()
  {
    $option = 'per_page';
    $args   = [
      'label'   => 'Subscribers per page',
      'default' => 5,
      'option'  => 'customers_per_page'
    ];

    add_screen_option( $option, $args );

    $this->customers = new Members_List();
  }

  public static function set_screen( $status, $option, $value ) {
    return $value;
  }
  /**
  * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
  * @static
  */

  public static function activate()
  {

    global $wpdb;

    $table = $wpdb->prefix . 'subscribers';

    // create the ECPT metabox database table
    if($wpdb->get_var("show tables like '$table'") != $table)
    {
      $sql = "CREATE TABLE " . $table . " (
      `id` mediumint(9) NOT NULL AUTO_INCREMENT,
      `fullname` TINYTEXT,
      `displayname` VARCHAR(255),
      `contact` VARCHAR(255),
      `email` VARCHAR(100) NOT NULL,
      `activation_key` VARCHAR(255) NOT NULL,
      `status` TINYINT(1) NOT NULL,
      UNIQUE KEY id (id)
    );";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  if ( version_compare( $GLOBALS['wp_version'], SUBSCRIBE_NOW_MIN_WP_VERSION, '<' ) ) {
    load_plugin_textdomain( 'subscribenow' );

    $message = '<strong>'.sprintf( 'Subscribe now %s requires WordPress %s or higher.', SUBSCRIBE_NOW_VERSION, SUBSCRIBE_NOW_MIN_WP_VERSION ).'</strong> '.sprintf('Please <a href="%1$s">upgrade WordPress</a> to a current version.', 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/akismet/download/');

    SubsribeNow::bail_on_activation( $message );
  }
} // END public static function activate


public static function deactivate()
{
  // Do nothing
} // END public static function deactivate
}
}

function ajax_subscribe_init(){
  wp_register_script('subscribenow-ajax', SUBSCRIBE_NOW_PLUGIN_URL . 'assets/subscribenow.js', array('jquery') );
  wp_localize_script( 'subscribenow-ajax', 'ajax_subscribenow_object', array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'redirecturl' => home_url(),
    'loadingmessage' => __('We are now sending confirmation link to your email, please wait...'),
    'redirecturl' => site_url() . '/' . get_option('subscribenow_landing_page')
  ));

  wp_register_script('subscribenow-verify-ajax', SUBSCRIBE_NOW_PLUGIN_URL . 'assets/subscribenow-verify.js', array('jquery') );
  wp_localize_script( 'subscribenow-verify-ajax', 'ajax_subscribenow_object', array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'redirecturl' => home_url(),
    'loadingmessage' => __('Completing your subscription, please wait...'),
    'redirecturl' => site_url() . '/' . get_option('subscribenow_landing_page')
  ));

  add_action( 'wp_ajax_nopriv_ajax_subscribe_save', 'ajax_subscribe_save' );
  add_action( 'wp_ajax_nopriv_ajax_subscribe_verify', 'ajax_subscribe_verify' );
}

function ajax_subscribe_save(){

  // First check the nonce, if it fails the function will break
  check_ajax_referer( 'ajax-subscription-nonce', 'security' );

  // Nonce is checked, get the POST data and sign user on
  $info = array();
  $email = $_POST['email'];
  if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
    $secret = '6Le_uiMTAAAAAEQcjfHmY0F7l3y4nYNDbX8ZGJLX';
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
      $responseData = json_decode($verifyResponse);
      if(!$responseData->success){
        echo json_encode(array('sent'=>false, 'message'=>__('Invalid Captcha code.'),'response'=>$responseData));
        die();
      }
    }
    $newmember = addNewMember($email);
    if ($newmember === 'exist'){
      echo json_encode(array('sent'=>'exist', 'message'=>__('Email already on mailing list. Please check your email')));
    } else if ( is_wp_error($newmember) ){
      echo json_encode(array('sent'=>false, 'message'=>__('Something went wrong.')));
    } else {
      // Send Email
      if(sendConfirmationEmail($email,$_SERVER['HTTP_REFERER']))
      echo json_encode(array('sent'=>true, 'message'=>__('Email confirmation sent. Please check your email')));
      else
      echo json_encode(array('sent'=>false, 'message'=>__('Something went wrong. Contact Web Administrator')));
    }

    die();
  }

  function sendConfirmationEmail($email,$link){

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
      '{btn_confirm_txt}' => 'Confirm my Email Address'
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

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-subscription-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['email'] = trim($_POST['email']);
    $info['fullname'] = ucwords(strtolower(trim($_POST['fullname'])));
    $info['nickname'] = strtolower(trim($_POST['fullname']));
    $info['contact'] = trim($_POST['contact']);
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
      $secret = '6Le_uiMTAAAAAEQcjfHmY0F7l3y4nYNDbX8ZGJLX';
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

    function addNewMember($email){
      require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-member.php');
      $member = new Member();
      return $member->addMemberToList($email);
    }

    function addNewMemberInfo($email,$fullname,$nickname,$contact){
      require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-member.php');
      $member = new Member();
      return $member->addMemberInfo($email,$fullname,$nickname,$contact);
    }
