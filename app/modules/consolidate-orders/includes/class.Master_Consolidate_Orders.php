<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Consolidate_Orders;
class Master_Consolidate_Orders extends Consolidate {

  public function __construct ( $date = false ) {
    if ( ! $date ) {
      $date = '-5 minutes';
    }
    $this->set_args ( array (
      'nopaging'    => true,
      'date_query'  => array (
        'after' => $date,
        'inclusive' => true
      )
    ) );
    $this->set_columns ( );
    $this->set_report_name ( 'master' , false );
  }

  public function register_report ( ) {
    $next_five_minute = ceil ( time ( ) / 300) * 300;
    $date = new \DateTime( date ( 'Y-m-d H:i:s' , $next_five_minute ) );
    $date->modify ( '+5 minutes' );
    $date->setTime ( $date->format ( 'H' ), $date->format ( 'i' ) , 00);
    if ( ! wp_next_scheduled ( 'master_consolidate_orders' ) ) {
	    wp_schedule_event($date->getTimestamp ( ), 'wp_team_rubiks_five_minutes', 'master_consolidate_orders' );
    }
  }

  public function backdate ( ) {
    $first_order_date = new \DateTime ( self::get_first_order_date ( ) );
    $first_order_date->modify ( '-1 month' );
    $report = new Master_Consolidate_Orders ( $first_order_date->format ( 'Y-m-d H:i:s' ) );
    $report->report_to_google_drive ( );
  }


}
