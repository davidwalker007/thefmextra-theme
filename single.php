<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<div class="container content-with-sidebar">
	<div class="content-main">
		<?php while (have_posts()) : the_post(); ?>
			<article <?php post_class('single-article'); ?>>
				<header class="single-article-header">
					<h1><?php the_title(); ?></h1>
					<?php np_article_meta(); ?>
				</header>

				<?php if (has_post_thumbnail()) : ?>
					<div class="single-article-thumb"><?php the_post_thumbnail('large'); ?></div>
				<?php endif; ?>

				<div class="single-article-content">
					<?php the_content(); ?>
				</div>
			</article>

			<nav class="post-nav" aria-label="<?php esc_attr_e('More articles', 'wp-newspaper-theme'); ?>">
				<div class="post-nav-prev"><?php previous_post_link('%link', '&larr; %title'); ?></div>
				<div class="post-nav-next"><?php next_post_link('%link', '%title &rarr;'); ?></div>
			</nav>

			<?php if (comments_open() || get_comments_number()) : comments_template(); endif; ?>
		<?php endwhile; ?>
	</div>
	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
