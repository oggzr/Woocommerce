<?php
function my_theme_enqueue_styles() {

    $parent_style = 'parent-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',get_stylesheet_directory_uri() . '/assets/css/style.css',array( $parent_style ),wp_get_theme()->get('Version'));
    wp_enqueue_script('btree', 'https://js.braintreegateway.com/web/dropin/1.9.2/js/dropin.min.js');
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

function create_post_type() {
  register_post_type( 'store',
    array(
      'labels' => array(
        'name' => __( 'Stores' ),
        'singular_name' => __( 'Store' )
      ),
      'public' => true,
      'has_archive' => true,
    )
  );
}
add_action( 'init', 'create_post_type' );


function my_acf_init() {

	acf_update_setting('google_api_key', 'AIzaSyDbonxkrQqIDtjVmVaOO-xUpYvFNKkzVIo');
}

add_action('acf/init', 'my_acf_init');

function myRelated($name) {
  $args = array(
		'post_type'			=>'product',
		'posts_per_page'	=> 3,
		'product_cat' => $name
	);

	$loop = new WP_Query($args);

  while($loop->have_posts()) : $loop->the_post();

    wc_get_template_part('content', 'product');

endwhile;


}
