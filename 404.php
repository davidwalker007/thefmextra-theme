<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<div class="container">
	<div class="narrow page-content text-center">
		<h1><?php esc_html_e('Page Not Found', 'wp-newspaper-theme'); ?></h1>
		<p><?php esc_html_e("The page you're looking for doesn't exist or has moved.", 'wp-newspaper-theme'); ?></p>
		<?php get_search_form(); ?>
	</div>
</div>

<?php get_footer(); ?>
