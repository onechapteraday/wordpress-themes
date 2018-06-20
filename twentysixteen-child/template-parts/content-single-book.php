<?php
/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
                <?php
                    if( shortcode_exists( 'wp_breadcrumb_single' ) ) {
                        do_shortcode( '[wp_breadcrumb_single id=' . get_the_ID() . ']' );
                    }
                ?>

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php twentysixteen_excerpt(); ?>

	<?php twentysixteen_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
			the_content();
		?>
		<h2>À propos de ce livre</h2>
		<table>
			<?php
			$book_id = get_the_ID();

			$title = get_book_title_french( $book_id );
			if ($title) {
			?>
		        <tr>
			        <td>
					<b>Titre</b>
			        </td>
			        <td>
					<?php echo $title; ?>
			        </td>
		        </tr>
			<?php
			}
			?>
		        <tr>
			        <td>
					<b>Titre original</b>
			        </td>
			        <td>
					<?php echo get_book_title_original( $book_id ); ?>
			        </td>
		        </tr>
			<?php
			$author = get_book_author( $book_id );
			$author_second = get_book_author_second( $book_id );

			if ( $author ) {
			?>
		        <tr>
			        <td>
					<b>Auteur<?php if ( $author_second ) { echo 's'; } ?></b>
			        </td>
			        <td>
					<?php
					echo '<a href="' . get_term_link( $author->term_id ). '">' . $author->name . '</a>';

					if ( $author_second ) {
						echo ', <a href="' . get_term_link( $author_second->term_id ). '">' . $author_second->name . '</a>';
					}
					?>
			        </td>
		        </tr>
			<?php
			}
			?>
			<?php
				$translator = get_book_translator( $book_id );

				if ($translator) {
				?>
		        	<tr>
				        <td>
						<b>Traducteur</b>
				        </td>
				        <td>
						<?php
							echo '<a href="' . get_term_link( $translator->term_id ). '">' . $translator->name . '</a>';
						?>
		        	</tr>
				<?php
				}
			?>
			<?php
				$illustrator = get_book_illustrator( $book_id );

				if ($illustrator) {
				?>
		        	<tr>
				        <td>
						<b>Illustrateur</b>
				        </td>
				        <td>
						<?php
							echo '<a href="' . get_term_link( $illustrator->term_id ). '">' . $illustrator->name . '</a>';
						?>
		        	</tr>
				<?php
				}
			?>
			<?php
				$colourist = get_book_colourist( $book_id );

				if ($colourist) {
				?>
		                <tr>
				        <td>
						<b>Coloriste</b>
				        </td>
				        <td>
						<?php
							echo '<a href="' . get_term_link( $colourist->term_id ). '">' . $colourist->name . '</a>';
						?>
		                </tr>
				<?php
				}
			?>
			<?php
				$preface = get_book_author_preface( $book_id );

				if( $preface ){
				?>
		                <tr>
				        <td>
						<b>Préface</b>
				        </td>
				        <td>
						<?php
							echo '<a href="' . get_term_link( $preface->term_id ). '">' . $preface->name . '</a>';
						?>
		                </tr>
				<?php
				}
			?>
			<?php
				$publisher = get_book_publisher( $book_id );

				if ( $publisher ) {
				?>
                                <tr>
                                    <td>
                                        <b>Éditeur</b>
                                    </td>
                                    <td>
                                        <?php
                                            echo '<a href="' . get_term_link( $publisher->term_id ). '">' . $publisher->name . '</a>';
                                        ?>
                                    </td>
                                </tr>
				<?php
				}
			?>
			<?php
				$collection = get_book_collection( $book_id );

				if ($collection) {
				?>
		        	<tr>
				        <td>
						<b>Collection</b>
				        </td>
				        <td>
						<?php
							echo '<a href="' . get_term_link( $collection->term_id ). '">' . $collection->name . '</a>';
						?>
		        	</tr>
				<?php
				}
			?>
		        <tr>
			        <td>
					<b>ISBN</b>
			        </td>
			        <td>
					<?php echo get_book_isbn( $book_id ); ?>
			        </td>
		        </tr>
		        <tr>
			        <td>
					<b>Prix</b>
			        </td>
			        <td>
					<?php echo get_book_price( $book_id ); ?> &euro;
			        </td>
		        </tr>
		        <tr>
			        <td>
					<b>Nombre de pages</b>
			        </td>
			        <td>
					<?php echo get_book_pages_number( $book_id ); ?> pages
			        </td>
		        </tr>
		        <tr>
			        <td>
					<b>Date de parution</b>
			        </td>
			        <td>
					<?php echo date_i18n( 'j F Y', strtotime(get_book_date_release( $book_id )) ); ?>
			        </td>
		        </tr>
			<?php
				$date = get_book_date_first_publication( $book_id );

				if ( $date && $date != get_book_date_release( $book_id ) ) {
				?>
		        	<tr>
				        <td>
						<b>Première publication</b>
				        </td>
				        <td>
						<?php echo date_i18n( 'j F Y', strtotime($date) ); ?>
				        </td>
		        	</tr>
				<?php
			}
			
			$rating = get_book_rating( $book_id );

			if ($rating) {

			?>
		        <tr>
			        <td>
					<b>Ma note</b>
			        </td>
			        <td>
					<?php
						$rating = get_book_rating( $book_id );

						switch ( $rating ) {
						    case 1:
						        $rating_title = 'Je n\'ai pas aimé';
						        break;
						    case 2:
						        $rating_title = 'Pourquoi pas...';
						        break;
						    case 3:
						        $rating_title = 'J\'ai aimé';
						        break;
						    case 4:
						        $rating_title = 'J\'ai adoré';
						        break;
						    case 5:
						        $rating_title = 'Coup de c&oelig;ur';
						        break;
						    default:
						        $rating_title = '';
						        break;
						}
						?>
						<span title="<?php echo $rating_title; ?>">
						<?php
						for ($i = 0; $i < 5; $i++) {
							if ($i < $rating) {
								echo '&bigstar;';
							} else {
								echo '&star;';
							}
						}
						?>
						</span>
						<?php
					?>
			        </td>
		        </tr>
			<?php
			
			}
			
			?>
		        <tr>
			        <td>
					<b>Disponible sur</b>
			        </td>
			        <td>
					<?php

					$amazon = get_book_amazon( $book_id );
					$fnac = get_book_fnac( $book_id );

					?>
					<a href="<?php echo $amazon['link']; ?>" target="_blank" rel="nofollow" class="logo_partner logo_amazon">
						<img src="<?php echo $amazon['img']; ?>" alt="Amazon" />
					</a>
			        </td>
		        </tr>
		</table>
		<?php
                        if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
                            echo do_shortcode( '[jetpack-related-posts]' );
                        }

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentysixteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'twentysixteen' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php twentysixteen_entry_meta(); ?>
		<?php
			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post */
					__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'twentysixteen' ),
					get_the_title()
				),
				'<span class="edit-link">',
				'</span>'
			);
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
