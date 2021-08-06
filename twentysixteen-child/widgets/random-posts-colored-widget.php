<?php

/**
 * Add Random posts (Colored) widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_randomposts_color extends WP_Widget {

    public function __construct() {
        parent::__construct(
            # Base ID of your widget
            'twentysixteenchild_randomposts_color',

            # Widget name will appear in UI
            __( 'Random Posts (Background)', 'twentysixteen-child' ),

            # Widget description
            array(
                'classname'   => 'widget_twentysixteenchild_randomposts_color',
                'description' => __( 'Medium-sized Random Posts with a background color.', 'twentysixteen-child' ),
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
        $selection  = isset($instance['selection']) ? $instance['selection'] : '';

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

        # And compose

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

                $color_query = new WP_Query(array (
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

            } else {

                $color_query = new WP_Query(array (
                    'post_status'         => 'publish',
                    'post_type'           => $post_types,
                    'posts_per_page'      => $postnumber,
                    'category_name'       => $category,
                    'tag'                 => $tag,
                    'tax_query'           => $tax_query,
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

                $color_query = new WP_Query(array (
                    'post_status'         => 'publish',
                    'post_type'           => $post_types,
                    'posts_per_page'      => $postnumber,
                    'category_name'       => $category,
                    'tag'                 => $tag,
                    'post__not_in'        => $latest_posts->posts,
                    'orderby'             => 'rand',
                    'ignore_sticky_posts' => 1
                ));

            } else {

                $color_query = new WP_Query(array (
                    'post_status'         => 'publish',
                    'post_type'           => $post_types,
                    'posts_per_page'      => $postnumber,
                    'category_name'       => $category,
                    'tag'                 => $tag,
                    'orderby'             => 'rand',
                    'ignore_sticky_posts' => 1
                ));
            }
        }

        ?>

        <div class="bg-wrap cf">
            <?php
            # The Loop
            if($color_query->have_posts()) : ?>

                <?php while($color_query->have_posts()) : $color_query->the_post() ?>
                <article class="rp-color cf">
                    <?php if ( '' != get_the_post_thumbnail() ) : ?>
                        <div class="entry-thumb">
                            <?php twentysixteen_post_thumbnail( 'twentysixteenchild-medium-portrait', true ); ?>
                        </div><!-- end .entry-thumb -->
                    <?php endif; ?>

                    <header class="entry-header">
                        <div class="entry-cats">
                            <?php the_category( ', ' ); ?>
                        </div><!-- end .entry-cats -->

                        <h3 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentysixteen-child' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a></h3>
                    </header>

                    <div class="story">
                        <p class="summary"><?php echo twentysixteenchild_excerpt(30); ?></p>

                        <footer class="entry-footer">
                            <div class="entry-date"><a href="<?php the_permalink(); ?>" class="entry-date"><?php echo get_the_date(); ?></a></div>

                            <?php if ( comments_open() ) : ?>
                                <div class="entry-comments">
				    <?php
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
                                    ?>
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
        $instance['title']      = $new_instance['title'];
        $instance['postnumber'] = $new_instance['postnumber'];
        $instance['category']   = $new_instance['category'];
        $instance['tag']        = $new_instance['tag'];
        $instance['except']     = $new_instance['except'];

        $instance['publisher']  = $new_instance['publisher'];
        $instance['location']   = $new_instance['location'];
        $instance['person']     = $new_instance['person'];
        $instance['prize']      = $new_instance['prize'];
        $instance['selection']  = $new_instance['selection'];

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
        $selection  = isset( $instance['selection'] ) ? esc_attr( $instance['selection'] ) : '';

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'postnumber' ); ?>"><?php _e( 'Number of posts to show:', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'postnumber' ); ?>" value="<?php echo esc_attr( $postnumber ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'postnumber' ); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'except' ); ?>"><?php _e( 'Number of posts to exclude (optional):', 'twentysixteen-child' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'except' ); ?>" value="<?php echo esc_attr( $except ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'except' ); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'category' ); ?>" value="<?php echo esc_attr( $category ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>" />
	</p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php _e( 'Tag slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'tag' ); ?>" value="<?php echo esc_attr( $tag ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'tag' ); ?>" />
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
	    <label for="<?php echo $this->get_field_id( 'prize' ); ?>"><?php _e( 'Prize slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'prize' ); ?>" value="<?php echo esc_attr( $prize ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'prize' ); ?>" />
	</p>
	<?php

        }

        if( taxonomy_exists( 'selection' )) {

        ?>
	<p>
	    <label for="<?php echo $this->get_field_id( 'selection' ) ; ?>"><?php _e( 'Selection slug (optional):', 'twentysixteen-child' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'selection' ) ; ?>" value="<?php echo esc_attr( $selection ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'selection' ) ; ?>" />
	</p>
	<?php

        }

    }
}

# Register and load the widget
function twentysixteenchild_randomposts_color_register() {
    register_widget( 'twentysixteenchild_randomposts_color' );
}

add_action( 'widgets_init', 'twentysixteenchild_randomposts_color_register' );

?>
