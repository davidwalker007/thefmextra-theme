<?php if (!defined('ABSPATH')) exit; get_header();

/**
 * The front page previously just listed every post in one long undifferentiated
 * feed — no lead story, no sections, same treatment for a breaking headline as
 * a weekly BBQ column. This pulls a real lead + secondary stories from the
 * paper's own editorial categories (Front Page Lead, falling back to Front
 * Page) plus a Local News rail, then leaves everything else in the normal
 * feed below exactly as before. Only runs on page 1 — /page/2/ etc. just show
 * the plain feed, so pagination isn't affected.
 */
$np_featured_ids = array();
$np_hero_id = null;
$np_secondary_ids = array();
$np_news_ids = array();

if (!is_paged()) {
	$lead_query = new WP_Query(array(
		'category_name'  => 'front-page-lead',
		'posts_per_page' => 4,
		'post_status'    => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'  => true,
	));
	if (!$lead_query->have_posts()) {
		$lead_query = new WP_Query(array(
			'category_name'  => 'front-page',
			'posts_per_page' => 4,
			'post_status'    => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'  => true,
		));
	}
	$lead_ids = wp_list_pluck($lead_query->posts, 'ID');
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
		$hero_thumb = np_list_thumbnail_html();
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
				$sec_thumb = np_list_thumbnail_html();
			?>
				<article class="front-secondary-card">
					<?php if ($sec_thumb) : ?>
						<a class="front-secondary-thumb" href="<?php echo esc_url(get_permalink($sec_post)); ?>"><?php echo $sec_thumb; ?></a>
					<?php endif; ?>
					<h3 class="front-secondary-title"><a href="<?php echo esc_url(get_permalink($sec_post)); ?>"><?php echo esc_html(get_the_title($sec_post)); ?></a></h3>
					<?php np_article_meta(); ?>
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
			$news_thumb = np_list_thumbnail_html();
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
