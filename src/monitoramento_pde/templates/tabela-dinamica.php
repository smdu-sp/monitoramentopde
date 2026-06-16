<?php
/**
 * Template Name: Tabela Dinâmica
 */
?>

<?php 
$page_slug = get_post_field( 'post_name', get_post() );
$nome_tabela = str_replace('-', '_', $page_slug);
?>

<script>
    const NOME_TABELA = "<?php echo esc_js($nome_tabela); ?>";
</script>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'tabela-dinamica'); ?>
<?php endwhile; ?>
