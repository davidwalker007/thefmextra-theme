<?php if (!defined('ABSPATH')) exit; ?>
</main>

<footer class="site-footer">
	<div class="container">
		<?php $np_facebook_url = get_theme_mod('facebook_url'); ?>
		<?php if ($np_facebook_url) : ?>
			<div class="social-links">
				<a href="<?php echo esc_url($np_facebook_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Facebook', 'wp-newspaper-theme'); ?>">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12.06C22 6.505 17.523 2 12 2S2 6.505 2 12.06c0 5.02 3.657 9.184 8.438 9.94v-7.03H7.898v-2.91h2.54V9.845c0-2.522 1.492-3.915 3.777-3.915 1.094 0 2.238.196 2.238.196v2.475h-1.26c-1.243 0-1.63.775-1.63 1.57v1.888h2.773l-.443 2.91h-2.33V22c4.78-.756 8.437-4.92 8.437-9.94Z"/></svg>
				</a>
			</div>
		<?php endif; ?>
		<p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
