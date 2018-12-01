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
                            <?php twentysixteen_post_thumbnail( 'twentysixteenchild-medium-portrait', true ); ?>
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

?>