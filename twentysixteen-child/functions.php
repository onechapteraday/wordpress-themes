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
                $html .= '<figcaption class="wp-post-image-copyright">&rsaquo; ';
		$html .= '<a href="' . $link . '" target="_blank" rel="nofollow">';
		$html .= $author . '</a></figcaption>';
	}
    }

    $html .= '</figure>';

    # Make filter magic happen here...
    return $html;
};

add_filter( 'post_thumbnail_html', 'filter_post_thumbnail_html', 10, 5 );

?>
