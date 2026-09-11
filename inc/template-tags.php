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
 *
 * Takes the post explicitly (defaulting to the global $post, same as
 * get_post(null)) rather than using no-args get_the_title()/get_the_content() —
 * those two template tags default their $post parameter differently (null vs
 * 0), so outside a real have_posts()/the_post() loop — e.g. front-page.php's
 * manual setup_postdata() calls for the hero/secondary/Local News cards —
 * get_the_title() silently returns an empty string while get_the_content()
 * still works. That mismatch produced a real bug: an attachment's alt text
 * came out as whatever post's title happened to still be in $post_title from
 * PHP reusing the last real title string, not the actual current post.
 */
function np_list_thumbnail_html($post = null) {
	$post = get_post($post);
	if (!$post) return '';
	if (has_post_thumbnail($post)) {
		return get_the_post_thumbnail($post, 'medium_large');
	}
	$content = $post->post_content;
	if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
		return '<img src="' . esc_url($matches[1]) . '" alt="' . esc_attr($post->post_title) . '" loading="lazy">';
	}
	// Some posts' only image is a [gallery ids="1,2,3"] shortcode — that's
	// literal shortcode text in the raw content, not an <img> tag, so it's
	// invisible to the check above until the shortcode actually renders.
	// Grab the first attached image directly instead.
	if (preg_match('/\[gallery[^\]]*\bids=["\'](\d+)/i', $content, $matches)) {
		$image = wp_get_attachment_image($matches[1], 'medium_large', false, array('alt' => $post->post_title, 'loading' => 'lazy'));
		if ($image) return $image;
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
