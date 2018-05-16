<?php

# Import parent styles

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

function my_theme_enqueue_styles() {
    $parent_style = 'twentysixteen-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}



# Short title

function twentysixteenchild_title_limit($length, $replacer = '...') {
    $string = the_title('','',FALSE);

    if(strlen($string) > $length)
        $string = (preg_match('/^(.*)\W.*$/', substr($string, 0, $length+1), $matches) ? $matches[1] : substr($string, 0, $length)) . $replacer;

    echo $string;
}



# Multiple custom excerpt lengths

function twentysixteenchild_excerpt($limit) {
    $excerpt = explode(' ', get_the_excerpt(), $limit);

    if (count($excerpt)>=$limit) {
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'...';
    } else {
        $excerpt = implode(" ",$excerpt);
    }

    $excerpt = preg_replace('`[[^]]*]`','',$excerpt);

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

function twentysixteen_post_thumbnail() {
    if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
        return;
    }

    if ( is_singular() ) :
    ?>

    <div class="post-thumbnail">
        <figure>
            <?php the_post_thumbnail(); ?>
        </figure>
    </div><!-- .post-thumbnail -->

    <?php else : ?>

    <div class="post-thumbnail">
        <figure>
            <?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
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
    if( !is_singular() ){
       $html = '<a href="'. get_permalink( $post_id ). '" aria-hidden="true">' . $html . '</a>';
    }

    if( function_exists( 'get_featured_image_copyright' ) ){
        $link   = get_featured_image_copyright_link( $post_thumbnail_id );
        $author = get_featured_image_copyright_author( $post_thumbnail_id );

	if( $link ){
            $html .= '<figcaption class="wp-post-image-copyright">&copy; ';
	    $html .= '<a href="' . $link . '" target="_blank" rel="nofollow">';
	    $html .= $author . '</a></figcaption>';
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
        comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) );
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
function sortPersonByName( $a, $b ){
    $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
    $at = strtr( $a->name, $translit );
    $bt = strtr( $b->name, $translit );

    return strcoll( $at, $bt );
}

function sortLocationByTranslation( $a, $b ){
    $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
    $at = strtr( $a->translation, $translit );
    $bt = strtr( $b->translation, $translit );

    return strcoll( $at, $bt );
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
            usort( $people_list, 'sortPersonByName' );
            $people = '';

            foreach($people_list as $i => $tag) {
                if ( $i > 0) $people .= ', ';
                $people .= '<a href="' . get_term_link( $tag->term_id ) . '">';
                $people .= $tag->name;
                $people .= '</a>';
            }

            if ( $people ) {
                printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                    _x( 'People', 'Used before tag names.', 'twentysixteen' ),
                    $people
                );
            }
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

            printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                _x( 'Locations', 'Used before tag names.', 'twentysixteen' ),
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

    register_sidebar( array(
        'name'          => __( 'Content Bottom 1', 'twentysixteen' ),
        'id'            => 'sidebar-2',
        'description'   => __( 'Appears at the bottom of the content on posts and pages.', 'twentysixteen' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar( array(
        'name'          => __( 'Content Bottom 2', 'twentysixteen' ),
        'id'            => 'sidebar-3',
        'description'   => __( 'Appears at the bottom of the content on posts and pages.', 'twentysixteen' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __( 'Sidebar for Books', 'twentysixteen-child' ),
        'id'            => 'book',
        'description'   => 'Add widgets here to appears in your Books sidebar.',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __( 'Sidebar for Music', 'twentysixteen-child' ),
        'id'            => 'music',
        'description'   => 'Add widgets here to appears in your Music sidebar.',
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
}

add_action( 'widgets_init', 'twentysixteenchild_widgets_init' );



/**
 * Add Recent posts (Small 1) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_recentposts_small_one extends WP_Widget {

    public function __construct() {
        parent::__construct( 'twentysixteenchild_recentposts_small_one', __( 'New: Recent Posts (Small 1)', 'twentysixteen-child' ), array(
            'classname'   => 'widget_twentysixteenchild_recentposts_small_one',
            'description' => __( 'Small Recents Posts widget with featured images.', 'twentysixteen-child' ),
        ));
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $postnumber = isset($instance['postnumber']) ? $instance['postnumber'] : '';
        $category = isset($instance['category']) ? apply_filters('widget_title', $instance['category']) : '';
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
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

        $smallone_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => $post_types,
            'posts_per_page'      => $postnumber,
            'category_name'       => $category,
            'tag'                 => $tag,
            'ignore_sticky_posts' => 1
        ));

        # The Loop
        if($smallone_query->have_posts()) : ?>

            <?php while($smallone_query->have_posts()) : $smallone_query->the_post() ?>
            <article class="rp-small-one">
                <div class="rp-small-one-content cf">
                    <?php if ( '' != get_the_post_thumbnail() ) : ?>
                        <div class="entry-thumb">
                            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_post_thumbnail('twentysixteenchild-small-square'); ?></a>
                        </div><!-- end .entry-thumb -->
                    <?php endif; ?>

                    <div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>
                    <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php twentysixteenchild_title_limit( 60, '...'); ?></a></h3>
                </div><!--end .rp-small-one-content -->
            </article><!--end .rp-small-one -->
            <?php endwhile ?>

        <?php endif ?>

        <?php
        echo $args['after_widget'];

        # Reset the post globals as this query will have stomped on it
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category'] = $new_instance['category'];
        $instance['tag'] = $new_instance['tag'];

        return $new_instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('postnumber'); ?>"><?php _e('Number of posts to show:','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('postnumber'); ?>" value="<?php echo esc_attr($postnumber); ?>" class="widefat" id="<?php echo $this->get_field_id('postnumber'); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category slug (optional, separate multiple categories by comma):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('category'); ?>" value="<?php echo esc_attr($category); ?>" class="widefat" id="<?php echo $this->get_field_id('category'); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag slug (optional, separate multiple tags by comma):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo esc_attr($tag); ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
        </p>
        <?php

     }
}

register_widget('twentysixteenchild_recentposts_small_one');



/**
 * Add Recent posts (Small 2) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_recentposts_small_two extends WP_Widget {

    public function __construct() {
        parent::__construct( 'twentysixteenchild_recentposts_small_two', __( 'New: Recent Posts (Small 2)', 'twentysixteen-child' ), array(
            'classname'   => 'widget_twentysixteenchild_recentposts_small_two',
            'description' => __( 'Small Recents Posts widget without featured images.', 'twentysixteen-child' ),
        ));
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $postnumber = isset($instance['postnumber']) ? $instance['postnumber'] : '';
        $category = isset($instance['category']) ? apply_filters('widget_title', $instance['category']) : '';
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
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

        $smalltwo_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => $post_types,
            'posts_per_page'      => $postnumber,
            'category_name'       => $category,
            'tag'                 => $tag,
            'ignore_sticky_posts' => 1
        ));

        # The Loop
        if($smalltwo_query->have_posts()) : ?>

            <?php while($smalltwo_query->have_posts()) : $smalltwo_query->the_post() ?>
            <article class="rp-small-two">
                <p class="summary"><a href="<?php the_permalink(); ?>"><span class="entry-title"><?php the_title(); ?></span><?php echo twentysixteenchild_excerpt(15); ?></a><span class="entry-date"><?php echo get_the_date(); ?></span></p>
            </article><!--end .rp-small-two -->

            <?php endwhile ?>

        <?php endif ?>

        <?php
	echo $args['after_widget'];

        # Reset the post globals as this query will have stomped on it
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category'] = $new_instance['category'];
        $instance['tag'] = $new_instance['tag'];

        return $new_instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('postnumber'); ?>"><?php _e('Number of posts to show:','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('postnumber'); ?>" value="<?php echo esc_attr($postnumber); ?>" class="widefat" id="<?php echo $this->get_field_id('postnumber'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category slug (optional):','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('category'); ?>" value="<?php echo esc_attr($category); ?>" class="widefat" id="<?php echo $this->get_field_id('category'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag slug (optional):','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo esc_attr($tag); ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
	</p>
	<?php

    }
}

register_widget('twentysixteenchild_recentposts_small_two');



/**
 * Add Recent posts (Medium 1) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_recentposts_medium_one extends WP_Widget {

    public function __construct() {
        parent::__construct( 'twentysixteenchild_recentposts_medium_one', __( 'New: Recent Posts (Medium 1)', 'twentysixteen-child' ), array(
            'classname'   => 'widget_twentysixteenchild_recentposts_medium_one',
            'description' => __( 'Medium-sized Recents Posts with featured image and excerpt.', 'twentysixteen-child' ),
        ) );
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $postnumber = isset($instance['postnumber']) ? $instance['postnumber'] : '';
        $category = isset($instance['category']) ? apply_filters('widget_title', $instance['category']) : '';
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
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

        $mediumone_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => $post_types,
            'posts_per_page'      => $postnumber,
            'category_name'       => $category,
            'tag'                 => $tag,
            'ignore_sticky_posts' => 1
        ));

        # The Loop
        if($mediumone_query->have_posts()) : ?>

            <?php while($mediumone_query->have_posts()) : $mediumone_query->the_post() ?>
                <article class="rp-medium-one">
                    <div class="rp-medium-one-content">
                        <?php if ( '' != get_the_post_thumbnail() ) : ?>
                            <div class="entry-thumb">
                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_post_thumbnail('twentysixteenchild-medium-landscape'); ?></a>
                            </div><!-- end .entry-thumb -->
                        <?php endif; ?>

                        <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php twentysixteenchild_title_limit( 85, '...'); ?></a></h3>
                        <p class="summary"><?php echo twentysixteenchild_excerpt(20); ?></p>
                        <div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>

                        <?php if ( comments_open() ) : ?>
                            <div class="entry-comments">
				<?php comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) ); ?>
                            </div><!-- end .entry-comments -->
                        <?php endif; // comments_open() ?>
                    </div><!--end .rp-medium-one -->
                </article><!--end .rp-medium-one -->
            <?php endwhile ?>

        <?php endif ?>

        <?php
        echo $args['after_widget'];

        # Reset the post globals as this query will have stomped on it
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category'] = $new_instance['category'];
        $instance['tag'] = $new_instance['tag'];

        return $new_instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('postnumber'); ?>"><?php _e('Number of posts to show:','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('postnumber'); ?>" value="<?php echo esc_attr($postnumber); ?>" class="widefat" id="<?php echo $this->get_field_id('postnumber'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('category'); ?>" value="<?php echo esc_attr($category); ?>" class="widefat" id="<?php echo $this->get_field_id('category'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo esc_attr($tag); ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
	</p>
	<?php

    }
}

register_widget('twentysixteenchild_recentposts_medium_one');



/**
 * Add Recent posts (Medium 2) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_recentposts_medium_two extends WP_Widget {

    public function __construct() {
        parent::__construct( 'twentysixteenchild_recentposts_medium_two', __( 'New: Recent Posts (Medium 2)', 'twentysixteen-child' ), array(
            'classname'   => 'widget_twentysixteenchild_recentposts_medium_two',
            'description' => __( 'Medium-sized Recents Posts in a 2-column layout with featured image and excerpt.', 'twentysixteen-child' ),
        ));
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $postnumber = isset($instance['postnumber']) ? $instance['postnumber'] : '';
        $category = isset($instance['category']) ? apply_filters('widget_title', $instance['category']) : '';
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
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

        $mediumtwo_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => $post_types,
            'posts_per_page'      => $postnumber,
            'category_name'       => $category,
            'tag'                 => $tag,
            'ignore_sticky_posts' => 1
        ));

        # The Loop
        if($mediumtwo_query->have_posts()) : ?>

            <?php while($mediumtwo_query->have_posts()) : $mediumtwo_query->the_post() ?>
            <article class="rp-medium-two">
                <div class="rp-medium-two-content">
                    <?php if ( '' != get_the_post_thumbnail() ) : ?>
                        <div class="entry-thumb">
                            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_post_thumbnail('twentysixteenchild-medium-landscape'); ?></a>
                        </div><!-- end .entry-thumb -->
                    <?php endif; ?>

                    <div class="story">
                        <h3 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a></h3>

                        <p class="summary"><?php echo twentysixteenchild_excerpt(30); ?></p>
                        <div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>

                        <?php if ( comments_open() ) : ?>
                            <div class="entry-comments">
				<?php comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) ); ?>
                            </div><!-- end .entry-comments -->
                        <?php endif; // comments_open() ?>

                        <div class="entry-cats">
                            <?php the_category(', '); ?>
                        </div><!-- end .entry-cats -->
                    </div><!--end .story -->
                </div><!--end .rp-medium-two-content -->
            </article><!--end .rp-medium-two -->
            <?php endwhile ?>

        <?php endif ?>

        <?php
        echo $args['after_widget'];

        # Reset the post globals as this query will have stomped on it
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category'] = $new_instance['category'];
        $instance['tag'] = $new_instance['tag'];

        return $new_instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('postnumber'); ?>"><?php _e('Number of posts to show:','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('postnumber'); ?>" value="<?php echo esc_attr($postnumber); ?>" class="widefat" id="<?php echo $this->get_field_id('postnumber'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category slug (optional):','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('category'); ?>" value="<?php echo esc_attr($category); ?>" class="widefat" id="<?php echo $this->get_field_id('category'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag slug (optional):','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo esc_attr($tag); ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
	</p>
	<?php

    }
}

register_widget('twentysixteenchild_recentposts_medium_two');



/**
 * Add Recent posts (Big 1) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_recentposts_big_one extends WP_Widget {

    public function __construct() {
        parent::__construct( 'twentysixteenchild_recentposts_big_one', __( 'New: Recent Posts (Big 1)', 'twentysixteen-child' ), array(
            'classname'   => 'widget_twentysixteenchild_recentposts_big_one',
            'description' => __( 'Big Recents Posts with an overlay excerpt text. Featured images must have a minimum size of 1200x800 pixel.', 'twentysixteen-child' ),
        ));
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $postnumber = isset($instance['postnumber']) ? $instance['postnumber'] : '';
        $category = isset($instance['category']) ? apply_filters('widget_title', $instance['category']) : '';
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
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

        $bigone_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => $post_types,
            'posts_per_page'      => $postnumber,
            'category_name'       => $category,
            'tag'                 => $tag,
            'ignore_sticky_posts' => 1
        ));

        # The Loop
        if($bigone_query->have_posts()) : ?>

            <?php while($bigone_query->have_posts()) : $bigone_query->the_post() ?>
            <article class="rp-big-one cf">
                <div class="rp-big-one-content">

                    <?php if ( '' != get_the_post_thumbnail() ) : ?>
                        <div class="entry-thumb">
                            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_post_thumbnail('twentysixteenchild-fullwidth'); ?></a>
                        </div><!-- end .entry-thumb -->
                    <?php endif; ?>

                    <div class="story">
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a></h2>

			<p class="summary"><?php echo twentysixteenchild_excerpt(65); ?></p>
			<div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>

                        <?php if ( comments_open() ) : ?>
                            <div class="entry-comments">
				<?php comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) ); ?>
                            </div><!-- end .entry-comments -->
                        <?php endif; // comments_open() ?>

                        <div class="entry-cats">
		            <?php the_category(', '); ?>
                        </div><!-- end .entry-cats -->
                    </div><!--end .story -->

                </div><!--end .rp-big-one-content -->
            </article><!--end .rp-big-one -->
            <?php endwhile ?>

        <?php endif ?>

        <?php
        echo $args['after_widget'];

        # Reset the post globals as this query will have stomped on it
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category'] = $new_instance['category'];
        $instance['tag'] = $new_instance['tag'];

        return $new_instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('postnumber'); ?>"><?php _e('Number of posts to show:','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('postnumber'); ?>" value="<?php echo esc_attr($postnumber); ?>" class="widefat" id="<?php echo $this->get_field_id('postnumber'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('category'); ?>" value="<?php echo esc_attr($category); ?>" class="widefat" id="<?php echo $this->get_field_id('category'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo esc_attr($tag); ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
	</p>
	<?php
    }
}

register_widget('twentysixteenchild_recentposts_big_one');



/**
 * Add Recent posts (Big 2) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_recentposts_big_two extends WP_Widget {

    public function __construct() {
        parent::__construct( 'twentysixteenchild_recentposts_big_two', __( 'New: Recent Posts (Big 2)', 'twentysixteen-child' ), array(
            'classname'   => 'widget_twentysixteenchild_recentposts_big_two',
            'description' => __( 'Big Recents Posts with featured image and a 2-column excerpt. Featured images must have a minimum size of 1200x800 pixel.', 'twentysixteen-child' ),
        ));
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $postnumber = isset($instance['postnumber']) ? $instance['postnumber'] : '';
        $category = isset($instance['category']) ? apply_filters('widget_title', $instance['category']) : '';
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
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

        $bigtwo_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => $post_types,
            'posts_per_page'      => $postnumber,
            'category_name'       => $category,
            'tag'                 => $tag,
            'ignore_sticky_posts' => 1
        ));

    # The Loop
    if($bigtwo_query->have_posts()) : ?>

        <?php while($bigtwo_query->have_posts()) : $bigtwo_query->the_post() ?>
        <article class="rp-big-two cf">
            <div class="rp-big-two-content">

            <?php if ( '' != get_the_post_thumbnail() ) : ?>
                <div class="entry-thumb">
                    <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_post_thumbnail('twentysixteenchild-fullwidth'); ?></a>
                </div><!-- end .entry-thumb -->
            <?php endif; ?>

            <header class="entry-header">
                <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a></h2>
            </header>

            <div class="story">
                <p class="summary"><?php echo twentysixteenchild_excerpt(175); ?></p>

                <footer class="entry-footer">
                    <div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>

                    <?php if ( comments_open() ) : ?>
                        <div class="entry-comments">
                            <?php comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) ); ?>
                        </div><!-- end .entry-comments -->
                    <?php endif; // comments_open() ?>

                    <div class="entry-cats">
                        <?php the_category(', '); ?>
                    </div><!-- end .entry-cats -->
                </footer>
             </div><!--end .story -->
             </div><!--end .rp-big-two-content -->
         </article><!--end .rp-big-two -->
       <?php endwhile ?>

    <?php endif ?>

    <?php
        echo $args['after_widget'];

        # Reset the post globals as this query will have stomped on it
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category'] = $new_instance['category'];
        $instance['tag'] = $new_instance['tag'];

        return $new_instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('postnumber'); ?>"><?php _e('Number of posts to show:','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('postnumber'); ?>" value="<?php echo esc_attr($postnumber); ?>" class="widefat" id="<?php echo $this->get_field_id('postnumber'); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('category'); ?>" value="<?php echo esc_attr($category); ?>" class="widefat" id="<?php echo $this->get_field_id('category'); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo esc_attr($tag); ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
        </p>
        <?php

    }
}

register_widget('twentysixteenchild_recentposts_big_two');



/**
 * Add Recent posts (Colored) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_recentposts_color extends WP_Widget {

    public function __construct() {
        parent::__construct( 'twentysixteenchild_recentposts_color', __( 'New: Recent Posts (Background)', 'twentysixteen-child' ), array(
            'classname'   => 'widget_twentysixteenchild_recentposts_color',
            'description' => __( 'Medium-sized Recents Posts with a background color.', 'twentysixteen-child' ),
        ));
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $postnumber = isset($instance['postnumber']) ? $instance['postnumber'] : '';
        $category = isset($instance['category']) ? apply_filters('widget_title', $instance['category']) : '';
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
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

        $color_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => $post_types,
            'posts_per_page'      => $postnumber,
            'category_name'       => $category,
            'tag'                 => $tag,
            'ignore_sticky_posts' => 1
        ));
        ?>

        <div class="bg-wrap cf">
            <?php
            # The Loop
            if($color_query->have_posts()) : ?>

                <?php while($color_query->have_posts()) : $color_query->the_post() ?>
                <article class="rp-color cf">
                    <?php if ( '' != get_the_post_thumbnail() ) : ?>
                        <div class="entry-thumb">
                            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_post_thumbnail('twentysixteenchild-medium-portrait'); ?></a>
                        </div><!-- end .entry-thumb -->
                    <?php endif; ?>

                    <header class="entry-header">
                        <div class="entry-cats">
                            <?php the_category(', '); ?>
                        </div><!-- end .entry-cats -->

                        <h3 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a></h3>
                    </header>

                    <div class="story">
                        <p class="summary"><?php echo twentysixteenchild_excerpt(30); ?></p>

                        <footer class="entry-footer">
                            <div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>

                            <?php if ( comments_open() ) : ?>
                                <div class="entry-comments">
                                    <?php comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) ); ?>
                                </div><!-- end .entry-comments -->
                            <?php endif; // comments_open() ?>
                        </footer>
                    </div><!--end .story -->
                 </article><!--end .rp-color -->

                <?php endwhile ?>
            <?php endif ?>
        </div><!--end .bg-wrap -->

        <?php
        echo $args['after_widget'];

        # Reset the post globals as this query will have stomped on it
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category'] = $new_instance['category'];
        $instance['tag'] = $new_instance['tag'];

        return $new_instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('postnumber'); ?>"><?php _e('Number of posts to show:','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('postnumber'); ?>" value="<?php echo esc_attr($postnumber); ?>" class="widefat" id="<?php echo $this->get_field_id('postnumber'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('category'); ?>" value="<?php echo esc_attr($category); ?>" class="widefat" id="<?php echo $this->get_field_id('category'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo esc_attr($tag); ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
	</p>
	<?php

    }
}

register_widget('twentysixteenchild_recentposts_color');




/**
 * Add Random posts (Colored) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_randomposts_color extends WP_Widget {

    public function __construct() {
        parent::__construct( 'twentysixteenchild_randomposts_color', __( 'New: Random Posts (Background)', 'twentysixteen-child' ), array(
            'classname'   => 'widget_twentysixteenchild_randomposts_color',
            'description' => __( 'Medium-sized Random Posts with a background color.', 'twentysixteen-child' ),
        ));
    }

    public function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $postnumber = isset($instance['postnumber']) ? $instance['postnumber'] : '';
        $category = isset($instance['category']) ? apply_filters('widget_title', $instance['category']) : '';
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
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

        $color_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => $post_types,
            'posts_per_page'      => $postnumber,
            'category_name'       => $category,
            'tag'                 => $tag,
            'orderby'             => 'rand',
            'ignore_sticky_posts' => 1
        ));
        ?>

        <div class="bg-wrap cf">
            <?php
            # The Loop
            if($color_query->have_posts()) : ?>

                <?php while($color_query->have_posts()) : $color_query->the_post() ?>
                <article class="rp-color cf">
                    <?php if ( '' != get_the_post_thumbnail() ) : ?>
                        <div class="entry-thumb">
                            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_post_thumbnail('twentysixteenchild-medium-portrait'); ?></a>
                        </div><!-- end .entry-thumb -->
                    <?php endif; ?>

                    <header class="entry-header">
                        <div class="entry-cats">
                            <?php the_category(', '); ?>
                        </div><!-- end .entry-cats -->

                        <h3 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a></h3>
                    </header>

                    <div class="story">
                        <p class="summary"><?php echo twentysixteenchild_excerpt(30); ?></p>

                        <footer class="entry-footer">
                            <div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>

                            <?php if ( comments_open() ) : ?>
                                <div class="entry-comments">
                                    <?php comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) ); ?>
                                </div><!-- end .entry-comments -->
                            <?php endif; // comments_open() ?>
                        </footer>
                    </div><!--end .story -->
                 </article><!--end .rp-color -->

                <?php endwhile ?>
            <?php endif ?>
        </div><!--end .bg-wrap -->

        <?php
        echo $args['after_widget'];

        # Reset the post globals as this query will have stomped on it
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category'] = $new_instance['category'];
        $instance['tag'] = $new_instance['tag'];

        return $new_instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('postnumber'); ?>"><?php _e('Number of posts to show:','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('postnumber'); ?>" value="<?php echo esc_attr($postnumber); ?>" class="widefat" id="<?php echo $this->get_field_id('postnumber'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('category'); ?>" value="<?php echo esc_attr($category); ?>" class="widefat" id="<?php echo $this->get_field_id('category'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag slug (optional):','twentysixteen-child'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo esc_attr($tag); ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
	</p>
	<?php

    }
}

register_widget('twentysixteenchild_randomposts_color');



/**
 * Add Quote widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_quote extends WP_Widget {

    public function __construct() {
        parent::__construct( 'twentysixteenchild_quote', __( 'New: Quote', 'twentysixteen-child' ), array(
            'classname'   => 'widget_twentysixteenchild_quote',
            'description' => __( 'A big quote or text slogan.', 'twentysixteen-child' ),
        ));
    }

    public function widget($args, $instance) {
        extract( $args );
        $title = isset($instance['title']) ? $instance['title'] : '';
        $quotetext = isset($instance['quotetext']) ? $instance['quotetext'] : '';
        $quoteauthor = isset($instance['quoteauthor']) ? $instance['quoteauthor'] : '';

        echo $before_widget;

        if($title != '')
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

	?>
        <div class="quote-wrap">
            <blockquote class="quote-text"><?php echo ( wp_kses_post(wpautop($quotetext))  ); ?>
            <?php
	        if($quoteauthor != '') {
                    echo '<cite class="quote-author"> ' . ( wp_kses_post($quoteauthor) ) . ' </cite>';
                }
            ?>
            </blockquote>
        </div><!-- end .quote-wrap -->
        <?php

        echo $after_widget;

        # Reset the post globals as this query will have stomped on it
        wp_reset_postdata();
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = $new_instance['title'];
        $instance['quotetext'] = $new_instance['quotetext'];
        $instance['quoteauthor'] = $new_instance['quoteauthor'];

        return $new_instance;
    }

    function form($instance) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $quotetext = isset( $instance['quotetext'] ) ? esc_attr( $instance['quotetext'] ) : '';
        $quoteauthor = isset( $instance['quoteauthor'] ) ? esc_attr( $instance['quoteauthor'] ) : '';

	?>
	<p>
	    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('quotetext'); ?>"><?php _e('Quote Text:','twentysixteen-child'); ?></label>
	    <textarea name="<?php echo $this->get_field_name('quotetext'); ?>" class="widefat" rows="8" cols="12" id="<?php echo $this->get_field_id('quotetext'); ?>"><?php echo( $quotetext ); ?></textarea>
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id('quoteauthor'); ?>"><?php _e('Quote Author (optional):','twentysixteen-child'); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name('quoteauthor'); ?>" value="<?php echo esc_attr($quoteauthor); ?>" class="widefat" id="<?php echo $this->get_field_id('quoteauthor'); ?>" />
	</p>
	<?php

    }
}

register_widget('twentysixteenchild_quote');


/**
 * Create widget to retrieve popular tags in specific category
 *
 */

class popular_tags_in_category_widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            # Base ID of your widget
            'popular_tags_in_category_widget',

            # Widget name will appear in UI
            __('Popular Tags in Category Widget', 'popular_tags_in_category_widget_domain'),

            # Widget description
            array( 'description' => __( 'This widget will show all the tags in the specific category you choose', 'popular_tags_in_category_widget_domain' ), )
        );
    }

    # Creating widget front-end
    public function widget( $args, $instance ) {
        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';

        # Before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        # This is where you run the code and display the output

        # Find the category where is displayed the widget
        $categories = get_the_category();

	$catID = null;
        if( !is_home() ){
	    if( isset( $categories[0] ) ) {
                $catID = $categories[0]->cat_ID;
	    }
	}

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

        if ( $catID ) {
            $posts_with_category = get_posts( array(
                         'category'       => $catID,
                         'post_type'      => $post_types,
                         'number_posts'   => -1,
                         'posts_per_page' => -1,
                     ));
        }
        else {
            $posts_with_category = get_posts( array(
                         'post_type'      => $post_types,
                         'number_posts'   => -1,
                         'posts_per_page' => -1,
                     ));
        }

        $array_of_terms_in_category = array();

        foreach( $posts_with_category as $post ) {
            $terms = wp_get_post_terms( $post->ID );

            foreach( $terms as $value ){
                if( !in_array( $value, $array_of_terms_in_category, true ) ){
                    array_push( $array_of_terms_in_category, $value->term_id );
                }
            }
        }

        $tag_args = array(
                    'smallest' => 1,
                    'largest'  => 1,
                    'unit'     => 'em',
                    'format'   => 'list',
                    'number'   => 75,
                    'orderby'  => 'count',
                    'order'    => 'DESC',
                    'include'  => $array_of_terms_in_category,
                );

        echo '<div class="tagcloud">';

        $mytags_array = get_terms ( $tag_args );

        if( sizeof( $mytags_array ) ){
            function widget_sort_tag_by_name( $a, $b ){
                $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
                $at = strtolower( strtr( $a->name, $translit ) );
                $bt = strtolower( strtr( $b->name, $translit ) );

                return strcoll( $at, $bt );
            }

            usort( $mytags_array, 'widget_sort_tag_by_name' );

            echo '<ul class="wp-tag-cloud">';

	    foreach ( $mytags_array as $mytag ) {
                echo '<li><a href="' . get_term_link( $mytag->term_id ) . '" class="tag-cloud-link tag-link-' . $mytag->term_id . '">';
                echo $mytag->name;
                echo '</a></li>';
	    }

            echo '</ul>';
	}

        echo '</div>';

        echo $args['after_widget'];
    }

    # Widget Backend
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        } else {
            $title = __( 'New title', 'popular_tags_in_category_widget_domain' );
        }

        # Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    # Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}

# Register and load the widget
function twentysixteenchild_popular_tags() {
    register_widget( 'popular_tags_in_category_widget' );
}

add_action( 'widgets_init', 'twentysixteenchild_popular_tags' );


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

function jetpack_relatedposts_update_thumbnail_size( $thumbnail_size ){
    $thumbnail_size['width'] = 420;
    $thumbnail_size['height'] = 655;
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

?>
