<?php get_header() ?>

<?php
if(have_posts()) :
  while(have_posts()) : the_post(); ?>



  <article class="post">
    <div class="leftColumn">
      <?php if (has_post_thumbnail( get_the_ID() ))
      echo get_the_post_thumbnail(get_the_ID(), 'medium');
       else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="product placeholder Image" width="300px" height="300px" />'; ?>
    </div>
    <div class="rightColumn">
        <h2><?php the_title() ?></h2>
        <?php  the_content(); ?>
    </div>
    <div class="og-related">
      <h2>Related Products</h2>
    </div>
    <div class="related">
      <?php
      if(get_field('related')){
        $name = get_field('related');
        $name = $name->name;
        myRelated($name);
        }
       ?>
    </div>
  </article>


  <?php
endwhile;
endif;


get_footer() ?>
