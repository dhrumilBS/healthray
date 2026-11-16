<?php
/**
 * Author template – Healthray (stratusx-child design system)
 * Uses theme tokens from style.css (:root). Fallbacks are provided in case a token is missing.
 */
$author       = get_queried_object();
$author_id    = $author->ID;
$avatar_url   = get_avatar_url($author_id, ['size' => 250]);
$display_name = esc_html($author->display_name);
$position     = esc_html(get_the_author_meta('user_position', $author_id));
$description  = get_user_meta($author_id, 'user_description', true);
$linkedin     = get_the_author_meta('linkedin', $author_id);
$youtube      = get_the_author_meta('youtube', $author_id);
$twitter      = get_the_author_meta('twitter', $author_id);

// Recent posts
$recent_posts_query = new WP_Query([
	'author'         => $author_id,
	'posts_per_page' => 3,
	'post_status'    => 'publish',
	'no_found_rows'  => true,
]);

// ProfilePage schema
$schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'ProfilePage',
	'mainEntity' => [
		'@type'    => 'Person',
		'name'     => $author->display_name,
		'url'      => get_author_posts_url($author_id),
		'image'    => $avatar_url,
		'jobTitle' => wp_strip_all_tags($position),
		'worksFor' => [
			'@type' => 'Organization',
			'name'  => 'Healthray Technologies Pvt. Ltd.',
			'url'   => home_url('/'),
		],
		'sameAs'   => array_values(array_filter([$linkedin, $youtube, $twitter])),
	],
];
?>

<style>
/* ===== Author page – built on stratusx-child tokens ===== */
.hr-author-template{
	font-family:var(--font-body,Mulish,system-ui,sans-serif);
	color:var(--hr-ink-soft,#475569);
	background:var(--bg,#fff);
}
.hr-author-template *{box-sizing:border-box}
.hr-author-container{max-width:1240px;margin:0 auto;padding:0 15px}

/* ---- Hero (same light gradient as blog / archive heroes) ---- */
.hr-author-hero{
	background:var(--hr-hero-gradient,linear-gradient(to bottom,#fff 0%,#fff 30%,#EFF4FF 65%,#EFF4FF 100%));
	padding:28px 0 56px;border-bottom:1px solid var(--hr-line,#e2e8f0);
}
.hr-author-hero__inner{max-width:1240px;margin:0 auto;padding:0 15px}
.hr-author-breadcrumb{display:flex;flex-wrap:wrap;align-items:center;gap:6px;font-size:14px;margin-bottom:32px;color:var(--hr-ink-faint,#64748b)}
.hr-author-breadcrumb a{color:var(--hr-navy,#1b3c74);font-weight:600}
.hr-author-breadcrumb a:hover{text-decoration:underline}
.hr-author-breadcrumb__current{color:var(--hr-ink-strong,#0f172a);font-weight:700}

.hr-author-profile-row{display:flex;align-items:center;gap:32px}
.hr-author-avatar-wrap{position:relative;flex:0 0 auto}
.hr-author-avatar-ring{
	width:160px;height:160px;border-radius:50%;padding:4px;background:#fff;
	border:1px solid var(--hr-line-strong,#d9e2f2);box-shadow:var(--hr-shadow-soft,0 14px 30px rgba(15,23,42,.06));
}
.hr-author-avatar-img{width:100%;height:100%;border-radius:50%;object-fit:cover;display:block}
.hr-author-avatar-badge{
	position:absolute;right:6px;bottom:6px;width:32px;height:32px;border-radius:50%;
	background:var(--hr-blue,#0f68ea);border:3px solid #fff;display:flex;align-items:center;justify-content:center;
}
.hr-author-avatar-badge svg{fill:none;stroke:#fff;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}

.hr-author-eyebrow{
	display:inline-flex;align-items:center;gap:8px;padding:5px 14px;border-radius:999px;
	background:var(--brand-tint,#EFF4FF);border:1px solid var(--hr-line-blue,#cfdcfa);
	color:var(--brand-deep,#0F2A66);font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;
}
.hr-author-role-dot{width:8px;height:8px;border-radius:50%;background:var(--hr-blue,#0f68ea);box-shadow:0 0 0 4px rgba(var(--hr-blue-rgb,15,104,234),.15)}
.hr-author-name{
	margin:14px 0 6px;font-size:var(--hr-h1-size,clamp(30px,4.5vw,50px));font-weight:800;line-height:1.15;
	letter-spacing:-.02em;color:var(--hr-navy,#1b3c74);
}
.hr-author-position{margin:0 0 18px;font-size:18px;color:var(--hr-ink-soft,#475569);font-weight:600}
.hr-author-socials{display:flex;flex-wrap:wrap;gap:10px}
.hr-author-social-btn{
	display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:12px;
	background:#fff;border:1px solid var(--hr-line,#e2e8f0);color:var(--hr-navy,#1b3c74);
	font-size:14px;font-weight:700;box-shadow:0 2px 8px rgba(var(--hr-ink-strong-rgb,15,23,42),.06);
	transition:transform .18s ease,background .18s ease,color .18s ease,border-color .18s ease;
}
.hr-author-social-btn:hover{transform:translateY(-2px);background:var(--hr-navy,#1b3c74);border-color:var(--hr-navy,#1b3c74);color:#fff}

/* ---- Sections ---- */
.hr-author-section{padding:56px 0 0}
.hr-author-section-label{
	display:inline-block;margin-bottom:8px;font-size:11px;font-weight:800;letter-spacing:.06em;
	text-transform:uppercase;color:var(--hr-blue,#0f68ea);
}
.hr-author-section-title{margin:0 0 6px;font-size:var(--hr-h2-size,clamp(24px,4vw,36px));font-weight:800;letter-spacing:-.02em;color:var(--hr-navy,#1b3c74);text-transform:none}

/* ---- Bio ---- */
.hr-author-bio-card{
	margin-top:18px;padding:28px 30px;background:#fff;border:1px solid var(--hr-line,#e2e8f0);
	border-left:4px solid var(--hr-blue,#0f68ea);border-radius:var(--hr-radius-card,18px);
	box-shadow:var(--hr-shadow-card,0 8px 24px rgba(15,23,42,.06));
}
.hr-author-bio-body{font-size:17px;line-height:1.8;color:var(--hr-ink-soft,#475569)}
.hr-author-bio-body p+p{margin-top:12px}
.hr-author-bio-body a{color:var(--hr-indigo,#5669ec);font-weight:700}
.hr-author-bio-body p{margin:0 0 16px}
.hr-author-bio-body h2,.hr-author-bio-body h3,.hr-author-bio-body h4{margin:26px 0 12px;font-size:clamp(20px,2vw,26px);line-height:1.35;color:var(--hr-navy,#1b3c74);text-transform:none}
.hr-author-bio-body .title{font-size:26px}
.hr-author-bio-body ul,.hr-author-bio-body ol{margin:0 0 16px;padding-left:22px}
.hr-author-bio-body li{margin-bottom:8px}
.hr-author-bio-body strong{color:var(--hr-ink-strong,#0f172a);font-weight:700}
.hr-author-bio-body .box-container+.box-container{margin-top:20px}
.hr-author-bio-body>div>:last-child,.hr-author-bio-body>:last-child{margin-bottom:0}

/* ---- Articles (mirrors .mas-blog card) ---- */
.hr-author-articles-header{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:28px}
.hr-author-view-all,.hr-author-article-read-more{display:inline-flex;align-items:center;gap:6px;color:var(--hr-navy,#1b3c74);font-weight:700;font-size:14px}
.hr-author-view-all:hover,.hr-author-article-read-more:hover{color:var(--hr-blue,#0f68ea)}
.hr-author-view-all svg,.hr-author-article-read-more svg{transition:transform .2s}
.hr-author-view-all:hover svg,.hr-author-article-read-more:hover svg{transform:translateX(3px)}
.hr-author-articles-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.hr-author-article-card{
	display:flex;flex-direction:column;padding:14px;background:#fff;border:1px solid var(--hr-line,#e2e8f0);
	border-radius:var(--hr-radius-card,18px);box-shadow:var(--hr-shadow-card,0 8px 24px rgba(15,23,42,.06));
	transition:box-shadow .2s ease,transform .2s ease,border-color .2s ease;
}
.hr-author-article-card:hover{transform:translateY(-3px);border-color:var(--hr-line-strong,#d9e2f2);box-shadow:0 16px 34px rgba(var(--hr-ink-strong-rgb,15,23,42),.10)}
.hr-author-article-media{position:relative;margin-bottom:14px}
.hr-author-article-thumb-link,.hr-author-article-thumb-placeholder{
	display:block;border-radius:var(--hr-radius-soft,14px);overflow:hidden;background:var(--hr-tint,#f3f6ff);
}
.hr-author-article-thumb{display:block;width:100%;aspect-ratio:16/9;object-fit:contain;transition:transform .4s ease}
.hr-author-article-card:hover .hr-author-article-thumb{transform:scale(1.03)}
.hr-author-article-thumb-placeholder{aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--hr-pale-cool,#eef2ff) 0%,var(--hr-wash-cool,#f8faff) 100%)}
.hr-author-article-body{display:flex;flex-direction:column;flex:1}
.hr-author-article-cat{
	position:absolute;left:10px;top:10px;z-index:2;max-width:calc(100% - 20px);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
	padding:5px 12px;box-shadow:0 2px 8px rgba(var(--hr-ink-strong-rgb,15,23,42),.10);border:1px solid var(--hr-line,#e2e8f0);border-radius:999px;
	background:#fff;color:var(--hr-navy,#1b3c74);font-size:11px;font-weight:800;letter-spacing:.06em;line-height:1.3;text-transform:uppercase;
	transition:all .2s ease;
}
.hr-author-article-cat:hover{background:var(--hr-navy,#1b3c74);border-color:var(--hr-navy,#1b3c74);color:#fff}
.hr-author-article-title{margin:0 0 12px;font-size:18px;font-weight:700;line-height:1.4;letter-spacing:-.01em;text-transform:none}
.hr-author-article-title a{color:var(--hr-ink-strong,#0f172a)}
.hr-author-article-title a:hover{color:var(--hr-navy,#1b3c74)}
.hr-author-article-footer{margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid var(--hr-line,#e2e8f0)}
.hr-author-article-date{font-size:13.5px;color:var(--hr-ink-faint,#64748b)}

/* ---- Trust strip ---- */
.hr-author-trust{padding:56px 0 0;text-align:center}
.hr-author-trust p{margin:0 0 14px;font-size:14px;color:var(--hr-ink-faint,#64748b)}
.hr-author-trust__badges{display:flex;flex-wrap:wrap;justify-content:center;gap:10px}
.hr-author-trust__badges span{
	padding:7px 16px;border:1px solid var(--hr-line,#e2e8f0);border-radius:999px;background:var(--hr-tint,#f3f6ff);
	color:var(--hr-navy,#1b3c74);font-size:12px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;
}

/* ---- CTA (same pattern as .blog-cta-section) ---- */
.hr-author-cta-section{padding:56px 0 72px}
.hr-author-cta-card{
	position:relative;overflow:hidden;text-align:center;padding:clamp(28px,4vw,48px);
	background:var(--hr-tint,#f3f6ff);border:1px solid var(--hr-line-strong,#d9e2f2);border-radius:16px;
	box-shadow:0 4px 18px rgba(var(--hr-ink-strong-rgb,15,23,42),.06);
}
.hr-author-cta-deco{position:absolute;right:-80px;top:-80px;width:260px;height:260px;border-radius:50%;background:radial-gradient(circle,rgba(var(--hr-blue-rgb,15,104,234),.12),transparent 70%)}
.hr-author-cta-card>*:not(.hr-author-cta-deco){position:relative;z-index:1}
.hr-author-cta-kicker{display:inline-block;margin-bottom:10px;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--hr-blue,#0f68ea)}
.hr-author-cta-title{margin:0 0 12px;font-size:clamp(24px,3vw,32px);font-weight:800;line-height:1.25;letter-spacing:-.02em;color:var(--hr-ink-strong,#0f172a);text-transform:none}
.hr-author-cta-sub{max-width:640px;margin:0 auto 18px;font-size:17px;line-height:1.7;color:var(--hr-ink-soft,#475569)}
.hr-author-cta-list{display:flex;flex-wrap:wrap;justify-content:center;gap:10px 24px;margin:0 0 26px;padding:0;list-style:none}
.hr-author-cta-list li{display:flex;align-items:center;gap:8px;font-size:14.5px;font-weight:600;color:var(--hr-navy,#1b3c74)}
.hr-author-cta-list li::before{content:"";width:20px;height:20px;flex:0 0 20px;background:var(--hr-tick) center/20px 20px no-repeat}
.hr-author-cta-btns{display:flex;flex-wrap:wrap;justify-content:center;gap:12px}
.hr-author-cta-btn{display:inline-flex;align-items:center;justify-content:center;padding:15px 32px;border-radius:12px;font-weight:700;font-size:16px;transition:background .18s ease,transform .18s ease,box-shadow .18s ease,color .18s ease}
.hr-author-cta-btn--primary{background:var(--hr-blue,#0f68ea);color:#fff;box-shadow:0 14px 28px rgba(var(--hr-navy-rgb,27,60,116),.24)}
.hr-author-cta-btn--primary:hover{background:var(--hr-blue-dark,#0b57c8);color:#fff;transform:translateY(-2px);box-shadow:0 18px 36px rgba(var(--hr-navy-rgb,27,60,116),.3)}
.hr-author-cta-btn--secondary{background:#fff;color:var(--hr-navy,#1b3c74);border:1.5px solid var(--hr-line-blue,#cfdcfa)}
.hr-author-cta-btn--secondary:hover{background:var(--hr-navy,#1b3c74);border-color:var(--hr-navy,#1b3c74);color:#fff;transform:translateY(-2px)}
.hr-author-cta-note{margin:16px 0 0;font-size:.8rem;color:var(--ink-soft,#4B5B70)}

/* ---- Responsive ---- */
@media (max-width:991px){.hr-author-articles-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:767px){
	.hr-author-hero{padding-bottom:44px}
	.hr-author-profile-row{flex-direction:column;text-align:center;gap:20px}
	.hr-author-breadcrumb,.hr-author-socials{justify-content:center}
	.hr-author-avatar-ring{width:136px;height:136px}
	.hr-author-bio-card{padding:22px 20px}
	.hr-author-position{font-size:16px}
}
@media (max-width:575px){
	.hr-author-articles-grid{grid-template-columns:1fr;gap:22px}
	.hr-author-article-card{padding:12px;border-radius:14px}
	.hr-author-article-title{font-size:17px}
	.hr-author-cta-btn{width:100%}
	.hr-author-cta-list{flex-direction:column;align-items:center}
}
@media (prefers-reduced-motion:reduce){.hr-author-template *{transition:none!important}}
</style>

<script type="application/ld+json"><?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

<div class="hr-author-template">

	<!-- ============ HERO ============ -->
	<section class="hr-author-hero" aria-label="Author profile">
		<div class="hr-author-hero__inner">

			<nav class="hr-author-breadcrumb" aria-label="Breadcrumb">
				<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
				<span aria-hidden="true">›</span>
				<a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a>
				<span aria-hidden="true">›</span>
				<span class="hr-author-breadcrumb__current"><?php echo $display_name; ?></span>
			</nav>

			<div class="hr-author-profile-row">

				<div class="hr-author-avatar-wrap">
					<div class="hr-author-avatar-ring">
						<img src="<?php echo esc_url($avatar_url); ?>" width="160" height="160" alt="<?php echo esc_attr($author->display_name); ?>" decoding="async" fetchpriority="high" class="hr-author-avatar-img" />
					</div>
					<div class="hr-author-avatar-badge" aria-hidden="true">
						<svg viewBox="0 0 10 10" width="14" height="14"><path d="M1.5 5.5L3.5 7.5L8.5 2.5" /></svg>
					</div>
				</div>

				<div class="hr-author-meta">
					<div class="hr-author-eyebrow"><span class="hr-author-role-dot"></span>Healthray Expert Author</div>
					<h1 class="hr-author-name"><?php echo $display_name; ?></h1>

					<?php if ($position) : ?>
						<p class="hr-author-position"><?php echo $position; ?></p>
					<?php endif; ?>

					<div class="hr-author-socials">
						<?php if ($linkedin) : ?>
							<a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="nofollow noopener noreferrer" class="hr-author-social-btn hr-author-social-btn--linkedin" aria-label="<?php echo esc_attr("LinkedIn profile of {$author->display_name}"); ?>">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M6.94 5a2 2 0 1 1-4-.002A2 2 0 0 1 6.94 5zM7 8.48H3V21h4V8.48zm6.32 0H9.34V21h3.94v-6.57c0-3.66 4.77-3.96 4.77 0V21H22v-7.93c0-6.17-7.06-5.94-8.72-2.91l.04-1.68z" /></svg>
								LinkedIn
							</a>
						<?php endif; ?>

						<?php if ($youtube) : ?>
							<a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="nofollow noopener noreferrer" class="hr-author-social-btn hr-author-social-btn--youtube" aria-label="<?php echo esc_attr("YouTube channel of {$author->display_name}"); ?>">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M21.8 8s-.2-1.4-.8-2c-.8-.8-1.6-.8-2-.9C16.3 5 12 5 12 5s-4.3 0-7 .1c-.4 0-1.2.1-2 .9-.6.6-.8 2-.8 2S2 9.6 2 11.2v1.5c0 1.6.2 3.2.2 3.2s.2 1.4.8 2c.8.8 1.8.8 2.3.9C6.9 19 12 19 12 19s4.3 0 7-.2c.4 0 1.2-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.5C22 9.6 21.8 8 21.8 8zM9.7 14.5V9l5.3 2.8-5.3 2.7z" /></svg>
								YouTube
							</a>
						<?php endif; ?>

						<?php if ($twitter) : ?>
							<a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="nofollow noopener noreferrer" class="hr-author-social-btn hr-author-social-btn--twitter" aria-label="<?php echo esc_attr("X (Twitter) profile of {$author->display_name}"); ?>">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
								X / Twitter
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ BIO ============ -->
	<?php if ($description) : ?>
		<section class="hr-author-section" aria-labelledby="hr-author-bio-heading">
			<div class="hr-author-container">
				<span class="hr-author-section-label">Professional Background</span>
				<h2 class="hr-author-section-title" id="hr-author-bio-heading">About <?php echo $display_name; ?></h2>
				<div class="hr-author-bio-card">
					<div class="hr-author-bio-body">
						<?php echo wp_kses_post($description); ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ RECENT ARTICLES ============ -->
	<?php if ($recent_posts_query->have_posts()) : ?>
		<section class="hr-author-section" aria-labelledby="hr-author-articles-heading">
			<div class="hr-author-container">
				<div class="hr-author-articles-header">
					<div>
						<span class="hr-author-section-label">Latest Content</span>
						<h2 class="hr-author-section-title" id="hr-author-articles-heading"><?php echo $display_name; ?>&rsquo;s Recent Articles</h2>
					</div>
					<a href="<?php echo esc_url(home_url('/blog/')); ?>" class="hr-author-view-all">
						Explore all blogs
						<svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" /></svg>
					</a>
				</div>

				<div class="hr-author-articles-grid">
					<?php while ($recent_posts_query->have_posts()) : $recent_posts_query->the_post(); ?>
						<article class="hr-author-article-card" itemscope itemtype="https://schema.org/BlogPosting">
							<div class="hr-author-article-media">
								<?php if (has_post_thumbnail()) : ?>
									<a href="<?php the_permalink(); ?>" class="hr-author-article-thumb-link" tabindex="-1" aria-hidden="true">
										<?php the_post_thumbnail('medium', ['class' => 'hr-author-article-thumb', 'loading' => 'lazy', 'itemprop' => 'image']); ?>
									</a>
								<?php else : ?>
									<div class="hr-author-article-thumb-placeholder" aria-hidden="true">
										<svg viewBox="0 0 48 48" width="40" height="40" fill="none" stroke="#1b3c74" stroke-width="1.5"><rect x="6" y="10" width="36" height="28" rx="4" /><path d="M6 32l10-10 6 6 8-10 12 12" /></svg>
									</div>
								<?php endif; ?>
								<?php $cats = get_the_category(); if ($cats) : ?>
									<a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" class="hr-author-article-cat" itemprop="articleSection"><?php echo esc_html($cats[0]->name); ?></a>
								<?php endif; ?>
							</div>

							<div class="hr-author-article-body">
								<h3 class="hr-author-article-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<div class="hr-author-article-footer">
									<time class="hr-author-article-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>" itemprop="datePublished"><?php echo esc_html(get_the_date('M j, Y')); ?></time>
									<a href="<?php the_permalink(); ?>" class="hr-author-article-read-more">Read more
										<svg viewBox="0 0 16 16" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" /></svg>
									</a>
								</div>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ TRUST STRIP ============ -->
	<section class="hr-author-trust" aria-label="Certifications and standards">
		<div class="hr-author-container">
			<p>Certified &amp; compliant with Indian and global health standards</p>
			<div class="hr-author-trust__badges">
				<span>NHA Approved</span><span>ABDM Compliant</span><span>HIPAA</span><span>FHIR</span><span>SNOMED CT</span><span>ICD-10/11</span><span>CERT-In</span>
			</div>
		</div>
	</section>

	<!-- ============ CTA ============ -->
	<section class="hr-author-cta-section" aria-label="Contact Healthray">
		<div class="hr-author-container">
			<div class="hr-author-cta-card">
				<div class="hr-author-cta-deco" aria-hidden="true"></div>
				<span class="hr-author-cta-kicker">AI-powered · ABDM compliant · Safe &amp; Secure</span>
				<h3 class="hr-author-cta-title">See your hospital on Healthray</h3>
				<p class="hr-author-cta-sub">A 30-minute demo on your own workflows &ndash; OPD, billing, pharmacy and ABHA &ndash; with the cloud HMS trusted by 2,500+ hospitals across India.</p>
				<ul class="hr-author-cta-list">
					<li>Free, no-obligation walkthrough</li>
					<li>Data migration &amp; training included</li>
					<li>Support in English, Hindi &amp; Gujarati</li>
				</ul>
				<div class="hr-author-cta-btns">
					<a href="https://calendly.com/healthray/healthray-technologies-lead-meeting" class="hr-author-cta-btn hr-author-cta-btn--primary" target="_blank" rel="noopener noreferrer">Book a Free Demo</a>
					<a href="<?php echo esc_url(home_url('/contact/')); ?>" class="hr-author-cta-btn hr-author-cta-btn--secondary">Contact Us</a>
				</div>
				<p class="hr-author-cta-note">Our team replies within one business day.</p>
			</div>
		</div>
	</section>

</div>