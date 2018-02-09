<?php
/**
 * The template for the sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen Child 1.0
 */
?>

<?php if ( is_active_sidebar( 'book' )  ) : ?>
	<aside id="secondary" class="sidebar widget-area" role="complementary">
		<?php dynamic_sidebar( 'book' ); ?>
	</aside><!-- .sidebar .widget-area -->
<?php endif; ?>

