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

		            <div class="cloud-locations taxonomy-description">
                                <?php
                                    $args = array(
                                                'echo'       => 0,
                                                'number'     => 0,
                                                'format'     => 'array',
                                                'taxonomy'   => 'location',
                                                'smallest'   => 11,
                                                'largest'    => 18,
                                                'unit'       => 'px',
                                                'pad_counts' => true,
                                            );

                                    $locs = wp_tag_cloud( $args );
                                    $locations = array();

                                    foreach( $locs as $loc ){
                                        preg_match('/(?i)<a([^>]+)>(.+?)<\/a>/', $loc, $output);

                                        $location = new stdClass();
                                        $location->name = $output[2];
                                        $location->link = $output[1];
                                        $location->translation = __( $output[2], 'location-taxonomy' );

                                        array_push( $locations, $location );
                                    }

                                    if( function_exists('sortLocationByTranslation') ){
                                        usort( $locations, 'sortLocationByTranslation' );
                                    }

                                    foreach( $locations as $location ){
                                        echo '<a' . $location->link . '">' . __( $location->translation ) . '</a>';
                                    }
                                ?>
                            </div>
			</header><!-- .page-header -->
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
