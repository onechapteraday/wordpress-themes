<?php
/**
 * The Bottom FullWidth Widget Area on the Custom Front Page Template
 *
 * @package Twenty Sixteen Child
 * @since Twenty Sixteen Child 1.0
 */

if ( ! is_active_sidebar( 'front-fullwidth-bottom' ) ) {
	return;
}
?>
<div id="front-fullwidth-bottom" class="front-fullwidth widget-area">
	<?php dynamic_sidebar( 'front-fullwidth-bottom' ); ?>
</div><!-- #front-fullwidth-bottom -->
