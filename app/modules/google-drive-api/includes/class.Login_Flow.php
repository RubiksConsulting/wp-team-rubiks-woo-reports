<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Google_Drive_API;

class Login_Flow {

  protected $auth_url = '';

  public function __construct (  ) {
    $this->views_dir = dirname ( dirname ( __FILE__ ) ) . '/views';
  }

  public function output_auth_url_instructions ( $auth_url , $error = false ) {
    
  }
}
