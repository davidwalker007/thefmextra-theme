<?php if (!defined('ABSPATH')) exit; get_header();

/**
 * The front page previously just listed every post in one long undifferentiated
 * feed — no lead story, no sections, same treatment for a breaking headline as
 * a weekly BBQ column. This pulls a real lead + secondary stories, plus a
 * Local News rail, then leaves everything else in the normal feed below
 * exactly as before. Only runs on page 1 — /page/2/ etc. just show the plain
 * feed, so pagination isn't affected.
 *
 * The lead/secondary slots prefer whatever an editor has manually marked
 * "Homepage Spotlight" (see the meta box added in functions.php), ordered by
 * their chosen position — 1 = hero, 2-5 = secondary, blank sorts last. Any
 * slots an editor hasn't filled fall back to the automatic category pick
 * (Front Page Lead, then Front Page), same as before this feature existed.
 */
$np_featured_ids = array();
$np_hero_id = null;
$np_secondary_ids = array();
$np_news_ids = array();

if (!is_paged()) {
	$manual_query = new WP_Query(array(
		'post_type'      => 'post',
		'posts_per_page' => 5,
		'post_status'    => 'publish',
		'meta_key'       => '_fmx_featured',
		'meta_value'     => '1',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'ignore_sticky_posts' => true,
		'no_found_rows'  => true,
	));
	$manual_posts = $manual_query->posts;
	usort($manual_posts, function ($a, $b) {
		$pos_a = (int) get_post_meta($a->ID, '_fmx_featured_position', true) ?: 999;
		$pos_b = (int) get_post_meta($b->ID, '_fmx_featured_position', true) ?: 999;
		return $pos_a <=> $pos_b;
	});
	$lead_ids = wp_list_pluck($manual_posts, 'ID');

	if (count($lead_ids) < 5) {
		$auto_query = new WP_Query(array(
			'category_name'  => 'front-page-lead',
			'posts_per_page' => 5,
			'post_status'    => 'publish',
			'post__not_in'   => $lead_ids,
			'ignore_sticky_posts' => true,
			'no_found_rows'  => true,
		));
		if (!$auto_query->have_posts()) {
			$auto_query = new WP_Query(array(
				'category_name'  => 'front-page',
				'posts_per_page' => 5,
				'post_status'    => 'publish',
				'post__not_in'   => $lead_ids,
				'ignore_sticky_posts' => true,
				'no_found_rows'  => true,
			));
		}
		$lead_ids = array_slice(array_merge($lead_ids, wp_list_pluck($auto_query->posts, 'ID')), 0, 5);
	}

	$np_hero_id = array_shift($lead_ids);
	$np_secondary_ids = $lead_ids;

	if ($np_hero_id) {
		$news_query = new WP_Query(array(
			'category_name'  => 'news',
			'posts_per_page' => 4,
			'post_status'    => 'publish',
			'post__not_in'   => array_merge(array($np_hero_id), $np_secondary_ids),
			'ignore_sticky_posts' => true,
			'no_found_rows'  => true,
		));
		$np_news_ids = wp_list_pluck($news_query->posts, 'ID');
	}

	$np_featured_ids = array_merge(
		$np_hero_id ? array($np_hero_id) : array(),
		$np_secondary_ids,
		$np_news_ids
	);
}
?>

<?php if ($np_hero_id) : ?>
<div class="container front-lead-section">
	<div class="front-lead-grid">
		<?php
		$hero_post = get_post($np_hero_id);
		setup_postdata($hero_post);
		$hero_thumb = np_list_thumbnail_html($hero_post);
		?>
		<article class="front-hero">
			<?php if ($hero_thumb) : ?>
				<a class="front-hero-thumb" href="<?php echo esc_url(get_permalink($hero_post)); ?>"><?php echo $hero_thumb; ?></a>
			<?php endif; ?>
			<h1 class="front-hero-title"><a href="<?php echo esc_url(get_permalink($hero_post)); ?>"><?php echo esc_html(get_the_title($hero_post)); ?></a></h1>
			<?php np_article_meta(); ?>
			<div class="front-hero-excerpt"><?php echo wp_kses_post(get_the_excerpt($hero_post)); ?></div>
			<a class="read-more" href="<?php echo esc_url(get_permalink($hero_post)); ?>"><?php esc_html_e('Read more', 'wp-newspaper-theme'); ?></a>
		</article>

		<?php if (!empty($np_secondary_ids)) : ?>
		<div class="front-secondary-list">
			<?php foreach ($np_secondary_ids as $sec_id) :
				$sec_post = get_post($sec_id);
				setup_postdata($sec_post);
				$sec_thumb = np_list_thumbnail_html($sec_post);
			?>
				<article class="front-secondary-card">
					<?php if ($sec_thumb) : ?>
						<a class="front-secondary-thumb" href="<?php echo esc_url(get_permalink($sec_post)); ?>"><?php echo $sec_thumb; ?></a>
					<?php endif; ?>
					<div class="front-secondary-body">
						<h3 class="front-secondary-title"><a href="<?php echo esc_url(get_permalink($sec_post)); ?>"><?php echo esc_html(get_the_title($sec_post)); ?></a></h3>
						<?php np_article_meta(); ?>
						<div class="front-secondary-excerpt"><?php echo wp_trim_words(wp_strip_all_tags(get_the_excerpt($sec_post)), 14, '&hellip;'); ?></div>
					</div>
				</article>
			<?php endforeach; wp_reset_postdata(); ?>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<?php if (!empty($np_news_ids)) : ?>
<div class="container front-news-section">
	<h2 class="section-heading"><?php esc_html_e('Local News', 'wp-newspaper-theme'); ?></h2>
	<div class="front-news-grid">
		<?php foreach ($np_news_ids as $news_id) :
			$news_post = get_post($news_id);
			setup_postdata($news_post);
			$news_thumb = np_list_thumbnail_html($news_post);
		?>
			<article class="front-news-card">
				<?php if ($news_thumb) : ?>
					<a class="front-news-thumb" href="<?php echo esc_url(get_permalink($news_post)); ?>"><?php echo $news_thumb; ?></a>
				<?php endif; ?>
				<h3 class="front-news-title"><a href="<?php echo esc_url(get_permalink($news_post)); ?>"><?php echo esc_html(get_the_title($news_post)); ?></a></h3>
				<?php np_article_meta(); ?>
			</article>
		<?php endforeach; wp_reset_postdata(); ?>
	</div>
</div>
<?php endif; ?>

<div class="container content-with-sidebar">
	<div class="content-main">
		<?php if (!empty($np_featured_ids)) : ?>
			<h2 class="section-heading"><?php esc_html_e('More Headlines', 'wp-newspaper-theme'); ?></h2>
		<?php endif; ?>
		<?php if (have_posts()) : ?>
			<div class="article-list">
				<?php while (have_posts()) : the_post();
					if (in_array(get_the_ID(), $np_featured_ids, true)) continue;
					np_article_card();
				endwhile; ?>
			</div>
			<?php np_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e('Nothing published yet.', 'wp-newspaper-theme'); ?></p>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
