<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Consolidate_Orders;
class All_Consolidate_Orders extends Consolidate {

  public function __construct ( $modify = false ) {
    $this->set_args ( array (
      'nopaging'    => true
    ) );
    $this->set_columns ( );
    $this->set_report_name ( 'all' , false );
  }

  public function register_report ( ) {
    return;
  }


}
