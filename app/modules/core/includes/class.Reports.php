<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Core;
use WP_Team_Rubiks_Woo\Reports\Woocommerce as WooCommerce;
use WP_Team_Rubiks_Woo\Reports\Google_Drive_API as Google_Drive_API;

abstract class Reports extends WooCommerce\Woo_Helpers {

  protected $args = array ( );

  protected $columns = array ( );

  protected $posts = array();

  protected $rows = array();

  protected $footer = array();

  protected $filename = false;

  protected $fileextension = '.csv';

  protected static $reports = array (
    'master_consolidate_orders'  => array (
      'namespace' => '\WP_Team_Rubiks_Woo\Reports\Consolidate_Orders\\',
      'class'     => 'Master_Consolidate_Orders',
      'append'    => true,
      'drive'     => array (
        'parent'  => '',
        'parent_id'  => '',
        'file_id'      => ''
        )
    ),
    'weekly_consolidate_orders'  => array (
      'namespace' => '\WP_Team_Rubiks_Woo\Reports\Consolidate_Orders\\',
      'class'     => 'Weekly_Consolidate_Orders',
      'append'    => false,
      'drive'     => array (
        'parent'  => '',
        'parent_id'  => '',
        'file_id'      => ''
        )
    ),
    'weekly_consolidate_refunds'  => array (
      'namespace' => '\WP_Team_Rubiks_Woo\Reports\Consolidate_Orders\\',
      'class'     => 'Weekly_Consolidate_Refunds',
      'append'    => false,
      'drive'     => array (
        'parent'  => '',
        'parent_id'  => '',
        'file_id'      => ''
        )
    ),
    'monthly_consolidate_orders'  => array (
      'namespace' => '\WP_Team_Rubiks_Woo\Reports\Consolidate_Orders\\',
      'class'     => 'Monthly_Consolidate_Orders',
      'append'    => false,
      'drive'     => array (
        'parent'  => '',
        'parent_id'  => '',
        'file_id'      => ''
        )
    ),
    'monthly_consolidate_refunds'  => array (
      'namespace' => '\WP_Team_Rubiks_Woo\Reports\Consolidate_Orders\\',
      'class'     => 'Monthly_Consolidate_Refunds',
      'append'    => false,
      'drive'     => array (
        'parent'  => '',
        'parent_id'  => '',
        'file_id'      => ''
        )
    ),

  );

  abstract public function register_report ( );

  abstract protected function set_args ( $args = array ( ) );

  abstract protected function set_columns ( );

  abstract protected function get_report_data ( );

  abstract protected function get_formatted_report_data ( );

  abstract protected function get_formatted_row ( $order , $item , $first_line );

  abstract protected function get_formatted_footer ( );

  abstract protected function get_column_value ( $order , $item , $column , $first_line );

  abstract protected function set_report_name ( $name , $date);

  public static function get_reports ( $single = false , $from_options = false ) {
    if ( $single && ! $from_options ) {
      if ( isset ( self::reports[ $single ] ) ) {
        return self::$reports[ $single ];
      }
      return array ( );
    }
    $stored_reports = array();
    if ( $from_options ) {
      foreach ( self::$reports as $report => $options ) {
        $stored_reports[$report] = get_option ( $report );
      }
      if ( $single ) {
        return $stored_reports [ $single ];
      }
      return $stored_reports;
    }
    return self::$reports;
  }

  public function report_to_csv ( $client , $report ) {
    $old_data = array ( );
    if ( $report [ 'append' ] ) {
      $old_data = $this->get_master_spreadsheet_data ( $client , $report );
    }
    array_unshift ( $old_data , $this->columns);
    $new_data = array_merge ( $old_data , $this->get_formatted_report_data ( ) );
    $this->save_report ( $new_data );
  }

  public function save_report ( $data ) {
    $location = $this->get_full_filepath ( true );
    touch ($location);
    $fp = fopen( $location , 'w');

    foreach ($data as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
  }

  protected function get_master_spreadsheet_data ( $client , $report ) {
    $data = array ( );
    $location = $this->get_full_filepath ( true );
    $drive_reader = new Google_Drive_API\Drive_Reader ( $client );
    if ( ! $drive_reader->download_file ( $this->filename.$this->fileextension , $report['drive']['file_id'] , $location) ) {
      return array ( );
    }
    $csv_data = str_getcsv ( file_get_contents ( $location ) , "\n" );
    foreach ( $csv_data as $row ) {
      $data[] = str_getcsv($row, ","); //parse the items in rows
    }
    //var_dump($data);
    unset($data[0]);
    unlink ( $location );
    return $data;
  }

  public function report_to_google_drive ( ) {
    //$auth_code = ( isset ( $_POST['auth_code'] ) ) ? $_POST[ 'auth_code' ] : false;
    $grantAccess = new Google_Drive_API\Grant_Access ( false );
    $client = $grantAccess->login ( );
    $option_key = str_replace ( array ( '_report_to_google_drive' , '_backdate' ) , '' , current_action ( ) );
    $report = get_option ( $option_key );
    $this->report_to_csv ( $client , $report );

    $drive_writer = new Google_Drive_API\Drive_Writer ( $client );
    $drive_writer->upload_file ( $option_key , $this->get_full_filepath ( ), $report['drive'] , 'application/vnd.google-apps.spreadsheet' );

    unlink ( $this->get_full_filepath ( true ) );
  }

  public function get_full_filepath ( $string = false ) {
    if ( ! $string ) {
      return array (
        'location'  => WP_TEAM_RUBIKS_WOO_REPORTS_LOCATION ,
        'name'      => $this->filename ,
        'extension' => $this->fileextension
      );
    }
    return WP_TEAM_RUBIKS_WOO_REPORTS_LOCATION . '/' . $this->filename . $this->fileextension;
  }

  public function get_first_order_date ( ) {
    $first_post = get_posts ( array (
      'post_type'       => array ( 'shop_order' ),
      'post_status'     => array ( 'wc-completed' , 'wc-processing' , 'wc-refunded'),
      'order'           => 'ASC',
      'posts_per_page'  => 1
    ) );
    $order = wc_get_order ( $first_post[0] );
    return $order->get_date_paid ( );
  }
}
