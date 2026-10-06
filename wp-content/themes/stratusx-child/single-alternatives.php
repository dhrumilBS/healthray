<?php
$post_id = get_the_ID();
$author_id = get_the_author_meta('ID');
$author_name = get_the_author_meta('display_name');
$author_bio = get_the_author_meta('description');
$author_url = get_author_posts_url($author_id);
$author_avatar = get_avatar_url($author_id, ['size' => 100]);
$author_initials = implode('', array_map(
	fn($w) => strtoupper($w[0]),
	preg_split('/\s+/', trim((string) $author_name), -1, PREG_SPLIT_NO_EMPTY) ?: array()
));
$site_host = wp_parse_url(home_url(), PHP_URL_HOST);
$why_look_content = get_field('why_look_content', $post_id);
$methodology_content = get_field('methodology_content', $post_id);
$competitors = get_field('competitors', $post_id) ?: array();
$glance_rows = get_field('glance_rows', $post_id);
$comparison_categories = get_field('comparison_categories', $post_id);
$has_comparison = $glance_rows || $comparison_categories;
$competitor_profiles = get_field('competitor_profiles', $post_id);
$profiles_heading = trim((string) get_field('profiles_heading', $post_id));
$how_to_choose_content = get_field('how_to_choose_content', $post_id);
$final_verdict_content = get_field('final_verdict_content', $post_id);
$faqs = get_field('faqs', $post_id);

$toc_items = array(array('id' => 'alt-overview', 'label' => 'Overview'));
if ($why_look_content) {
	$toc_items[] = array('id' => 'alt-why-look', 'label' => 'Why Look for Alternatives', 'level' => 2);
}
if ($methodology_content) {
	$toc_items[] = array('id' => 'alt-methodology', 'label' => 'How We Analyse', 'level' => 2);
}
if ($has_comparison) {
	$toc_items[] = array('id' => 'alt-comparison', 'label' => 'Compare at a Glance', 'level' => 2);
}
if ($competitor_profiles) {
	if ($profiles_heading) {
		$toc_items[] = array('id' => 'alt-profiles', 'label' => $profiles_heading, 'level' => 2);
	}
	foreach ($competitor_profiles as $p_i => $p) {
		$toc_items[] = array('id' => 'alt-profile-' . (int) $p_i, 'label' => ($p_i + 1) . '. ' . $p['name'], 'level' => 3);
	}
}
if ($how_to_choose_content) {
	$toc_items[] = array('id' => 'alt-how-to-choose', 'label' => 'How to Choose', 'level' => 2);
}
if ($final_verdict_content) {
	$toc_items[] = array('id' => 'alt-verdict', 'label' => 'Final Verdict', 'level' => 2);
}
if ($faqs) {
	$toc_items[] = array('id' => 'alt-faq', 'label' => 'FAQ', 'level' => 2);
}

$toc_heading_levels = get_field('toc_heading_levels', $post_id) ?: 'h3';
if ($toc_heading_levels !== 'both') {
	$toc_wanted_level = ($toc_heading_levels === 'h2') ? 2 : 3;
	$toc_items = array_values(array_filter($toc_items, function ($item) use ($toc_wanted_level) {
		return !isset($item['level']) || $item['level'] === $toc_wanted_level;
	}));
}

?>
<main class="alt-single">
	<section class="alt-hero" id="alt-overview">
		<div class="container">
			<div class="alt-hero__grid<?= has_post_thumbnail() ? '' : ' alt-hero__grid--no-visual'; ?>">
				<div class="alt-hero__content">
					<span class="alt-hero__eyebrow"><?= esc_html(get_the_date('F j, Y')); ?></span>

					<div class="heading">
						<h1><?php the_title(); ?></h1>
					</div>

					<div class="alt-hero__cta">
						<?php if ($has_comparison): ?>
							<a href="#alt-comparison" class="alt-hero__cta-secondary">
								Compare Options
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</a>
						<?php endif; ?>
					</div>
				</div>

				<div class="alt-hero__visual">
					<div class="alt-hero__visual-frame">
						<?php if (has_post_thumbnail()): ?>
							<?= get_the_post_thumbnail($post_id, 'large', array('class' => 'alt-hero__visual-img')); ?>
						<?php else: ?>
							<div class="alt-hero__visual-fallback">
								<?= wp_get_attachment_image(27662, 'medium', false, array('class' => 'alt-hero__visual-logo')); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="alt-layout container">
		<aside class="alt-sidebar">
			<div class="alt-sidebar__sticky">
				<nav class="alt-toc" aria-label="Table of contents">
					<p class="alt-toc__title">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<rect x="3" y="4" width="18" height="17" rx="3" stroke="currentColor" stroke-width="1.8" />
							<path d="M7 9h10M7 13h10M7 17h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
						</svg>
						On This Page
					</p>
					<ul>
						<?php foreach ($toc_items as $item): ?>
							<li><a href="#<?= esc_attr($item['id']); ?>" class="alt-toc__link<?= (($item['level'] ?? 2) === 3) ? ' alt-toc__link--sub' : ''; ?>" data-toc-link><?= esc_html($item['label']); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>

				<div class="alt-sidebar__cta">
					<p class="alt-sidebar__cta-heading">See Why Hospitals Switch to Healthray</p>
					<ul class="alt-sidebar__cta-list">
						<li>ABDM Compliant</li>
						<li>Fast Go-Live</li>
						<li>Dedicated Support</li>
					</ul>
					<a href="https://healthray.com/contact/" class="alt-sidebar__cta-btn hr-cta-btn" <?= hr_alt_cta_target_attr('https://healthray.com/contact/', $site_host); ?>>Book a Free Demo</a>
				</div>
			</div>
		</aside>

		<div class="alt-main">

			<?php
			if (count($toc_items) > 1):
				?>
				<div class="alt-toc-mobile" id="alt-toc-mobile">
					<button class="alt-toc-mobile__toggle" type="button" aria-expanded="false" aria-controls="alt-toc-mobile-panel">
						<span class="alt-toc-mobile__icon" aria-hidden="true">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="3" y="4" width="18" height="17" rx="3" stroke="currentColor" stroke-width="1.8" />
								<path d="M7 9h10M7 13h10M7 17h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
							</svg>
						</span>
						<span class="alt-toc-mobile__label">On This Page</span>
						<span class="alt-toc-mobile__chevron" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</span>
					</button>
					<nav id="alt-toc-mobile-panel" class="alt-toc-mobile__panel is-collapsed" aria-label="Table of contents">
						<ul>
							<?php foreach ($toc_items as $item): ?>
								<li><a href="#<?= esc_attr($item['id']); ?>" class="alt-toc__link<?= (($item['level'] ?? 2) === 3) ? ' alt-toc__link--sub' : ''; ?>" data-toc-link><?= esc_html($item['label']); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				</div>
			<?php endif; ?>

			<?php if (get_field('intro_content', $post_id)): ?>
				<section class="alt-intro">
					<div class="container narrow">
						<?= wp_kses_post(get_field('intro_content', $post_id)); ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ($why_look_content): ?>
				<section class="alt-intro" id="alt-why-look">
					<div class="container narrow">
						<div class="heading">
							<h2><?= esc_html(get_field('why_look_title', $post_id) ?: 'Why Look for an Alternative'); ?></h2>
						</div>
						<?= wp_kses_post($why_look_content); ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ($methodology_content):
				$methodology_title = get_field('methodology_title', $post_id);
				?>
				<section class="alt-intro" id="alt-methodology">
					<div class="container narrow">
						<?php if ($methodology_title): ?>
							<div class="heading">
								<h2><?= esc_html($methodology_title); ?></h2>
							</div>
						<?php endif; ?>
						<?= wp_kses_post($methodology_content); ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ($has_comparison):
				$cta_label = get_field('comparison_cta_text', $post_id) ?: 'Get A Demo';

				$product_count = 1 + count($competitors);
				$total_cols = $product_count + 1;
				$feature_pct = 22;
				$product_pct = round((100 - $feature_pct) / $product_count, 4);
				$grid_template = $feature_pct . '% ' . implode(' ', array_fill(0, $product_count, $product_pct . '%'));
				$matrix_style = '--alt-grid-cols:' . esc_attr($grid_template) . ';';
				?>
				<section class="alt-comparison" id="alt-comparison">
					<div class="container narrow">
						<div class="heading">
							<h2><?= esc_html(get_field('comparison_title', $post_id) ?: 'Compare the Options at a Glance'); ?></h2>
						</div>

						<?php
						$comparison_intro = get_field('comparison_intro', $post_id);
						if ($comparison_intro): ?>
							<div class="alt-comparison__intro"><?= hr_alt_rich_text($comparison_intro); ?></div>
						<?php endif; ?>

						<div class="alt-matrix-wrap" style="<?= $matrix_style; ?>">
							<table class="alt-matrix">
								<colgroup>
									<col style="width:<?= $feature_pct; ?>%">
									<?php for ($i = 0; $i < $product_count; $i++): ?>
										<col style="width:<?= $product_pct; ?>%">
									<?php endfor; ?>
								</colgroup>
								<tbody>
									<tr class="alt-matrix__header">
										<th></th>
										<th class="is-us"><?= hr_alt_render_glance_header('Healthray', 0, true); ?></th>
										<?php foreach ($competitors as $c): ?>
											<th><?= hr_alt_render_glance_header($c['name'], $c['logo'] ?? 0); ?></th>
										<?php endforeach; ?>
									</tr>

									<?php if ($glance_rows):
										foreach ($glance_rows as $row):
											$comp_values = $row['competitor_values'] ?? array();
											?>
											<tr class="alt-matrix__summary-row">
												<td><?= esc_html($row['label']); ?></td>
												<td class="is-us"><?= hr_alt_render_glance_value($row['healthray_value'], $row['value_type']); ?></td>
												<?php foreach ($competitors as $i => $c): ?>
													<td><?= hr_alt_render_glance_value($comp_values[$i]['value'] ?? '', $row['value_type']); ?></td>
												<?php endforeach; ?>
											</tr>
										<?php endforeach;
									endif; ?>

									<?php if ($comparison_categories):
										foreach ($comparison_categories as $cat_i => $cat):
											if (empty($cat['features']))
												continue;
											$is_open = false; // All categories start collapsed for a consistent first view.
											$panel_key = 'alt-cat-' . (int) $cat_i;
											?>
											<tr class="alt-accordion__header<?= $is_open ? ' is-open' : ''; ?>" data-accordion="<?= esc_attr($panel_key); ?>">
												<td colspan="<?= (int) $total_cols; ?>" class="alt-accordion__header-td">
													<div class="alt-accordion__header-cell" role="button" tabindex="0" aria-expanded="<?= $is_open ? 'true' : 'false'; ?>">
														<span class="alt-accordion__label"><?= esc_html($cat['category_name']); ?></span>
														<svg class="alt-accordion__chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
															<path d="M9 6l6 6-6 6" stroke="#2563eb" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
														</svg>
													</div>
												</td>
											</tr>
											<tr class="alt-accordion__panel<?= $is_open ? ' is-open' : ''; ?>" data-accordion-panel="<?= esc_attr($panel_key); ?>">
												<td colspan="<?= (int) $total_cols; ?>" class="alt-accordion__panel-cell">
													<div class="alt-accordion__collapse">
														<div class="alt-accordion__collapse-inner">
															<?php foreach ($cat['features'] as $feature):
																$fc_values = $feature['competitor_values'] ?? array();
																?>
																<div class="alt-matrix__row">
																	<div class="alt-matrix__cell alt-matrix__cell--label"><?= esc_html($feature['feature_name']); ?></div>
																	<div class="alt-matrix__cell is-us"><?= hr_alt_render_cell($feature['healthray_value']); ?></div>
																	<?php foreach ($competitors as $i => $c): ?>
																		<div class="alt-matrix__cell"><?= hr_alt_render_cell($fc_values[$i]['value'] ?? ''); ?></div>
																	<?php endforeach; ?>
																</div>
															<?php endforeach; ?>
														</div>
													</div>
												</td>
											</tr>
										<?php endforeach;
									endif; ?>

									<tr class="alt-matrix__cta-row">
										<td></td>
										<td class="is-us"><a href="https://healthray.com/contact/" class="alt-matrix__cta-btn" <?= hr_alt_cta_target_attr('https://healthray.com/contact/', $site_host); ?>><?= esc_html($cta_label); ?></a></td>
										<?php foreach ($competitors as $c): ?>
											<td></td>
										<?php endforeach; ?>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</section>
			<?php endif; ?>

			<?php if ($competitor_profiles): ?>
				<section class="alt-profiles" id="alt-profiles">
					<div class="container narrow">
						<?php if ($profiles_heading): ?>
							<div class="heading alt-profiles__heading">
								<h2><?= esc_html($profiles_heading); ?></h2>
							</div>
						<?php endif; ?>

						<?php foreach ($competitor_profiles as $p_i => $p): ?>
							<article class="alt-profile" id="alt-profile-<?= (int) $p_i; ?>">
								<div class="alt-profile__head">
									<h3 class="alt-profile__heading"><span class="alt-profile__number"><?= (int) $p_i + 1; ?>.</span> <?= esc_html($p['name']); ?></h3>
								</div>

								<?php
								foreach (hr_alt_profile_parts(hr_alt_profile_content($p)) as $part):
									$block = ($part['type'] === 'block') ? $part['value'] : '';
									?>

									<?php if ($part['type'] === 'html'): ?>
										<?= $part['value']; ?>

									<?php elseif ($block === 'screenshot' && !empty($p['screenshot'])): ?>
										<div class="alt-profile__screenshot"><?= wp_get_attachment_image($p['screenshot'], 'large'); ?></div>

									<?php elseif ($block === 'rating' && !empty($p['rating_value'])): ?>
										<div class="alt-profile__rating">
											<?= hr_alt_render_stars((float) $p['rating_value']); ?>
											<span><?= esc_html($p['rating_value']); ?>/5<?php if (!empty($p['rating_source'])): ?> by <?= esc_html($p['rating_source']); ?><?php endif; ?></span>
										</div>

									<?php elseif ($block === 'pros_cons' && (!empty($p['pros']) || !empty($p['cons']))): ?>
										<div class="alt-pros-cons__grid">
											<?php if (!empty($p['pros'])): ?>
												<div class="fre-pros-box">
													<div class="pc-box__head">
														<span class="pc-box__icon pc-box__icon--pros" aria-hidden="true"><img src="<?= esc_url(content_url('/uploads/2026/08/Pros-alternative.svg')); ?>" width="32" height="32" alt="" loading="lazy"></span>
														<h4 class="pc-box__title">Pros</h4>
														<span class="pc-box__line" aria-hidden="true"></span>
													</div>
													<ul>
														<?php foreach ($p['pros'] as $pro): ?>
															<li><?php if (!empty($pro['title'])): ?><strong><?= esc_html($pro['title']); ?></strong>: <?php endif; ?><?= hr_alt_rich_inline($pro['text']); ?></li>
														<?php endforeach; ?>
													</ul>
													<?php if (!empty($p['pros_review_enable']) && !empty($p['pros_review_quote'])): ?>
														<?php
														$pros_review_label = hr_alt_review_label($p, 'pros');
														$pros_review_stars = trim((string) $p['pros_review_rating']);
														$pros_review_meta = !empty($p['pros_review_author']) || $pros_review_stars !== '';
														?>
														<div class="users-feedback">
															<div class="customer-feedback-dec">
																<?php if ($pros_review_meta): ?>
																	<div class="customer-feedback-dec__meta">
																		<?php if (!empty($p['pros_review_author'])): ?>
																			<p class="user-name"><?= esc_html($p['pros_review_author']); ?></p><?php endif; ?>
																		<?php if ($pros_review_stars !== ''): ?> 								<?= hr_alt_render_stars((float) $pros_review_stars); ?> 							<?php endif; ?>
																	</div>
																<?php endif; ?>
																<p><?= hr_alt_rich_inline($p['pros_review_quote']); ?></p>
																<p class="mb-0"><?php if (!empty($p['pros_review_url'])): ?><a href="<?= esc_url($p['pros_review_url']); ?>" target="_blank" rel="noopener noreferrer nofollow"><?= esc_html($pros_review_label); ?></a><?php else: ?><?= esc_html($pros_review_label); ?><?php endif; ?></p>
															</div>
														</div>
													<?php endif; ?>
												</div>
											<?php endif; ?>

											<?php if (!empty($p['cons'])): ?>
												<div class="fre-cons-box">
													<div class="pc-box__head">
														<span class="pc-box__icon pc-box__icon--cons" aria-hidden="true"><img src="<?= esc_url(content_url('/uploads/2026/08/Cons-alternative.svg')); ?>" width="32" height="32" alt="" loading="lazy"></span>
														<h4 class="pc-box__title">Cons</h4>
														<span class="pc-box__line" aria-hidden="true"></span>
													</div>
													<ul>
														<?php foreach ($p['cons'] as $con): ?>
															<li><?php if (!empty($con['title'])): ?><strong><?= esc_html($con['title']); ?></strong>: <?php endif; ?><?= hr_alt_rich_inline($con['text']); ?></li>
														<?php endforeach; ?>
													</ul>
													<?php if (!empty($p['cons_review_enable']) && !empty($p['cons_review_quote'])): ?>
														<?php
														$cons_review_label = hr_alt_review_label($p, 'cons');
														$cons_review_stars = trim((string) $p['cons_review_rating']);
														$cons_review_meta = !empty($p['cons_review_author']) || $cons_review_stars !== '';
														?>
														<div class="users-feedback">
															<div class="customer-feedback-dec">
																<?php if ($cons_review_meta): ?>
																	<div class="customer-feedback-dec__meta">
																		<?php if (!empty($p['cons_review_author'])): ?>
																			<p class="user-name"><?= esc_html($p['cons_review_author']); ?></p><?php endif; ?>
																		<?php if ($cons_review_stars !== ''): ?> 								<?= hr_alt_render_stars((float) $cons_review_stars); ?> 							<?php endif; ?>
																	</div>
																<?php endif; ?>
																<p><?= hr_alt_rich_inline($p['cons_review_quote']); ?></p>
																<p class="mb-0"><?php if (!empty($p['cons_review_url'])): ?><a href="<?= esc_url($p['cons_review_url']); ?>" target="_blank" rel="noopener noreferrer nofollow"><?= esc_html($cons_review_label); ?></a><?php else: ?><?= esc_html($cons_review_label); ?><?php endif; ?></p>
															</div>
														</div>
													<?php endif; ?>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>

								<?php endforeach; ?>
							</article>

							<?php
							if ($p_i === 0) {
								echo hr_alt_render_cta(
									array(
										'heading' => get_field('inline_cta_heading', $post_id),
										'text' => get_field('inline_cta_text', $post_id),
										'button_text' => get_field('inline_cta_button_text', $post_id),
										'url' => get_field('inline_cta_url', $post_id),
										'modifier' => 'alt-mid-cta--inline',
									),
									$site_host
								);
							}
							?>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ($how_to_choose_content): ?>
				<section class="alt-intro" id="alt-how-to-choose">
					<div class="container narrow">
						<div class="heading">
							<h2><?= esc_html(get_field('how_to_choose_title', $post_id) ?: 'How to Choose the Right Alternative'); ?></h2>
						</div>
						<?= wp_kses_post($how_to_choose_content); ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ($final_verdict_content): ?>
				<section class="alt-verdict" id="alt-verdict">
					<div class="container narrow">
						<div class="heading">
							<h3><?= esc_html(get_field('final_verdict_title', $post_id) ?: 'Final Verdict'); ?></h3>
						</div>
						<?= wp_kses_post($final_verdict_content); ?>
					</div>
				</section>
			<?php endif; ?>

			<?php
			// End-of-article CTA. Same renderer as the inline one above.
			echo hr_alt_render_cta(
				array(
					'heading' => get_field('mid_cta_heading', $post_id),
					'text' => get_field('mid_cta_text', $post_id),
					'button_text' => get_field('mid_cta_button_text', $post_id),
					'url' => get_field('mid_cta_url', $post_id),
					'wrap' => true,
				),
				$site_host
			);
			?>

			<?php if ($faqs): ?>
				<section class="alt-faqs" id="alt-faq">
					<div class="container narrow">
						<div class="heading">
							<h3>Frequently Asked Questions</h3>
						</div>
						<div class="faq-list">
							<?php foreach ($faqs as $faq): ?>
								<details name="alt-faq-group">
									<summary><?= esc_html($faq['question']); ?></summary>
									<div class="answer"><?= hr_alt_rich_text($faq['answer']); ?></div>
								</details>
							<?php endforeach; ?>
						</div>
					</div>
				</section>
			<?php endif; ?>

			<div class="container">
				<?php
				/**
				 * Author bio. Two rows: avatar + name on top, description at full
				 * width below. .hr-bio-desc and .hr-bio-link are siblings of
				 * .hr-bio-top rather than children of .hr-bio-info, so the
				 * description can span the whole box instead of being squeezed
				 * beside the avatar. Structure is identical in single.php.
				 */
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
					<a href="<?= esc_url($author_url); ?>" class="hr-bio-link" itemprop="url">View all posts &rarr;</a>
				</div>
			</div>

		</div>
	</div>

</main>