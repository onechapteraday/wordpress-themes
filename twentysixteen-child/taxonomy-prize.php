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

                                        $prize      = get_term( $prize_id, 'prize' );
					$prize_link = get_prize_option( 'prize_link' );

                                        # selections

                                        if( taxonomy_exists( 'selection' ) ){
                                            $selections = get_terms( array(
                                                'taxonomy'   => 'selection',
                                                'hide_empty' => false,
                                            ) );

                                            $prize_selections = array();

                                            foreach( $selections as $selection ){
                                                $options = get_option("taxonomy_$selection->term_id");

                                                if( $options['selection_prize'] == $prize->slug ){
                                                    array_push( $prize_selections, $selection );
                                                }
                                            }

                                            # sort by order

                                            function sortSelection( $a, $b ){
                                                $a_op = get_option( "taxonomy_$a->term_id" );
                                                $b_op = get_option( "taxonomy_$b->term_id" );

                                                return strcasecmp( $a_op['selection_order'], $b_op['selection_order'] );
                                            }

                                            if( $prize_selections ){
                                                usort( $prize_selections, 'sortSelection' );

                                                ?>
                                                <div class="taxonomy-description">
                                                    <ul class="prize-selections">
                                                        <?php
                                                        foreach( $prize_selections as $selection ){
                                                            ?>
                                                            <li><a href="<?php echo get_term_link( $selection, 'selection' ); ?>"><?php echo $selection->name; ?></a></li>
                                                            <?php
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                                <?php
                                            }
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
