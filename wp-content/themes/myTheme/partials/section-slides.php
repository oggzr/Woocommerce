<?php

$catID = get_sub_field('category');

$args = array(
  'post_type'			=>'product',
  'posts_per_page'	=> 5,
  'tax_query' => array(
    array(
      'taxonomy' => 'product_cat',
      'terms' => $catID,
      'operator' => 'IN'
    )
  )
);


$loop = new WP_Query($args); ?>

<section class="slides">
<?php


while ( $loop->have_posts() ) : $loop->the_post();
global $product; ?>
<?php if (has_post_thumbnail( $loop->post->id ))
$url = get_the_post_thumbnail_url($loop->post->id, 'full');?>


    <div class="mySlides" style="background-image: url( <?php echo $url ?>)">
      <div class="content">

        <a id="id-<?php the_id(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
          <h2><?php the_sub_field('text') ?></h2>

          <h4>Use the code: <span class="code"><?php the_sub_field('code') ?></span> </h4>

      </a>

    </div>
  </div>



  <?php endwhile; ?>
</section>
<script type="text/javascript">
var slideIndex = 0;
carousel();

function carousel() {
    var i;
    var x = document.getElementsByClassName("mySlides");
    for (i = 0; i < x.length; i++) {
      x[i].style.opacity = "0";
    }
    slideIndex++;
    if (slideIndex > x.length) {slideIndex = 1}
    x[slideIndex-1].style.opacity = "0.9";
    setTimeout(carousel, 5000); // Change image every 2 seconds
}
</script>

<?php  wp_reset_query(); ?>
