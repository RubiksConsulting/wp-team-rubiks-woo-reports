<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Google_Drive_API;

class Grant_Access extends Drive_Helpers{

  protected $client = '';

  protected $auth_code = false;

  public function __construct ( $auth_code ) {
    $this->auth_code = $auth_code;
  }

  public function new_login ( $error = false ) {
    $auth_url = $this->client->createAuthUrl();
    return array ( 'auth_url' => $auth_url , 'error' => $error);
  }

  public function auth_code_login ( ) {
    return $this->login_via_auth_code ( );
  }

  public function client_secret_login ( ) {
    $this->login_via_client_secret ( );
    return $this->client;
  }
  //
  // public function auth_login_flow ( ) {
  //   $login_flow = new Login_Flow ( $this->client->createAuthUrl() , $this->auth_code );
  //   $login_flow->output_auth_url_instructions ( );
  // }



  public function login ( ) {
    $this->setup_client ( );
    if ( $this->auth_code ) {
      return $this->auth_code_login ( );
    } else if ( ! $this->auth_code && file_exists ( $this->get_client_credentials ( ) ) ) {
      return $this->client_secret_login ( );
    }
    return $this->new_login( );
  }
}
