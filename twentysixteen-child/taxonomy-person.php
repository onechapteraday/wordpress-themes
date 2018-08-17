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
                                    if( shortcode_exists( 'wp_breadcrumb_person' ) ) {
					$person_id = get_queried_object()->term_id;
                                        do_shortcode( '[wp_breadcrumb_person id=' . $person_id . ']' );
                                    }
                                ?>
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
					    echo '<div class="taxonomy-description social-icons">';

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

<?php
    $person = get_queried_object();
    $authors = array(
        'agatha-christie',
	'agnes-debord',
	'aime-cesaire',
	'alexandre-clerisse',
	'alex-xoul',
	'amelie-antoine',
	'andre-breton',
	'andre-schwarz-bart',
	'angie-thomas',
	'anna-gavalda',
	'anna-hope',
	'anne-damour',
	'anne-de-kinkelin',
	'anne-egger',
	'anne-goscinny',
	'anne-zali',
	'antoine-paje',
	'arundhati-roy',
	'audrey-pulvar',
	'ayelet-gundar-goshen',
	'benedicte-jourgeaud',
	'bernardin-de-saint-pierre',
	'bertice-berry',
	'blandine-le-callet',
	'boulet',
	'brit-bennett',
	'brownie-wise',
	'camille-benyamina',
	'carene-ponte',
	'carole-maurel',
	'catherine-meurisse',
	'cecile-brun',
	'celeste-ng',
	'chantel-acevedo',
	'chimamanda-ngozi-adichie',
	'claude-demanuelli',
	'cloe-mehdi',
	'clotilde-de-brito',
	'colin-reingewirtz',
	'daniel-glet',
	'daniel-maximin',
	'daniel-pennac',
	'daniel-picouly',
	'deloupy',
	'delphine-coulin',
	'delphine-minoui',
	'didier-kassai',
	'diniz-galhos',
	'durian-sukegawa',
	'edmonde-permingeat',
	'edouard-louis',
	'elena-ferrante',
	'elizabeth-acevedo',
	'elodie-leplat',
	'emily-ruskovich',
	'emmanuel-brault',
	'fabien-toulme',
	'fabienne-kanor',
	'gabrielle-zevin',
	'gael-octavia',
	'gael-faye',
	'gary-victor',
	'gary-younge',
	'georges-emmanuel-clancier',
	'gilles-marchand',
	'guillaume-musso',
	'hakan-gunday',
	'halim-mahmoudi',
	'ingrid-chabbert',
	'isabelle-reinharez',
	'ito-ogawa',
	'james-noel',
	'jane-deuxard',
	'jane-shemilt',
	'jean-baptiste-naudet',
	'jean-descat',
	'jean-ehrard',
	'jean-esch',
	'jean-kwok',
	'jean-moomou',
	'jessica-oublie',
	'jessie-burton',
	'jmg-le-clezio',
	'jonas-jonasson',
	'josep-busquet',
	'joseph-kessel',
	'julien-sandrel',
	'karine-lalechere',
	'kei-lam',
	'kei-miller',
	'laetitia-colombani',
	'laetitia-coryn',
	'laia-jufresa',
	'laurence-sendrowicz',
	'laurent-natrella',
	'leila-slimani',
	'levi-henriksen',
	'liz-moore',
	'louise-erdrich',
	'loup-maelle-besancon',
	'lucas-vallerie',
	'lucie-firoud',
	'mademoiselle-caroline',
	'marc-ellison',
	'marc-alexandre-oho-bambe',
	'marcel-ayme',
	'marianne-millon',
	'marie-ange-rousseau',
	'marie-france-etchegoin',
	'marion-montaigne',
	'martha-batalha',
	'mary-higgins-clark',
	'maryse-conde',
	'mathilde-bach',
	'matt-haig',
	'miguel-bonnefoy',
	'mohamed-mbougar-sarr',
	'myriam-dartois-ako',
	'natasha-soobramanien',
	'nathacha-appanah',
	'nathan-hill',
	'nina-simone',
	'olivier-bourdeaut',
	'olivier-pichard',
	'patrick-chamoiseau',
	'paula-hawkins',
	'penelope-bagieu',
	'philippe-lancon',
	'pierre-charras',
	'raphael-confiant',
	'rebecca-amsellem',
	'rupi-kaur',
	'sabine-rolland',
	'samuel-figuiere',
	'sandra-desmazieres',
	'sarah-alderson',
	'sarah-crossan',
	'scholastique-mukasonga',
	'shari-lapena',
	'simone-schwarz-bart',
	'sophie-astrabie',
	'stephane-blanco',
	'swann-meralli',
	'thierry-smolderen',
	'thomas-pesquet',
	'timothe-le-boucher',
	'tomas-eloy-martinez',
	'valerie-le-plouhinec',
	'veronica-henry',
	'veronique-cazot',
	'vincent-raynaud',
	'wendy-guerra',
	'wifredo-lam',
	'yaa-gyasi',
	'yasmina-khadra',
	'yvon-roy',
	'zadie-smith',
	'zeruya-shalev',
    );

    # Check if author
    if( in_array( $person->slug, $authors ) ){
        get_sidebar( 'book' );
    }
    else {
        get_sidebar();
    }
?>

<?php get_footer(); ?>
