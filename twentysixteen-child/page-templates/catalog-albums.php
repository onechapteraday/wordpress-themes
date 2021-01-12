<?php
/**
 * Template Name: Albums Catalog Page
 *
 * Description: A page template for a Albums Catalog Page
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
                                    do_shortcode( '[wp_breadcrumb_taxonomy page=catalog_album]' );
                                }

			    	the_title( '<h1 class="page-title">', '</h1>' );
			    ?>

		            <div class="cloud-tags taxonomy-description">
                                <?php
                                    echo get_the_content();
                                ?>
                            </div>
			</header><!-- .page-header -->

                        <div class="catalog-mosaic">
                            <?php
                                $args = array(
                                            'numberposts' => -1,
                                            'post_type'   => 'album',
                                        );

                                $posts = get_posts( $args );
                                $posts_ = array();

                                if( sizeof( $posts ) ){
                                    function catalog_sort_by_title_sort( $a, $b ){
                                        $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Œ'=>'OE','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','œ'=>'oe','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');


			                $a_title_sort = ( get_album_title_sort( $a->ID ) != '' ) ? get_album_title_sort( $a->ID ) : get_album_title_original( $a->ID );
			                $b_title_sort = ( get_album_title_sort( $b->ID ) != '' ) ? get_album_title_sort( $b->ID ) : get_album_title_original( $b->ID );

                                        $at = strtolower( strtr( $a_title_sort, $translit ) );
                                        $bt = strtolower( strtr( $b_title_sort, $translit ) );

                                        return strcoll( $at, $bt );
                                    }

                                    usort( $posts, 'catalog_sort_by_title_sort' );

                                    # Display all albums

                                    $count = 1;
                                    $items_per_row = 5;
                                    $last_letter = '';
                                    $numeric_letter = false;
                                    $vowels = array( 'a', 'e', 'i', 'o', 'u' );
                                    $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Œ'=>'OE','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','œ'=>'oe','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');

                                    foreach( $posts as $element ){
			                $element_id      = $element->ID;
			                $element_link    = get_post_permalink( $element_id );
				        $element_release = date_i18n( 'j F Y', strtotime( get_album_date_release( $element_id ) ) );
			                $element_title   = get_album_title_original( $element_id );
			                $element_sort    = ( get_album_title_sort( $element_id ) != '' ) ? get_album_title_sort( $element_id ) : $element_title;
                                        $element_sort    = strtolower( strtr( $element_sort, $translit ) );
                                        $current_letter  = ( $element_sort ) ? strtolower( $element_sort[0] ) : '';

                                        # Retrieve authors

                                        $element_authors = get_album_author( $element_id );
                                        $element_featured_authors = get_album_author_featured( $element_id );

                                        # Display current letter in catalog

                                        if( is_numeric( $current_letter ) ){
                                            if( !$numeric_letter ){
		                                echo '<div id="catalog-num" class="catalog-input catalog-numeric">#</div>';
                                                $numeric_letter = true;
		                            }
                                        } else {
		                            if( $last_letter != $current_letter ) {
		                                echo '<div id="catalog-letter-' . $current_letter . '" class="catalog-input catalog-letter">' . strtoupper( $current_letter ) . '</div>';
		                                $last_letter = $current_letter;
		                            }
		                        }

                                        # Display album

                                        ?>
                                        <div class="catalog-element">
                                            <?php
                                            echo '<span class="catalog-element-title"><a href="' . $element_link . '">' . str_replace( '\'', '’', $element_title ) . '</a></span>';

                                            if( $element_authors ){
                                                echo '<span class="catalog-element-authors">';

                                                $item_count = 0;
                                                $letter_1st = $element_authors[$item_count]->name[0];
                                                $letter_2nd = $element_authors[$item_count]->name[1];

                                                if( in_array( strtolower( $letter_1st ), $vowels ) || ( strtolower( $letter_1st ) == 'y' && !in_array( strtolower( $letter_2nd ), $vowels ) ) ){
					            echo _x( ' by ', 'album catalog before author elision', 'twentysixteen-child' );
                                                } else {
					            echo _x( ' by ', 'album catalog before author normal form', 'twentysixteen-child' );
                                                }

			                        foreach( $element_authors as $author ){
				                    if( $item_count > 0 ){
                                                        if( $item_count == sizeof( $element_authors )-1 ){
					                    echo _x( ' and ', 'album catalog additional author', 'twentysixteen-child' );
                                                        } else {
                                                            echo ', ';
                                                        }
                                                    }

				                    echo '<a href="' . get_term_link( $author->term_id ) . '" class="catalog-element-author">' . str_replace( '\'', '’', $author->name ) . '</a>';
                                                    $item_count++;
				                }

                                                if( $element_featured_authors ){
                                                    $item_count = 0;
                                                    echo ' featuring ';

			                            foreach( $element_featured_authors as $author ){
				                        if( $item_count > 0 ){
                                                            if( $item_count == sizeof( $element_featured_authors )-1 ){
					                        echo _x( ' and ', 'album catalog additional author', 'twentysixteen-child' );
                                                            } else {
                                                                echo ', ';
                                                            }
                                                        }

				                        echo '<a href="' . get_term_link( $author->term_id ) . '" class="catalog-element-author">' . $author->name . '</a>';
                                                        $item_count++;
				                    }
				                }

                                                echo '</span>';
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                <?php
                            }
                        ?>
                    </div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
