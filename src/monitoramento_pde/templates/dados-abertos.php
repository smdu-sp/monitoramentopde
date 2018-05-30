<?php
/**
 * Template Name: Dados abertos
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'dados-abertos'); ?>
<?php endwhile; ?>
