<?php
/**
 * Template Name: Tags Cloud Page
 *
 * Description: A page template for a Tags Cloud Page
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
                                    do_shortcode( '[wp_breadcrumb_taxonomy page=tag]' );
                                }
                            ?>

			    <?php
			    	the_title( '<h1 class="page-title">', '</h1>' );
			    ?>

		            <div class="cloud-tags taxonomy-description">
                                <?php
                                    $args = array(
                                                'number'    => 0,
                                                'taxonomy'  => 'post_tag',
                                                'smallest'  => 11,
                                                'largest'   => 18,
                                                'format'    => 'array',
                                                'echo'      => 0,
                                                'unit'      => 'px',
                                            );

                                    $tags = wp_tag_cloud( $args );
                                    $tags_ = array();

                                    foreach( $tags as $element ){
                                        preg_match('/(?i)<a([^>]+)>(.+?)<\/a>/', $element, $output);

                                        $element = new stdClass();
                                        $element->name = $output[2];
                                        $element->link = $output[1];

                                        array_push( $tags_, $element );
                                    }

                                    if( function_exists('sortByName') ){
                                        usort( $tags_, 'sortByName' );
                                    }

                                    foreach( $tags_ as $element ){
                                        echo '<a' . $element->link . '">' . $element->name . '</a>';
                                    }
                                ?>
                            </div>
			</header><!-- .page-header -->
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
