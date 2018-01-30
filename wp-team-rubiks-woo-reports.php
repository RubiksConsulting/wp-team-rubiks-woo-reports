<?php
/**
 * Plugin Name: WP Team Rubiks Woo Reports
 * Plugin URI: https://github.com/TeamRubiks/WP-Team-Rubiks-Woo-Reports
 * Description: Custom WooCommerce reports that intergrates with google drive.
 * Version: 1.0.0
 * Author: Team Rubiks
 * Author URI: https://teamrubiks.com
 * Requires at least: 4.8
 * Tested up to: 4.8
 *
 * Text Domain: wp-team-rubiks-woo-reports
 *
 * @package WP Team Rubiks Woo Reports
 * @category Core
 * @author Team Rubiks
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
define('WP_TEAM_RUBIKS_WOO_REPORTS_PATH', dirname(dirname(__FILE__)));
define('WP_TEAM_RUBIKS_WOO_REPORTS_FILE', __FILE__);
define('WP_TEAM_RUBIKS_WOO_REPORTS_URI', plugins_url());
define('WP_TEAM_RUBIKS_WOO_REPORTS_VERSION', '1.0.0');
define('WP_TEAM_RUBIKS_WOO_REPORTS_LIBRARIES_PATH', dirname ( WP_TEAM_RUBIKS_WOO_REPORTS_FILE ) . '/app/libraries');
/**
* Autoloader for classes within the \WP_Team_Rubiks_Woo namespace
* @param string $class string containing the class to include
* @return void
*/
function wp_team_rubiks_woo_reports_autoloader($class){
  $parts = explode('\\', $class);
  if($parts[0] != 'WP_Team_Rubiks_Woo'){
    return;
  }
  $plugin = strtolower(str_replace('_', '-', $parts[0].'-'.$parts[1]));
	$parts[0] = 'app';
	$parts[1] = 'modules';
  $file = 'class.' . end($parts) . '.php';
  array_pop($parts);
	$parts[] = 'includes';
  $path = $plugin.'/'.strtolower(str_replace('_', '-', implode('/', $parts)));
	$filepath = WP_TEAM_RUBIKS_WOO_REPORTS_PATH . '/' . $path . '/'. $file;
  require_once($filepath);
}
spl_autoload_register('wp_team_rubiks_woo_reports_autoloader');
$wp_team_rubiks_woo_reports = new WP_Team_Rubiks_Woo\Reports\Core\Setup();
