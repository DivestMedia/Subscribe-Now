<?php defined( 'ABSPATH' ) or die( 'No' );
/**
* Plugin Name: Subscribe Now
* Plugin URI:
* Description: A newsletter subscription plugin.
* Version: 1.0
* Author: Ralph John Galindo
* Author URI:
* License: GPLv2 or later
* Text Domain: dm-subscribe-now
*/

define( 'SUBSCRIBE_NOW_VERSION', '1.0' );
define( 'SUBSCRIBE_NOW_MIN_WP_VERSION', '3.2' );
define( 'SUBSCRIBE_NOW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SUBSCRIBE_NOW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SUBSCRIBE_DEBUG' , true );
require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-member-list.php');
require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-subscribe-now.php');
require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-ajax-functions.php');
require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-shortcodes.php');

if(class_exists('SubscribeNow'))
{

  register_activation_hook(__FILE__, array('SubscribeNow', 'activate'));
  register_deactivation_hook(__FILE__, array('SubscribeNow', 'deactivate'));

  // instantiate the plugin class
  $SubscribeNow = new SubscribeNow();
}

require SUBSCRIBE_NOW_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://raw.githubusercontent.com/DivestMedia/Subscribe-Now/Master/version.json',
    __FILE__
);

$myUpdateChecker->setAccessToken('d02638e6d6b03a259fb54eb6f87d60c90bbf2cf8');
