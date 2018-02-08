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

    $people_list = get_the_terms( $post->ID, 'person', '', ', ' );
    if ( $people_list ) {
        printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
            _x( 'People', 'Used before tag names.', 'twentysixteen' ),
            $people_list
        );
    }

    $locations_list = get_the_terms( $post->ID, 'location', '', ', ' );
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

?>
