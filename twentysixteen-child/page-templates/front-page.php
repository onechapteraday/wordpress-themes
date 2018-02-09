<?php
/**
 * Template Name: Front Page
 *
 * Description: A page template for a Custom Front Page
 *
 * @package Twenty Sixteen Child
 * @since Twenty Sixteen Child 1.0
 */

get_header(); ?>

<div id="content" class="site-content no-sidebar" role="main">

<?php get_sidebar( 'fullwidth-top' ); ?>
<?php get_sidebar( 'content-one' ); ?>
<?php get_sidebar( 'front-one' ); ?>
<?php get_sidebar( 'fullwidth-center' ); ?>
<?php get_sidebar( 'content-two' ); ?>
<?php get_sidebar( 'front-two' ); ?>
<?php get_sidebar( 'fullwidth-bottom' ); ?>

</div><!-- end #primary -->

<?php get_footer(); ?>

