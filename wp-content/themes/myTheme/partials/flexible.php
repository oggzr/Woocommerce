<?php

if ( have_rows( 'sections' ) ) :

	while ( have_rows( 'sections' ) ) :

		the_row();

      if ( get_row_layout() == 'slider' ) :

        get_template_part( 'partials/section' , 'slides' );

        elseif (get_row_layout() == 'content') :

        get_template_part( 'partials/section' , 'content' );

        elseif (get_row_layout() == 'map') :

        get_template_part( 'partials/section' , 'map' );

        elseif (get_row_layout() == 'contact') :

        get_template_part( 'partials/section' , 'contact' );

      endif;

    endwhile;

  endif;
