<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Google_Drive_API;

class Set_Upload_Location extends Drive_Helpers{

  public function __construct ( ) {
    $this->init ( );
  }

  public function init ( ) {
    $client = $this->get_client ( );
    $client->setAuthConfig ( $this->get_client_secret ( ) );
    $client->addScope ( $this->get_scope ( ) );

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
      $client->setAccessToken($_SESSION['access_token']);
      $drive = new \Google_Service_Drive($client);
      $files = $drive->files->listFiles(array())->getFiles();
      echo '<pre>';
      print_r($files);
      echo '</pre>';
    } else {
      //header('Location: ' . filter_var($this->getOAuthURL ( ), FILTER_SANITIZE_URL));
      //echo $redirect_uri;
    }
  }
}
