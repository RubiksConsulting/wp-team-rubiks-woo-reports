<?PHP if ( isset ( $_POST[ 'submit' ] ) ) {
  unset ( $_POST [ 'submit' ] );
  $options = $_POST;
  $drive_writer = new \WP_Team_Rubiks_Woo\Reports\Google_Drive_API\Drive_Writer ( $client );
  foreach($options as $key => $option) {
    $drive_writer->update_drive_folder ( $option['drive']['parent'] , $key );
    $folder_id = $drive_writer->create_folder ($option['drive']['parent'] , $option['drive']['parent_id'] );
    $drive_writer->update_drive_folder_id ( $folder_id ,  $key );
  }
}
$orders = get_posts ( array (
  'post_type' => 'shop_order',
  'nopaging' => true,
  'post_status' => array ('wc-processing', 'wc-completed')
  ) );
//   $total_items = array();
// foreach($orders as $order) {
//   $order = wc_get_order ($order);
//   $items = $order->get_items();
//   $total_items[] = count ($items);
// }
// echo count($orders);
// echo '<br>';
// echo array_sum ($total_items);
//var_dump( _get_cron_array());
?>

<?PHP $fields = apply_filters ( 'get_wp_team_rubiks_woo_options' , false );?>
<table class="form-table">
    <?PHP foreach($fields as $key => $field) :?>
    <form method="post">
      <tr valign="top">
        <th scope="row"><?PHP echo $field['class'];?></th>
        <td>
          <label for="">Google Drive Folder: <?PHP echo $field['drive']['parent_id'];?></label><br/>
          <input type="text" name="<?PHP echo $key;?>[drive][parent]" value="<?php echo $field['drive']['parent']; ?>" placeholder="Enter New Folder Name"/>
          <input type="hidden" name="<?PHP echo $key;?>[drive][parent_id]" value="<?php echo $field['drive']['parent_id']; ?>" placeholder="Enter New Folder Name"/>
        </td>
        <td>
          <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save &amp; Update Google Drive"></p>
        </td>
        <td>
        </td>
      </tr>
    </form>
    <?PHP endforeach;?>

</table>

</div>
