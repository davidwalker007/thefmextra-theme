<?php
if (!defined('ABSPATH')) exit;

/**
 * One article-list row: thumbnail, title, byline/date/category, excerpt.
 * Shared by front-page.php, archive.php, category.php, and search.php so the
 * list markup only lives in one place.
 */
function np_article_card() {
	?>
	<article <?php post_class('article-card'); ?>>
		<?php if (has_post_thumbnail()) : ?>
			<a class="article-card-thumb" href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail('medium_large'); ?>
			</a>
		<?php endif; ?>
		<div class="article-card-body">
			<h2 class="article-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php np_article_meta(); ?>
			<div class="article-card-excerpt"><?php the_excerpt(); ?></div>
			<a class="read-more" href="<?php the_permalink(); ?>"><?php esc_html_e('Read more', 'wp-newspaper-theme'); ?></a>
		</div>
	</article>
	<?php
}
