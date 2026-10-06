<?php
/**
 * Server-rendered homepage footer.
 *
 * Replaces the previous JavaScript-generated homepage footer (buildFooterHTML() / overrideFooter() / window.hrFooterData) with fully server-side rendered, SEO-friendly markup.
 *
 * @package stratusx-child
 */

$hr_home_url = home_url('/');
$hr_logo_url = 'https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg';
$hr_phone = get_field('talk_to_team', 'option');
$hr_email = get_field('customer_support_email', 'option');
$hr_year = date('Y');
?>

<style>
	.hr-home-footer {
		--hrf-primary: #1b2374;
		--hrf-tint: #f3f6ff;
		--hrf-line: #e2e8f0;
		--hrf-line-strong: #d9e2f2;
		--hrf-ink-strong: #0f172a;
		--hrf-ink-soft: #475569;
		--hrf-ink-faint: #64748b;
		position: relative; margin-top: 0; padding: 72px 0 32px; border-top: 1px solid var(--hrf-line); background: linear-gradient(180deg, #ffffff 0%, #f9faff 55%, #f4f7ff 100%); color: var(--hrf-ink-soft); font-size: 15px;
	}
	.hr-home-footer .wrap { max-width: var(--maxw, 1284px); margin: 0 auto; padding: 0 24px; }
	.hr-home-footer .foot-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 44px 40px; margin-bottom: 40px; }
	.hr-home-footer .foot-grid h3 { margin: 0 0 16px; font-family: var(--font-body); font-size: 13px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--hrf-ink-strong); }
	.hr-home-footer .foot-grid ul { list-style: none; display: grid; gap: 12px; margin: 0; padding: 0; }
	.hr-home-footer .foot-grid a { color: var(--hrf-ink-soft); font-size: 15px; line-height: 1.5; text-decoration: none; transition: color .18s ease; }
	.hr-home-footer .foot-grid a:hover { color: var(--hrf-primary); }
	.hr-home-footer .foot-brand img { height: 70px; width: auto; }
	.hr-home-footer .foot-brand p { margin-top: 16px; max-width: 300px; font-size: 15px; line-height: 1.7; color: var(--hrf-ink-soft); }
	.hr-home-footer .foot-contact-list { display: flex; flex-direction: column; align-items: flex-start; gap: 10px; margin-top: 22px; }
	.hr-home-footer .foot-contact { display: inline-flex; align-items: center; gap: 10px; padding: 9px 15px; border: 1px solid var(--hrf-line-strong); border-radius: 999px; background: #fff; color: var(--hrf-ink-strong); font-size: 14.5px; font-weight: 700; text-decoration: none; transition: color .18s ease, border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
	.hr-home-footer .foot-contact svg { width: 18px; height: 18px; stroke: var(--hrf-primary); flex: none; }
	.hr-home-footer .foot-contact .text { margin: 0; color: inherit; font: inherit; }
	.hr-home-footer .foot-contact:hover { color: var(--hrf-primary); border-color: var(--hrf-primary); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(14, 33, 154, .10); }
	.hr-home-footer .foot-contact:hover svg { stroke: var(--hrf-primary); }
	.hr-home-footer .soc-widget { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 24px; }
	.hr-home-footer .soc-widget a { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; padding: 0; border: 1px solid var(--hrf-line-strong); border-radius: 10px; background: var(--hrf-tint); color: var(--hrf-primary); font-size: 18px; line-height: 1; text-align: center; transition: background .18s ease, border-color .18s ease, color .18s ease, transform .18s ease, box-shadow .18s ease; }
	.hr-home-footer .soc-widget a:hover { background: var(--hrf-primary); border-color: var(--hrf-primary); color: #fff; transform: translateY(-2px); box-shadow: 0 10px 22px rgba(14, 33, 154, .22); }
	.hr-home-footer .soc-widget a svg { color: inherit; fill: currentColor; padding: 0 !important; width: 1em; height: 1em; font-size: 1em; }
	.hr-home-footer .foot-bottom { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding-top: 24px; border-top: 1px solid var(--hrf-line); color: var(--hrf-ink-faint); font-size: 13.5px; }
	.hr-home-footer .foot-bottom a { color: var(--hrf-primary); font-weight: 600; text-decoration: none; }
	.hr-home-footer .foot-bottom a:hover { text-decoration: underline; text-underline-offset: 3px; }
	@media (max-width: 991px) {
		.hr-home-footer { padding: 64px 0 28px; }
		.hr-home-footer .foot-grid { grid-template-columns: 1fr 1fr; gap: 36px 32px; }
		.hr-home-footer .foot-brand { grid-column: 1 / -1; }
	}
	@media (max-width: 720px) {
		.hr-home-footer { padding: 48px 0 24px; }
		.hr-home-footer .foot-grid { grid-template-columns: 1fr; gap: 32px; margin-bottom: 32px; }
		.hr-home-footer .foot-brand p { max-width: none; }
		.hr-home-footer .foot-bottom { justify-content: flex-start; }
	}
</style>
<footer class="hr-home-footer">
	<div class="wrap">
		<div class="foot-grid">
			<div class="foot-brand">
				<a href="<?php echo esc_url($hr_home_url); ?>" aria-label="Healthray home">
					<?php if ($hr_logo_url): ?>
					<img src="<?php echo esc_url($hr_logo_url); ?>" alt="Healthray" height="70" loading="lazy">
					<?php else: ?>
					<strong style="font-size:1.3rem">Healthray</strong>
					<?php endif; ?>
				</a>
				<p>AI-powered, ABDM-compliant hospital management system for India's hospitals, clinics, labs and pharmacies.</p>
				<div class="foot-contact-list">
					<?php if (!empty($hr_phone['url'])): ?>
					<a class="foot-contact" href="<?php echo esc_url($hr_phone['url']); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M13 2a9 9 0 0 1 9 9" />
							<path d="M13 6a5 5 0 0 1 5 5" />
							<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384" />
						</svg>
						<span class="text">
							<?php echo esc_html($hr_phone['title']); ?>
						</span>
					</a>
					<?php endif; ?>

					<?php if (!empty($hr_email['url'])): ?>
					<a class="foot-contact" href="<?php echo esc_url($hr_email['url']); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7" />
							<rect x="2" y="4" width="20" height="16" rx="2" />
						</svg>
						<span class="text">
							<?php echo esc_html($hr_email['title']); ?>
						</span>
					</a>
					<?php endif; ?>
				</div>

				<div class="soc-widget">
					<a href="https://www.facebook.com/Healthraytechnologies/" target="_blank" rel="noopener" aria-label="Facebook">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="currentcolor">
							<path d="M14 13.5H16.5L17.5 9.5H14V7.5C14 6.47 14 5.5 16 5.5H17.5V2.14C17.174 2.097 15.943 2 14.643 2C11.928 2 10 3.657 10 6.7V9.5H7V13.5H10V22H14V13.5Z"></path>
						</svg>
					</a>
					<a href="https://www.instagram.com/healthraytechnologies/" target="_blank" rel="noopener" aria-label="Instagram">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
							<path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
						</svg>
					</a>
					<a href="https://www.linkedin.com/company/healthraytechnologies/" target="_blank" rel="noopener" aria-label="Linkedin">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="currentcolor">
							<path d="M6.93994 5.00002C6.93968 5.53046 6.72871 6.03906 6.35345 6.41394C5.97819 6.78883 5.46937 6.99929 4.93894 6.99902C4.40851 6.99876 3.89991 6.78779 3.52502 6.41253C3.15014 6.03727 2.93968 5.52846 2.93994 4.99802C2.94021 4.46759 3.15117 3.95899 3.52644 3.5841C3.9017 3.20922 4.41051 2.99876 4.94094 2.99902C5.47137 2.99929 5.97998 3.21026 6.35486 3.58552C6.72975 3.96078 6.94021 4.46959 6.93994 5.00002ZM6.99994 8.48002H2.99994V21H6.99994V8.48002ZM13.3199 8.48002H9.33994V21H13.2799V14.43C13.2799 10.77 18.0499 10.43 18.0499 14.43V21H21.9999V13.07C21.9999 6.90002 14.9399 7.13002 13.2799 10.16L13.3199 8.48002Z"></path>
						</svg>
					</a>
					<a href="https://x.com/healthray_" target="_blank" rel="noopener" aria-label="x">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="currentcolor">
							<path d="M8 2H1L9.26 13.015L1.45 22H4.1L10.488 14.651L16 22H23L14.392 10.522L21.8 2H19.15L13.164 8.886L8 2ZM17 20L5 4H7L19 20H17Z"></path>
						</svg>
					</a>
					<a href="https://www.youtube.com/@healthraytechnologies" target="_blank" rel="nofollow noopener" title="YouTube">
						<svg viewBox="0 0 50 50" width="24" height="24" fill="currentcolor">
							<path d="M 44.898438 14.5 C 44.5 12.300781 42.601563 10.699219 40.398438 10.199219 C 37.101563 9.5 31 9 24.398438 9 C 17.800781 9 11.601563 9.5 8.300781 10.199219 C 6.101563 10.699219 4.199219 12.199219 3.800781 14.5 C 3.398438 17 3 20.5 3 25 C 3 29.5 3.398438 33 3.898438 35.5 C 4.300781 37.699219 6.199219 39.300781 8.398438 39.800781 C 11.898438 40.5 17.898438 41 24.5 41 C 31.101563 41 37.101563 40.5 40.601563 39.800781 C 42.800781 39.300781 44.699219 37.800781 45.101563 35.5 C 45.5 33 46 29.398438 46.101563 25 C 45.898438 20.5 45.398438 17 44.898438 14.5 Z M 19 32 L 19 18 L 31.199219 25 Z"></path>
						</svg>
					</a>
				</div>
			</div>

			<nav aria-label="Products">
				<h3>Products</h3>
				<ul>
					<li><a href="<?php echo esc_url($hr_home_url . 'hospital-information-management-system/'); ?>">Hospital Information Management System</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'emr-software/'); ?>">EMR Software</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'ehr-software/'); ?>">EHR Software</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'pharmacy-management-system/'); ?>">Pharmacy Management System</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'laboratory-information-management-system/'); ?>">Laboratory Management (LIMS)</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'clinic-management-software/'); ?>">Clinic Management Software</a></li>
				</ul>
			</nav>

			<nav aria-label="Digital health mission">
				<h3>Digital Health</h3>
				<ul>
					<li><a href="<?php echo esc_url($hr_home_url . 'abdm/'); ?>">ABDM</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'abha/'); ?>">ABHA Health ID</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'pmjay/'); ?>">PMJAY</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'dhis/'); ?>">DHIS</a></li>
				</ul>
			</nav>

			<nav aria-label="Company">
				<h3>Company</h3>
				<ul>
					<li><a href="<?php echo esc_url($hr_home_url . 'our-story/'); ?>">Our Story</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'case-studies/'); ?>">Case Studies</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'blogs/'); ?>">Blog</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'become-a-partner/'); ?>">Become a Partner</a></li>
					<li><a href="<?php echo esc_url($hr_home_url . 'contact/'); ?>">Contact</a></li>
				</ul>
			</nav>
		</div>

		<div class="foot-bottom">
			<span>&copy;
				<?php echo esc_html($hr_year); ?> Healthray Technologies Pvt. Ltd. All rights reserved.
			</span>
			<span><a href="<?php echo esc_url($hr_home_url . 'privacy-policy/'); ?>">Privacy Policy</a></span>
		</div>
	</div>
</footer>