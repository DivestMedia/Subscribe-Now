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
      register_setting('wp_plugin_template-group', 'setting_a');
      register_setting('wp_plugin_template-group', 'setting_b');
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
        `firstname` TINYTEXT,
        `middlename` TINYTEXT,
        `lastname` TINYTEXT,
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
  wp_localize_script( 'login', 'ajax_subscribenow_object', array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'redirecturl' => home_url(),
    'loadingmessage' => __('Sending user info, please wait...')
  ));
}
function ajax_subscribe_save(){

  // First check the nonce, if it fails the function will break
  check_ajax_referer( 'ajax-subscription-nonce', 'security' );

  // Nonce is checked, get the POST data and sign user on
  $info = array();
  $info['email'] = $_POST['email'];

  if ( is_wp_error() ){
    echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
  } else {
    echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...')));
  }

  die();
}
