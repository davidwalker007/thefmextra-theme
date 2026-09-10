<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<div class="container">
	<div class="narrow page-content">
		<?php while (have_posts()) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</div>
</div>

<?php get_footer(); ?>
