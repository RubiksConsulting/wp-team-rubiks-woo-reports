<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Woocommerce;
use WP_Team_Rubiks_Woo\Reports\Google_Drive_API as Google_Drive_API;
class Report_Tabs {

  /**
  * Class constructor
  * @return void
  */
  public function __construct(){
    $this->init_hooks();
  }

  /**
  * Registers custom function with woocommerce hooks
  * @return void
  */
  public function init_hooks(){
    add_filter('woocommerce_admin_reports', array( $this, 'add_report_tabs'));
  }

  /**
  * Adds custom tabs to WooCommerce reports
  * @param array $reports - array of tabs
  * @return array $reports - modified $reports tab
  */
  public function add_report_tabs($reports){
    $reports['google_drive'] = array(
			'title'  => __( 'Google Drive', 'woocommerce' ),
			'reports' => array(
				'grant_access' => array(
					'title'       => __( 'Settings', 'woocommerce' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_tab' ),
				),
        'run_reports' => array(
					'title'       => __( 'Reports', 'woocommerce' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_tab' ),
				)
			)
		);
    return $reports;
  }

  /**
  * Helper to include the correct class based on the current report
  * @param string $name - report being viewed
  * @return void
  */
  public function get_tab($name){
    return include dirname ( WP_TEAM_RUBIKS_WOO_REPORTS_FILE ) . '/app/modules/woocommerce/views/report-tab-' . $name . '.php';
  }

  public static function report_settings ( ) {
    $auth_code = ( isset ( $_POST['auth_code'] ) ) ? $_POST[ 'auth_code' ] : false;
    $grantAccess = new Google_Drive_API\Grant_Access ( $auth_code );
    $client = $grantAccess->login ( );
    if ( is_array ( $client ) ){
      $auth_url = $client['auth_url'];
      $error = $client['error'];
      return include dirname ( WP_TEAM_RUBIKS_WOO_REPORTS_FILE ) . '/app/modules/google-drive-api/views/auth_url_instructions.php';
    }
    return include dirname ( WP_TEAM_RUBIKS_WOO_REPORTS_FILE ) . '/app/modules/google-drive-api/views/report_settings.php';

  }

  public static function report_actions ( ) {
    $reports = apply_filters ( 'get_wp_team_rubiks_woo_reports' , false , true);
    return include dirname ( WP_TEAM_RUBIKS_WOO_REPORTS_FILE ) . '/app/modules/google-drive-api/views/report_actions.php';
  }
}
