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
    $check = false;
    $tag = get_queried_object();

    $book_patterns = array(
        '/amitie/',
        '/bande/',
        '/biographie/',
        '/deuil/',
        '/ecriture/',
        '/enfant/',
        '/esclavage/',
        '/fantastique/',
        '/feminisme/',
        '/femme/',
        '/fiction/',
        '/hommage/',
        '/identite/',
        '/immigration/',
        '/lecture/',
        '/litteraire/',
        '/litterature/',
        '/livre/',
        '/memoire/',
        '/narrateur/',
        '/poesie/',
        '/racisme/',
        '/realisme-magique/',
        '/recit/',
        '/recueil/',
        '/reportage/',
        '/roman/',
        '/sexualite/',
        '/slam/',
        '/temoignage/',
        '/thriller/',
        '/violence/',
    );

    # Check if book_tag

    foreach( $book_patterns as $pattern ){
        if( preg_match( $pattern, $tag->slug ) ){
            $check = true;
            break;
        }
    }

    if( $check ){
        get_sidebar( 'book' );
    }
    else {
        get_sidebar();
    }
?>

<?php get_footer(); ?>
