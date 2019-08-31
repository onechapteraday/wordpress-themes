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
                <div class="single-metadata metadata-album">
		<?php
			the_content();

                        if( has_tag( 'single' ) ){
		            ?>
		            <h2><?php echo _x( 'About this single', 'single metadata section title', 'twentysixteen-child' ); ?></h2>
                            <?php
                        } else {
		            ?>
		            <h2><?php echo _x( 'About this album', 'album metadata section title', 'twentysixteen-child' ); ?></h2>
                            <?php
                        }
                ?>
		<table>
			<?php
			$album_id = get_the_ID();
			?>
		        <tr>
			        <td>
					<b><?php echo _x( 'Title', 'album metadata title', 'twentysixteen-child' ); ?></b>
			        </td>
			        <td>
					<?php echo get_album_title_original( $album_id ); ?>
			        </td>
		        </tr>
			<?php

			$authors = get_album_author( $album_id );

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
					        echo _x( 'Artists', 'album metadata female artists', 'twentysixteen-child' );
					    } else {
					        echo _x( 'Artists', 'album metadata artists', 'twentysixteen-child' );
					    }
					} else {
				            $gender = get_option( 'taxonomy_' . $authors[0]->term_id )['gender'];

					    if( $gender == 1 ){
					        echo _x( 'Artist', 'album metadata female artist', 'twentysixteen-child' );
					    } else {
					        echo _x( 'Artist', 'album metadata male artist', 'twentysixteen-child' );
					    }
					}
                                        ?></b>
			        </td>
			        <td>
					<?php
                                        $item_count = 0;
				        foreach( $authors as $author ){
					    if( $item_count > 0 ) echo ', ';

					    echo '<a href="' . get_term_link( $author->term_id ) . '">' . $author->name . '</a>';
                                            $item_count++;
					}
					?>
			        </td>
		        </tr>
			<?php
			}

			$authors_featured = get_album_author_featured( $album_id );

			if( $authors_featured ){
			?>
		        <tr>
			        <td>
					<b><?php
				        if( count( $authors_featured ) > 1 ){
					    $female_only = true;

					    foreach( $authors_featured as $author ){
				                $gender = get_option( 'taxonomy_' . $author->term_id )['gender'];

                                                if( $gender == 0 ){
                                                    $female_only = false;
                                                    break;
                                                }
					    }

					    if( $female_only ){
					        echo _x( 'Featured artists', 'album metadata female featured artists', 'twentysixteen-child' );
					    } else {
					        echo _x( 'Featured artists', 'album metadata featured artists', 'twentysixteen-child' );
					    }
					} else {
				            $gender = get_option( 'taxonomy_' . $authors_featured[0]->term_id )['gender'];

					    if( $gender == 1 ){
					        echo _x( 'Featured artist', 'album metadata female featured artist', 'twentysixteen-child' );
					    } else {
					        echo _x( 'Featured artist', 'album metadata male featured artist', 'twentysixteen-child' );
					    }
					}
                                        ?></b>
			        </td>
			        <td>
					<?php
                                        $item_count = 0;
				        foreach( $authors_featured as $author ){
					    if( $item_count > 0 ) echo ', ';

					    echo '<a href="' . get_term_link( $author->term_id ) . '">' . $author->name . '</a>';
                                            $item_count++;
					}
					?>
			        </td>
		        </tr>
			<?php
			}

                        if( get_album_date_release( $album_id ) ){

			        ?>
		                <tr>
			                <td>
                                                <b><?php echo _x( 'Release date', 'album metadata release date', 'twentysixteen-child' ); ?></b>
			                </td>
			                <td>
                                                <?php echo date_i18n( 'j F Y', strtotime(get_album_date_release( $album_id )) ); ?>
			                </td>
		                </tr>
			        <?php
			}

			$price = get_album_price( $album_id );

			if( $price ){

			?>
		        <tr>
			        <td>
					<b><?php echo _x( 'Price', 'album metadata price', 'twentysixteen-child' ); ?></b>
			        </td>
			        <td>
					<?php echo $price; ?> &euro;
			        </td>
		        </tr>
			<?php

                        }

			$amazon = get_album_amazon( $album_id );

                        if( $amazon['link'] ){

			?>
		        <tr>
			        <td>
					<b><?php echo _x( 'Affiliate link', 'album metadata Amazon affiliate link', 'twentysixteen-child' ); ?></b>
			        </td>
			        <td>
					<a href="<?php echo $amazon['link']; ?>" target="_blank" rel="nofollow" class="logo_partner logo_amazon">
				            <img src="<?php echo $amazon['img_buy']; ?>" alt="Amazon" />
                                            <span><?php echo _x( 'Buy on Amazon', 'album metadata Amazon affiliate message', 'twentysixteen-child' ); ?></span>
					</a>
			        </td>
		        </tr>
                        <?php
                        }
                        ?>
		</table>
                </div>
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
