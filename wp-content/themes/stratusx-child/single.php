<?php

/**
 * Gives every H2 an anchor ID and collects the matching TOC entries.
 *
 * @param string $content Post content, already run through the_content filters.
 * @return array{0: string, 1: list<array{tag: string, text: string, anchor: string}>}
 *         [0] rewritten content, [1] TOC items.
 */
function healthray_generate_toc($content)
{
	$toc_items = [];

	$content = preg_replace_callback(
		'/<(h2)([^>]*)>(.*?)<\/h2>/is',
		function ($matches) use (&$toc_items) {
			$tag = $matches[1];
			$attrs = $matches[2];
			$inner = $matches[3];

			// Clean heading text
			$text = wp_strip_all_tags(html_entity_decode($inner, ENT_QUOTES, 'UTF-8'));

			// Remove special chars
			$clean_text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);

			// Skip empty headings
			if (empty(trim($clean_text))) {
				return $matches[0];
			}
			$anchor = 'h-' . substr(md5($clean_text), 0, 8);
			$attrs = preg_replace('/\sid=("|\')(.*?)\1/i', '', $attrs);

			// Save TOC item
			$toc_items[] = ['tag' => $tag, 'text' => $text, 'anchor' => $anchor,];

			// Add same ID as TOC anchor
			return sprintf(
				'<%1$s%2$s id="%3$s">%4$s</%1$s>',
				$tag,
				$attrs,
				esc_attr($anchor),
				$inner
			);
		},
		$content
	);

	$content = preg_replace(
		'/<(h[3-6])([^>]*)\sid=("|\')(.*?)\3([^>]*)>/i',
		'<$1$2 $5>',
		$content
	);

	return [$content, $toc_items];
}

// Reading Time
function healthray_reading_time($content)
{
	$word_count = str_word_count(wp_strip_all_tags($content));
	$reading_time = max(1, ceil($word_count / 200));
	return $reading_time . ' Min Read';
}
?>

<?php while (have_posts()):
	the_post();

	$post_id = get_the_ID();
	$post_content = get_the_content();
	$post_content = apply_filters('the_content', $post_content);

	[$post_content_with_ids, $toc_items] = healthray_generate_toc($post_content);

	// Post meta
	$author_id = get_the_author_meta('ID');
	$author_name = get_the_author_meta('display_name');
	$author_bio = get_the_author_meta('description');
	$author_url = get_author_posts_url($author_id);
	$author_avatar = get_avatar_url($author_id, ['size' => 100]);
	$author_initials = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $author_name)));

	$published_date = get_the_date('M j, Y');
	$modified_date = get_the_modified_date('M j, Y');
	$published_iso = get_the_date('c');
	$modified_iso = get_the_modified_date('c');
	$reading_time = healthray_reading_time($post_content);
	$post_url = get_permalink();
	$post_title = get_the_title();
	$categories = get_the_category();
	$primary_cat = !empty($categories) ? $categories[0] : null;

	$yoast_primary_cat = (int) get_post_meta($post_id, '_yoast_wpseo_primary_category', true);
	if ($yoast_primary_cat) {
		foreach ($categories as $cat) {
			if ((int) $cat->term_id === $yoast_primary_cat) {
				$primary_cat = $cat;
				break;
			}
		}
	}

	// Blog landing page, used by the breadcrumb.
	$blog_page_id = (int) get_option('page_for_posts');
	$has_blog_page = $blog_page_id && 'publish' === get_post_status($blog_page_id);

?>

	<!-- HERO / ARTICLE HEADER -->
	<div class="hr-single-wrapper" id="hr-single-wrapper">
		<!-- HERO SECTION — same structure as the Alternatives hero (.alt-hero) -->
		<header class="hr-hero-section" role="banner">
			<div class="container">
				<div class="hr-hero-grid<?= has_post_thumbnail() ? '' : ' hr-hero-grid--no-visual'; ?>">
					<div class="hr-hero-content">

						<?php
						?>
						<nav class="hr-breadcrumb" aria-label="Breadcrumb">
							<ol>
								<?php if ($has_blog_page): ?>
									<li><a href="<?= esc_url(get_permalink($blog_page_id)); ?>"><?= esc_html(get_the_title($blog_page_id)); ?></a></li>
								<?php endif; ?>
								<?php if ($primary_cat): ?>
									<li><a href="<?= esc_url(get_category_link($primary_cat->term_id)); ?>"><?= esc_html($primary_cat->name); ?></a></li>
								<?php endif; ?>
							</ol>
						</nav>

						<h1 class="hr-blog-title"><?php the_title(); ?></h1>

						<div class="hr-meta-pills">
							<div class="hr-date-reading">
								<time datetime="<?= esc_attr($published_iso); ?>"><?= esc_html($published_date); ?></time>
								<span aria-hidden="true">·</span>
								<span><?= esc_html($reading_time); ?></span>

								<?php if (is_user_logged_in() && ($published_date !== $modified_date)): ?>
									<span aria-hidden="true">·</span>
									<span>Updated <?= esc_html($modified_date); ?></span>
								<?php endif; ?>
							</div>
						</div>

						<?php if (has_excerpt()): // Hand-written excerpt only, never an auto-generated one. 
						?>
							<p class="hr-hero-lead"><?= esc_html(get_the_excerpt()); ?></p>
						<?php endif; ?>

						<!-- Author Row -->
						<div class="hr-author-row">
							<div class="hr-author-info-wrap">
								<div class="hr-author-avatar" aria-hidden="true">
									<?php if ($author_avatar): ?>
										<img src="<?= esc_url($author_avatar); ?>" alt="<?= esc_attr($author_name); ?>" width="40" height="40" loading="lazy">
									<?php else: ?>
										<span class="hr-author-initials"><?= esc_html($author_initials); ?></span>
									<?php endif; ?>
								</div>
								<div class="hr-author-details">
									<a href="<?= esc_url($author_url); ?>" class="hr-author-name" rel="author"><span><?= esc_html($author_name); ?></span> </a>
									<?php
									$author_job = get_the_author_meta('job_title') ?: get_bloginfo('name');
									?>
									<span class="hr-author-role"><?= esc_html($author_job); ?></span>
								</div>
							</div>
						</div>

					</div>

					<?php if (has_post_thumbnail()): ?>
						<div class="hr-hero-visual">
							<div class="hr-hero-visual-frame">
								<?php
								the_post_thumbnail('large', [
									'class' => 'hr-hero-img',
									'loading' => 'eager',
									'fetchpriority' => 'high',
									'decoding' => 'async',
									'sizes' => '(max-width: 991px) 100vw, 600px',
								]);
								?>
							</div>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</header>

		<div id="hr-content" tabindex="-1">
			<div class="container">
				<div class="hr-blog-row">
					<main class="hr-content-area" id="hr-main" role="main">

						<?php // The featured image now runs as the hero visual, above. 
						?>

						<?php if (!empty($toc_items)): ?>
							<div class="hr-toc-mobile">
								<button class="hr-toc-toggle" type="button" aria-expanded="false" aria-controls="hr-toc-mobile-list">
									<span class="hr-toc-icon">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true">
											<line x1="3" y1="6" x2="21" y2="6" />
											<line x1="3" y1="12" x2="15" y2="12" />
											<line x1="3" y1="18" x2="18" y2="18" />
										</svg>
									</span>
									<span>Table of Contents</span>
									<span class="hr-toc-chevron" aria-hidden="true">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14">
											<polyline points="6 9 12 15 18 9" />
										</svg>
									</span>
								</button>
								<?php
								// Dismiss control. A sibling of the toggle rather than a
								// child, because a button cannot be nested inside another
								// button. Only rendered visible while the box is the sticky
								// mobile one, and nothing is persisted: it hides the TOC for
								// the current page view, so a refresh or a revisit brings it
								// straight back.
								?>
								<button class="hr-toc-dismiss" type="button" aria-label="Hide the table of contents">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" width="16" height="16" aria-hidden="true">
										<line x1="6" y1="6" x2="18" y2="18" />
										<line x1="18" y1="6" x2="6" y2="18" />
									</svg>
								</button>
								<nav id="hr-toc-mobile-list" class="hr-toc-nav hr-toc-collapsed" aria-label="Table of Contents">
									<ol class="hr-toc-list">
										<?php foreach ($toc_items as $i => $item): ?>
											<li class="hr-toc-item hr-toc-<?= esc_attr($item['tag']); ?>">
												<a href="#<?= esc_attr($item['anchor']); ?>" class="hr-toc-link">
													<span class="hr-toc-num"><?= esc_html($i + 1); ?>.</span>
													<?= esc_html($item['text']); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ol>
								</nav>
							</div>
						<?php endif; ?>

						<div class="hr-post-content entry-content">
							<?= $post_content_with_ids; // already filtered above 
							?>
						</div>

						<div class="hr-post-cta"><?= do_shortcode('[blog_cta_end]'); ?></div>

						<?php if (have_rows('blog_faqs')): ?>
							<div class="hr-post-faqs">
								<h2 class="hr-faqs-heading">Frequently Asked Questions</h2>
								<div class="faq-list">
									<?php while (have_rows('blog_faqs')):
										the_row(); ?>
										<details>
											<summary><?= get_sub_field('question'); ?></summary>
											<div class="answer"><?= get_sub_field('answer'); ?></div>
										</details>
									<?php
									endwhile;
									$faq_rows = get_field('blog_faqs');
									if (!empty($faq_rows)):
										$faq_json = [
											'@context' => 'https://schema.org',
											'@type' => 'FAQPage',
											'mainEntity' => [],
										];
										foreach ($faq_rows as $faq_item) {
											$faq_json['mainEntity'][] = [
												'@type' => 'Question',
												'name' => wp_strip_all_tags($faq_item['question']),
												'acceptedAnswer' => [
													'@type' => 'Answer',
													'text' => wp_strip_all_tags($faq_item['answer']),
												],
											];
										}
									?>
										<script type="application/ld+json">
											<?= wp_json_encode($faq_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
										</script>
									<?php endif; ?>
								</div><!-- /.faq-list.accordion-list -->
							</div>
						<?php endif; ?>

						<?php
						?>
						<div class="hr-author-bio-box" aria-label="About the author">
							<div class="hr-bio-top">
								<div class="hr-bio-avatar">
									<?php if ($author_avatar): ?>
										<img src="<?= esc_url($author_avatar); ?>" alt="<?= esc_attr($author_name); ?>" width="80" height="80" loading="lazy" class="hr-bio-img">
									<?php else: ?>
										<div class="hr-bio-initials" aria-hidden="true"><?= esc_html($author_initials); ?></div>
									<?php endif; ?>
								</div>
								<div class="hr-bio-info">
									<p class="hr-bio-heading">About the Author</p>
									<div class="hr-bio-name h3" itemprop="name"><?= esc_html($author_name); ?></div>
								</div>
							</div>
							<?php if ($author_bio): ?>
								<div class="hr-bio-desc" itemprop="description">
									<?= wp_kses_post(wpautop($author_bio)); ?>
								</div>
							<?php endif; ?>
							<a href="<?= esc_url($author_url); ?>" class="hr-bio-link" itemprop="url">
								View all posts →
							</a>
						</div>
					</main>

					<aside class="hr-sidebar" id="hr-sidebar" aria-label="Article sidebar">

						<!-- Sticky column: TOC + CTA, mirroring the Alternatives sidebar -->
						<div class="hr-sidebar__sticky">

							<?php if (!empty($toc_items)): ?>
								<nav class="hr-sidebar-toc hr-toc-sticky" id="hr-toc-sidebar" aria-label="Table of Contents (sidebar)">
									<p class="hr-toc-heading">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
											<rect x="3" y="4" width="18" height="17" rx="3" stroke="currentColor" stroke-width="1.8" />
											<path d="M7 9h10M7 13h10M7 17h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
										</svg>
										Table of Contents
									</p>
									<ol class="hr-toc-list">
										<?php foreach ($toc_items as $i => $item): ?>
											<li class="hr-toc-item hr-toc-<?= esc_attr($item['tag']); ?>">
												<a href="#<?= esc_attr($item['anchor']); ?>" class="hr-toc-link" data-anchor="<?= esc_attr($item['anchor']); ?>">
													<span class="hr-toc-num">
														<?= esc_html($i + 1); ?>.
													</span>
													<?= esc_html($item['text']); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ol>
								</nav>
							<?php endif; ?>

							<!-- CTA Box -->
							<div class="hr-sidebar-cta">
								<p class="hr-sidebar-cta__heading">Explore Healthray for Free</p>
								<ul class="hr-sidebar-cta__list">
									<li>Manage patient records</li>
									<li>Appointment scheduling</li>
									<li>Prescription management</li>
									<li>24/7 support</li>
								</ul>
								<!-- .hr-cta-btn is the site-wide demo-popup hook (js/script.js) - keep it. -->
								<button type="button" class="hr-sidebar-cta__btn hr-cta-btn">Try it Now!</button>
							</div>

						</div><!-- /.hr-sidebar__sticky -->
					</aside>
				</div>
			</div>
		</div>

	</div>

	<!-- INLINE JS — TOC Active State + Smooth Scroll + Mobile Toggle -->
	<script>
		(function() {
			'use strict';
			const mobileToc = document.querySelector('.hr-toc-mobile');
			const mobileToggle = document.querySelector('.hr-toc-toggle');
			const mobileTocNav = document.getElementById('hr-toc-mobile-list');

			function tocIsOpen() {
				return !!mobileTocNav && !mobileTocNav.classList.contains('hr-toc-collapsed');
			}

			function setTocOpen(open) {
				if (!mobileTocNav) return;
				mobileTocNav.classList.toggle('hr-toc-collapsed', !open);
				if (mobileToggle) mobileToggle.setAttribute('aria-expanded', String(open));
			}

			if (mobileToggle && mobileTocNav) {
				mobileToggle.addEventListener('click', function(e) {
					e.stopPropagation();
					setTocOpen(!tocIsOpen());
				});

				document.addEventListener('click', function(e) {
					if (tocIsOpen() && mobileToc && !mobileToc.contains(e.target)) {
						setTocOpen(false);
					}
				});

				document.addEventListener('keydown', function(e) {
					if (('Escape' === e.key || 'Esc' === e.key) && tocIsOpen()) {
						setTocOpen(false);
						mobileToggle.focus();
					}
				});
			}

			const mobileDismiss = document.querySelector('.hr-toc-dismiss');

			if (mobileDismiss && mobileToc) {
				mobileDismiss.addEventListener('click', function(e) {
					e.stopPropagation();
					setTocOpen(false);
					mobileToc.classList.add('is-dismissed');

					const article = document.querySelector('.hr-post-content') || document.getElementById('hr-main');
					if (article) {
						article.setAttribute('tabindex', '-1');
						article.focus({
							preventScroll: true
						});
					}
				});
			}

			function scrollOffset() {
				const siteHeader = document.querySelector('header.elementor-location-header');
				let offset = (siteHeader ? siteHeader.getBoundingClientRect().height : 80) + 12;
				const barHeight = mobileToggle ? mobileToggle.getBoundingClientRect().height : 0;

				if (barHeight && mobileToc && 'sticky' === window.getComputedStyle(mobileToc).position) {
					offset += barHeight + 12;
				}

				return offset;
			}

			/* ── Smooth Scroll for TOC Links ── */
			document.querySelectorAll('.hr-toc-link').forEach(function(link) {
				link.addEventListener('click', function(e) {
					const targetId = this.getAttribute('href').slice(1);
					const target = document.getElementById(targetId);
					if (target) {
						e.preventDefault();
						const top = target.getBoundingClientRect().top + window.pageYOffset - scrollOffset();
						window.scrollTo({
							top: top,
							behavior: 'smooth'
						});
						setTocOpen(false);
					}
				});
			});

			/* ── TOC Active Highlight on Scroll (sidebar) ── */
			const tocLinks = document.querySelectorAll('#hr-toc-sidebar .hr-toc-link[data-anchor]');

			if (tocLinks.length) {
				const headings = [];
				tocLinks.forEach(function(link) {
					const id = link.getAttribute('data-anchor');
					const el = document.getElementById(id);
					if (el) headings.push({
						el: el,
						link: link
					});
				});

				let ticking = false;

				function updateActiveToc() {
					const scrollY = window.pageYOffset;
					let activeIndex = 0;
					headings.forEach(function(h, i) {
						if (h.el.getBoundingClientRect().top + scrollY - 120 <= scrollY) {
							activeIndex = i;
						}
					});
					tocLinks.forEach(function(l) {
						l.classList.remove('hr-toc-active');
					});
					if (headings[activeIndex]) {
						headings[activeIndex].link.classList.add('hr-toc-active');
					}
					ticking = false;
				}

				window.addEventListener('scroll', function() {
					if (!ticking) {
						requestAnimationFrame(updateActiveToc);
						ticking = true;
					}
				}, {
					passive: true
				});

				updateActiveToc();
			}

			/* ── Reading Progress Bar ── */
			const progressBar = document.getElementById('hr-progress-bar');
			if (progressBar) {
				window.addEventListener('scroll', function() {
					const docEl = document.documentElement;
					const scroll = docEl.scrollTop || document.body.scrollTop;
					const height = docEl.scrollHeight - docEl.clientHeight;
					progressBar.style.width = (height > 0 ? (scroll / height) * 100 : 0) + '%';
				}, {
					passive: true
				});
			}
		})();
	</script>
<?php endwhile; ?>