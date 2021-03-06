<?php

/**
 * Add Random posts (Small 2) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_randomposts_small_two extends WP_Widget {

    public function __construct() {
        parent::__construct(
            # Base ID of your widget
            'twentysixteenchild_randomposts_small_two',

            # Widget name will appear in UI
            __( 'Random Posts (Small 2)', 'twentysixteen-child' ),

            # Widget description
            array(
                'classname'   => 'widget_twentysixteenchild_randomposts_small_two',
                'description' => __( 'Small Random Posts widget without featured images.', 'twentysixteen-child' ),
            )
        );
    }

    public function widget($args, $instance) {
        $title      = isset($instance['title']) ? $instance['title'] : '';
        $postnumber = isset($instance['postnumber']) ? $instance['postnumber'] : '';
        $category   = isset($instance['category']) ? apply_filters( 'widget_title', $instance['category']) : '';
        $tag        = isset($instance['tag']) ? $instance['tag'] : '';
        $except     = isset($instance['except']) ? $instance['except'] : '';

        $publisher  = isset($instance['publisher']) ? $instance['publisher'] : '';
        $location   = isset($instance['location']) ? $instance['location'] : '';
        $person     = isset($instance['person']) ? $instance['person'] : '';
        $prize      = isset($instance['prize']) ? $instance['prize'] : '';

        echo $args['before_widget'];

        if( ! empty( $title ) )
            echo '<div class="widget-title-wrap"><h3 class="widget-title"><span>'. esc_html($title) .'</span></h3></div>';

        ## The Query

        # Add every existing post types
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

        # Add custom taxonomy to WP_Query
        $tax_query = array();

        if( $publisher ){
            $publisher = str_replace( ' ', '', $publisher );
            $pu_array  = explode( ',', $publisher );

            $publisher_array = array(
                'taxonomy' => 'publisher',
                'field'    => 'slug',
                'terms'    => $pu_array,
            );

            array_push( $tax_query, $publisher_array );
        }

        if( $location ){
            $location = str_replace( ' ', '', $location );
            $lo_array = explode( ',', $location );

            $location_array = array(
                'taxonomy' => 'location',
                'field'    => 'slug',
                'terms'    => $lo_array,
            );

            array_push( $tax_query, $location_array );
        }

        if( $person ){
            $person   = str_replace( ' ', '', $person );
            $pe_array = explode( ',', $person );

            $person_array = array(
                'taxonomy' => 'person',
                'field'    => 'slug',
                'terms'    => $pe_array,
            );

            array_push( $tax_query, $person_array );
        }

        if( $prize ){
            $prize    = str_replace( ' ', '', $prize );
            $pr_array = explode( ',', $prize );

            $prize_array = array(
                'taxonomy' => 'prize',
                'field'    => 'slug',
                'terms'    => $pr_array,
            );

            array_push( $tax_query, $prize_array );
        }

        # And compose
        $latest_posts = false;

        if( $tax_query ){
            if( $except != '' ){
                $latest_posts = new WP_Query( array(
                    'post_status'         => 'publish',
                    'post_type'           => $post_types,
                    'posts_per_page'      => $except,
                    'category_name'       => $category,
                    'tag'                 => $tag,
                    'tax_query'           => $tax_query,
                    'fields'              => 'ids',
                    'ignore_sticky_posts' => 1
                ));
            }

            # If $latest_posts does not exist
            if( !$latest_posts ){
                $smalltwo_query = new WP_Query(array (
                    'post_status'         => 'publish',
                    'post_type'           => $post_types,
                    'posts_per_page'      => $postnumber,
                    'category_name'       => $category,
                    'tag'                 => $tag,
                    'tax_query'           => $tax_query,
                    'orderby'             => 'rand',
                    'ignore_sticky_posts' => 1
                ));

            # If $latest_posts exists
            } else {
                $smalltwo_query = new WP_Query(array (
                    'post_status'         => 'publish',
                    'post_type'           => $post_types,
                    'posts_per_page'      => $postnumber,
                    'category_name'       => $category,
                    'tag'                 => $tag,
                    'tax_query'           => $tax_query,
                    'post__not_in'        => $latest_posts->posts,
                    'orderby'             => 'rand',
                    'ignore_sticky_posts' => 1
                ));
            }
        }
        else {
            if( $except != '' ){
                $latest_posts = new WP_Query( array(
                    'post_status'         => 'publish',
                    'post_type'           => $post_types,
                    'posts_per_page'      => $except,
                    'category_name'       => $category,
                    'tag'                 => $tag,
                    'fields'              => 'ids',
                    'ignore_sticky_posts' => 1
                ));
            }

            # If $latest_posts does not exist
            if( !$latest_posts ){
                $smalltwo_query = new WP_Query(array (
                    'post_status'         => 'publish',
                    'post_type'           => $post_types,
                    'posts_per_page'      => $postnumber,
                    'category_name'       => $category,
                    'tag'                 => $tag,
                    'orderby'             => 'rand',
                    'ignore_sticky_posts' => 1
                ));

            # If $latest_posts exists
            } else {
                $smalltwo_query = new WP_Query(array (
                    'post_status'         => 'publish',
                    'post_type'           => $post_types,
                    'posts_per_page'      => $postnumber,
                    'category_name'       => $category,
                    'tag'                 => $tag,
                    'post__not_in'        => $latest_posts->posts,
                    'orderby'             => 'rand',
                    'ignore_sticky_posts' => 1
                ));
            }
        }

        # The Loop
        if( $smalltwo_query->have_posts() ) : ?>

            <?php while( $smalltwo_query->have_posts() ) : $smalltwo_query->the_post() ?>
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
        $instance['title']      = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category']   = $new_instance['category'];
        $instance['tag']        = $new_instance['tag'];
        $instance['except']     = $new_instance['except'];

        $instance['publisher']  = $new_instance['publisher'];
        $instance['location']   = $new_instance['location'];
        $instance['person']     = $new_instance['person'];
        $instance['prize']      = $new_instance['prize'];

        return $new_instance;
    }

    function form($instance) {
        $title      = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category   = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag        = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';
        $except     = isset( $instance['except'] ) ? esc_attr( $instance['except'] ) : '';

        $publisher  = isset( $instance['publisher'] ) ? esc_attr( $instance['publisher'] ) : '';
        $location   = isset( $instance['location'] ) ? esc_attr( $instance['location'] ) : '';
        $person     = isset( $instance['person'] ) ? esc_attr( $instance['person'] ) : '';
        $prize      = isset( $instance['prize'] ) ? esc_attr( $instance['prize'] ) : '';

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'postnumber' ); ?>"><?php _e( 'Number of posts to show:', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'postnumber' ); ?>" value="<?php echo esc_attr($postnumber); ?>" class="widefat" id="<?php echo $this->get_field_id( 'postnumber' ); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'except' ); ?>"><?php _e( 'Number of posts to exclude (optional):', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'except' ); ?>" value="<?php echo esc_attr( $except ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'except' ); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category slug (optional):', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'category' ); ?>" value="<?php echo esc_attr($category); ?>" class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php _e( 'Tag slug (optional):', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'tag' ); ?>" value="<?php echo esc_attr($tag); ?>" class="widefat" id="<?php echo $this->get_field_id( 'tag' ); ?>" />
	</p>

        <?php

        if( taxonomy_exists( 'publisher' )) {

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'publisher' ); ?>"><?php _e( 'Publisher slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'publisher' ); ?>" value="<?php echo esc_attr( $publisher ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'publisher' ); ?>" />
	</p>
        <?php

        }

        if( taxonomy_exists( 'location' )) {

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'location' ); ?>"><?php _e( 'Location slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'location' ); ?>" value="<?php echo esc_attr( $location ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'location' ); ?>" />
	</p>
        <?php

        }

        if( taxonomy_exists( 'person' )) {

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'person' ); ?>"><?php _e( 'Person slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'person' ); ?>" value="<?php echo esc_attr( $person ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'person' ); ?>" />
	</p>
        <?php

        }

        if( taxonomy_exists( 'prize' )) {

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'prize' ) ; ?>"><?php _e( 'Prize slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'prize' ) ; ?>" value="<?php echo esc_attr( $prize ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'prize' ) ; ?>" />
	</p>
	<?php

        }

    }
}

# Register and load the widget
function twentysixteenchild_randomposts_small_two_register() {
    register_widget( 'twentysixteenchild_randomposts_small_two' );
}

add_action( 'widgets_init', 'twentysixteenchild_randomposts_small_two_register' );

?>
