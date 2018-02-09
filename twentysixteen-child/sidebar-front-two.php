<?php
/**
 * The Bottom Right Sidebar on the Custom Front Page Template
 *
 * @package Twenty Sixteen Child
 * @since Twenty Sixteen Child 1.0
 */

if ( ! is_active_sidebar( 'front-sidebar-2' ) ) {
	return;
}
?>
<div id="front-sidebar-two" class="front-sidebar widget-area" role="complementary">
	<?php dynamic_sidebar( 'front-sidebar-2' ); ?>
</div><!-- #front-sidebar-two -->
