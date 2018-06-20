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
					echo '<h1 class="page-title">' . mb_strtoupper( mb_substr( $title, 0, 1 )) . mb_substr( $title, 1 ) . '</h1>';

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
        'amitie',
        'bande-dessinee',
        'biographie',
        'cheveux-naturels',
        'deception-litteraire',
        'deuil',
        'ecriture',
        'esclavage',
        'famille',
        'fantastique',
        'feminisme',
        'femme-forte',
        'fiction-historique',
        'fiction-romantique',
        'gun-violence',
        'histoire',
        'hommage',
        'identite',
        'illustration',
        'immigration',
        'journee-internationale-de-la-poesie',
        'journee-internationale-du-livre',
        'lecture',
        'litterature-africaine',
        'litterature-algerienne',
        'litterature-americaine',
        'litterature-anglaise',
        'litterature-antillaise',
        'litterature-argentine',
        'litterature-asiatique',
        'litterature-australienne',
        'litterature-bresilienne',
        'litterature-canadienne',
        'litterature-chinoise',
        'litterature-classique',
        'litterature-contemporaine',
        'litterature-etats-unienne',
        'litterature-etrangere',
        'litterature-europeenne',
        'litterature-francaise',
        'litterature-ghaneenne',
        'litterature-haitienne',
        'litterature-indienne',
        'litterature-irlandaise',
        'litterature-israelienne',
        'litterature-japonaise',
        'litterature-jeune-adulte',
        'litterature-jeunesse',
        'litterature-marocaine',
        'litterature-mauricienne',
        'litterature-mexicaine',
        'litterature-nord-americaine',
        'litterature-nordique',
        'litterature-oceanienne',
        'litterature-rwandaise',
        'litterature-sud-americaine',
        'litterature-suedoise',
        'litterature-turque',
        'litterature-venezuelienne',
        'livre',
        'livre-numerique',
        'maltraitance-des-enfants',
        'manifestation-litteraire',
        'memoire',
        'non-fiction',
        'poesie',
        'premier-roman',
        'racisme',
        'realisme-magique',
        'recit-de-voyage',
        'recueil-de-nouvelles',
        'reportage',
        'roman-graphique',
        'roman-policier',
        'salon-du-livre',
        'sexualite',
        'slam',
        'science-fiction',
        'temoignage',
        'thriller',
        'violence-armee',
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
