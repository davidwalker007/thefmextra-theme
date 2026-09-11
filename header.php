<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="utility-bar">
		<div class="container utility-bar-inner">
			<span class="utility-date"><?php echo esc_html(date_i18n('l, F j, Y')); ?></span>
			<span class="utility-links">
				<a href="<?php echo esc_url(home_url('/e-editions/')); ?>"><?php esc_html_e('E-Edition', 'thefmextra-theme'); ?></a>
				<a href="<?php echo esc_url(home_url('/print-subscription/')); ?>" class="utility-subscribe"><?php esc_html_e('Subscribe', 'thefmextra-theme'); ?></a>
			</span>
		</div>
	</div>
	<div class="top-bar">
		<div class="container">
			<?php
			// David's call: the logo links to the Front Page category
			// archive, not the homepage — the_custom_logo() always links
			// home, so the logo is rendered manually here to point it
			// wherever np_logo_link_url() resolves instead.
			$np_logo_url = np_logo_link_url();
			?>
			<div class="logo">
				<?php if (has_custom_logo()) :
					$np_logo_id = get_theme_mod('custom_logo');
				?>
					<a href="<?php echo esc_url($np_logo_url); ?>" class="custom-logo-link">
						<?php echo wp_get_attachment_image($np_logo_id, 'full', false, array('class' => 'custom-logo')); ?>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url($np_logo_url); ?>"><?php bloginfo('name'); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<nav class="site-nav" id="site-nav">
		<div class="container">
			<button type="button" class="menu-toggle" id="menu-toggle" aria-expanded="false" aria-controls="primary-menu-wrap">
				<span class="menu-toggle-bars" aria-hidden="true"><span></span><span></span><span></span></span>
				<span class="screen-reader-text">Menu</span>
			</button>
			<div class="primary-menu-wrap" id="primary-menu-wrap">
				<?php
				wp_nav_menu(array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'np_fallback_menu',
				));
				?>
			</div>
		</div>
	</nav>
</header>

<main class="site-main">
