<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<?php np_page_title_bar(sprintf(esc_html__('Search results for: %s', 'wp-newspaper-theme'), '<span>' . get_search_query() . '</span>')); ?>

<div class="container content-with-sidebar">
	<div class="content-main">
		<?php if (have_posts()) : ?>
			<div class="article-list">
				<?php while (have_posts()) : the_post(); np_article_card(); endwhile; ?>
			</div>
			<?php np_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e('No results found.', 'wp-newspaper-theme'); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
