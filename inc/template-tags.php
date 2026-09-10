<?php
if (!defined('ABSPATH')) exit;

/**
 * Full-width title band for page.php, archive.php, and search.php — sits
 * flush against the nav bar, background bleeds edge-to-edge, text stays
 * aligned with the rest of the page via the inner .container. $title_html
 * is trusted, already-escaped output from a WP title function (get_the_title(),
 * get_the_archive_title(), etc.), not raw user input.
 */
function np_page_title_bar($title_html) {
	echo '<header class="page-title-bar"><div class="container"><h1>' . $title_html . '</h1></div></header>';
}

/**
 * Facebook's "Page Plugin" iframe embed — the modern, officially-supported
 * replacement for the old deprecated "Like Box" widget. Deliberately just an
 * iframe, not the full Facebook JS SDK: lighter, no third-party script
 * running elsewhere on the page, matches this theme's no-dependency approach.
 * Trade-off: without the SDK, the embed can't truly reflow responsively, so
 * $width is a fixed pixel size chosen to comfortably fit the sidebar column
 * and most mobile viewports once it stacks full-width.
 */
function np_facebook_page_embed($url, $width = 300, $height = 460) {
	$src = 'https://www.facebook.com/plugins/page.php?' . http_build_query(array(
		'href'         => $url,
		'tabs'         => 'timeline',
		'width'        => $width,
		'height'       => $height,
		'small_header' => 'false',
		'hide_cover'   => 'false',
		'show_facepile' => 'true',
	));
	return '<iframe src="' . esc_url($src) . '" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" style="border:none;overflow:hidden;max-width:100%;" scrolling="no" frameborder="0" allowfullscreen="true" loading="lazy" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" title="' . esc_attr__('Facebook Page', 'wp-newspaper-theme') . '"></iframe>';
}

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
