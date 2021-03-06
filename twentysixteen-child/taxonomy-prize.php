<?php
/**
 * The template for displaying prize taxonomy archive pages
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
                                    if( shortcode_exists( 'wp_breadcrumb_prize' ) ) {
					$prize_id = get_queried_object()->term_id;
                                        do_shortcode( '[wp_breadcrumb_prize id=' . $prize_id . ']' );
                                    }
                                ?>
				<?php
					$title = single_term_title( '', false );
					echo '<h1 class="page-title">' . $title . '</h1>';

					the_archive_description( '<div class="taxonomy-description">', '</div>' );

					$prize_link = get_prize_option( 'prize_link' );

					$prize_1st_selection = get_prize_option( 'prize_1st_selection' );
					$prize_2nd_selection = get_prize_option( 'prize_2nd_selection' );
					$prize_3rd_selection = get_prize_option( 'prize_3rd_selection' );

                                        if( $prize_1st_selection || $prize_2nd_selection || $prize_3rd_selection ){
                                            $selections = array();

                                            if( $prize_1st_selection ){
                                                array_push( $selections,
                                                    array(
                                                        "name" => "Première sélection",
                                                        "slug" => $prize_1st_selection
                                                    )
                                                );
                                            }

                                            if( $prize_2nd_selection ){
                                                array_push( $selections,
                                                    array(
                                                        "name" => "Deuxième sélection",
                                                        "slug" => $prize_2nd_selection
                                                    )
                                                );
                                            }

                                            if( $prize_3rd_selection ){
                                                array_push( $selections,
                                                    array(
                                                        "name" => "Troisième sélection",
                                                        "slug" => $prize_3rd_selection
                                                    )
                                                );
                                            }
                                            ?>
                                            <div class="taxonomy-description">
                                                <ul class="prize-selections">
                                                    <?php
                                                    foreach( $selections as $selection ){
                                                        $term = get_term_by( 'slug', $selection['slug'], 'post_tag' );
                                                        ?>
                                                        <li><a href="<?php echo get_term_link( $term, 'prize' ); ?>"><?php echo $selection['name']; ?></a></li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php
                                        }

					if( $prize_link ){
					    echo '<div class="taxonomy-description"><p>';
					    echo '<a href="' . $prize_link . '" target="_blank">Site web</a>';
					    echo '</p></div>';
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
