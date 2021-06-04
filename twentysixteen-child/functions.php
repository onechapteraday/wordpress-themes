<?php

# Import parent styles

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

function my_theme_enqueue_styles() {
    $parent_style = 'twentysixteen-style';

    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get('Version')
    );

    if( is_home() || is_front_page() ){
        wp_enqueue_style( 'child-widgets-style',
            get_stylesheet_directory_uri() . '/css/widgets.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }

    if( is_single() ){
        wp_enqueue_style( 'child-single-style',
            get_stylesheet_directory_uri() . '/css/single.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }

    if( is_archive() ){
        wp_enqueue_style( 'child-archive-style',
            get_stylesheet_directory_uri() . '/css/archive.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}



# Short title

function twentysixteenchild_title_limit($length, $replacer = '...') {
    $string = the_title('','',FALSE);

    if(strlen($string) > $length)
        $string = (preg_match('/^(.*)\W.*$/', substr($string, 0, $length+1), $matches) ? $matches[1] : substr($string, 0, $length)) . $replacer;

    echo $string;
}



# Multiple custom excerpt lengths

function twentysixteenchild_excerpt( $limit ){
    $excerpt = explode( ' ', get_the_excerpt(), $limit );

    if( count( $excerpt ) >= $limit ){
        array_pop( $excerpt );
        $excerpt = implode( " ", $excerpt ) . '...';
    } else {
        $excerpt = implode( " ", $excerpt );
    }

    $excerpt = preg_replace( '`[[^]]*]`', '', $excerpt );

    return $excerpt;
}



/**
 * Filter the except length to 120 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */

function wp_custom_excerpt_length( $length ) {
    return 120;
}

add_filter( 'excerpt_length', 'wp_custom_excerpt_length', 999 );



/**
 * Add new custom post type book into archives pages.
 *
 * @since Twenty Sixteen Child 1.0
 *
 * @param array $query Query of the page.
 * @return array A new modified query.
 */

function add_my_post_types_to_query( $query ) {
    if ( !is_admin() ) {
        $post_types = array( 'post' );

        if( post_type_exists( 'book' ) ){
            array_push( $post_types, 'book' );
        }

        if( post_type_exists( 'album' ) ){
            array_push( $post_types, 'album' );
        }

        if( post_type_exists( 'interview' ) ){
            array_push( $post_types, 'interview' );
        }

        if( post_type_exists( 'concert' ) ){
            array_push( $post_types, 'concert' );
        }

        if ( is_archive() && $query->is_main_query() ){
            $query->set( 'post_type', $post_types );
	}
    }

    return $query;
}

add_action( 'pre_get_posts', 'add_my_post_types_to_query' );


/**
 * Add new custom post type book into home and feed pages.
 *
 * @since Twenty Sixteen Child 1.0
 *
 * @param array $query Query of the page.
 * @return array A new modified query.
 */

function add_custom_post_types_filter( $query ) {
    $post_types = array( 'post' );

    if( post_type_exists( 'book' ) ){
        array_push( $post_types, 'book' );
    }

    if( post_type_exists( 'album' ) ){
        array_push( $post_types, 'album' );
    }

    if( post_type_exists( 'interview' ) ){
        array_push( $post_types, 'interview' );
    }

    if( post_type_exists( 'concert' ) ){
        array_push( $post_types, 'concert' );
    }

    if ( ($query->is_home() && $query->is_main_query()) || $query->is_feed() ) {
        $query->set( 'post_type', $post_types );
    }

    return $query;
}

add_action( 'pre_get_posts', 'add_custom_post_types_filter' );



/**
 * Sets up theme defaults for WordPress features
 *
 * @since Twenty Sixteen Child 1.0
 */

function twentysixteenchild_setup() {
    # Make Twenty Sixteen Child available for translation. Translations can be added to the /languages/ directory.
    load_theme_textdomain( 'twentysixteen-child', get_stylesheet_directory() . '/languages' );

    # This theme uses post thumbnails.
    add_theme_support( 'post-thumbnails' );

    #  Adding several sizes for Post Thumbnails
    add_image_size( 'twentysixteenchild-small-square', 120, 120, true ); // Small Square thumbnails (cropped)
    add_image_size( 'twentysixteenchild-medium-portrait', 420, 560, true ); // Medium Portrait thumbnails (cropped)
    add_image_size( 'twentysixteenchild-medium-landscape', 840, 560, true ); // Medium landscape thumbnails (cropped)
    add_image_size( 'twentysixteenchild-fullwidth', 1200, 800, true ); // Big thumbnails (cropped)
}

add_action( 'after_setup_theme', 'twentysixteenchild_setup' );


/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 *
 * Override twentysixteen_post_thumbnail() function.
 *
 * @since Twenty Sixteen Child 1.0
 */

function twentysixteen_post_thumbnail( $post_thumbnail_size = 'post-thumbnail', $post_thumbnail_link = false ) {
    if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
        return;
    }

    if ( is_singular() && ! $post_thumbnail_link ) :
    ?>

    <div class="post-thumbnail">
        <figure>
            <?php the_post_thumbnail(); ?>
        </figure>
    </div><!-- .post-thumbnail -->

    <?php else : ?>

    <div class="post-thumbnail">
        <figure>
            <?php the_post_thumbnail( $post_thumbnail_size, array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
        </figure>
    </div>

    <?php endif; // End is_singular()
}


/**
 * Add copyright for featured images.
 *
 * @since Twenty Sixteen Child 1.0
 *
 * @param string $html Post thumbnail HTML.
 * @param int    $post_id Post ID.
 * @param string $post_thumbnail_id Post thumbnail ID.
 * @param string $size Post thumbnail size.
 * @param string $attr Query string of attributes.
 * @return string $html New post thumbnail HTML with copyright.
 */

function filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ){
    if( ! is_singular() || ! empty ( $attr ) ){
       $html = '<a href="'. get_permalink( $post_id ). '" aria-hidden="true">' . $html . '</a>';
    }

    if( function_exists( 'get_featured_image_copyright' ) ){
        $link   = get_featured_image_copyright_link( $post_thumbnail_id );
        $author = get_featured_image_copyright_author( $post_thumbnail_id );

	if( $link ){
            $html .= '<figcaption class="wp-post-image-copyright">';
	    $html .= '<a href="' . $link . '" target="_blank" rel="nofollow">Copyright : <span class="copyright_author">';
	    $html .= $author . '</span></a></figcaption>';
	}
    }

    # Make filter magic happen here...
    return $html;
};

add_filter( 'post_thumbnail_html', 'filter_post_thumbnail_html', 10, 5 );


/**
 * Custom Twenty Sixteen Child template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen Child 1.0
 */

if ( ! function_exists( 'twentysixteen_entry_meta' ) ) :
/**
 * Prints HTML with meta information for the categories, tags.
 *
 * @since Twenty Sixteen Child 1.0
 */
function twentysixteen_entry_meta() {
    $post_types = array( 'post' );

    if( post_type_exists( 'book' ) ){
        array_push( $post_types, 'book' );
    }

    if( post_type_exists( 'album' ) ){
        array_push( $post_types, 'album' );
    }

    if( post_type_exists( 'interview' ) ){
        array_push( $post_types, 'interview' );
    }

    if( post_type_exists( 'concert' ) ){
        array_push( $post_types, 'concert' );
    }

    foreach( $post_types as $type ){
        if( $type === get_post_type() ){
            $author_avatar_size = apply_filters( 'twentysixteen_author_avatar_size', 49 );
            printf( '<span class="byline"><span class="author vcard">%1$s<span class="screen-reader-text">%2$s </span> <a class="url fn n" href="%3$s">%4$s</a></span></span>',
                get_avatar( get_the_author_meta( 'user_email' ), $author_avatar_size ),
                _x( 'Author', 'Used before post author name.', 'twentysixteen' ),
                esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                get_the_author()
            );
        }
    }

    if ( in_array( get_post_type(), $post_types ) ) {
        $post_type = get_post_type();

        if( $post_type == 'book' ){
            twentysixteen_child_release_date();
        }

        if( $post_type == 'album' ){
            twentysixteen_child_release_date();
        }

        twentysixteen_entry_date();
    }

    $format = get_post_format();
    if ( current_theme_supports( 'post-formats', $format ) ) {
        printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
            sprintf( '<span class="screen-reader-text">%s </span>', _x( 'Format', 'Used before post format.', 'twentysixteen' ) ),
            esc_url( get_post_format_link( $format ) ),
            get_post_format_string( $format )
        );
    }

    foreach( $post_types as $type ){
        if( $type === get_post_type() ){
            twentysixteen_entry_taxonomies();
        }
    }

    if ( ! is_singular() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
        echo '<span class="comments-link">';

        $comments_number = get_comments_number();
        $comments_letter = $comments_number;
        $locale          = substr( get_locale(), 0, 2 );

        if( class_exists('NumberFormatter') ){
            $numberFormatter = new NumberFormatter( $locale, NumberFormatter::SPELLOUT );
            $comments_letter = ucfirst( $numberFormatter->format( $comments_number ) );
        }

        comments_popup_link(
            # zero
            sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ),

            # one
            $comments_letter . ' ' . __( 'comment', 'twentysixteen-child' ),

            # more
            $comments_letter . ' ' . __( 'comments', 'twentysixteen-child' )
        );

        echo '</span>';
    }
}
endif;



if ( ! function_exists( 'twentysixteen_entry_taxonomies' ) ) :
/**
 * Prints HTML with category and tags for current post.
 *
 * @since Twenty Sixteen Child 1.0
 */
function sortByName( $a, $b ){
    $asort = $a->name;
    $bsort = $b->name;

    if( $a->taxonomy == 'person' ){
        $a_op = get_option( "taxonomy_$a->term_id" );
        $b_op = get_option( "taxonomy_$b->term_id" );

        if( isset( $a_op['sortname'] ) ){
            $asort = ( $a_op['sortname'] ) ? $a_op['sortname'] : $asort;
        }

        if( isset( $b_op['sortname'] ) ){
            $bsort = ( $b_op['sortname'] ) ? $b_op['sortname'] : $bsort;
        }
    }

    $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');

    $at = strtr( $asort, $translit );
    $bt = strtr( $bsort, $translit );

    return strcasecmp( $at, $bt );
}

function sortLocationByTranslation( $a, $b ){
    $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
    $at = strtr( $a->translation, $translit );
    $bt = strtr( $b->translation, $translit );

    return strcasecmp( $at, $bt );
}

function twentysixteen_entry_taxonomies() {
    # Categories list
    $categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentysixteen' ) );

    if ( $categories_list && twentysixteen_categorized_blog() ) {
        printf( '<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
            _x( 'Categories', 'Used before category names.', 'twentysixteen' ),
            $categories_list
        );
    }

    # Persons list
    if( taxonomy_exists( 'person' )) {
        $people_list = get_the_terms( get_the_ID(), 'person', '', ', ' );

        if ( $people_list ) {
            usort( $people_list, 'sortByName' );
            $people = '';

            foreach($people_list as $i => $tag) {
                if ( $i > 0) $people .= ', ';
                $people .= '<a href="' . get_term_link( $tag->term_id ) . '">';
                $people .= $tag->name;
                $people .= '</a>';
            }

            if ( $people ) {
                printf( '<span class="persons-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                    _x( 'People', 'Used before person names.', 'twentysixteen' ),
                    $people
                );
            }
        }
    }

    # Literary Prizes list
    if( taxonomy_exists( 'prize' )) {
        $prizes_list = get_the_terms( get_the_ID(), 'prize', '', ', ' );

        if ( $prizes_list ) {
            foreach( $prizes_list as $myprize ) {
                $myprize->translation = __( $myprize->name, 'prize-taxonomy' );
            }

            usort( $prizes_list, 'sortByName' );
            $prizes = '';

            foreach($prizes_list as $i => $tag) {
                if ( $i > 0) $prizes .= ', ';
                $prizes .= '<a href="' . get_term_link( $tag->term_id ) . '">';
                $prizes .= $tag->name;
                $prizes .= '</a>';
            }

            printf( '<span class="prizes-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                _x( 'Literary Prizes', 'Used before prize names.', 'twentysixteen' ),
                $prizes
            );
        }
    }


    # Locations list
    if( taxonomy_exists( 'location' )) {
        $locations_list = get_the_terms( get_the_ID(), 'location', '', ', ' );

        if ( $locations_list ) {
            foreach( $locations_list as $mylocation ) {
                $mylocation->translation = __( $mylocation->name, 'location-taxonomy' );
            }

            usort( $locations_list, 'sortLocationByTranslation' );
            $locations = '';

            foreach($locations_list as $i => $tag) {
                if ( $i > 0) $locations .= ', ';
                $locations .= '<a href="' . get_term_link( $tag->term_id ) . '">';
                $locations .= $tag->translation;
                $locations .= '</a>';
            }

            printf( '<span class="locations-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                _x( 'Locations', 'Used before location names.', 'twentysixteen' ),
                $locations
            );
        }
    }

    # Tags list
    $tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'twentysixteen' ) );

    if ( $tags_list && ! is_wp_error( $tags_list ) ) {
        printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
           _x( 'Tags', 'Used before tag names.', 'twentysixteen' ),
           $tags_list
        );
    }
}
endif;



/**
 * Override widgets area
 *
 * @link https://developer.wordpress.org/reference/functions/register_sidebar/
 *
 * @since Twenty Sixteen Child 1.0
 */

function twentysixteenchild_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'twentysixteen' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Add widgets here to appear in your sidebar.', 'twentysixteen' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __( 'Sidebar for Books', 'twentysixteen-child' ),
        'id'            => 'book',
        'description'   => __('Add widgets here to appears in your Book sidebar.', 'twentysixteen-child'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __( 'Sidebar for Music', 'twentysixteen-child' ),
        'id'            => 'music',
        'description'   => __('Add widgets here to appears in your Music sidebar.', 'twentysixteen-child'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar( array (
        'name' => __( 'Front Page - FullWidth Top', 'twentysixteen-child' ),
        'id' => 'front-fullwidth-top',
        'description' => __( 'Widgets appear in a single-column widget area on the top of the Front Page (and above the Featured Content slider, if active).', 'twentysixteen-child' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar( array (
        'name' => __( 'Front Page - Post Content 1', 'twentysixteen-child' ),
        'id' => 'front-content-1',
        'description' => __( 'Widgets appear left of Sidebar 1 and below the FullWidth Top widget area. This widget area is especially designed for the custom Twenty Sixteen Child Posts by Category widgets.', 'twentysixteen-child' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar( array (
        'name' => __( 'Front Page - Sidebar 1', 'twentysixteen-child' ),
        'id' => 'front-sidebar-1',
        'description' => __( 'Widgets appear in a right-aligned sidebar area next to the Post Content 1 widget area.', 'twentysixteen-child' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar( array (
        'name' => __( 'Front Page - FullWidth Center', 'twentysixteen-child' ),
        'id' => 'front-fullwidth-center',
        'description' => __( 'Widgets will appear in a single-column widget area below the Post Content 1 and Sidebar 1 widget areas.', 'twentysixteen-child' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar( array (
         'name' => __( 'Front Page - Post Content 2', 'twentysixteen-child' ),
         'id' => 'front-content-2',
         'description' => __( 'Widgets appear left of Sidebar 2 and below the FullWidth Center widget area. This widget area is especially designed for the custom Twenty Sixteen Child Posts by Category widgets.', 'twentysixteen-child' ),
         'before_widget' => '<aside id="%1$s" class="widget %2$s">',
         'after_widget' => "</aside>",
         'before_title' => '<h3 class="widget-title">',
         'after_title' => '</h3>',
    ));

    register_sidebar( array (
        'name' => __( 'Front Page - Sidebar 2', 'twentysixteen-child' ),
        'id' => 'front-sidebar-2',
        'description' => __( 'Widgets appear in a right-aligned sidebar area next to the Post Content 2 widget area.', 'twentysixteen-child' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar( array (
        'name' => __( 'Front Page - FullWidth Bottom', 'twentysixteen-child' ),
        'id' => 'front-fullwidth-bottom',
        'description' => __( 'Widgets will appear in a single-column widget area at the bottom of your Front Page above the footer.', 'twentysixteen-child' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar( array(
        'name'          => __( 'Content Bottom 1', 'twentysixteen-child' ),
        'id'            => 'sidebar-2',
        'description'   => __( 'Appears at the bottom of the content on posts and pages.', 'twentysixteen-child' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar( array(
        'name'          => __( 'Content Bottom 2', 'twentysixteen-child' ),
        'id'            => 'sidebar-3',
        'description'   => __( 'Appears at the bottom of the content on posts and pages.', 'twentysixteen-child' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar( array(
        'name'          => __( 'EU Cookie Law Footer', 'twentysixteen-child' ),
        'id'            => 'eu-cookie-law-footer',
        'description'   => __( 'Appears at the bottom of all pages.', 'twentysixteen-child' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}

add_action( 'widgets_init', 'twentysixteenchild_widgets_init' );



# Import widgets

require_once( __DIR__ . '/widgets/random-posts-small-one-widget.php');
require_once( __DIR__ . '/widgets/random-posts-small-two-widget.php');
require_once( __DIR__ . '/widgets/random-posts-medium-one-widget.php');
require_once( __DIR__ . '/widgets/random-posts-medium-two-widget.php');
require_once( __DIR__ . '/widgets/random-posts-big-one-widget.php');
require_once( __DIR__ . '/widgets/random-posts-big-two-widget.php');
require_once( __DIR__ . '/widgets/random-posts-colored-widget.php');

require_once( __DIR__ . '/widgets/recent-posts-small-one-widget.php');
require_once( __DIR__ . '/widgets/recent-posts-small-two-widget.php');
require_once( __DIR__ . '/widgets/recent-posts-medium-one-widget.php');
require_once( __DIR__ . '/widgets/recent-posts-medium-two-widget.php');
require_once( __DIR__ . '/widgets/recent-posts-big-one-widget.php');
require_once( __DIR__ . '/widgets/recent-posts-big-two-widget.php');
require_once( __DIR__ . '/widgets/recent-posts-colored-widget.php');

require_once( __DIR__ . '/widgets/random-recent-posts-medium-two-widget.php');

require_once( __DIR__ . '/widgets/popular-tags-in-category-widget.php');

require_once( __DIR__ . '/widgets/quote-widget.php');


/**
 * Remove prefix for get_the_archive_title()
 */

add_filter('get_the_archive_title', function ($title) {
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( is_author() ) {
        $title = '<span class="vcard">' . ucfirst( get_the_author() ). '</span>';
    } elseif ( is_year() ) {
        $title = get_the_date( _x( 'Y', 'yearly archives date format' ) );
    } elseif ( is_month() ) {
        $title = ucfirst( get_the_date( _x( 'F Y', 'monthly archives date format' ) ) );
    } elseif ( is_day() ) {
        $title = get_the_date( _x( 'F j, Y', 'daily archives date format' ) );
    } elseif ( is_tax( 'post_format' ) ) {
        if ( is_tax( 'post_format', 'post-format-aside' ) ) {
            $title = _x( 'Asides', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
            $title = _x( 'Galleries', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
            $title = _x( 'Images', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
            $title = _x( 'Videos', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
            $title = _x( 'Quotes', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
            $title = _x( 'Links', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
            $title = _x( 'Statuses', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
            $title = _x( 'Audio', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
            $title = _x( 'Chats', 'post format archive title' );
        }
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    } elseif ( is_tax() ) {
        $title = single_term_title( '', false );
    } else {
        $title = __( 'Archives' );
    }
    return $title;
});

# Update CSS within in Admin

function admin_style() {
  wp_enqueue_style('admin-styles', get_stylesheet_directory_uri().'/admin.css');
}

add_action('admin_enqueue_scripts', 'admin_style');


/**
 * Jetpack Related Posts functions
 */

# Remove automatic display

function jetpack_relatedposts_remove_display() {
    if( class_exists( 'Jetpack_RelatedPosts' ) ){
        $jprp = Jetpack_RelatedPosts::init();
        $callback = array( $jprp, 'filter_add_target_to_dom' );

        remove_filter( 'the_content', $callback, 40 );
    }
}

add_filter( 'wp', 'jetpack_relatedposts_remove_display', 20 );

# Update thumbnail size

function jetpack_relatedposts_custom_image( $media, $post_id, $args ) {
    if ( $media ) {
        $source = $media[0]['src'];

        $info = pathinfo( $source );
        $dirname   = $info['dirname'];
        $filename  = $info['filename'];
        $extension = $info['extension'];

        $media[0]['src'] = $dirname . '/' . $filename . '-420x560.' . $extension;

        return $media;
    }
}

add_filter( 'jetpack_images_get_images', 'jetpack_relatedposts_custom_image', 10, 3 );

function jetpack_relatedposts_update_thumbnail_size( $thumbnail_size ){
    $thumbnail_size['width'] = 195;
    $thumbnail_size['height'] = 260;
    $thumbnail_size['crop'] = true;

    return $thumbnail_size;
}

add_filter( 'jetpack_relatedposts_filter_thumbnail_size', 'jetpack_relatedposts_update_thumbnail_size' );


/**
 * Add class no-sidebar to fullwidth templates
 */

function twentysixteen_child_body_classes( $classes ){
    if(
        is_page_template( 'page-templates/fullwidth-single.php' )
        || is_page_template( 'page-templates/fullwidth-single-book.php' )
        || is_page_template( 'page-templates/fullwidth-single-album.php' )
        || is_page_template( 'page-templates/fullwidth-single-interview.php' )
    ){
        $classes[] = 'no-sidebar';
    }

    return $classes;
}

add_filter( 'body_class', 'twentysixteen_child_body_classes' );


/**
 * Update posts order in literary prize archive
 */

function update_post_order_query( $query ) {
    # Display prizes by year of attribution desc
    if( $query->is_tax( 'prize' ) ) {

        $queried_slug = $query->queried_object->slug;

        $sql = "SELECT *
                FROM `wp_posts` p, `wp_terms` t, `wp_term_relationships` r, `wp_term_taxonomy` x
                WHERE p.ID = r.object_id
                AND t.term_id = x.term_id
                AND r.term_taxonomy_id = x.term_taxonomy_id
                AND p.post_status = 'publish'
                AND (
                    x.term_id = (
                        SELECT term_id
                        FROM `wp_terms`
                        WHERE slug = '$queried_slug'
                    ) OR
                    x.parent = (
                        SELECT term_id
                        FROM `wp_terms`
                        WHERE slug = '$queried_slug'
                    )
                )
                ORDER BY t.name DESC";

        $results = $GLOBALS['wpdb']->get_results( $sql );
        $posts_id = array();

        foreach( $results as $result ){
            array_push( $posts_id, $result->ID );
        }

        $query->set( 'post__in', $posts_id );
        $query->set( 'orderby', 'post__in' );
    }

    # Display book releases by title desc
    if( $query->is_category( 'book-releases' ) ) {
        $query->set( 'orderby', 'post_title' );
        $query->set( 'order', 'desc' );
    }

    # Display publisher and collection by release date of book
    if( $query->is_tax( 'publisher' ) ) {
        $query->set( 'meta_key', 'date_release' );
        $query->set( 'orderby', 'meta_value' );
        $query->set( 'order', 'desc' );
    }

    # Display person by post type, then by release date of book or album
    if( $query->is_tax( 'person' ) ) {
        $queried_slug = $query->queried_object->slug;

        $sql = "SELECT *,
                LOCATE(
                    p.ID,
                    (
                        SELECT GROUP_CONCAT( m.`post_id` ORDER BY m.`meta_value` DESC )
                        FROM `wp_postmeta` m, `wp_term_taxonomy` x, `wp_term_relationships` r, `wp_terms` t
                        WHERE m.`post_id` = r.object_id
                        AND t.term_id = x.term_id
                        AND r.term_taxonomy_id = x.term_taxonomy_id
                        AND m.`meta_key` = 'date_release'
                        AND t.`slug` = '$queried_slug')
                    ) as position
                FROM `wp_posts` p, `wp_terms` t, `wp_term_relationships` r, `wp_term_taxonomy` x
                WHERE p.ID = r.object_id
                AND t.term_id = x.term_id
                AND r.term_taxonomy_id = x.term_taxonomy_id
                AND p.post_status = 'publish'
                AND (
                    x.term_id = (
                        SELECT term_id
                        FROM `wp_terms`
                        WHERE slug = '$queried_slug'
                    )
                )
                ORDER BY
                    CASE p.post_type
                        WHEN 'post'      THEN 1
                        WHEN 'book'      THEN 2
                        WHEN 'album'     THEN 3
                        WHEN 'interview' THEN 4
                        WHEN 'concert'   THEN 5
                    END,
                    position ASC,
                    p.post_date DESC";

        $results = $GLOBALS['wpdb']->get_results( $sql );
        $posts_id = array();

        foreach( $results as $result ){
            array_push( $posts_id, $result->ID );
        }

        $query->set( 'post__in', $posts_id );
        $query->set( 'orderby', 'post__in' );
    }
}

add_action( 'pre_get_posts', 'update_post_order_query' );


/**
 * Get release date of book and display it
 */

function twentysixteen_child_release_date(){
    $release_date = get_post_meta( get_the_ID(), 'date_release', true );
    $post_type    = get_post_type( get_the_ID() );

    if( $post_type == 'book' ){
        printf( '<span class="posted-on book-release-date"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
            _x( 'Released on', 'Used before release date of book.', 'twentysixteen-child' ),
            esc_url( get_permalink() ),
            _x( 'Released on', 'Used before release date of book.', 'twentysixteen-child' ) . ' ' . date_i18n( 'j F Y', strtotime( $release_date ) )
        );
    }

    if( $post_type == 'album' ){
        printf( '<span class="posted-on album-release-date"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
            _x( 'Released on', 'Used before release date of album.', 'twentysixteen-child' ),
            esc_url( get_permalink() ),
            _x( 'Released on', 'Used before release date of album.', 'twentysixteen-child' ) . ' ' . date_i18n( 'j F Y', strtotime( $release_date ) )
        );
    }
}


/**
 * Remove automatic display of sharing buttons
 */

function jptweak_remove_share() {
    remove_filter( 'the_content', 'sharing_display', 19 );
    remove_filter( 'the_excerpt', 'sharing_display', 19 );
    if ( class_exists( 'Jetpack_Likes' ) ) {
        remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
    }
}

add_action( 'loop_start', 'jptweak_remove_share' );


/**
 * Add third party tags
 */

function footer_third_party_tags(){
    if( !is_user_logged_in() ){
        ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-16598900-4"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', 'UA-16598900-4');
        </script>
        <?php
    }

    if( is_single() ){
        ?>
        <div id="amzn-assoc-ad-353aef04-e181-46c1-a75a-74b1a4e6ef93"></div><script async src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US&adInstanceId=353aef04-e181-46c1-a75a-74b1a4e6ef93"></script>
        <?php
    }
}

add_action('wp_footer', 'footer_third_party_tags');


/**
 * Remove unused CSS and JS
 */

function wps_deregister_styles() {
    # WP Block library
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );

    # Unused twentysixteen styles
    wp_dequeue_style( 'twentysixteen-jetpack' );

    wp_deregister_style( 'twentysixteen-style' );
    wp_dequeue_style( 'twentysixteen-style' );

    wp_deregister_style( 'twentysixteen-block-style' );
    wp_dequeue_style( 'twentysixteen-block-style' );

    # Unused wp-embed
    wp_deregister_script( 'wp-embed' );
    wp_dequeue_script( 'wp-embed' );
}

add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );


/**
 * Remove unused Jetpack CSS
 */

# Make sure Jetpack doesn't concatenate all its CSS

add_filter( 'jetpack_implode_frontend_css', '__return_false' );

# Remove each CSS file, one at a time

function wp_remove_all_jp_css() {
    wp_deregister_style( 'AtD_style' );                    # After the Deadline
    wp_deregister_style( 'jetpack_likes' );                # Likes
    wp_deregister_style( 'jetpack_related-posts' );        # Related Posts
    wp_deregister_style( 'jetpack-carousel' );             # Carousel
    wp_deregister_style( 'grunion.css' );                  # Grunion contact form
    wp_deregister_style( 'the-neverending-homepage' );     # Infinite Scroll
    wp_deregister_style( 'infinity-twentyten' );           # Infinite Scroll - Twentyten Theme
    wp_deregister_style( 'infinity-twentyeleven' );        # Infinite Scroll - Twentyeleven Theme
    wp_deregister_style( 'infinity-twentytwelve' );        # Infinite Scroll - Twentytwelve Theme
    wp_deregister_style( 'noticons' );                     # Notes
    wp_deregister_style( 'post-by-email' );                # Post by Email
    wp_deregister_style( 'publicize' );                    # Publicize
    wp_dequeue_style( 'sharedaddy' );                      # Sharedaddy
    wp_deregister_style( 'sharing' );                      # Sharedaddy Sharing
    wp_deregister_style( 'jetpack-widgets' );              # Widgets
    wp_deregister_style( 'jetpack-slideshow' );            # Slideshows
    wp_deregister_style( 'presentations' );                # Presentation shortcode
    wp_deregister_style( 'jetpack-subscriptions' );        # Subscriptions
    wp_deregister_style( 'widget-conditions' );            # Widget Visibility
    wp_deregister_style( 'jetpack_display_posts_widget' ); # Display Posts Widget
    wp_deregister_style( 'gravatar-profile-widget' );      # Gravatar Widget
    wp_deregister_style( 'widget-grid-and-list' );         # Top Posts widget
}

add_action('wp_print_styles', 'wp_remove_all_jp_css' );


# Enable/disable susbcriptions for a specific post

add_filter( 'jetpack_allow_per_post_subscriptions', '__return_true' );


?>
