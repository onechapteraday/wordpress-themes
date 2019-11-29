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

		            <div class="cloud-publishers taxonomy-description">
                                <?php
                                    $parents_arg = array(
                                                       'taxonomy'   => 'publisher',
                                                       'number'     => 0,
                                                       'parent'     => 0,
                                                   );

                                    $parents = get_terms( $parents_arg );
                                    $parents_id = array();

                                    foreach( $parents as $parent ){
                                        array_push( $parents_id, $parent->term_id );
                                    }

                                    $args = array(
                                                'echo'       => 0,
                                                'number'     => 0,
                                                'format'     => 'array',
                                                'taxonomy'   => 'publisher',
                                                'smallest'   => 11,
                                                'largest'    => 18,
                                                'unit'       => 'px',
                                                'pad_counts' => true,
                                            );

                                    $publishers = wp_tag_cloud( $args );

                                    foreach( $publishers as $publisher ){
                                        $check = false;
                                        preg_match('/class=\"([^"]*)\"/', $publisher, $attrs);

                                        foreach( $parents_id as $parent ){
                                            if( strpos( $attrs[1], strval( $parent ) ) !== false ){
                                                $check = true;
                                                break;
                                            }
                                        }

                                        if( $check ){
                                            echo $publisher;
                                        }
                                    }
                                ?>
                            </div>
			</header><!-- .page-header -->
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar( 'book' ); ?>
<?php get_footer(); ?>