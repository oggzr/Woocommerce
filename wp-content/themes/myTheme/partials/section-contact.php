<?php
if (isset($_POST['sub'])) {
  $attachments;
  if (!empty($_FILES['file'])) {
    $allowed =  array('gif','png' ,'jpg');
    $filename = $_FILES['file']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(!in_array($ext,$allowed) && !empty($_POST['file']) ) {
      echo '<div class="fileError">file must be gif,png ore jpg. Please refresh this page and try again</div>';
      return;
    }else {
      if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
      }


      $uploadedfile = $_FILES['file'];

      $upload_overrides = array( 'test_form' => false );

      $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

      $attachments = array( $movefile['file'] );

      if ( $movefile && ! isset( $movefile['error'] ) ) {
        echo "File is valid, and was successfully uploaded.\n";

      } else {
        echo $movefile['error'];
        $valid = false;
}


    }
  }

  $headers = array(
    'From: '.$_POST['email'],
);
  wp_mail( 'ogg_ish@hotmail.com', $_POST['errand'], $_POST['message'], $headers, $attachments );
  echo "SUCCESS";
}
?>


<form class="" enctype="multipart/form-data" action="" method="post">
  <select class="errand" name="errand" placeholder="Subject">
    <option value="contact">Contact</option>
    <option value="complaint">Complaint</option>
    <option value="invoice">Invoice</option>
  </select>
  <input type="text" name="user" value="" placeholder="Name">
  <input type="email" name="email" value="" placeholder="Email">
  <textarea name="message" rows="8" cols="80" placeholder="Message"></textarea>
  <input type="file" name="file" value="" placeholder="file">
  <input type="hidden" name="action" value="contact_form">
  <input type="submit" name="sub" value="Send">
</form>
