<?php get_header() ?>
<article class="stores">
<?php if (have_posts()) :
  while (have_posts()) : the_post(); ?>


    <div class="store">
      <?php if (has_post_thumbnail( get_the_ID() ))
      echo get_the_post_thumbnail(get_the_ID(), 'medium');
       else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="product placeholder Image" width="300px" height="300px" />'; ?>
       <a href="<?php echo get_permalink() ?>"><h2><?php the_title(); ?></h2></a>
       <p><?php the_content(); ?></p>
       <P><?php the_field('address'); ?><P>
    </div>


<?php endwhile;
endif; ?>
</article>
<?php get_footer(); ?>
