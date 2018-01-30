<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Consolidate_Orders;
class Monthly_Consolidate_Orders extends Consolidate {

  public function __construct ( $date = false ) {
    if ( $date ) {
      $date->modify ( '-1 month' );
      $after = new \DateTime(date ( 'Y-m', $date->getTimestamp ( ) ) . '-10');
      $date->modify ( '+1 month' );
      $before = new \DateTime(date ( 'Y-m', $date->getTimestamp ( ) ) . '-09');
    } else {
      $after = new \DateTime(date ( 'Y-m', strtotime ( '-1 month' ) ) . '-10');
      $before = new \DateTime(date ( 'Y-m', time() ) . '-09');
    }
    $this->set_args ( array (
      'nopaging'    => true,
      'date_query'  => array (
        'after' => array (
          'year'  => $after->format ( 'Y' ),
          'month'  => $after->format ( 'm' ),
          'day'  => $after->format ( 'd' ),
        ),
        'before' => array (
          'year'  => $before->format ( 'Y' ),
          'month'  => $before->format ( 'm' ),
          'day'  => $before->format ( 'd' ),
        ),
        'inclusive' => true
      )
    ) );
    $this->set_columns ( );
    $this->set_report_name ( 'monthly_orders' , $after );
  }

  public function register_report ( ) {
    $date = new \DateTime( date ( 'Y-m-\1\0 H:i:s' , time() ) );
    $date->modify ( '+1 months' );
    $date->setTime ( 00 , 00 , 00);
    if ( ! wp_next_scheduled ( 'monthly_consolidate_orders' ) ) {
	    wp_schedule_event($date->getTimestamp ( ), 'wp_team_rubiks_monthly', 'monthly_consolidate_orders' );
    }
  }

  public function backdate ( ) {
    $first_order_date = new \DateTime ( self::get_first_order_date ( ) );
    $date = new \DateTime(date ( 'Y-m', time ( ) ) . '-10');
    $i = 0;
    do {
      $report = new Monthly_Consolidate_Orders ( $first_order_date );
      $report->report_to_google_drive ( );
      $first_order_date->modify ( '+1 month' );
    } while ($first_order_date->getTimestamp ( ) < ( $date->getTimestamp ( ) + ( 60 * 60 * 24 * 30 * 1) ) );
  }


}
