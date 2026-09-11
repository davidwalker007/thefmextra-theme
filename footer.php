<?php if (!defined('ABSPATH')) exit; ?>
</main>

<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-col">
			<h3 class="footer-heading"><?php bloginfo('name'); ?></h3>
			<p class="footer-tagline"><?php esc_html_e('Serving the Fargo-Moorhead community.', 'thefmextra-theme'); ?></p>
			<?php $np_facebook_url = get_theme_mod('facebook_url'); ?>
			<?php if ($np_facebook_url) : ?>
				<div class="social-links">
					<a href="<?php echo esc_url($np_facebook_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Facebook', 'wp-newspaper-theme'); ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12.06C22 6.505 17.523 2 12 2S2 6.505 2 12.06c0 5.02 3.657 9.184 8.438 9.94v-7.03H7.898v-2.91h2.54V9.845c0-2.522 1.492-3.915 3.777-3.915 1.094 0 2.238.196 2.238.196v2.475h-1.26c-1.243 0-1.63.775-1.63 1.57v1.888h2.773l-.443 2.91h-2.33V22c4.78-.756 8.437-4.92 8.437-9.94Z"/></svg>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<div class="footer-col">
			<h3 class="footer-heading"><?php esc_html_e('Sections', 'thefmextra-theme'); ?></h3>
			<ul class="footer-links">
				<li><a href="<?php echo esc_url(home_url('/e-editions/')); ?>"><?php esc_html_e('E-Edition', 'thefmextra-theme'); ?></a></li>
				<li><a href="<?php echo esc_url(home_url('/living/')); ?>"><?php esc_html_e('Living Magazine', 'thefmextra-theme'); ?></a></li>
				<li><a href="<?php echo esc_url(home_url('/communitymagazines/')); ?>"><?php esc_html_e('Community Magazines', 'thefmextra-theme'); ?></a></li>
			</ul>
		</div>
		<div class="footer-col">
			<h3 class="footer-heading"><?php esc_html_e('About', 'thefmextra-theme'); ?></h3>
			<ul class="footer-links">
				<li><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About Us', 'thefmextra-theme'); ?></a></li>
				<li><a href="<?php echo esc_url(home_url('/contacts/')); ?>"><?php esc_html_e('Staff', 'thefmextra-theme'); ?></a></li>
				<li><a href="<?php echo esc_url(home_url('/print-subscription/')); ?>"><?php esc_html_e('Print Subscription', 'thefmextra-theme'); ?></a></li>
			</ul>
			<p class="footer-email"><a href="mailto:extramediasales@aol.com">extramediasales@aol.com</a></p>
		</div>
	</div>
	<div class="container footer-bottom">
		<p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
