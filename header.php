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
	<div class="container top-bar">
		<div class="logo">
			<?php if (has_custom_logo()) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
			<?php endif; ?>
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
