<?php

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
                $check = false;
                $tag_check = false;

                $patterns = array(
                    '/amitie/',
                    '/bande/',
                    '/biographie/',
                    '/deuil/',
                    '/ecriture',
                    '/enfant/',
                    '/esclavage/',
                    '/fantastique/',
                    '/feminisme/',
                    '/femme/',
                    '/fiction/',
                    '/hommage/',
                    '/identite/',
                    '/immigration/',
                    '/lecture/',
                    '/litteraire/',
                    '/litterature/',
                    '/livre/',
                    '/memoire/',
                    '/narrateur/',
                    '/poesie/',
                    '/prix/',
                    '/racisme/',
                    '/realisme-magique/',
                    '/recit/',
                    '/recueil/',
                    '/reportage/',
                    '/roman/',
                    '/sexualite/',
                    '/slam/',
                    '/temoignage/',
                    '/terrorisme/',
                    '/thriller/',
                    '/violence/',
                );

                # Check if is_tag with book patterns

                if( is_tag() ){
                    $tag = get_queried_object();
                    $tag_slug = $tag->slug;

                    foreach( $patterns as $pattern ){
                        if( preg_match( $pattern, $tag_slug ) ){
                            $tag_check = true;
                            break;
                        }
                    }
                }

                # Check whether or not if I should update tag cloud with specific book tags

                if( is_singular( 'book' ) || is_category( 'reads' ) || is_tax( 'publisher' ) || $tag_check == true ){
                    $term_slug = $value->slug;

                    foreach( $patterns as $pattern ){
                        if( preg_match( $pattern, $term_slug ) ){
                            $check = true;
                            break;
                        }
                    }

                    # Will construct tag cloud with only specific book tags from posts

                    if( $check == true && !in_array( $value, $array_of_terms_in_category, true ) ){
                        array_push( $array_of_terms_in_category, $value->term_id );
                    }
                }

                # Will construct tag cloud with only tags from posts

                else {
                    if( !in_array( $value, $array_of_terms_in_category, true ) ){
                        array_push( $array_of_terms_in_category, $value->term_id );
                    }
                }
            }
        }

        $tag_args = array(
                        'smallest' => 1,
                        'largest'  => 1,
                        'unit'     => 'em',
                        'format'   => 'list',
                        'number'   => 55,
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

?>
