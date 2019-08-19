<?php

/**
 * Add Quote widget
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @since Twenty Sixteen Child 1.0
 */

class twentysixteenchild_quote extends WP_Widget {

    public function __construct() {
        parent::__construct(
            # Base ID of your widget
            'twentysixteenchild_quote',

            # Widget name will appear in UI
            __( 'Quote', 'twentysixteen-child' ),

            # Widget description
            array(
                'classname'   => 'widget_twentysixteenchild_quote',
                'description' => __( 'A big quote or text slogan.', 'twentysixteen-child' ),
            )
        );
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

?>
