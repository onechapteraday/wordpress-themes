<?php
/**
 * The template for displaying selection taxonomy archive pages
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
                                    if( shortcode_exists( 'wp_breadcrumb_selection' ) ) {
					$selection_id = get_queried_object()->term_id;
                                        do_shortcode( '[wp_breadcrumb_selection id=' . $selection_id . ']' );
                                    }
                                ?>
				<?php
					$title = single_term_title( '', false );
					echo '<h1 class="page-title">' . $title . '</h1>';

					the_archive_description( '<div class="taxonomy-description">', '</div>' );

				?>
			</header><!-- .page-header -->

			<?php
                        # Display selection by first author's sortname

                        global $wp_query;
                        $posts = $wp_query->posts;

                        function get_author_sortname( $post ){
                            $authors = get_post_meta( $post->ID, 'author' );

                            if( $authors ){
                                $pe_array = explode( ',', $authors[0] );

                                # only use first author
                                $person = get_term_by( 'slug', $pe_array[0], 'person' );

                                # find sortname
                                $person_id   = $person->term_id;
                                $person_meta = get_option( 'taxonomy_' . $person_id );
                                $person_sort = $person_meta['sortname'];

                                return $person_sort;
                            }
                        }

                        function sort_by_author_sortname( $a, $b ){
                            $a_sortname = get_author_sortname ( $a );
                            $b_sortname = get_author_sortname ( $b );

                            return strcasecmp( $a_sortname, $b_sortname );
                        }

                        usort( $posts, 'sort_by_author_sortname' );

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
