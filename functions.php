<?php
if (!defined('ABSPATH')) exit;

function np_setup() {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('automatic-feed-links');
	add_theme_support('html5', array('search-form', 'gallery', 'caption', 'comment-list', 'comment-form'));
	add_theme_support('custom-logo', array(
		'height'      => 80,
		'width'       => 300,
		'flex-height' => true,
		'flex-width'  => true,
	));
	register_nav_menus(array(
		'primary' => __('Primary Menu', 'wp-newspaper-theme'),
	));
	add_post_type_support('page', 'excerpt');
}
add_action('after_setup_theme', 'np_setup');

/**
 * Social links, set under Appearance → Customize → Social Links.
 * Only Facebook so far — add more the same way if a project needs them.
 */
function np_customize_register($wp_customize) {
	$wp_customize->add_section('np_social', array(
		'title'    => __('Social Links', 'wp-newspaper-theme'),
		'priority' => 35,
	));
	$wp_customize->add_setting('facebook_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('facebook_url', array(
		'label'   => __('Facebook Page URL', 'wp-newspaper-theme'),
		'section' => 'np_social',
		'type'    => 'url',
	));
}
add_action('customize_register', 'np_customize_register');

function np_widgets_init() {
	register_sidebar(array(
		'name'          => __('Sidebar', 'wp-newspaper-theme'),
		'id'            => 'sidebar-1',
		'description'   => __('Ads, social widgets, and links shown on the front page, archives, and single articles.', 'wp-newspaper-theme'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
}
add_action('widgets_init', 'np_widgets_init');

function np_assets() {
	// Swap/remove the Google Fonts line if the project's design tokens use system fonts instead.
	wp_enqueue_style('np-google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Lato:wght@300;400;700&display=swap', array(), null);

	// filemtime() cache-busting: without this, browsers will silently keep serving a stale
	// style.css/nav.js after every edit.
	wp_enqueue_style('np-style', get_stylesheet_uri(), array(), filemtime(get_stylesheet_directory() . '/style.css'));
	wp_enqueue_script('np-nav', get_template_directory_uri() . '/js/nav.js', array(), filemtime(get_template_directory() . '/js/nav.js'), true);
}
add_action('wp_enqueue_scripts', 'np_assets');

/**
 * Fallback nav if no menu is assigned to the 'primary' location yet.
 * Edit the links below to match the project's actual top-level pages.
 */
function np_fallback_menu() {
	echo '<ul>';
	echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
	echo '<li><a href="' . esc_url(home_url('/about/')) . '">About</a></li>';
	echo '</ul>';
}

/**
 * "Read more" excerpt marker for article-list views, instead of the default […].
 */
function np_excerpt_more($more) {
	return is_admin() ? $more : '&hellip;';
}
add_filter('excerpt_more', 'np_excerpt_more');

/**
 * Drop WordPress's default "Category: " / "Tag: " / "Archives: " prefix on
 * archive.php's <h1> — just the term/date name reads better as a page title.
 */
function np_archive_title($title) {
	if (is_category()) {
		$title = single_cat_title('', false);
	} elseif (is_tag()) {
		$title = single_tag_title('', false);
	} elseif (is_author()) {
		$title = get_the_author();
	}
	return $title;
}
add_filter('get_the_archive_title', 'np_archive_title');

function np_excerpt_length($length) {
	return 32;
}
add_filter('excerpt_length', 'np_excerpt_length');

/**
 * These print-era section categories (Front Page, Home, etc.) were how the
 * old site's layout was organized, not something a reader needs to see as a
 * "category" on every article — every post has one, so showing it as a tag
 * added no information. Real categories (Obituaries, Sports, etc.) still
 * display normally.
 */
function np_is_layout_category($slug) {
	return in_array($slug, array('front-page', 'front-page-archive', 'front-page-lead', 'home'), true);
}

/**
 * Byline + date + category tag, used by front-page.php, archive.php, and single.php.
 * Keeps that markup in one place instead of repeating it per template.
 */
function np_article_meta() {
	$categories = get_the_category();
	$real_category = null;
	foreach ($categories as $cat) {
		if (!np_is_layout_category($cat->slug)) {
			$real_category = $cat;
			break;
		}
	}
	echo '<div class="article-meta">';
	if ($real_category) {
		echo '<a class="category-tag" href="' . esc_url(get_category_link($real_category->term_id)) . '">' . esc_html($real_category->name) . '</a>';
	}
	echo '<span class="article-date">' . esc_html(get_the_date()) . '</span>';
	echo '</div>';
}

/**
 * Decades of migrated content have no real per-post author (everything's
 * attributed to a generic "admin" account from the import), but recent
 * articles do carry a real byline as literal text in the body — a bolded
 * 2-4 word name standing alone as the first real line, right after any
 * leading image caption/gallery. Older archive posts don't reliably follow
 * this pattern, so this only ever fires on content that matches tightly;
 * anything else is left alone (no byline shown, nothing stripped).
 */
function np_extract_byline_and_strip($content) {
	// \x{00A0} (non-breaking space) shows up constantly in this migrated
	// content — e.g. "Nancy Edmonds Hanson\xc2\xa0</strong>" — and plain \s
	// doesn't match it, so the /u modifier + explicit \x{00A0} is needed
	// throughout or real bylines silently fail to match.
	$stripped = preg_replace('/^[\s\x{00A0}]*(\[caption[^\]]*\].*?\[\/caption\][\s\x{00A0}]*|\[gallery[^\]]*\][\s\x{00A0}]*)+/isu', '', $content);
	if (preg_match('/^[\s\x{00A0}]*<strong>[\s\x{00A0}]*([^<]{3,50}?)[\s\x{00A0}]*<\/strong>[\s\x{00A0}]*/iu', $stripped, $m)) {
		$name = trim(preg_replace('/\x{00A0}/u', ' ', $m[1]));
		// Looks like "Firstname Lastname" (2-4 capitalized words) — not a
		// generic bolded phrase like "Free community meals".
		if (preg_match('/^[A-Z][A-Za-z.\'-]*(?:\s+[A-Z][A-Za-z.\'-]*){1,3}$/u', $name)) {
			$cleaned = str_replace($m[0], '', $content);
			return array($name, $cleaned);
		}
	}
	return array(null, $content);
}

/**
 * Strip the byline paragraph from the rendered single-article body. Bylines
 * aren't displayed anywhere on this site (too inconsistent across decades of
 * migrated content to show reliably) — this just keeps the stray name out of
 * the visible article text.
 */
function np_strip_byline_from_content($content) {
	if (!is_singular('post')) return $content;
	list($byline, $cleaned) = np_extract_byline_and_strip($content);
	return $byline ? $cleaned : $content;
}
add_filter('the_content', 'np_strip_byline_from_content', 5);

/**
 * Without this, the byline paragraph (and often an image caption right
 * before it) gets swept into WordPress's auto-generated excerpt with no
 * separating punctuation — e.g. "Nancy Edmonds Hanson  Greater Moorhead
 * Days draws to a close...". Only replaces the excerpt when a byline was
 * actually detected; otherwise WordPress's default excerpt is untouched.
 */
function np_fix_byline_excerpt($excerpt, $post = null) {
	$post = get_post($post);
	if (!$post || $post->post_excerpt) return $excerpt;
	list($byline, $cleaned) = np_extract_byline_and_strip($post->post_content);
	if (!$byline) return $excerpt;
	$text = strip_shortcodes($cleaned);
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = wp_strip_all_tags($text);
	return wp_trim_words($text, apply_filters('excerpt_length', 32), apply_filters('excerpt_more', ' &hellip;'));
}
add_filter('get_the_excerpt', 'np_fix_byline_excerpt', 5, 2);

require get_template_directory() . '/inc/pagination.php';
require get_template_directory() . '/inc/template-tags.php';
