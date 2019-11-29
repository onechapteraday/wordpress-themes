<?php
/**
 * Template Name: Persons Cloud Page
 *
 * Description: A page template for a Persons Cloud Page
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
                                    do_shortcode( '[wp_breadcrumb_taxonomy page=person]' );
                                }
                            ?>

			    <?php
			    	the_title( '<h1 class="page-title">', '</h1>' );
			    ?>
			</header><!-- .page-header -->
		        <div class="taxonomy-description">
                            <?php
                                $args = array(
                                            'number'    => 0,
                                            'taxonomy'  => 'person',
                                            'smallest'  => 14,
                                            'largest'   => 14,
                                            'unit'      => 'px',
                                            'format'    => 'list',
                                        );

                                wp_tag_cloud( $args );
                            ?>
                        </div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
