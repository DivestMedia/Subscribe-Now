<?php
if(!class_exists('SubscribeNow'))
{
  class SubscribeNow
  {
    static $instance;
    public $customers;
    private $tablename = 'subscribenow';
    /**
    * Construct the plugin object
    */
    public function __construct()
    {
      global $table_prefix;
      $this->tablename = $table_prefix . $this->tablename;
      add_action('admin_init', array(&$this, 'admin_init'));
      add_action('admin_menu', array(&$this, 'add_menu'));
      add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
      add_action('init','ajax_subscribe_init');

    } // END public function __construct


    public function admin_init()
    {
      $this->init_settings();
    } // END public static function activate

    /**
    * Initialize some custom settings
    */

    public function init_settings()
    {
      register_setting('subscribenow-settings-group', 'subscribenow_landing_page');
      register_setting('subscribenow-recaptcha-settings-group', 'subscribenow_recaptcha_site_key');
      register_setting('subscribenow-recaptcha-settings-group', 'subscribenow_recaptcha_client_key');
    } // END public function init_custom_settings()

    /**
    * add a menu
    */
    public function add_menu()
    {
      $hook = add_options_page(
      'Subscribe Now',
      'Subscribe Now',
      'manage_options',
      'subscribe-now',
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
      if(!$this->checkiftableexists()){
        echo "Table '".$this->tablename."' Does not exist";
      };
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

    public function checkiftableexists(){
      global $wpdb;
      return ($wpdb->get_var("show tables like '$this->tablename'") == $this->tablename);
    }

    public static function activate()
    {

      global $wpdb;
      global $table_prefix;
      $table  = $table_prefix . 'subscribenow';
      // create the ECPT metabox database table
      if($wpdb->get_var("show tables like '$table'") != $table)
      {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table . " (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `fullname` TINYTEXT,
        `displayname` VARCHAR(255),
        `contact` VARCHAR(255),
        `email` VARCHAR(100) NOT NULL,
        `activation_key` VARCHAR(255) NOT NULL,
        `status` TINYINT(1) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        UNIQUE KEY id (id)
      );";

      $wpdb->query($sql);
    }

    if ( version_compare( $GLOBALS['wp_version'], SUBSCRIBE_NOW_MIN_WP_VERSION, '<' ) ) {
      load_plugin_textdomain( 'subscribenow' );

      $message = '<strong>'.sprintf( 'Subscribe now %s requires WordPress %s or higher.', SUBSCRIBE_NOW_VERSION, SUBSCRIBE_NOW_MIN_WP_VERSION ).'</strong> '.sprintf('Please <a href="%1$s">upgrade WordPress</a> to a current version.', 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/akismet/download/');

      SubsribeNow::bail_on_activation( $message );
    }
  } // END public static function activate


  public static function deactivate()
  {
    global $wpdb;
    global $table_prefix;
    $table  = $table_prefix . 'subscribenow';

    // create the ECPT metabox database table
    if($wpdb->get_var("show tables like '$table'") == $table)
    {
      $wpdb->query("DROP TABLE IF EXISTS $table");
      self::subscribe_debug($wpdb->last_error,'1001');
    } // END public static function deactivate
  }

  public function subscribe_debug($wp = '',$code = ''){
    if(!empty($wp)){
      if(SUBSCRIBE_DEBUG){
        echo $wp . ' Error code :  ' . $code;
      }
    }
  }
}
}
