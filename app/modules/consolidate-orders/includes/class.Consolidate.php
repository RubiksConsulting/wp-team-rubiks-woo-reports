<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Consolidate_Orders;
use WP_Team_Rubiks_Woo\Reports\Core as Core;

abstract class Consolidate extends Core\Reports {

  abstract protected function __construct ( $date = false );

  abstract protected function backdate ( );

  protected function set_args ( $args = array( ) ) {
    $default_args = array (
      'post_type'   => array ( 'shop_order' ),
      'post_status' => array ( 'wc-completed' , 'wc-processing' ),
      'order'       => 'ASC'
    );
    $this->args = array_merge( $default_args , $args );
  }

  protected function set_columns ( ) {
    $this->columns = array (
      'order_id'        => 'Invoice No.',
      'payment_date'    => 'Date',
      'customer_name'   => 'Customer',
      'phone_number'    => 'Phone',
      'email_address'   => 'Email',
      'product_name'    => 'Product',
      'product_meta'    => 'Product Meta',
      'product_price'   => 'Price',
      'product_cost'    => 'Cost',
      'product_profit'  => 'Profit',
      'order_total'     => 'Order Total',
      'payment_method'  => 'Payment Method',
      'order_notes'     => 'Notes'
    );
  }

  protected function get_report_data ( ) {
    return get_posts( $this->args );
  }

  protected function get_formatted_report_data ( ) {
    $this->add_blank_row();
    $this->posts = $this->get_report_data();
    $this->footer['order_id'] = array( 'value' => count($this->posts) , 'label' => 'Total Orders');
    $this->footer['order_total'] = array( 'value' => array ( ) , 'label' => 'Order Total');
    $this->footer['product_name'] = array ( 'value' => 0 , 'label' => 'Total Products');
    foreach( $this->posts as $post ) {
      $order = $this->get_order_from_post ( $post );
      $items = $order->get_items ( );
      $total_items = count ( $items );
      // foreach( $items as $item ){
      //   $this->rows[] = $this->get_formatted_row ( $order , $item , false );
      // }
      $items = array_values ( $items );
      for ( $i = 0; $i < $total_items; $i++ ) {
        $this->footer['product_name']['value']++;
        $end_of_order = ($i+1) == $total_items;
        $this->rows[] = $this->get_formatted_row ( $order , $items[$i] , ( $i < 1 ) );
        if ( $end_of_order ) {
          $this->add_blank_row();
        }
      }
    }
    $option_key = str_replace ( array ( '_report_to_google_drive' , '_backdate' ) , '' , current_action ( ) );
    $report = get_option ( $option_key );
    if ( ! $report[ 'append' ] ) {
      $this->rows[] = $this->get_formatted_footer ( );
    }
    return $this->rows;
  }

  protected function add_blank_row ( ) {
    $data = array ( );
    foreach ( $this->columns as $column => $value ) {
      $data[ $column ] = '';
    }
    $this->rows[] = $data;
  }

  protected function get_formatted_footer ( ) {
    $data = array ();
    foreach( $this->columns as $column => $label ) {
      if ( isset ( $this->footer[ $column ] ) ) {
        $value = $this->footer[ $column ]['value'];
        $prefix = $this->footer[ $column ]['label'];
        if ( is_array ( $value) ) {
          $data[$column] = $prefix.": ".array_sum ( $value );
        }else{
          $data[$column] = $prefix.": ".$value;
        }
      } else {
        $data[$column] = '';
      }
    }
    return $data;
  }

  protected function get_formatted_row ( $order , $item , $first_line = false ) {
    $data = array ( );
    foreach( $this->columns as $column => $label ) {
      $data[$column] = $this->get_column_value ( $order , $item , $column , $first_line );
    }
    return $data;
  }

  protected function get_column_value ( $order , $item , $column , $first_line ){
    if ( $first_line) {
      switch ( $column ) {
        case 'order_id':
          return $order->get_id ( );
        break;
        case 'payment_date':
          if ( $order->has_status ( 'refunded' ) ){
            return date( 'd-m-Y H:i:s', strtotime ($order->get_date_modified ( ) ) );
          }
          return date( 'd-m-Y H:i:s', strtotime ($order->get_date_paid ( ) ) );
        break;
        case 'customer_name':
          return $order->get_formatted_billing_full_name();
        break;
        case 'phone_number':
          return $order->get_billing_phone();
        break;
        case 'email_address':
          return $order->get_billing_email();
        break;
        case 'product_name':
          return $this->format_variation_title ( $item );
        break;
        case 'product_meta':
          return $this->format_variation_attributes ( $item , true );
        break;
        case 'product_price':
          return $this->format_variation_price ( $item );
        break;
        case 'order_total':
          $this->footer['order_total']['value'][] = $order->get_total();
          return $order->get_total();
        break;
        case 'payment_method':
          return $order->get_payment_method_title();
        break;
        case 'order_notes':
          return $order->get_customer_note();
        break;
        default:
          return '';
        break;
      }
    }
    switch ( $column ) {
      case 'product_name':
        $this->footer['product_name'][] = count($this->rows);
        return $this->format_variation_title ( $item );
      break;
      case 'product_meta':
        return $this->format_variation_attributes ( $item , true );
      break;
      case 'product_price':
        return $this->format_variation_price ( $item );
      break;
      default:
        return '';
      break;
    }
  }


  protected function set_report_name ( $name , $date ) {
    if ( ! $date ) {
      $date = new \DateTime ( );
    }

    switch ( $name ) {
      case 'weekly_orders':
        $this->filename = 'WC_' . $date->format('Y-m-d') . ' Orders';
      break;
      case 'weekly_refunds':
        $this->filename = 'WC_' . $date->format('Y-m-d') . ' Refunds';
      break;
      case 'monthly_orders':
        $this->filename = 'MC_' . $date->format ( 'Y-m-d' ) . ' Orders';
      break;
      case 'monthly_refunds':
        $this->filename = 'MC_' . $date->format ( 'Y-m-d' ) . ' Refunds';
      break;
      case 'all':
      case 'master':
        $this->filename = '[Master]Consolidated_Orders';
      break;
      default:
        $this->filename = $name;
      break;
    }
  }

}
