<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-W4XJXSXT');</script>
	<!-- End Google Tag Manager -->
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W4XJXSXT"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
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
			<div class="logo">
				<?php if (has_custom_logo()) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
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
