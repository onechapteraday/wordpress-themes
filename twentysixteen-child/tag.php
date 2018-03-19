<?php
/**
 * The template for displaying archive pages
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
                                    if( shortcode_exists( 'wp_breadcrumb_tag' ) ) {
					$tag_id = get_queried_object()->term_id;
                                        do_shortcode( '[wp_breadcrumb_tag id=' . $tag_id . ']' );
                                    }
                                ?>
				<?php
					$title = single_term_title('', false);
					echo '<h1 class="page-title">' . ucfirst( $title ) . '</h1>';

					the_archive_description( '<div class="taxonomy-description">', '</div>' );
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

<?php
    $tag = get_queried_object();
    $book_tags = array(
        'bande-dessinee',
        'biographie',
        'deception-litteraire',
        'feminisme',
        'femmes-fortes',
        'fiction-historique',
        'fiction-romantique',
        'immigration',
        'journee-internationale-du-livre',
        'lecture',
        'litterature-africaine',
        'litterature-algerienne',
        'litterature-americaine',
        'litterature-anglaise',
        'litterature-antillaise',
        'litterature-argentine',
        'litterature-asiatique',
        'litterature-bresilienne',
        'litterature-classique',
        'litterature-contemporaine',
        'litterature-etrangere',
        'litterature-francaise',
        'litterature-ghaneenne',
        'litterature-haitienne',
        'litterature-israelienne',
        'litterature-japonaise',
        'litterature-mauricienne',
        'litterature-rwandaise',
        'litterature-sud-americaine',
        'litterature-suedoise',
        'litterature-turque',
        'livre',
        'memoire',
        'non-fiction',
        'premier-roman',
        'recit-de-voyage',
        'recueil-de-nouvelles',
        'roman-graphique',
        'roman-policier',
        'salon-du-livre',
        'science-fiction',
        'thriller',
    );

    # Check if book_tag
    if( in_array( $tag->slug, $book_tags ) ){
        get_sidebar( 'book' );
    }
    else {
        get_sidebar();
    }
?>

<?php get_footer(); ?>
