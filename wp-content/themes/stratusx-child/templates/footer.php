<?php get_template_part('templates/lead-popup'); ?>

<?php
$hr_footer_type = apply_filters('hr_footer_type', get_field('footer_type'));

if ($hr_footer_type !== 'legacy') {
	get_template_part('templates/home-footer');
	return;
}
?>

<footer class="footer-section">

	<?php if (!empty(get_field('company_address', 'option')) || !empty(get_field('talk_to_team', 'option')) || !empty(get_field('customer_support_email', 'option'))) { ?>
		<div class="footer">
			<div class="container">
				<div class="footer-cta row row-cols-1 row-cols-md-3">

					<?php if (!empty(get_field('company_address', 'option'))) { ?>
						<div class="col">
							<div class="single-cta">
								<span class="footer-icon"> <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
										<path fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"> </path>
									</svg>
								</span>
								<div class="cta-text">
									<h3 class="title">Find us</h3>
									<p class="text"><?= get_field('company_address', 'option'); ?></p>
								</div>
							</div>
						</div>
					<?php } ?>


					<?php if (!empty(get_field('talk_to_team', 'option'))) { ?>
						<div class="col">
							<div class="single-cta justify-content-start justify-content-lg-center">
								<span class="footer-icon"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 33">
										<path d="M16 15.1205C19.4793 15.1205 22.3 12.2999 22.3 8.82051C22.3 5.34111 19.4793 2.52051 16 2.52051C12.5206 2.52051 9.69995 5.34111 9.69995 8.82051C9.69995 12.2999 12.5206 15.1205 16 15.1205Z" fill="currentcolor" />
										<path d="M16.55 20.5703H15.45L14.5 30.5203H17.5L16.55 20.5703ZM12.55 17.1203V16.2203C10.7499 16.594 9.10626 17.507 7.83791 18.8379C6.56955 20.1687 5.73662 21.8543 5.44995 23.6703L4.74995 27.8203C4.70804 28.1515 4.73566 28.4877 4.83106 28.8076C4.92645 29.1275 5.08752 29.4239 5.30397 29.678C5.52043 29.9321 5.7875 30.1382 6.08814 30.2833C6.38877 30.4283 6.71635 30.509 7.04995 30.5203H13.1L14.05 19.9703C13.5838 19.6563 13.2029 19.2315 12.9411 18.7342C12.6793 18.2368 12.5449 17.6823 12.55 17.1203ZM13.95 17.1203C13.9627 17.66 14.1827 18.1741 14.5644 18.5558C14.9462 18.9375 15.4603 19.1576 16 19.1703C16.5397 19.1576 17.0537 18.9375 17.4355 18.5558C17.8172 18.1741 18.0372 17.66 18.05 17.1203V16.0703H13.95V17.1203ZM27.2 27.7203L26.3 23.3203C25.953 21.6127 25.123 20.0406 23.9088 18.7908C22.6945 17.5411 21.1469 16.6663 19.45 16.2703V17.1203C19.455 17.6823 19.3206 18.2368 19.0588 18.7342C18.797 19.2315 18.4161 19.6563 17.95 19.9703L18.9 30.5203H24.95C25.2945 30.5225 25.6351 30.4473 25.9467 30.3001C26.2582 30.153 26.5327 29.9378 26.75 29.6703C26.9703 29.402 27.1256 29.0864 27.2037 28.7481C27.2818 28.4098 27.2805 28.058 27.2 27.7203Z" fill="currentcolor" />
									</svg> </span>
								<div class="cta-text">
									<h3 class="title">For Inquiries</h3>
									<a href="<?= get_field('talk_to_team', 'option')['url']; ?>">
										<p class="text"><?= get_field('talk_to_team', 'option')['title']; ?></p>
									</a>
								</div>
							</div>
						</div>
					<?php } ?>


					<?php if (!empty(get_field('customer_support_email', 'option'))) { ?>
						<div class="col">
							<div class="single-cta justify-content-start justify-content-lg-center">
								<span class="footer-icon"> <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
										<path fill="currentColor" d="M255.4 48.2c.2-.1 .4-.2 .6-.2s.4 .1 .6 .2L460.6 194c2.1 1.5 3.4 3.9 3.4 6.5v13.6L291.5 355.7c-20.7 17-50.4 17-71.1 0L48 214.1V200.5c0-2.6 1.2-5 3.4-6.5L255.4 48.2zM48 276.2L190 392.8c38.4 31.5 93.7 31.5 132 0L464 276.2V456c0 4.4-3.6 8-8 8H56c-4.4 0-8-3.6-8-8V276.2zM256 0c-10.2 0-20.2 3.2-28.5 9.1L23.5 154.9C8.7 165.4 0 182.4 0 200.5V456c0 30.9 25.1 56 56 56H456c30.9 0 56-25.1 56-56V200.5c0-18.1-8.7-35.1-23.4-45.6L284.5 9.1C276.2 3.2 266.2 0 256 0z"> </path>
									</svg> </span>
								<div class="cta-text">
									<h3 class="title">Mail us</h3>
									<a href="<?= get_field('customer_support_email', 'option')['url']; ?>">
										<p class="text"><?= get_field('customer_support_email', 'option')['title']; ?></p>
									</a>
								</div>
							</div>
						</div>
					<?php } ?>

				</div>
			</div>
		</div>
	<?php } ?>

	<div class="container">
		<div class="footer-content">
			<?php if (is_active_sidebar('sidebar-footer-1')) : ?>
				<div class="th-d-flex footer-flex-content">
					<div class="footer-menu-col sidebar-footer-1">
						<div class="footer-widget footer-widget-heading">
							<div class="footer-area-1 col"><?php dynamic_sidebar('sidebar-footer-1'); ?></div>
						</div>
					</div>

					<div class="footer-menu-col sidebar-footer-2"></div>

					<?php if (is_active_sidebar('sidebar-footer-3') || is_active_sidebar('sidebar-footer-4') || is_active_sidebar('sidebar-footer-5')) : ?>
						<div class="footer-menu-col sidebar-footer">
							<?php foreach (array('sidebar-footer-2', 'sidebar-footer-3', 'sidebar-footer-4', 'sidebar-footer-5') as $hr_footer_col) : ?>
								<?php if (is_active_sidebar($hr_footer_col)) : ?>
									<div class="footer-widget footer-widget-heading"><?php dynamic_sidebar($hr_footer_col); ?></div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="copyright-area <?= has_nav_menu('footer_navigation') ? "footer-navigation" : 'simple-footer'; ?>">
		<div class="container">
			<div class="th-d-flex">
				<div class="copyright footer-menu"> <?php wp_nav_menu(['theme_location' => 'footer_navigation',]); ?> </div>

				<div class="copyright copyright-text">
					<p>
						<span class="footer_copy">&copy; Healthray & Healthray Sales 2026 | All Rights Reserve</span>
						-
						<span class="footer_credit">Made with <span class="heart">❤️</span> by <a href="<?= get_home_url(); ?>">Healthray</a></span>
					</p>
				</div>
			</div>
		</div>
	</div>
</footer>