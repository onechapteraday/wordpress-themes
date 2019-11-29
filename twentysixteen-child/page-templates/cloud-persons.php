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

		            <div class="cloud-persons taxonomy-description">
                                <?php
                                    $args = array(
                                                'number'    => 0,
                                                'taxonomy'  => 'person',
                                                'smallest'  => 11,
                                                'largest'   => 18,
                                                'unit'      => 'px',
                                            );

                                    wp_tag_cloud( $args );
                                ?>
                            </div>
			</header><!-- .page-header -->
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
