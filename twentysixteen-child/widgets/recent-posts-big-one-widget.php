<?php

/**
 * Add Recent posts (Big 1) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_recentposts_big_one extends WP_Widget {

    public function __construct() {
        parent::__construct(
            # Base ID of your widget
            'twentysixteenchild_recentposts_big_one',

            # Widget name will appear in UI
            __( 'Recent Posts (Big 1)', 'twentysixteen-child' ),

            # Widget description
            array(
                'classname'   => 'widget_twentysixteenchild_recentposts_big_one',
                'description' => __( 'Big Recent Posts with an overlay excerpt text. Featured images must have a minimum size of 1200x800 pixel.', 'twentysixteen-child' ),
            )
        );
    }

    public function widget( $args, $instance ){
        $title      = isset( $instance['title'] ) ? $instance['title'] : '';
        $postnumber = isset( $instance['postnumber'] ) ? $instance['postnumber'] : '';
        $category   = isset( $instance['category'] ) ? apply_filters( 'widget_title', $instance['category'] ) : '';
        $tag        = isset( $instance['tag'] ) ? $instance['tag'] : '';
        $except     = isset( $instance['except'] ) ? $instance['except'] : '';
        $excluded   = isset( $instance['excluded'] ) ? $instance['excluded'] : '';
        $random     = isset( $instance['random'] ) ? $instance['random'] : '';
        $between    = isset( $instance['between'] ) ? $instance['between'] : '';

        $publisher  = isset( $instance['publisher'] ) ? $instance['publisher'] : '';
        $location   = isset( $instance['location'] ) ? $instance['location'] : '';
        $person     = isset( $instance['person'] ) ? $instance['person'] : '';
        $prize      = isset( $instance['prize'] ) ? $instance['prize'] : '';
        $selection  = isset( $instance['selection'] ) ? $instance['selection'] : '';

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

        $posts_excluded = array();
        $tax_query = array();
        $tax__used = false;

        if( $publisher ){
            $publisher = str_replace( ' ', '', $publisher );
            $pu_array  = explode( ',', $publisher );

            $publisher_array = array(
                'taxonomy' => 'publisher',
                'field'    => 'slug',
                'terms'    => $pu_array,
            );

            array_push( $tax_query, $publisher_array );
            $tax__used = true;
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
            $tax__used = true;
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
            $tax__used = true;
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
            $tax__used = true;
        }

        if( $selection ){
            $selection = str_replace( ' ', '', $selection );
            $sel_array  = explode( ',', $selection );

            $selection_array = array(
                'taxonomy' => 'selection',
                'field'    => 'slug',
                'terms'    => $sel_array,
            );

            array_push( $tax_query, $selection_array );
            $tax__used  = true;
        }

        # Compose WP_Query

        $query_args = array (
            'post_status'         => 'publish',
            'post_type'           => $post_types,
            'posts_per_page'      => $postnumber,
            'ignore_sticky_posts' => 1
        );

        # Add category

        if( $category ){
            $query_args['category_name'] = $category;
        }

        # Add tag

        if( $tag ){
            $query_args['tag'] = $tag;
        }

        # Add random

        if( $random == '1' ){
            $query_args['orderby'] = 'rand';
        }

        # Add custom taxonomies

        if( $tax__used ){
            $query_args['tax_query'] = $tax_query;
        }

        # Add between dates

        if( $between ){
            $between_dates = explode( ';', $between );

            $meta_query = array(
                'key'      => 'date_release',
                'value'    => $between_dates,
                'compare'  => 'BETWEEN',
                'type'     => 'DATE',
            );

            $query_args['meta_query'] = array( $meta_query );
        }

        # Add excluded posts

        if( $excluded ){
            $posts_excluded = explode( ',', $excluded );
            $query_args['post__not_in'] = $posts_excluded;
        }

        # Add number of posts to exclude

        if( $except != '' ){
            $latest_args = array(
                'post_status'         => 'publish',
                'post_type'           => $post_types,
                'posts_per_page'      => $except,
                'post__not_in'        => $posts_excluded,
                'category_name'       => $category,
                'tag'                 => $tag,
                'fields'              => 'ids',
                'ignore_sticky_posts' => 1
            );

            if( $tax__used ){
                $latest_args['tax_query'] = $tax_query;
            }

            $latest_posts = new WP_Query( $latest_args );
            $all_excluded = array_merge( $posts_excluded, $latest_posts->posts );

            $query_args['post__not_in'] = $all_excluded;
        }

        # Launch WP_Query with all arguments

        $bigone_query = new WP_Query( $query_args );

        ## The Loop

        if( $bigone_query->have_posts() ) : ?>

            <?php while($bigone_query->have_posts()) : $bigone_query->the_post() ?>
            <article class="rp-big-one cf">
                <div class="rp-big-one-content">

                    <?php if ( '' != get_the_post_thumbnail() ) : ?>
                        <div class="entry-thumb">
                            <?php twentysixteen_post_thumbnail( 'twentysixteenchild-fullwidth', true ); ?>
                        </div><!-- end .entry-thumb -->
                    <?php endif; ?>

                    <div class="story">
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a></h2>

			<p class="summary"><?php echo twentysixteenchild_excerpt(32); ?></p>
			<div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>
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

    function update( $new_instance, $old_instance ){
        $instance['title']      = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category']   = $new_instance['category'];
        $instance['tag']        = $new_instance['tag'];
        $instance['except']     = $new_instance['except'];
        $instance['excluded']   = $new_instance['excluded'];
        $instance['random']     = $new_instance['random'];
        $instance['between']    = $new_instance['between'];

        $instance['publisher']  = $new_instance['publisher'];
        $instance['location']   = $new_instance['location'];
        $instance['person']     = $new_instance['person'];
        $instance['prize']      = $new_instance['prize'];
        $instance['selection']  = $new_instance['selection'];

        return $new_instance;
    }

    function form( $instance ){
        $title      = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $postnumber = isset( $instance['postnumber'] ) ? esc_attr( $instance['postnumber'] ) : '';
        $category   = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
        $tag        = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';
        $except     = isset( $instance['except'] ) ? esc_attr( $instance['except'] ) : '';
        $excluded   = isset( $instance['excluded'] ) ? esc_attr( $instance['excluded'] ) : '';
        $random     = isset( $instance['random'] ) ? esc_attr( $instance['random'] ) : '';
        $between    = isset( $instance['between'] ) ? esc_attr( $instance['between'] ) : '';

        $publisher  = isset( $instance['publisher'] ) ? esc_attr( $instance['publisher'] ) : '';
        $location   = isset( $instance['location'] ) ? esc_attr( $instance['location'] ) : '';
        $person     = isset( $instance['person'] ) ? esc_attr( $instance['person'] ) : '';
        $prize      = isset( $instance['prize'] ) ? esc_attr( $instance['prize'] ) : '';
        $selection  = isset( $instance['selection'] ) ? esc_attr( $instance['selection'] ) : '';

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
	    <label for="<?php echo $this->get_field_id( 'excluded' ); ?>"><?php _e( 'Posts to exclude (optional):', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'excluded' ); ?>" value="<?php echo esc_attr( $excluded ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'excluded' ); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'random' ); ?>"><?php _e( 'Display randomly posts (optional):', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'random' ); ?>" value="<?php echo esc_attr( $random ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'random' ); ?>" />
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

        if( taxonomy_exists( 'publisher' ) ){

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'publisher' ); ?>"><?php _e( 'Publisher slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'publisher' ); ?>" value="<?php echo esc_attr( $publisher ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'publisher' ); ?>" />
	</p>
        <?php

        }

        if( taxonomy_exists( 'location' ) ){

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'location' ); ?>"><?php _e( 'Location slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'location' ); ?>" value="<?php echo esc_attr( $location ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'location' ); ?>" />
	</p>
        <?php

        }

        if( taxonomy_exists( 'person' ) ){

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'person' ); ?>"><?php _e( 'Person slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'person' ); ?>" value="<?php echo esc_attr( $person ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'person' ); ?>" />
	</p>
        <?php

        }

        if( taxonomy_exists( 'prize' ) ){

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'prize' ); ?>"><?php _e( 'Prize slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'prize' ); ?>" value="<?php echo esc_attr( $prize ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'prize' ); ?>" />
	</p>
	<?php

        }

        if( taxonomy_exists( 'selection' ) ){

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'selection' ) ; ?>"><?php _e( 'Selection slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'selection' ) ; ?>" value="<?php echo esc_attr( $selection ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'selection' ) ; ?>" />
	</p>
	<?php

        }

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'between' ); ?>"><?php _e( 'Releases from DATE to DATE (optional):', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'between' ); ?>" value="<?php echo esc_attr( $between ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'between' ); ?>" />
	</p>
        <?php

    }
}

# Register and load the widget
function twentysixteenchild_recentposts_big_one_register(){
    register_widget( 'twentysixteenchild_recentposts_big_one' );
}

add_action( 'widgets_init', 'twentysixteenchild_recentposts_big_one_register' );

?>
