<?php

$type = get_sub_field('type');

if ($type === '1'){
  $args = array(
		'post_type'			=>'product',
		'posts_per_page'	=> 4,
		'meta_key' 		 	=> 'total_sales',
		'orderby' 		 	=> 'meta_value_num',
	);

	$loop = new WP_Query($args);
  $message = "Best sellers";

}elseif ($type === '2'){
  $args = array(
    'posts_per_page'    => 4,
    'no_found_rows'     => 1,
    'post_status'       => 'publish',
    'post_type'         => 'product',
    'meta_query'        => WC()->query->get_meta_query(),
    'post__in'          => array_merge( array( 0 ), wc_get_product_ids_on_sale() )
  );

  $loop = new WP_Query( $args );
  $message = "On Sale";

}elseif ($type === '3') {
  $b = get_sub_field('products');
  $args = array(
    'posts_per_page'    => 4,
    'no_found_rows'     => 1,
    'post_status'       => 'publish',
    'post_type'         => 'product',
    'meta_query'        => WC()->query->get_meta_query(),
    'post__in'          => array_merge( array( 0 ), $b )
  );

  $loop = new WP_Query( $args );
  $message = "Featured Pets";

}elseif ($type === '4') {
  $args = array(
    'posts_per_page'    => 4,
    'post_status'       => 'publish',
    'post_type'         => 'post',

);

  $loop = new WP_Query( $args );
  $message = 'Recent Posts';
}

?>

<section class="content">
  <div class="og-row">
    <h2><?php echo $message ?></h2>
  </div>
  <div class="og-row">
    <?php while ( $loop->have_posts() ) : $loop->the_post();
  	global $product; ?>

  	   <div class="og-col">

          <a id="id-<?php the_id(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">

              <?php if (has_post_thumbnail( $loop->post->id ))
          		echo get_the_post_thumbnail($loop->post->id, 'shop_catalog');
         			 else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="product placeholder Image" width="300px" height="300px" />'; ?>
          </a>
          <h2><?php the_title(); ?></h2>
          <?php if($type !== '4'){ ?>
              <span><?php echo 'price: ' . $product->price; ?></span>
        <?php  } ?>
          <span><?php the_excerpt(); ?></span>
      </div>


    <?php endwhile; ?>
  </div>

</section>

<?php wp_reset_query(); ?>
