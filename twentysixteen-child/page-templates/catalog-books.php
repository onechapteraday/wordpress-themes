<?php
/**
 * Template Name: Books Catalog Page
 *
 * Description: A page template for a Books Catalog Page
 *
 * @package Twenty Sixteen Child
 * @since Twenty Sixteen Child 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">


			<header class="page-header">
                            <?php
                                #if( shortcode_exists( 'wp_breadcrumb_taxonomy' ) ) {
                                #    do_shortcode( '[wp_breadcrumb_taxonomy page=tag]' );
                                #}
                            ?>

			    <?php
			    	the_title( '<h1 class="page-title">', '</h1>' );
			    ?>

		            <div class="cloud-tags taxonomy-description">
                                <?php
                                    $args = array(
                                                'numberposts' => -1,
                                                'post_type'   => 'book',
                                                #'smallest'    => 11,
                                                #'largest'     => 18,
                                                #'format'      => 'array',
                                                #'echo'        => 0,
                                                #'unit'        => 'px',
                                            );

                                    $posts = get_posts( $args );
                                    $posts_ = array();

                                    if( sizeof( $posts ) ){
                                        function catalog_sort_by_title_sort( $a, $b ){
                                            $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Œ'=>'OE','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','œ'=>'oe','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');


			                    $a_title_sort = get_book_title_sort( $a->ID );
			                    $b_title_sort = get_book_title_sort( $b->ID );

                                            if( $a_title_sort == '' ){
			                        $a_title_sort = ( get_book_title_read( $a->ID ) != '' ) ? get_book_title_read( $a->ID ) : get_book_title_original( $a->ID );
                                            }

                                            if( $b_title_sort == '' ){
			                        $b_title_sort = ( get_book_title_read( $b->ID ) != '' ) ? get_book_title_read( $b->ID ) : get_book_title_original( $b->ID );
                                            }

                                            $at = strtolower( strtr( $a_title_sort, $translit ) );
                                            $bt = strtolower( strtr( $b_title_sort, $translit ) );

                                            return strcoll( $at, $bt );
                                        }

                                        usort( $posts, 'catalog_sort_by_title_sort' );

                                        ?>
                                        <div class="catalog-mosaic">
                                            <?php
                                            $count = 1;
                                            $items_per_row = 5;

                                            foreach( $posts as $element ){
                                                if( $count % $items_per_row == 1 ){
                                                    ?>
                                                    <div class="catalog-row">
                                                    <?php
                                                }
                                                ?>
                                                <div class="catalog-element">
                                                    <?php
			                            $element_id    = $element->ID;
			                            $element_title = get_book_title_read( $element_id );
			                            $element_link  = get_post_permalink( $element_id );

			                            if( empty( $element_title ) ){
                                                        $element_title = get_book_title_original( $element_id );
                                                    }

                                                    $element_cover = get_the_post_thumbnail( $element_id, 'twentysixteenchild-medium-portrait' );
					            $element_release_date = date_i18n( 'j F Y', strtotime( get_book_date_release( $element_id ) ) );

                                                    ?>
                                                    <figure class="catalog-element-cover">
                                                        <?php
                                                        echo $element_cover;
                                                        ?>
                                                    </figure>
                                                    <div class="catalog-element-data">
                                                        <?php
                                                        echo '<div class="catalog-element-link"><a href="' . $element_link . '">' . $element_title . '</a></div>';
                                                        echo '<div class="catalog-element-title"><a href="' . $element_link . '">' . $element_title . '</a></div>';
                                                        echo '<div class="catalog-element-date">' . $element_release_date . '</div>';
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                                if( $count % $items_per_row == 0 ){
                                                    ?>
                                                    </div>
                                                    <?php
                                                }
                                                $count++;
                                            }
                                            if( $count-1 % $items_per_row != 0 ){
                                                ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <div class="catalog-clear"></div>
                                        </div>
                                        <?php
                                    }
                                ?>
                            </div>
			</header><!-- .page-header -->
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
