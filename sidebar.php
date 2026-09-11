<?php if (!defined('ABSPATH')) exit; ?>
<?php $np_facebook_url = get_theme_mod('facebook_url'); ?>
<aside class="content-sidebar">
	<div class="widget widget-subscribe">
		<h3 class="widget-title"><?php esc_html_e('Get It Delivered', 'thefmextra-theme'); ?></h3>
		<p class="widget-subscribe-price">$75<span>/year</span></p>
		<p><?php esc_html_e('Home delivery of the printed FM Extra.', 'thefmextra-theme'); ?></p>
		<a class="btn btn-accent" href="<?php echo esc_url(home_url('/print-subscription/')); ?>"><?php esc_html_e('Subscribe', 'thefmextra-theme'); ?></a>
	</div>

	<?php
	$np_latest_edition = get_page_by_path('e-editions');
	if ($np_latest_edition && preg_match('/<h1>\s*([^<]+?)\s*<\/h1>/', $np_latest_edition->post_content, $np_ed_match)) :
	?>
	<div class="widget widget-eedition">
		<h3 class="widget-title"><?php esc_html_e('This Week\'s Edition', 'thefmextra-theme'); ?></h3>
		<p class="widget-eedition-date"><?php echo esc_html($np_ed_match[1]); ?></p>
		<a class="btn" href="<?php echo esc_url(get_permalink($np_latest_edition)); ?>"><?php esc_html_e('Read the E-Edition', 'thefmextra-theme'); ?></a>
	</div>
	<?php endif; ?>

	<?php if ($np_facebook_url) : ?>
		<div class="widget widget-facebook-page">
			<h3 class="widget-title"><?php esc_html_e('Follow Us', 'wp-newspaper-theme'); ?></h3>
			<?php echo np_facebook_page_embed($np_facebook_url); ?>
		</div>
	<?php endif; ?>
	<?php if (is_active_sidebar('sidebar-1')) : ?>
		<?php dynamic_sidebar('sidebar-1'); ?>
	<?php endif; ?>
</aside>
