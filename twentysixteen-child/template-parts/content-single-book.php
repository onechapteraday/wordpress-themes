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

                        # Add sharing buttons

                        if ( function_exists( 'sharing_display' ) ) {
                            sharing_display( '', true );
                        }

                        if ( class_exists( 'Jetpack_Likes' ) ) {
                            $custom_likes = new Jetpack_Likes;
                            echo $custom_likes->post_likes( '' );
                        }

                        add_action( 'loop_start', 'jptweak_remove_share' );
		?>
                <div class="single-metadata metadata-book">
		<h2><?php echo _x( 'About this book', 'book metadata section title', 'twentysixteen-child' ); ?></h2>
		<table>
			<?php
			$book_id = get_the_ID();

			$title = get_book_title_read( $book_id );
			if( $title ){
			?>
		        <tr class="title">
			        <td>
					<b><?php echo _x( 'Title read', 'book metadata title read', 'twentysixteen-child' ); ?></b>
			        </td>
			        <td>
					<?php echo $title; ?>
			        </td>
		        </tr>
			<?php
			}
			?>
		        <tr class="title">
			        <td>
					<b><?php echo _x( 'Original title', 'book metadata original title', 'twentysixteen-child' ); ?></b>
			        </td>
			        <td>
					<?php echo get_book_title_original( $book_id ); ?>
			        </td>
		        </tr>
			<?php
			$authors = get_book_author( $book_id );

			if( $authors ){
			?>
		        <tr>
			        <td>
					<b><?php
				        if( count( $authors ) > 1 ){
					    $female_only = true;

					    foreach( $authors as $author ){
				                $gender = get_option( 'taxonomy_' . $author->term_id )['gender'];

                                                if( $gender == 0 ){
                                                    $female_only = false;
                                                    break;
                                                }
					    }

					    if( $female_only ){
					        echo _x( 'Authors', 'book metadata female authors', 'twentysixteen-child' );
					    } else {
					        echo _x( 'Authors', 'book metadata authors', 'twentysixteen-child' );
					    }
					} else {
				            $gender = get_option( 'taxonomy_' . $authors[0]->term_id )['gender'];

					    if( $gender == 1 ){
					        echo _x( 'Author', 'book metadata female author', 'twentysixteen-child' );
					    } else {
					        echo _x( 'Author', 'book metadata male author', 'twentysixteen-child' );
					    }
					}
                                        ?></b>
			        </td>
			        <td>
					<?php
                                        $item_count = 0;
				        foreach( $authors as $author ){
					    if( $item_count > 0 ) echo ' &sdot; ';

					    echo '<a href="' . get_term_link( $author->term_id ) . '">' . $author->name . '</a>';
                                            $item_count++;
					}
					?>
			        </td>
		        </tr>
			<?php
			}
			?>
			<?php
				$scenarists = get_book_scenarist( $book_id );

				if( $scenarists ){
				?>
                                <tr>
				        <td>
					        <b><?php
				                if( count( $scenarists ) > 1 ){
					            $female_only = true;

					            foreach( $scenarists as $scenarist ){
				                        $gender = get_option( 'taxonomy_' . $scenarist->term_id )['gender'];

                                                        if( $gender == 0 ){
                                                            $female_only = false;
                                                            break;
                                                        }
					            }

					            if( $female_only ){
					                echo _x( 'Scripters', 'book metadata female scenarists', 'twentysixteen-child' );
					            } else {
					                echo _x( 'Scripters', 'book metadata scenarists', 'twentysixteen-child' );
					            }
					        } else {
				                    $gender = get_option( 'taxonomy_' . $scenarists[0]->term_id )['gender'];

					            if( $gender == 1 ){
					                echo _x( 'Scripter', 'book metadata female scenarist', 'twentysixteen-child' );
					            } else {
					                echo _x( 'Scripter', 'book metadata male scenarist', 'twentysixteen-child' );
					            }
					        }
                                                ?></b>
				        </td>
				        <td>
					        <?php
                                                $item_count = 0;
				                foreach( $scenarists as $scenarist ){
					            if( $item_count > 0 ) echo ' &sdot; ';

					            echo '<a href="' . get_term_link( $scenarist->term_id ) . '">' . $scenarist->name . '</a>';
                                                    $item_count++;
					        }
                                                ?>
				        </td>
                                    </tr>
				<?php
				}
			?>
			<?php
				$illustrators = get_book_illustrator( $book_id );

				if( $illustrators ){
				?>
		        	<tr>
				        <td>
					        <b><?php
				                if( count( $illustrators ) > 1 ){
					            $female_only = true;

					            foreach( $illustrators as $illustrator ){
				                        $gender = get_option( 'taxonomy_' . $illustrator->term_id )['gender'];

                                                        if( $gender == 0 ){
                                                            $female_only = false;
                                                            break;
                                                        }
					            }

					            if( $female_only ){
					                echo _x( 'Illustrators', 'book metadata female illustrators', 'twentysixteen-child' );
					            } else {
					                echo _x( 'Illustrators', 'book metadata illustrators', 'twentysixteen-child' );
					            }
					        } else {
				                    $gender = get_option( 'taxonomy_' . $illustrators[0]->term_id )['gender'];

					            if( $gender == 1 ){
					                echo _x( 'Illustrator', 'book metadata female illustrator', 'twentysixteen-child' );
					            } else {
					                echo _x( 'Illustrator', 'book metadata male illustrator', 'twentysixteen-child' );
					            }
					        }
                                                ?></b>
				        </td>
				        <td>
					        <?php
                                                $item_count = 0;
				                foreach( $illustrators as $illustrator ){
					            if( $item_count > 0 ) echo ' &sdot; ';

					            echo '<a href="' . get_term_link( $illustrator->term_id ) . '">' . $illustrator->name . '</a>';
                                                    $item_count++;
					        }
					        ?>
				        </td>
		        	</tr>
				<?php
				}
			?>
			<?php
				$colourists = get_book_colourist( $book_id );

				if( $colourists ){
				?>
		                <tr>
				        <td>
					        <b><?php
				                if( count( $colourists ) > 1 ){
					            $female_only = true;

					            foreach( $colourists as $colourist ){
				                        $gender = get_option( 'taxonomy_' . $colourist->term_id )['gender'];

                                                        if( $gender == 0 ){
                                                            $female_only = false;
                                                            break;
                                                        }
					            }

					            if( $female_only ){
					                echo _x( 'Colourists', 'book metadata female colourists', 'twentysixteen-child' );
					            } else {
					                echo _x( 'Colourists', 'book metadata colourists', 'twentysixteen-child' );
					            }
					        } else {
				                    $gender = get_option( 'taxonomy_' . $colourists[0]->term_id )['gender'];

					            if( $gender == 1 ){
					                echo _x( 'Colourist', 'book metadata female colourist', 'twentysixteen-child' );
					            } else {
					                echo _x( 'Colourist', 'book metadata male colourist', 'twentysixteen-child' );
					            }
					        }
                                                ?></b>
				        </td>
				        <td>
					        <?php
                                                $item_count = 0;
				                foreach( $colourists as $colourist ){
					            if( $item_count > 0 ) echo ' &sdot; ';

					            echo '<a href="' . get_term_link( $colourist->term_id ) . '">' . $colourist->name . '</a>';
                                                    $item_count++;
					        }
					        ?>
				        </td>
		                </tr>
				<?php
				}
			?>
			<?php
				$preface_authors = get_book_author_preface( $book_id );

				if( $preface_authors ){
				?>
		                <tr>
				        <td>
					        <b><?php echo _x( 'Preface', 'book metadata preface authors', 'twentysixteen-child' ); ?></b>
				        </td>
				        <td>
						<?php
                                                $item_count = 0;
				                foreach( $preface_authors as $author ){
					            if( $item_count > 0 ) echo ' &sdot; ';

					            echo '<a href="' . get_term_link( $author->term_id ) . '">' . $author->name . '</a>';
                                                    $item_count++;
					        }
						?>
				        </td>
		                </tr>
				<?php
				}
			?>
			<?php
				$postface_authors = get_book_author_postface( $book_id );

				if( $postface_authors ){
				?>
		                <tr>
				        <td>
					        <b><?php echo _x( 'Postface', 'book metadata postface authors', 'twentysixteen-child' ); ?></b>
				        </td>
				        <td>
						<?php
                                                $item_count = 0;
				                foreach( $postface_authors as $author ){
					            if( $item_count > 0 ) echo ' &sdot; ';

					            echo '<a href="' . get_term_link( $author->term_id ) . '">' . $author->name . '</a>';
                                                    $item_count++;
					        }
						?>
				        </td>
		                </tr>
				<?php
				}
			?>
			<?php
				$translators = get_book_translator( $book_id );

				if( $translators ){
				?>
		        	<tr>
				        <td>
					        <b><?php
				                if( count( $translators ) > 1 ){
					            $female_only = true;

					            foreach( $translators as $translator ){
				                        $gender = get_option( 'taxonomy_' . $translator->term_id )['gender'];

                                                        if( $gender == 0 ){
                                                            $female_only = false;
                                                            break;
                                                        }
					            }

					            if( $female_only ){
					                echo _x( 'Translators', 'book metadata female translators', 'twentysixteen-child' );
					            } else {
					                echo _x( 'Translators', 'book metadata translators', 'twentysixteen-child' );
					            }
					        } else {
				                    $gender = get_option( 'taxonomy_' . $translators[0]->term_id )['gender'];

					            if( $gender == 1 ){
					                echo _x( 'Translator', 'book metadata female translator', 'twentysixteen-child' );
					            } else {
					                echo _x( 'Translator', 'book metadata male translator', 'twentysixteen-child' );
					            }
					        }
                                                ?></b>
				        </td>
				        <td>
					        <?php
                                                $item_count = 0;
				                foreach( $translators as $translator ){
					            if( $item_count > 0 ) echo ' &sdot; ';

					            echo '<a href="' . get_term_link( $translator->term_id ) . '">' . $translator->name . '</a>';
                                                    $item_count++;
					        }
					        ?>
                                        </td>
                                </tr>
				<?php
				}
			?>
			<?php
				$publishers = get_book_publisher( $book_id );

				if( $publishers ){
				?>
                                <tr>
                                        <td>
					        <b><?php
				                    if( count( $publishers ) > 1 ){
                                                        echo _x( 'Publishers', 'book metadata publishers', 'twentysixteen-child' );
				                    }
				                    else {
                                                        echo _x( 'Publisher', 'book metadata publisher', 'twentysixteen-child' );
				                    }
                                                ?></b>
                                        </td>
                                        <td>
                                                <?php
                                                $item_count = 0;

                                                foreach( $publishers as $publisher ){
					            if( $item_count > 0 ) echo ' &sdot; ';

                                                    echo '<a href="' . get_term_link( $publisher->term_id ) . '">' . $publisher->name . '</a>';
                                                    $item_count++;
                                                }
                                                ?>
                                        </td>
                                </tr>
				<?php
				}
			?>
			<?php
				$collections = get_book_collection( $book_id );

				if( $collections ){
				?>
		        	<tr>
				        <td>
					        <b><?php
				                    if( count( $collections ) > 1 ){
                                                        echo _x( 'Collections', 'book metadata publisher collections', 'twentysixteen-child' );
				                    }
				                    else {
                                                        echo _x( 'Collection', 'book metadata publisher collection', 'twentysixteen-child' );
				                    }
                                                ?></b>
                                        </td>
				        <td>
                                                <?php
                                                $item_count = 0;

                                                foreach( $collections as $collection ){
					            if( $item_count > 0 ) echo ' &sdot; ';

						    echo '<a href="' . get_term_link( $collection->term_id ) . '">' . $collection->name . '</a>';
                                                    $item_count++;
                                                }
                                                ?>
                                        </td>
		        	</tr>
				<?php
				}
			?>
		        <tr>
			        <td>
					<b><?php echo _x( 'ISBN13', 'book metadata isbn13', 'twentysixteen-child' ); ?></b>
			        </td>
			        <td>
					<?php echo get_book_isbn13( $book_id ); ?>
			        </td>
		        </tr>
			<?php
				$isbn10 = get_book_isbn10( $book_id );

				if( $isbn10 ){
				?>
                                <tr>
				        <td>
                                                <b><?php echo _x( 'ISBN10', 'book metadata isbn10', 'twentysixteen-child' ); ?></b>
				        </td>
				        <td>
                                                <?php echo $isbn10; ?>
				        </td>
                                </tr>
				<?php
				}
			?>
			<?php
				$asin = get_book_asin( $book_id );

				if( $asin ){
				?>
                                <tr>
				        <td>
                                                <b><?php echo _x( 'ASIN', 'book metadata asin', 'twentysixteen-child' ); ?></b>
				        </td>
				        <td>
                                                <?php echo $asin; ?>
				        </td>
                                </tr>
				<?php
				}
			?>
		        <tr>
			        <td>
					<b><?php echo _x( 'Price', 'book metadata price', 'twentysixteen-child' ); ?></b>
			        </td>
			        <td>
					<?php echo get_book_price( $book_id ); ?> &euro;
			        </td>
		        </tr>
		        <tr>
			        <td>
					<b><?php echo _x( 'Number of pages', 'book metadata number of pages', 'twentysixteen-child' ); ?></b>
			        </td>
			        <td>
					<?php echo get_book_pages_number( $book_id ); ?> pages
			        </td>
		        </tr>
		        <tr>
			        <td>
					<b><?php echo _x( 'Release date', 'book metadata release date', 'twentysixteen-child' ); ?></b>
			        </td>
			        <td>
					<?php echo date_i18n( 'j F Y', strtotime( get_book_date_release( $book_id ) ) ); ?>
			        </td>
		        </tr>
			<?php

			$date = get_book_date_first_publication( $book_id );

			if( $date && $date != get_book_date_release( $book_id ) ){
                                $timestamp = $date;
                                $format = 'j F Y';

                                $date_first_release = DateTime::createFromFormat( 'Y-m-d', $date );

                                ?>
                                <tr>
                                        <td>
				                <b><?php echo _x( 'First publication', 'book metadata first publication date', 'twentysixteen-child' ); ?></b>
                                        </td>
                                        <td>
                                                <?php
                                                    $day   = $date_first_release->format( 'j' );
                                                    $month = $GLOBALS['wp_locale']->get_month( $date_first_release->format( 'm' ) );
                                                    $year  = $date_first_release->format( 'Y' );

                                                    echo $day . ' ' . $month . ' ' . $year;
                                                ?>
                                        </td>
                                </tr>
                                <?php
			}
			
			?>
			<?php

			$amazon       = get_book_amazon( $book_id );
			$leslibraires = get_book_leslibraires_ca( $book_id );

                        if( $amazon || $leslibraires ){
			?>
		        <tr class="affiliate_links">
			        <td>
                                        <?php
                                        if( $amazon && $leslibraires ){
                                            ?>
			                    <b><?php echo _x( 'Affiliate links', 'book metadata affiliate links', 'twentysixteen-child' ); ?></b>
                                            <?php
                                        } else {
                                            ?>
			                    <b><?php echo _x( 'Affiliate link', 'book metadata affiliate link', 'twentysixteen-child' ); ?></b>
                                            <?php
                                        }
                                        ?>
			        </td>
			        <td>
                                        <?php
                                        if( $leslibraires ){
                                        ?>
			                <a href="<?php echo $leslibraires[ 'link' ]; ?>" target="_blank" rel="nofollow" class="logo_partner logo_libraires_fr">
			                     <img src="<?php echo $leslibraires[ 'img' ]; ?>" alt="leslibraires.fr" />
                                             <span><?php echo _x( 'Buy on Les Libraires', 'book metadata leslibraires.fr affiliate message', 'twentysixteen-child' ); ?></span>
			                </a>
                                        <?php
                                        }

                                        if( $amazon ){
                                        ?>
			                <a href="<?php echo $amazon[ 'link' ]; ?>" target="_blank" rel="nofollow" class="logo_partner logo_amazon">
			                     <img src="<?php echo $amazon[ 'img_buy' ]; ?>" alt="Amazon" />
                                             <span><?php echo _x( 'Buy on Amazon', 'book metadata Amazon affiliate message', 'twentysixteen-child' ); ?></span>
			                </a>
                                        <?php
                                        }
                                        ?>
			        </td>
		        </tr>
                        <?php
                        }
                        ?>
		</table>
                </div>
		<?php
                        if( class_exists( 'Jetpack_RelatedPosts' ) ){
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
