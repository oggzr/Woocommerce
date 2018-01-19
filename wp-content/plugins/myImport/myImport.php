<?php
/*
Plugin Name: Uppgift3
*/

add_action('admin_menu', 'myAdminPage');

function myAdminPage() {
  $page_title = 'Uppgift3';
  $menu_title = 'Uppgift3';
  $capability = 'manage_options';
  $menu_slug  = 'uppgift3';
  $function   = 'myPage';
  $icon_url   = 'dashicons-media-code';
  $position   = 4;

  add_menu_page( $page_title,
                 $menu_title,
                 $capability,
                 $menu_slug,
                 $function,
                 $icon_url,
                 $position );
}

function myPage() {
  checkFile();
  ?>
  <h2>Choose a file to import</h2>
  <p>The file needs to be a CSV that is separated with , </p>
  <form class="" enctype="multipart/form-data" action="" method="post">
    <input type="file" name="CSVFile" id="CSVFile" value="pick a file">
    <input type="submit" name="sub" value="Upload">
  </form>
  <?php

}

function checkFile() {
  if(isset($_FILES['CSVFile'])){
    $csv = $_FILES['CSVFile'];
    move_uploaded_file($_FILES['CSVFile']['tmp_name'], '../wp-content/plugins/myImport/'.basename($_FILES["CSVFile"]["name"]));

    $file = fopen(__DIR__.'/'.basename($_FILES["CSVFile"]["name"]), 'r');

    while (($line = fgetcsv($file)) !== FALSE) {
      break;
    }
    $user_id = get_current_user_id();
    $myErrors = new WP_Error();
    while (($line = fgetcsv($file)) !== FALSE) {

      if (!empty($line[7])){
        if (!validateDate($line[7])) {
          $myErrors->add('start_date', __('Wrong format on start_date please use yyyy-mm-dd'));
        }
      }
      if (!empty($line[8])){
        if(!validateDate($line[8])) {
          $myErrors->add('end_date', __('Wrong format on end_date please use yyyy-mm-dd'));
        }
      }

      if ((integer)$line[5] < 0 ) {
        $myErrors->add('price', __('Price must be a positive number'));
      }
      if ((integer)$line[6] < 0 ) {
        $myErrors->add('sale_price', __('sale price must be a positive number'));
      }

      if(0 < count($myErrors->get_error_messages())) {
        // return $myErrors->get_error_messages();
        echo "<h2 style='color:red;'>ERRORS</h2><ul>";
        $errors = $myErrors->get_error_messages();
        foreach ($errors as $error) {
          echo "<li style='color:red;'>".$error."</li>";
        }
        echo "</ul>";
        return;
      }

      $check = wc_get_product_id_by_sku($line[0]);

      if($check){
        $post_id = $check;
      }else {
        $post = array(
          'post_author' => $user_id,
          'post_content' => $line[3],
          'post_status' => "publish",
          'post_title' => $line[2],
          'post_parent' => '',
          'post_type' => "product",
        );
        $post_id = wp_insert_post( $post, $wp_error );
      }

      if($post_id){
        $attach_id = get_post_meta($product->parent_id, "_thumbnail_id", true);
        add_post_meta($post_id, '_thumbnail_id', $attach_id);
      }

      wp_set_object_terms( $post_id, '', 'product_cat' );
      wp_set_object_terms($post_id, 'variable', 'product_type');

      update_post_meta( $post_id, '_visibility', 'visible' );
      update_post_meta( $post_id, '_stock_status', 'instock');
      update_post_meta( $post_id, 'total_sales', '0');
      update_post_meta( $post_id, '_regular_price', $line[5] );
      update_post_meta( $post_id, '_sale_price', $line[6] );
      update_post_meta( $post_id, '_purchase_note', '' );
      update_post_meta( $post_id, '_featured', "no" );
      update_post_meta( $post_id, '_weight', $line[4] );
      update_post_meta( $post_id, '_length', "" );
      update_post_meta( $post_id, '_width', "" );
      update_post_meta( $post_id, '_height', "" );
      update_post_meta($post_id, '_sku', $line[0]);
      update_post_meta( $post_id, '_product_attributes', array());
      update_post_meta( $post_id, '_sale_price_dates_from', $line[7] );
      update_post_meta( $post_id, '_sale_price_dates_to', $line[8] );
      update_post_meta( $post_id, '_price', $line[5] );
      update_post_meta( $post_id, '_sold_individually', "" );
      update_post_meta( $post_id, '_manage_stock', "no" );
      update_post_meta( $post_id, '_backorders', "no" );
      update_post_meta( $post_id, '_stock', "" );

      if(!empty($line[1])) {
        Create_thumbnail($line[1], $post_id);
      }

    }

    fclose($file);
  }
}

function Create_thumbnail( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
    else                                    $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}

function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
