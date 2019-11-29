<?php
/**
 * Template Name: Locations Cloud Page
 *
 * Description: A page template for a Locations Cloud Page
 *
 * @package Twenty Sixteen Child
 * @since Twenty Sixteen Child 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">


			<header class="page-header">
                            <?php
                                if( shortcode_exists( 'wp_breadcrumb_taxonomy' ) ) {
                                    do_shortcode( '[wp_breadcrumb_taxonomy page=location]' );
                                }
                            ?>

			    <?php
			    	the_title( '<h1 class="page-title">', '</h1>' );
			    ?>
			</header><!-- .page-header -->
		        <div class="taxonomy-description">
                            <?php
                                $locations = get_terms( 'location' );

                                foreach( $locations as $trans_location ) {
                                    $trans_location->translation = __( $trans_location->name, 'location-taxonomy' );
                                }

                                if( function_exists('sortLocationByTranslation') ){
                                    usort( $locations, 'sortLocationByTranslation' );
                                }

                                echo '<ul>';
                                foreach( $locations as $location ){
                                    echo '<li><a href="' . get_term_link( $location->term_id, 'location' ) . '">' . __( $location->translation ) . '</a></li>';
                                }
                                echo '</ul>';
                            ?>
                        </div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
