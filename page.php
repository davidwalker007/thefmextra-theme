<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
	<?php np_page_title_bar(get_the_title()); ?>
	<div class="container">
		<div class="narrow page-content">
			<article <?php post_class(); ?>>
				<?php the_content(); ?>
			</article>
		</div>
	</div>
<?php endwhile; ?>

<?php get_footer(); ?>
