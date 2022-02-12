<?php
/**
 * The template for displaying publisher taxonomy archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
                                <?php
                                    $publisher = get_queried_object();

                                    if( shortcode_exists( 'wp_breadcrumb_publisher' ) ) {
					$publisher_id = $publisher->term_id;
                                        do_shortcode( '[wp_breadcrumb_publisher id=' . $publisher_id . ']' );
                                    }
                                ?>
				<?php
				    $title = single_term_title('', false);
				    echo '<h1 class="page-title">' . $title . '</h1>';

				    the_archive_description( '<div class="taxonomy-description">', '</div>' );

                                    $coll_id = get_term_children( $publisher_id, 'publisher' );

                                    if( $coll_id ){
                                        $collections = array();
                                        $collections_with = array();

                                        foreach( $coll_id as $id ){
                                            $term = get_term( $id, 'publisher' );
                                            array_push( $collections, $term );
                                        }

                                        if( $collections ){
                                            foreach( $collections as $key => $collection ){
                                                if( $collection->count > 0 ){
                                                    array_push( $collections_with, $collection );
                                                }
                                            }
                                        }

                                        if( $collections_with ){
                                            usort( $collections_with, 'sortByCollectionName' );

				            echo '<div class="taxonomy-description publisher-collections">';
				            echo '<p>' . __( 'Collections', 'twentysixteen-child' ) . __( ': ', 'twentysixteen-child' ) . '</p>';
				            echo '<ul>';

                                            foreach( $collections_with as $key => $collection ){
                                               $term_link = get_term_link( $collection );
                                               echo '<li><a href="' . $term_link . '">' . $collection->name . '</a></li>';
                                            }

				            echo '</ul>';
				            echo '</div>';
                                        }
                                    }

				    $publisher_link       = get_publisher_option( 'publisher_link' )      ;
				    $publisher_twitter    = get_publisher_option( 'publisher_twitter' )   ;
				    $publisher_facebook   = get_publisher_option( 'publisher_facebook' )  ;
				    $publisher_instagram  = get_publisher_option( 'publisher_instagram' ) ;
				    $publisher_youtube    = get_publisher_option( 'publisher_youtube' )   ;

				    if( $publisher_link || $publisher_twitter || $publisher_facebook || $publisher_instagram || $publisher_youtube ){
				        echo '<div class="taxonomy-description social-icons">';
				        echo '<p><u>' . __( 'More information', 'twentysixteen-child' ) . '</u>' . __( ': ', 'twentysixteen-child' );


				        if ( $publisher_link ) {
				            echo '<a class="icon-link" href="' . $publisher_link . '" target="_blank" title="'. __( 'Website', 'twentysixteen' ) . '"></a>';
				        }

				        if ( $publisher_facebook ) {
				            echo '<a class="icon-facebook" href="http://facebook.com/' . $publisher_facebook . '" target="_blank" title="Facebook"></a>';
				        }

				        if ( $publisher_twitter ) {
				            echo '<a class="icon-twitter" href="http://twitter.com/' . $publisher_twitter . '" target="_blank" title="Twitter"></a>';
				        }

				        if ( $publisher_instagram ) {
				            echo '<a class="icon-instagram" href="http://instagram.com/' . $publisher_instagram . '" target="_blank" title="Instagram"></a>';
				        }

				        if ( $publisher_youtube ) {
				            echo '<a class="icon-youtube" href="http://youtube.com/' . $publisher_youtube . '" target="_blank" title="YouTube"></a>';
				        }

				        echo '</p>';
				        echo '</div>';
				    }
				?>
			</header><!-- .page-header -->

			<?php
			// Start the Loop.
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			// End the loop.
			endwhile;

			// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous page', 'twentysixteen' ),
				'next_text'          => __( 'Next page', 'twentysixteen' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentysixteen' ) . ' </span>',
			) );

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar( 'book' ); ?>
<?php get_footer(); ?>
