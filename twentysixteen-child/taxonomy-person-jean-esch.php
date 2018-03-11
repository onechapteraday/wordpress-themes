<?php
/**
 * The template for displaying person taxonomy archive pages
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
					$title = single_term_title('', false);
					echo '<h1 class="page-title">' . $title . '</h1>';

					the_archive_description( '<div class="taxonomy-description">', '</div>' );

					$facebook = get_person_option('facebook');
					$instagram = get_person_option('instagram');
					$soundcloud = get_person_option('soundcloud');
					$twitter = get_person_option('twitter');
					$youtube = get_person_option('youtube');
					$website = get_person_option('website');

					if ( $facebook || $instagram || $soundcloud || $twitter || $youtube || $website ) {
					    echo '<div class="taxonomy-description">';
					    echo '<p><b>Sur la toile :</b> ';

					    if ( $website ) {
					        echo '<a class="icon-mouse" href="http://' . $website . '" target="_blank"></a>';
					    }

					    if ( $facebook ) {
					        echo '<a class="icon-facebook" href="http://facebook.com/' . $facebook . '" target="_blank"></a>';
					    }

					    if ( $twitter ) {
					        echo '<a class="icon-twitter" href="http://twitter.com/' . $twitter . '" target="_blank"></a>';
					    }

					    if ( $instagram ) {
					        echo '<a class="icon-instagram" href="http://instagram.com/' . $instagram . '" target="_blank"></a>';
					    }

					    if ( $youtube ) {
					        echo '<a class="icon-youtube" href="http://youtube.com/' . $youtube . '" target="_blank"></a>';
					    }

					    if ( $soundcloud ) {
					        echo '<a class="icon-soundcloud" href="http://soundcloud.com/' . $soundcloud . '" target="_blank"></a>';
					    }

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
