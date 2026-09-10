<?php
if (!defined('ABSPATH')) exit;

/**
 * List-view thumbnail markup: uses the featured image if one is set, otherwise
 * falls back to the first <img> found in the post content. Most of the site's
 * older posts were never given a featured image but do have a photo in the
 * body — this matches how the old theme displayed a photo per article.
 */
function np_list_thumbnail_html() {
	if (has_post_thumbnail()) {
		return get_the_post_thumbnail(get_the_ID(), 'medium_large');
	}
	if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', get_the_content(), $matches)) {
		return '<img src="' . esc_url($matches[1]) . '" alt="' . esc_attr(get_the_title()) . '" loading="lazy">';
	}
	return '';
}

/**
 * One article-list row: thumbnail, title, byline/date/category, excerpt.
 * Shared by front-page.php, archive.php, category.php, and search.php so the
 * list markup only lives in one place.
 */
function np_article_card() {
	$thumb = np_list_thumbnail_html();
	?>
	<article <?php post_class('article-card'); ?>>
		<?php if ($thumb) : ?>
			<a class="article-card-thumb" href="<?php the_permalink(); ?>"><?php echo $thumb; ?></a>
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
