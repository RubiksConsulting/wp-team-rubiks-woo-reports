<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Core;

class Helpers {

  protected static $options = array ( );

  public static function get_options ( ) {
    $default_options = self::$options + apply_filters ( 'get_wp_team_rubiks_woo_reports' , false , false);
    foreach( $default_options as $key => $value ) {
      $options[$key] = get_option ( $key );
    }
    return $options;
  }

}
