<?php
if (!defined('ABSPATH')) exit;

/**
 * Numbered pagination, shared by front-page.php, archive.php, category.php, and search.php.
 */
function np_pagination() {
	$links = paginate_links(array(
		'prev_text' => __('&larr; Newer', 'wp-newspaper-theme'),
		'next_text' => __('Older &rarr;', 'wp-newspaper-theme'),
		'type'      => 'array',
	));
	if (empty($links)) return;
	echo '<nav class="pagination" aria-label="' . esc_attr__('Posts navigation', 'wp-newspaper-theme') . '"><ul>';
	foreach ($links as $link) {
		echo '<li>' . $link . '</li>';
	}
	echo '</ul></nav>';
}
