<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Woocommerce;

class Woo_Helpers {

  protected function get_order_from_post ( $post ){
    return new \WC_Order ( $post );
  }

  protected function format_variation_title ( $item ){
    $title = preg_replace('/[0-9+]/', '', $item->get_name ( ) );
    $title = preg_replace('/[+]/', '', $title );
    $title = preg_replace('/[-]/', '', $title );
    $title = preg_replace('/[,]/', '', $title );
    $title = preg_replace('!\s\s+!', '', $title );
    $variation = $this->format_variation_attributes ( $item );
    return $title . ' - ' . $variation;
  }

  public function format_variation_attributes ( $item , $wcj = false) {
    $data = $item->get_data ( );
    $attributes = array();
    foreach ( $data['meta_data'] as $meta_data ) {
      if ( !is_array ( $meta_data->value ) ) {
        if ( $wcj && strpos( $meta_data->key , 'wcj' ) !== false) {
          $attributes[] = str_replace ( array ( "\n" , "\r" ) , array (" | " , " | ") , $meta_data->value);
        } elseif ( !$wcj && strpos( $meta_data->key , 'wcj' ) === false) {
          $attributes[] = $meta_data->value;
        }
      }
    }
    return implode( ' | ', $attributes);
  }

  protected function format_variation_price ( $item ) {
    $data = $item->get_data ( );
    return $data['total'];
  }
}
