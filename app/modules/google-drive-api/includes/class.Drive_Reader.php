<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Google_Drive_API;
use WP_Team_Rubiks_Woo\Reports\Core as Core;

class Drive_Reader extends Drive_Helpers{

  public function __construct ( $client ) {
    $this->client = $client;
  }

  public function does_file_exist ( $name , $file_id ) {
    if ( $file_id == '' ){
      return false;
    }
    $service   = new \Google_Service_Drive ( $this->client );
    $response = $service->files->listFiles ( array (
      'q'         => 'name = "'.$name.'" and trashed = false',
      'spaces'    => 'drive',
      'pageToken' => null,
      'fields'    => 'nextPageToken, files(id, name)',
    ) );
    foreach ( $response->files as $file ) {
      if ( $file->id == $file_id) {
        return $file;
      }
    }
    return false;
  }

  public function does_folder_exist ( $name , $folder_id ) {
    // if ( $folder_id == '' ){
    //   return false;
    // }
    $service   = new \Google_Service_Drive ( $this->client );
    $response = $service->files->listFiles ( array (
      'q'         => 'name = "'.$name.'" and trashed = false and mimeType = "application/vnd.google-apps.folder" ',
      'spaces'    => 'drive',
      'pageToken' => null,
      'fields'    => 'nextPageToken, files(id, name)'
    ) );
    foreach ( $response->files as $file ) {
      //if ( $file->id == $folder_id) {
        return $file;
      //}
    }
    return false;
  }

  public function download_file ( $filename, $file_id , $save_location ) {
    $file = $this->does_file_exist ( $filename , $file_id );
    if ( ! $file ) {
      return false;
    }
    $service   = new \Google_Service_Drive ( $this->client );
    //$content = $service->files->export ( $file_id , 'text/csv' );
    $response = $service->files->export($file_id, 'text/csv', array(
    'alt' => 'media'));
    $content = $response->getBody()->getContents();
    file_put_contents ( $save_location , $content);
    return true;
  }

}
