<div class="prefooter"></div>

<?php
/* Themovation Theme Options */
if (function_exists('get_theme_mod')) {
	/* Footer  Columns */
	$footer_col = get_theme_mod('themo_footer_columns', 2);
	$footer_col_class = '';
	switch ($footer_col) {
		case 1:
			$footer_col_class = "footer-cols-1";
			break;
		case 2:
			$footer_col_class = "footer-cols-2";
			break;
		case 3:
			$footer_col_class = "footer-cols-3";
			break;
		case 4:
			$footer_col_class = "footer-cols-4";
			break;
		case 5:
			$footer_col_class = "footer-cols-5";
			break;
	}

	$lab_footer_col = get_theme_mod('themo_lab_footer_columns', 2);
	$lab_footer_col_class = '';
	switch ($lab_footer_col) {
		case 1:
			$lab_footer_col_class = "lab-footer-cols-1";
			break;
		case 2:
			$lab_footer_col_class = "lab-footer-cols-2";
			break;
		case 3:
			$lab_footer_col_class = "lab-footer-cols-3";
			break;
		case 4:
			$lab_footer_col_class = "lab-footer-cols-4";
			break;
	}
}


$class[] = explode('.', basename(get_page_template()))[0];
// $page_type = "template-lab.php";
$page_type = "lab";

if (get_field('footer_type') == $page_type) {
	$class[] = $lab_footer_col_class;
	$class[] = 'template-lab';
};

$class = implode(' ', $class);

?>
<footer class="footer footer-section <?= $class; ?>">

	<div class="container">
		<div class="footer-cta th-d-flex">
			<div class="single-cta">
				<span class="footer-icon"> <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
						<path fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"> </path>
					</svg>
				</span>
				<div class="cta-text">
					<h4>Find us</h4>
					<span><?= get_field('company_address', 'option'); ?></span>
				</div>
			</div>
			<div class="single-cta th-justify-content-phone-start th-justify-content-center">
				<span class="footer-icon"> <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
						<path fill="currentColor" d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"> </path>
					</svg> </span>
				<div class="cta-text">
					<h4>Call us</h4>
					<a href="<?= get_field('talk_to_team', 'option')['url']; ?>"><span><?= get_field('talk_to_team', 'option')['title']; ?></span></a>
				</div>
			</div>
			<div class="single-cta th-justify-content-phone-start th-justify-content-center">
				<span class="footer-icon"> <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
						<path fill="currentColor" d="M255.4 48.2c.2-.1 .4-.2 .6-.2s.4 .1 .6 .2L460.6 194c2.1 1.5 3.4 3.9 3.4 6.5v13.6L291.5 355.7c-20.7 17-50.4 17-71.1 0L48 214.1V200.5c0-2.6 1.2-5 3.4-6.5L255.4 48.2zM48 276.2L190 392.8c38.4 31.5 93.7 31.5 132 0L464 276.2V456c0 4.4-3.6 8-8 8H56c-4.4 0-8-3.6-8-8V276.2zM256 0c-10.2 0-20.2 3.2-28.5 9.1L23.5 154.9C8.7 165.4 0 182.4 0 200.5V456c0 30.9 25.1 56 56 56H456c30.9 0 56-25.1 56-56V200.5c0-18.1-8.7-35.1-23.4-45.6L284.5 9.1C276.2 3.2 266.2 0 256 0z"> </path>
					</svg> </span>
				<div class="cta-text">
					<h4>Mail us</h4>
					<a href="<?= get_field('customer_support_email', 'option')['url']; ?>"><span><?= get_field('customer_support_email', 'option')['title']; ?></span></a>
				</div>
			</div>
		</div>

		<div class="footer-content th-d-flex">
			<div class="footer-menu-col sidebar-footer-1">
				<div class="footer-widget footer-widget-heading">
					<?php if (is_active_sidebar('sidebar-footer-1')) { ?> <div class="footer-area-1 col"> <?php dynamic_sidebar('sidebar-footer-1'); ?> </div> <?php } ?>
				</div>
			</div>
			<?php
			if (get_field('footer_type') != $page_type) {;
			?>
				<?php if (is_active_sidebar('sidebar-footer-2')) { ?>
					<div class="footer-menu-col sidebar-footer-2">
						<div class="footer-widget footer-widget-heading">
							<div class="footer-area-2 col"> <?php dynamic_sidebar('sidebar-footer-2'); ?> </div>
						</div>
					</div>
				<?php } ?>
				<div class="footer-menu-col sidebar-footer">
					<?php
					if (is_active_sidebar('sidebar-footer-3')) {
						echo '<div class="footer-widget footer-widget-heading">';
						dynamic_sidebar('sidebar-footer-3');
						echo '</div>';
					}
					if (is_active_sidebar('sidebar-footer-4')) {
						echo '<div class="footer-widget footer-widget-heading">';
						dynamic_sidebar('sidebar-footer-4');
						echo '</div>';
					}
					if (is_active_sidebar('sidebar-footer-5')) {
						echo '<div class="footer-widget footer-widget-heading">';
						dynamic_sidebar('sidebar-footer-5');
						echo '</div>';
					}
					?>
				</div>
			<?php } else {   ?>
				<?php if (is_active_sidebar('lab-footer-2')) { ?>
					<div class="footer-menu-col sidebar-footer-2">
						<div class="footer-widget footer-widget-heading">
							<div class="footer-area-2 col"> <?php dynamic_sidebar('lab-footer-2'); ?> </div>
						</div>
					</div>
				<?php } ?>
				<div class="footer-menu-col sidebar-footer lab-footer">
					<?php if (is_active_sidebar('lab-footer-3')) {
						echo '<div class="footer-widget footer-widget-heading">';
						dynamic_sidebar('lab-footer-3');
						echo '</div>';
					}
					if (is_active_sidebar('lab-footer-4')) {
						echo '<div class="footer-widget footer-widget-heading">';
						dynamic_sidebar('lab-footer-4');
						echo '</div>';
					}
					if (is_active_sidebar('lab-footer-5')) {
						echo '<div class="footer-widget footer-widget-heading">';
						dynamic_sidebar('lab-footer-5');
						echo '</div>';
					} ?>
				</div>
			<?php } ?>
		</div>


	</div>


	<div class="copyright-area">
		<div class="container">
			<div class="th-d-flex">
				<div class="copyright footer-menu">
					<nav class="nav-main " role="navigation">
						<?php
						if (has_nav_menu('footer_navigation')) :
							wp_nav_menu(array('theme_location' => 'footer_navigation'));
						endif;
						?>
					</nav>
				</div>

				<div class="copyright copyright-text">
					<p>
						<span class="footer_copy">&copy; Healthray 2024 | All Rights Reserve</span>
						-
						<span class="footer_credit">Made with <span class="heart">❤️</span> by <a href="<?= get_home_url(); ?>">Healthray</a></span>
					</p>
				</div>
			</div>
		</div>
	</div>
</footer>