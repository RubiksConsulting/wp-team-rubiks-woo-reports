<?PHP
namespace WP_Team_Rubiks_Woo\Reports\Google_Drive_API;
use WP_Team_Rubiks_Woo\Reports\Core as Core;

class Drive_Writer extends Drive_Helpers{

  public function __construct ( $client ) {
    $this->client = $client;
  }

  public function create_folder ( $folder_name , $folder_id = ''  ) {
    $file = false;
    $service   = new \Google_Service_Drive ( $this->client );
    //if ( $folder_id != '' ) {
    $drive_reader = new Drive_Reader ( $this->client );
    $file = $drive_reader->does_folder_exist ( $folder_name , $folder_id );
    //}

    if ( ! $file ) {
      $file_data = new \Google_Service_Drive_DriveFile ( array (
        'name'      => $folder_name,
        'mimeType'  => 'application/vnd.google-apps.folder'
      ) );
      $file = $service->files->create ( $file_data , array (
        'fields' => 'id'
      ) );
    }
    return $file->id;
  }

  public function upload_file ( $file_key , $filename, $drive = array ( ) , $mime_type ) {
    $drive_reader = new Drive_Reader ( $this->client );
    $service  = new \Google_Service_Drive ( $this->client );
    $update = $drive_reader->does_file_exist ( $filename['name'].$filename['extension'] , $drive['file_id'] );
    $extension = end ( $filename );
    array_pop ( $filename );
    $data = file_get_contents ( implode ( '/' , $filename ) . $extension );
    try {
      if ( $update ) {
        $file     = new \Google_Service_Drive_DriveFile( );
        $file_response = $service->files->update ( $drive['file_id'],  $file , array (
          'data' => $data,
          'mimeType' => $mime_type,
        ) );
      } else {
        $file     = new \Google_Service_Drive_DriveFile( array (
          'name'      => $filename['name'].$extension,
          'mimeType' => $mime_type,
        ) );
        if ( $drive [ 'parent_id' ] != '' ) {
          $file->setParents( array ( $drive [ 'parent_id' ] ) ) ;
        }
        $file_response = $service->files->create ( $file , array (
          'data' => $data,
          'uploadType' => 'resumable',
        ) );
      }
      $this->update_drive_file_id ( $file_response->getId ( ) , $file_key );
    } catch ( \Exception $e ) {
      print "An Exception level error occurred: " . $e->getMessage();
    } catch ( \Google_Service_Exception $e ) {
      print "A Google_Service_Exception level error occurred: " . $e->getMessage();
    }
    if ( $update ) {
      print $filename['name'].' re-generated and re-uploaded to google drive'."<br>";
    } else {
      print $filename['name'].' generated and uploaded to google drive'."<br>";
    }
  }
}
