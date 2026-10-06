<?php
$author      = get_queried_object();
$author_id   = $author->ID;
$avatar_url  = get_avatar_url($author_id, ['size' => 250]);
$display_name = esc_html($author->display_name);
$position    = esc_html(get_the_author_meta('user_position',   $author_id));
$description = get_user_meta($author_id, 'user_description', true);
$linkedin    = get_the_author_meta('linkedin',  $author_id);
$youtube     = get_the_author_meta('youtube',   $author_id);
$twitter     = get_the_author_meta('twitter',   $author_id);
$email       = antispambot(get_the_author_meta('user_email', $author_id));

// Post counts & first-publish year for stats bar
$post_count  = count_user_posts($author_id, 'post', true);
$first_post  = get_posts([
	'author'         => $author_id,
	'posts_per_page' => 1,
	'orderby'        => 'date',
	'order'          => 'ASC',
	'post_status'    => 'publish',
	'fields'         => 'ids',
]);
$since_year  = ! empty($first_post)
	? get_the_date('Y', $first_post[0])
	: gmdate('Y');

// Recent posts query
$recent_posts_query = new WP_Query([
	'author'         => $author_id,
	'posts_per_page' => 3,
	'post_status'    => 'publish',
	'no_found_rows'  => true,
]);
?>

<div class="hr-author-template">
	<section class="hr-author-hero" aria-label="Author profile">
		<div class="hr-author-hero__inner">

			<!-- Breadcrumb -->
			<nav class="hr-author-breadcrumb" aria-label="Breadcrumb">
				<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
				<span aria-hidden="true">›</span>
				<span>Author</span>
				<span aria-hidden="true">›</span>
				<span class="hr-author-breadcrumb__current"><?php echo $display_name; ?></span>
			</nav>
                                                                  
			<!-- Profile row -->
			<div class="hr-author-profile-row">

				<!-- Avatar -->
				<div class="hr-author-avatar-wrap">
					<div class="hr-author-avatar-ring">
						<img src="<?php echo esc_url($avatar_url); ?>" width="136" height="136" alt="<?php echo esc_attr($display_name); ?>" decoding="async" fetchpriority="high" class="hr-author-avatar-img" />
					</div>
					<div class="hr-author-avatar-badge" aria-hidden="true">
						<svg viewBox="0 0 10 10" width="12" height="12" fill="white">
							<path d="M1.5 5.5L3.5 7.5L8.5 2.5" />
						</svg>
					</div>
				</div>

				<!-- Meta -->
				<div class="hr-author-meta">
					<div class="hr-author-role-badge"><span class="hr-author-role-dot"></span>Healthray Expert Author</div>
					<h1 class="hr-author-name"><?php echo $display_name; ?></h1>

					<?php if ($position) : ?>
						<p class="hr-author-position"><?php echo $position; ?></p>
					<?php endif; ?>

					<!-- Socials -->
					<div class="hr-author-socials">
						<?php if ($linkedin) : ?>
							<a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="nofollow noopener noreferrer" class="hr-author-social-btn hr-author-social-btn--linkedin" aria-label="<?php echo esc_attr("LinkedIn profile of $display_name"); ?>">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
									<path d="M6.94 5a2 2 0 1 1-4-.002A2 2 0 0 1 6.94 5zM7 8.48H3V21h4V8.48zm6.32 0H9.34V21h3.94v-6.57c0-3.66 4.77-3.96 4.77 0V21H22v-7.93c0-6.17-7.06-5.94-8.72-2.91l.04-1.68z" />
								</svg>
								LinkedIn
							</a>
						<?php endif; ?>

						<?php if ($youtube) : ?>
							<a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="nofollow noopener noreferrer" class="hr-author-social-btn hr-author-social-btn--youtube" aria-label="<?php echo esc_attr("YouTube channel of $display_name"); ?>">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
									<path d="M21.8 8s-.2-1.4-.8-2c-.8-.8-1.6-.8-2-.9C16.3 5 12 5 12 5s-4.3 0-7 .1c-.4 0-1.2.1-2 .9-.6.6-.8 2-.8 2S2 9.6 2 11.2v1.5c0 1.6.2 3.2.2 3.2s.2 1.4.8 2c.8.8 1.8.8 2.3.9C6.9 19 12 19 12 19s4.3 0 7-.2c.4 0 1.2-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.5C22 9.6 21.8 8 21.8 8zM9.7 14.5V9l5.3 2.8-5.3 2.7z" />
								</svg>YouTube
							</a>
						<?php endif; ?>

						<?php if ($twitter) : ?>
							<a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="nofollow noopener noreferrer" class="hr-author-social-btn hr-author-social-btn--twitter" aria-label="<?php echo esc_attr("Twitter/X profile of $display_name"); ?>">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
									<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
								</svg>Twitter / X </a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Stats bar -->
			<div class="hr-author-stats-bar" aria-label="Author stats">
				<div class="hr-author-stats-grid">
					<div class="hr-author-stat">
						<span class="hr-author-stat__value"><?php echo absint($post_count); ?>+</span>
						<span class="hr-author-stat__label">Articles Published</span>
					</div>
					<div class="hr-author-stat">
						<span class="hr-author-stat__value"><?php echo esc_html($since_year); ?></span>
						<span class="hr-author-stat__label">Writing Since</span>
					</div>
					<div class="hr-author-stat">
						<span class="hr-author-stat__value">
							<?php
							// Show categories the author has written in
							$cats = get_categories([ 'hide_empty' => true, 'object_ids' => wp_list_pluck( get_posts(['author' => $author_id, 'posts_per_page' => 50, 'fields' => 'ids']), 'ID' ), ]);
							echo count($cats) ? absint(count($cats)) : '—';
							?>
						</span>
						<span class="hr-author-stat__label">Topics Covered</span>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php if ($description) : ?>
		<section class="hr-author-section" aria-labelledby="hr-author-bio-heading">
			<div class="hr-author-container">
				<span class="hr-author-section-label">Professional Background</span>
				<div class="hr-author-bio-card">
					<div class="hr-author-bio-body">
						<?php echo wp_kses_post($description); ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ($recent_posts_query->have_posts()) : ?>
		<section class="hr-author-section" aria-labelledby="hr-author-articles-heading">
			<div class="hr-author-container">
				<div class="hr-author-articles-header">
					<div>
						<span class="hr-author-section-label">Latest Content</span>
						<h2 class="hr-author-section-title" id="hr-author-articles-heading">
							<?php echo $display_name; ?>'s Recent Articles
						</h2>
					</div>
					<a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="hr-author-view-all" aria-label="View all articles by <?php echo esc_attr($display_name); ?>"> View all articles
						<svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<path d="M3 8h10M9 4l4 4-4 4" />
						</svg>
					</a>
				</div>

				<div class="hr-author-articles-grid">
					<?php while ($recent_posts_query->have_posts()) : $recent_posts_query->the_post(); ?>
						<article class="hr-author-article-card" itemscope itemtype="https://schema.org/BlogPosting">
							<?php if (has_post_thumbnail()) : ?>
								<a href="<?php the_permalink(); ?>" class="hr-author-article-thumb-link" tabindex="-1" aria-hidden="true">
									<?php the_post_thumbnail('medium', [ 'class'   => 'hr-author-article-thumb', 'loading' => 'lazy', 'itemprop' => 'image', ]); ?>
								</a>
							<?php else : ?>
								<div class="hr-author-article-thumb-placeholder" aria-hidden="true">
									<svg viewBox="0 0 48 48" width="40" height="40" fill="none" stroke="#0e60a8" stroke-width="1.5">
										<rect x="6" y="10" width="36" height="28" rx="4" />
										<path d="M6 32l10-10 6 6 8-10 12 12" />
									</svg>
								</div>
							<?php endif; ?>

							<div class="hr-author-article-body">
								<?php $cats = get_the_category(); if ($cats) : ?>
									<a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" class="hr-author-article-cat" itemprop="articleSection">
										<?php echo esc_html($cats[0]->name); ?>
									</a>
								<?php endif; ?>
								<h3 class="hr-author-article-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="hr-author-article-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 18, '…'); ?></p>
								<div class="hr-author-article-footer">
									<time class="hr-author-article-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>" itemprop="datePublished">
										<?php echo esc_html(get_the_date('M j, Y')); ?>
									</time>
									<a href="<?php the_permalink(); ?>" class="hr-author-article-read-more">Read more
										<svg viewBox="0 0 16 16" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
											<path d="M3 8h10M9 4l4 4-4 4" />
										</svg>
									</a>
								</div>
							</div>
						</article>
					<?php endwhile;
					wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<section class="hr-author-cta-section" aria-label="Contact Healthray">
		<div class="hr-author-container">
			<div class="hr-author-cta-card">
				<div class="hr-author-cta-deco" aria-hidden="true"></div>
				<h3 class="hr-author-cta-title">Ready to transform your healthcare practice?</h3>
				<p class="hr-author-cta-sub">Connect with the Healthray team for a personalised demo of India's leading HMS platform.</p>
				<div class="hr-author-cta-btns">
					<a href="https://calendly.com/healthray/healthray-technologies-lead-meeting" class="hr-author-cta-btn hr-author-cta-btn--primary" target="_blank" rel="noopener noreferrer">Book a Demo</a>
					<a href="<?php echo esc_url(home_url('/contact/')); ?>" class="hr-author-cta-btn hr-author-cta-btn--secondary">Contact Us</a>
				</div>
			</div>
		</div>
	</section>

</div>