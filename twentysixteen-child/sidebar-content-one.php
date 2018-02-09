<?php
/**
 * The Content One Widget Area on the Custom Front Page Template
 *
 * @package Twenty Sixteen Child
 * @since Twenty Sixteen Child 1.0
 */

if ( ! is_active_sidebar( 'front-content-1' ) ) {
	return;
}
?>
<div id="front-content-one" class="front-content widget-area">
	<?php dynamic_sidebar( 'front-content-1' ); ?>
</div><!-- #front-content-one -->
