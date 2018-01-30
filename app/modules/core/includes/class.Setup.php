<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Core;
use WP_Team_Rubiks_Woo\Reports\Consolidate_Orders as Consolidate_Orders;
use WP_Team_Rubiks_Woo\Reports\Woocommerce as WooCommerce;

class Setup {

  public function __construct ( ) {#
    define('WP_TEAM_RUBIKS_WOO_REPORTS_LOCATION', Config::get_temp_directory ( ) );
    new WooCommerce\Report_Tabs ( );

    $this->init_hooks ( );
    $this->report_hooks ( );
    $this->plugin_hooks ( );
  }

  public function init_hooks ( ) {
    //add_action( 'plugins_loaded' , array( 'WP_Team_Rubiks_Woo\Reports\Woocommerce\Report_Tabs', 'init_hooks' ) );
    add_filter ( 'cron_schedules' , array( new \WP_Team_Rubiks_Woo\Reports\Core\Config(), 'custom_cron_schedules' ) );
    add_filter ( 'get_wp_team_rubiks_woo_reports', array ( '\WP_Team_Rubiks_Woo\Reports\Core\Reports' , 'get_reports' ) , 10 , 2 );
    add_filter ( 'get_wp_team_rubiks_woo_options', array ( '\WP_Team_Rubiks_Woo\Reports\Core\Config' , 'get_options' ) );
  }

  public function report_hooks ( ) {
    $reports = apply_filters ( 'get_wp_team_rubiks_woo_reports' , false , false );
    foreach ( $reports as $key => $value ) {
      $class = $value [ 'namespace' ] . $value [ 'class' ];
      add_action ( $key . '_report_to_google_drive', array( new $class ( ) , 'report_to_google_drive') );
      add_action ( $key . '_backdate', array( new $class ( ) , 'backdate') );
    }
  }

  public function plugin_hooks ( ) {
    register_activation_hook( WP_TEAM_RUBIKS_WOO_REPORTS_FILE, array( 'WP_Team_Rubiks_Woo\Reports\Core\Config', 'install' ) );
    register_deactivation_hook( WP_TEAM_RUBIKS_WOO_REPORTS_FILE, array( 'WP_Team_Rubiks_Woo\Reports\Core\Config', 'uninstall' ) );
  }
}
