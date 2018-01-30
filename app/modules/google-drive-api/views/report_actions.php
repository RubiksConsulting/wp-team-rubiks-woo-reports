<?php
  if ( ! empty ( $_POST ) ) {
    foreach ( $_POST as $key => $value ) {
      $action = $key;//str_replace ( array ( 'run-' , 'install-' , 'view-') , '' , $key );
      if ( has_action ( $action ) ) {
        do_action ( $action );
      }
    }
  }
 ?>
<form method="post" >
  <table class="form-table">
      <?PHP foreach($reports as $report => $options) :?>
        <tr valign="top">
          <th scope="row"><?PHP echo $options['class'];?></th>
          <td>
            <input type="submit" class="button-primary" name="<?PHP echo $report;?>_report_to_google_drive" value="Run" />
            <input type="submit" class="button-secondary" name="<?PHP echo $report;?>_backdate" value="Install" />
            <input type="submit" class="button-secondary" name="<?PHP echo $report;?>" value="View" />
          </td>
          <td>
          </td>
        </tr>
      <?PHP endforeach;?>
  </table>
</form>
