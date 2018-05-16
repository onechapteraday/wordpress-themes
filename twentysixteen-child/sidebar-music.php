<?php
/**
 * The template for the sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen Child 1.0
 */
?>

<?php if ( is_active_sidebar( 'music' )  ) : ?>
	<aside id="secondary" class="sidebar sidebar-music widget-area" role="complementary">
		<?php dynamic_sidebar( 'music' ); ?>
	</aside><!-- .sidebar .widget-area -->
<?php endif; ?>

