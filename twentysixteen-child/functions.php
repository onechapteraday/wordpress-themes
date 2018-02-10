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
 * Add new custom post type book into queries.
 *
 * @since Twenty Sixteen Child 1.0
 *
 * @param array $query Query of the page.
 * @return array A new modified query.
 */

function add_my_post_types_to_query( $query ) {
    if ( !is_admin() ) {
        if ( is_archive() && $query->is_main_query() )
            $query->set( 'post_type', array( 'post', 'book' ) );
    }

    return $query;
}

add_action( 'pre_get_posts', 'add_my_post_types_to_query' );



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

function filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( is_singular() ) {
        $html = '<figure>' . $html;
    } else {
        $html = '<figure><a href="'. get_permalink($post_id). '" aria-hidden="true">' . $html . '</a>';
    }

    if (function_exists('get_featured_image_copyright')) {
        $link = get_featured_image_copyright_link($post_thumbnail_id);
        $author = get_featured_image_copyright_author($post_thumbnail_id);

	if ( $link ) {
                $html .= '<figcaption class="wp-post-image-copyright">Copyright : ';
		$html .= '<a href="' . $link . '" target="_blank" rel="nofollow">';
		$html .= $author . '</a></figcaption>';
	}
    }

    $html .= '</figure>';

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
    if ( 'post' === get_post_type() || 'book' === get_post_type() ) {
        $author_avatar_size = apply_filters( 'twentysixteen_author_avatar_size', 49 );
        printf( '<span class="byline"><span class="author vcard">%1$s<span class="screen-reader-text">%2$s </span> <a class="url fn n" href="%3$s">%4$s</a></span></span>',
            get_avatar( get_the_author_meta( 'user_email' ), $author_avatar_size ),
            _x( 'Author', 'Used before post author name.', 'twentysixteen' ),
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            get_the_author()
        );
    }

    if ( in_array( get_post_type(), array( 'post', 'book', 'attachment' ) ) ) {
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

    if ( 'post' === get_post_type() || 'book' === get_post_type() ) {
        twentysixteen_entry_taxonomies();
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
function twentysixteen_entry_taxonomies() {
    $categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentysixteen' ) );
    if ( $categories_list && twentysixteen_categorized_blog() ) {
        printf( '<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
            _x( 'Categories', 'Used before category names.', 'twentysixteen' ),
            $categories_list
        );
    }

    $people_list = get_the_term_list( get_the_ID(), 'person', '', ', ' );
    if ( $people_list ) {
        printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
            _x( 'People', 'Used before tag names.', 'twentysixteen' ),
            $people_list
        );
    }

    $locations_list = get_the_terms( get_the_ID(), 'location', '', ', ' );
    if ( $locations_list ) {
        $i = 0;
        $locations = '';
        foreach($locations_list as $tag) {
            if ( $i != 0) $locations .= ', ';
            $locations .= '<a href="'.get_term_link($tag->term_id).'">';
            $locations .= __($tag->name, 'location-taxonomy');
            $locations .= '</a>';
            $i++;
        }
        printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
            _x( 'Locations', 'Used before tag names.', 'twentysixteen' ),
            $locations
        );
    }

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
        'description' => __( 'Widgets appear left of Sidebar 1 and below the FullWidth Top widget area. This widget area is especially designed for the custom Zuki Posts by Category widgets.', 'twentysixteen-child' ),
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
         'description' => __( 'Widgets appear left of Sidebar 2 and below the FullWidth Center widget area. This widget area is especially designed for the custom Zuki Posts by Category widgets.', 'twentysixteen-child' ),
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
        $title = $instance['title'];
        $postnumber = $instance['postnumber'];
        $category = apply_filters('widget_title', $instance['category']);
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
        $smallone_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => array('post', 'book'),
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
        $title = $instance['title'];
        $postnumber = $instance['postnumber'];
        $category = apply_filters('widget_title', $instance['category']);
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
        $smalltwo_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => array('post', 'book'),
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
        $title = $instance['title'];
        $postnumber = $instance['postnumber'];
        $category = apply_filters('widget_title', $instance['category']);
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
        $mediumone_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => array('post', 'book'),
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

                        <div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>
                        <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php twentysixteenchild_title_limit( 85, '...'); ?></a></h3>
                        <p class="summary"><?php echo twentysixteenchild_excerpt(20); ?></p>

                        <div class="entry-author">
                        <?php
                            printf( __( 'by <a href="%1$s" title="%2$s">%3$s</a>', 'twentysixteen-child' ),
                            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                            sprintf( esc_attr__( 'All posts by %s', 'twentysixteen-child' ), get_the_author() ),
                            get_the_author() );
                        ?>
                        </div><!-- end .entry-author -->

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
        $title = $instance['title'];
        $postnumber = $instance['postnumber'];
        $category = apply_filters('widget_title', $instance['category']);
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
        $mediumtwo_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => array('post', 'book'),
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
                        <div class="entry-author">
                            <?php
                                printf( __( 'Published by <a href="%1$s" title="%2$s">%3$s</a>', 'twentysixteen-child' ),
                                esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                                sprintf( esc_attr__( 'All posts by %s', 'twentysixteen-child' ), get_the_author() ),
                                get_the_author() );
                            ?>
                        </div><!-- end .entry-author -->

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
        $title = $instance['title'];
        $postnumber = $instance['postnumber'];
        $category = apply_filters('widget_title', $instance['category']);
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
        $bigone_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => array('post', 'book'),
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
                        <div class="entry-author">
                            <?php
                                printf( __( '<span>by</span> <a href="%1$s" title="%2$s">%3$s</a>', 'twentysixteen-child' ),
                                esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                                sprintf( esc_attr__( 'All posts by %s', 'twentysixteen-child' ), get_the_author() ),
                                get_the_author() );
                            ?>
                        </div><!-- end .entry-author -->

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
        $title = $instance['title'];
        $postnumber = $instance['postnumber'];
        $category = apply_filters('widget_title', $instance['category']);
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
        $bigtwo_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => array('post', 'book'),
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
                <div class="entry-author">
                <?php
                    printf( __( '<span>by</span> <a href="%1$s" title="%2$s">%3$s</a>', 'twentysixteen-child' ),
                    esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                    sprintf( esc_attr__( 'All posts by %s', 'twentysixteen-child' ), get_the_author() ),
                    get_the_author() );
                ?>
                </div><!-- end .entry-author -->

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
        $title = $instance['title'];
        $postnumber = $instance['postnumber'];
        $category = apply_filters('widget_title', $instance['category']);
        $tag = isset($instance['tag']) ? $instance['tag'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        # The Query
        $color_query = new WP_Query(array (
            'post_status'         => 'publish',
            'post_type'           => array('post', 'book'),
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
        $title = $instance['title'];
        $quotetext = $instance['quotetext'];
        $quoteauthor = $instance['quoteauthor'];

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
?>
