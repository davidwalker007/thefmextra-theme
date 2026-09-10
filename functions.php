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
 * Byline + date + category tag, used by front-page.php, archive.php, and single.php.
 * Keeps that markup in one place instead of repeating it per template.
 */
function np_article_meta() {
	$categories = get_the_category();
	echo '<div class="article-meta">';
	if (!empty($categories)) {
		echo '<a class="category-tag" href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
	}
	echo '<span class="article-date">' . esc_html(get_the_date()) . '</span>';
	echo '<span class="article-byline">' . esc_html(get_the_author()) . '</span>';
	echo '</div>';
}

require get_template_directory() . '/inc/pagination.php';
require get_template_directory() . '/inc/template-tags.php';
