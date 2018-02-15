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
		        <tr>
			        <td>
					<b>Auteur</b>
			        </td>
			        <td>
					<?php
						$author = get_book_author( $book_id );
						if ( $author ) {
							echo '<a href="' . get_term_link( $author->term_id ). '">' . $author->name . '</a>';
						}
					?>
		        </tr>
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
				$publisher = get_book_publisher( $book_id )[0];

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
					<?php
					if ( $fnac['link'] ) {
						?>
						<a href="<?php echo $fnac['link']; ?>" target="_blank" rel="nofollow" class="logo_partner logo_fnac">
							<img src="<?php echo $fnac['img']; ?>" alt="Amazon" />
						</a>
						<?php
					}
					?>
			        </td>
		        </tr>
		</table>
		<?php

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentysixteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'twentysixteen' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

			if ( '' !== get_the_author_meta( 'description' ) ) {
				get_template_part( 'template-parts/biography' );
			}

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
