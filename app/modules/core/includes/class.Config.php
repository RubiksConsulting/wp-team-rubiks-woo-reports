<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Core;
use WP_Team_Rubiks_Woo\Reports\Consolidate_Orders as Consolidate_Orders;

class Config extends Helpers {


  public static function install ( ) {
    self::register_options ( );
    self::deregister_reports ( );
    self::register_reports ( );
    self::create_report_directory ( );
  }

  public static function uninstall ( ) {
    self::remove_report_directory ( );
    self::deregister_reports ( );
    self::deregister_options ( );
  }

  /*======= Cron Methods =======*/
  public function custom_cron_schedules ( $schedules ) {
    $schedules['wp_team_rubiks_quarterly_fifteen'] = array (
  		'interval' => 900,
  		'display' => __('Once Quarterly Fifteen')
  	);
    $schedules['wp_team_rubiks_five_minutes'] = array (
  		'interval' => 300,
  		'display' => __('Once Every 5 minutes')
  	);
    $schedules['wp_team_rubiks_weekly'] = array (
  		'interval' => 604800,
  		'display' => __('Once Weekly')
  	);
  	$schedules['wp_team_rubiks_monthly'] = array (
  		'interval' => 2635200,
  		'display' => __('Once a month')
  	);
  	return $schedules;
  }

  protected static function register_reports ( ) {
    $reports = apply_filters ( 'get_wp_team_rubiks_woo_reports' , false , false);
    foreach ( $reports as $key => $value ) {
      $class = $value [ 'namespace' ] . $value [ 'class' ];
      $class :: register_report ();
    }
  }

  protected static function deregister_reports ( ) {
    $reports = apply_filters ( 'get_wp_team_rubiks_woo_reports' , false );
    foreach ( $reports as $key => $value ) {
      wp_clear_scheduled_hook($key);
    }
  }
  /*======= End Cron Methods =======*/

  /*======= Plugin Options Methods =======*/
  public static function register_options ( $options = array ( ) ) {
    if ( empty ( $options ) ) {
      $options = self::$options + apply_filters ( 'get_wp_team_rubiks_woo_reports' , false );
    }
    foreach( $options as $key => $value ) {
      update_option( $key, $value );
    }
  }

  protected function deregister_options ( ) {
    self::$options = self::$options + apply_filters ( 'get_wp_team_rubiks_woo_reports' , false );
    foreach( self::$options as $key => $value ) {
      delete_option( $key );
    }
  }
  /*======= End Plugin Options Methods =======*/

  /*======= Temp Directory Methods =======*/
  static function get_temp_directory ( ) {
    $upload_folder = wp_upload_dir ( );
    return $upload_folder[ 'basedir' ] . '/wp-team-rubiks-woo-reports';
  }

  protected function create_report_directory ( ) {
    wp_mkdir_p ( self::get_temp_directory ( ) );
  }

  protected function remove_report_directory ( ) {
    rmdir ( self::get_temp_directory ( ) );
  }
  /*======= End Temp Directory Methods =======*/
}
