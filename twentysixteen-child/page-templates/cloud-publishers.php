<?php
/**
 * Template Name: Publishers Cloud Page
 *
 * Description: A page template for a Publishers Cloud Page
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
                                    do_shortcode( '[wp_breadcrumb_taxonomy page=publisher]' );
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
                                            'taxonomy'  => 'publisher',
                                            'smallest'  => 14,
                                            'largest'   => 14,
                                            'unit'      => 'px',
                                            'format'    => 'list',
                                            'parent'    => 0,
                                        );

                                wp_tag_cloud( $args );
                            ?>
                        </div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar( 'book' ); ?>
<?php get_footer(); ?>
