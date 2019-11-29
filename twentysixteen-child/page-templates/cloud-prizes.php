<?php
/**
 * Template Name: Prizes Cloud Page
 *
 * Description: A page template for a Prizes Cloud Page
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
                                    do_shortcode( '[wp_breadcrumb_taxonomy page=prize]' );
                                }
                            ?>

			    <?php
			    	the_title( '<h1 class="page-title">', '</h1>' );
			    ?>

		            <div class="cloud-prizes taxonomy-description">
                                <?php
                                    $parents_arg = array(
                                                       'taxonomy'   => 'prize',
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
                                                'taxonomy'   => 'prize',
                                                'smallest'   => 11,
                                                'largest'    => 18,
                                                'unit'       => 'px',
                                                'pad_counts' => true,
                                            );

                                    $prizes = wp_tag_cloud( $args );
                                    $tags = array();

                                    foreach( $prizes as $element ){
                                        preg_match('/(?i)<a([^>]+)>(.+?)<\/a>/', $element, $output);

                                        $element = new stdClass();
                                        $element->name = $output[2];
                                        $element->link = $output[1];

                                        array_push( $tags, $element );
                                    }

                                    if( function_exists('sortByName') ){
                                        usort( $tags, 'sortByName' );
                                    }

                                    foreach( $tags as $element ){
                                        $check = false;
                                        preg_match('/class=\"([^"]*)\"/', $element->link, $attrs);

                                        foreach( $parents_id as $parent ){
                                            if( strpos( $attrs[1], strval( $parent ) ) !== false ){
                                                $check = true;
                                                break;
                                            }
                                        }

                                        if( $check ){
                                            echo '<a' . $element->link . '">' . $element->name . '</a>';
                                        }
                                    }
                                ?>
                            </div>
			</header><!-- .page-header -->
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar( 'book' ); ?>
<?php get_footer(); ?>
