<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Consolidate_Orders;
class Weekly_Consolidate_Orders extends Consolidate {

  public function __construct ( $date = false ) {
    //$date = strtotime ( '-1 weeks' );
    if ( ! $date ) {
      $date = new \DateTime ( );
      if ( $date->format ( 'N' ) > 1 ) {
        $date->modify ( 'last monday' );
      }
      $date->modify ( '-1 week' );
    }
    $this->set_args ( array (
      'nopaging'    => true,
      'date_query'  => array (
        array (
          'year'  => $date->format ( 'Y' ),
          'week'  => $date->format ( 'W' ),
        )
      )
    ) );
    $this->set_columns ( );
    $this->set_report_name ( 'weekly_orders' , $date );
  }

  public function register_report ( ) {
    $date = new \DateTime( date ( 'Y-m-d H:i:s' , strtotime( 'next monday' ) ) );
    $date->setTime ( 00 , 00 , 00);
    if ( ! wp_next_scheduled ( 'weekly_consolidate_orders' ) ) {
	    wp_schedule_event($date->getTimestamp ( ), 'wp_team_rubiks_weekly', 'weekly_consolidate_orders' );
    }
  }

  public function backdate ( ) {
    $first_order_date = new \DateTime ( self::get_first_order_date ( ) );
    $date = new \DateTime( date ( 'Y-m-d H:i:s' , strtotime( 'next monday' ) ) );
    if ( $first_order_date->format ( 'N' ) > 1 ) {
      $first_order_date->modify ( 'last monday' );
    }
    do {
      $report = new Weekly_Consolidate_Orders ( $first_order_date );
      $report->report_to_google_drive ( );
      $first_order_date->modify ( '+1 week' );
    } while ($first_order_date->getTimestamp ( ) < $date->getTimestamp ( ) );
  }


}
