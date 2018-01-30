<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Google_Drive_API;
require_once WP_TEAM_RUBIKS_WOO_REPORTS_LIBRARIES_PATH . '/vendor/autoload.php';
session_start();
class Drive_Helpers {

  protected function get_client ( ) {
    return new \Google_Client();
  }

  public function setup_client ( ) {
    $this->client = $this->get_client ( );
    $this->client->setApplicationName ( $this->get_application_name ( ) );
    $this->client->setAuthConfigFile ( $this->get_client_secret ( ) );
    $this->client->setAccessType("offline");
    $this->client->addScope ( $this->get_scope ( ) );
  }

  protected function get_application_name ( ) {
    return 'Social Followers Woo Reports';
  }

  protected function get_client_credentials ( ) {
    return dirname ( dirname ( __FILE__ ) ) . '/credentials/social_followers_woo_reports.json';
  }

  protected function get_client_secret ( ) {
    return dirname ( dirname ( __FILE__ ) ) . '/credentials/client_secret.json';
  }

  protected function get_scope ( ) {
    return \Google_Service_Drive::DRIVE_FILE;
  }

  protected function get_oauth_url ( ) {
    return 'https://socialfollowers.co.uk/wordpress/wp-admin/admin.php?page=wc-reports&tab=google_drive&report=grant_access';
  }

  protected function login_via_client_secret ( ) {
    $this->access_token = json_decode(file_get_contents( $this->get_client_credentials ( ) ), true );
    $this->client->setAccessToken( $this->access_token );
    if ($this->client->isAccessTokenExpired()) {
      $this->client->fetchAccessTokenWithRefreshToken( $this->client->getRefreshToken ( ) );
      file_put_contents( $this->get_client_credentials ( ) , json_encode( $this->client->getAccessToken ( ) ) );
    }
  }

  protected function login_via_auth_code ( ) {
    $this->access_token = $this->client->fetchAccessTokenWithAuthCode ( $this->auth_code );
    if ( isset ( $this->access_token[ 'error' ] ) ) {
      return $this->new_login ( $this->access_token [ 'error_description' ] );
    }
    $this->client->setAccessToken( $this->access_token );
    file_put_contents( $this->get_client_credentials ( ) , json_encode( $this->client->getAccessToken ( ) ) );
    return $this->client_secret_login ( );
  }

  public static function update_drive_folder ( $folder , $option_key ) {
    $stored_option = get_option ( $option_key );
    $stored_option['drive']['parent'] = $folder;
    update_option( $option_key, $stored_option );
  }

  public function update_drive_folder_id ( $folder , $option_key ) {
    $stored_option = get_option ( $option_key );
    $stored_option['drive']['parent_id'] = $folder;
    update_option( $option_key, $stored_option );
  }

  public function update_drive_file_id ( $file_id , $option_key ) {
    $stored_option = get_option ( $option_key );
    $stored_option['drive']['file_id'] = $file_id;
    update_option( $option_key, $stored_option );
  }
}
