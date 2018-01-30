<p>Click the following link to authorise uploads to your Google Drive.</p>
<p>After you've logged in, you'll be presented with a unique authorisation code; copy that code into the text box below and click "Authorise".</p>
<p><input class="regular-text" style="width:75%;background-color:#fff;" value="<?PHP echo $auth_url;?>" readonly /></p>
<form method="post">
  <p><input class="regular-text" name="auth_code" style="width:75%;background-color:#fff;" required placeholder="Enter Auth Code Here" /></p>
  <?PHP if ( $error !== false ):?>
  <span>Error: <?PHP echo $error;?> - Please try again.</span>
  <?PHP endif;?>
  <p><input type="submit" value="Authorise" /></p>
</form>
