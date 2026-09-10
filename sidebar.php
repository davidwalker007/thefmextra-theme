<?php if (!defined('ABSPATH')) exit; ?>
<?php $np_facebook_url = get_theme_mod('facebook_url'); ?>
<?php if ($np_facebook_url || is_active_sidebar('sidebar-1')) : ?>
<aside class="content-sidebar">
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
<?php endif; ?>
