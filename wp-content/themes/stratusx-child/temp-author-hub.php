<?php
/*
 * Template Name: Author Hub
 */

if (!defined('ABSPATH')) {
    exit;
}

/** User IDs never listed, even if they have posts (e.g. a generic admin account). */
$hub_exclude_ids = [1];

/* ── Authors with published blog posts, most articles first ── */
$hub_users = get_users([
    'has_published_posts' => ['post'],
    'exclude'             => $hub_exclude_ids,
    'fields'              => ['ID', 'display_name', 'user_nicename'],
]);

global $wpdb;

/* Top categories per author in one query (skips "Uncategorized"). */
$hub_topics = [];
if ($hub_users) {
    $ids = implode(',', array_map('intval', wp_list_pluck($hub_users, 'ID')));
    $rows = $wpdb->get_results(
        "SELECT p.post_author AS author, t.term_id, t.name, COUNT(*) AS n
           FROM {$wpdb->posts} p
           JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
           JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = 'category'
           JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
          WHERE p.post_type = 'post' AND p.post_status = 'publish' AND p.post_author IN ($ids)
            AND t.slug <> 'uncategorized'
          GROUP BY p.post_author, t.term_id, t.name
          ORDER BY n DESC, t.name ASC"
    );
    foreach ((array) $rows as $r) {
        // Term names are stored HTML-encoded ("Billing &amp; Claims") - decode once; esc_html() re-encodes on output.
        $hub_topics[(int) $r->author][] = ['id' => (int) $r->term_id, 'name' => html_entity_decode($r->name, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'n' => (int) $r->n];
    }
}

/** Profile photo: ACF "user_profile_photo" (ID / URL / array) when set, otherwise the Gravatar. */
function healthray_hub_author_photo($user_id, $size = 240)
{
    $photo = function_exists('get_field') ? get_field('user_profile_photo', 'user_' . $user_id) : '';
    if (is_array($photo) && !empty($photo['url'])) {
        return $photo['sizes']['medium'] ?? $photo['url'];
    }
    if (is_numeric($photo) && (int) $photo > 0) {
        $src = wp_get_attachment_image_url((int) $photo, 'medium');
        if ($src) {
            return $src;
        }
    }
    if (is_string($photo) && filter_var($photo, FILTER_VALIDATE_URL)) {
        return $photo;
    }

    /* Gravatar: build the URL directly from the user's email (d=mp = mystery person if no Gravatar exists). */
    $user = get_userdata($user_id);
    if ($user && !empty($user->user_email)) {
        $hash = md5(strtolower(trim($user->user_email)));
        return 'https://secure.gravatar.com/avatar/' . $hash . '?s=' . (int) $size . '&d=mp&r=g';
    }

    /* No email at all: Gravatar's default silhouette. */
    return 'https://secure.gravatar.com/avatar/00000000000000000000000000000000?s=' . (int) $size . '&d=mp&f=y';
}

$hub_authors = [];
$hub_total_posts = 0;
$hub_all_topics = [];
foreach ($hub_users as $u) {
    $id    = (int) $u->ID;
    $count = (int) count_user_posts($id, 'post', true);
    if ($count < 1) {
        continue;
    }
    $latest = get_posts([
        'author'         => $id,
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ]);
    $topics = array_slice($hub_topics[$id] ?? [], 0, 3);
    foreach ($hub_topics[$id] ?? [] as $t) {
        $hub_all_topics[$t['id']] = $t['name'];
    }
    $hub_total_posts += $count;
    $hub_authors[] = [
        'id'       => $id,
        'name'     => $u->display_name,
        'url'      => get_author_posts_url($id, $u->user_nicename),
        'role'     => trim((string) get_user_meta($id, 'user_position', true)),
        'bio'      => wp_strip_all_tags((string) get_user_meta($id, 'description', true)),
        'linkedin' => esc_url_raw((string) get_user_meta($id, 'linkedin', true)),
        'photo'    => healthray_hub_author_photo($id),
        'count'    => $count,
        'topics'   => $topics,
        'latest'   => $latest ? $latest[0] : null,
    ];
}
usort($hub_authors, fn($a, $b) => $b['count'] <=> $a['count'] ?: strcasecmp($a['name'], $b['name']));

/* Latest three articles across all listed authors */
$hub_latest = $hub_authors ? get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'author__in'     => wp_list_pluck($hub_authors, 'id'),
    'no_found_rows'  => true,
]) : [];

$hub_page_url = get_permalink();
?>

<style>
	.author-hub { --hub-radius: var(--hr-radius-card, 18px); --hub-shadow: var(--hr-shadow-card, 0 8px 24px rgba(15, 23, 42, .06)); background: #fff; }
	.author-hub { font-family: var(--font-body, Mulish, system-ui, sans-serif); color: var(--ink, #122033); line-height: 1.6; font-size: 16px; -webkit-font-smoothing: antialiased; }
	.author-hub *, .author-hub *::before, .author-hub *::after { box-sizing: border-box; }
	.author-hub img, .author-hub svg { display: block; max-width: 100%; height: auto; }
	.author-hub a { color: inherit; text-decoration: none; }
	.author-hub p { color: var(--ink-soft, #4B5B70); }
	.author-hub ul, .author-hub ol { margin: 0; padding: 0; }
	.author-hub h1, .author-hub h2, .author-hub h3 { font-family: var(--font-display, Mulish, system-ui, sans-serif); line-height: 1.15; letter-spacing: -.01em; }
	.author-hub .wrap { max-width: var(--maxw, 1284px); margin: 0 auto; padding: 0 24px; }
	.author-hub .crumbs { font-size: .8rem; color: var(--ink-soft, #4B5B70); }
	.author-hub .crumbs ol { list-style: none; display: flex; flex-wrap: wrap; gap: 8px; padding: 0; margin: 0; }
	.author-hub .crumbs li { margin: 0; }
	.author-hub .crumbs li + li::before { content: ">"; margin-right: 8px; color: var(--line, #DCE5F0); }
	.author-hub .crumbs a { color: var(--brand, #2563EB); }
	.author-hub .crumbs a:hover { text-decoration: underline; }
	.author-hub .crumbs [aria-current] { color: var(--ink-soft, #4B5B70); }
	.author-hub .eyebrow { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 14px; font-size: .78rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--brand, #2563EB); }
	.author-hub .eyebrow::before { content: ""; width: 22px; height: 2px; border-radius: 2px; background: var(--brand, #2563EB); }
	.author-hub .hero-sub { font-size: 1.1rem; }
	.author-hub .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 13px 24px; border: 0; border-radius: 10px; font-weight: 600; font-size: .95rem; line-height: 1.2; transition: transform .15s, background .15s, border-color .15s, color .15s, box-shadow .15s; }
	.author-hub .btn-primary { background: var(--brand, #2563EB); color: #fff; box-shadow: 0 6px 18px var(--brand-glow, rgba(37, 99, 235, .12)); }
	.author-hub .btn-primary:hover { background: var(--brand-dark, #1D4ED8); color: #fff; transform: translateY(-2px); }
	.author-hub .btn-ghost { background: #fff; color: var(--ink, #122033); border: 1.5px solid var(--line, #DCE5F0); }
	.author-hub .btn-ghost:hover { border-color: var(--brand, #2563EB); color: var(--brand, #2563EB); }
	.author-hub :focus-visible { outline: 3px solid var(--brand, #2563EB); outline-offset: 3px; border-radius: 6px; }
	@media (max-width: 720px) { .author-hub .wrap { padding: 0 16px; } }
	.author-hub .hub-hero { padding: 28px 0 56px; background: var(--hr-hero-gradient); border-bottom: 1px solid var(--hr-line, #e2e8f0); }
	.author-hub .hub-hero .crumbs { padding: 0 0 26px; }
	.author-hub .hub-hero h1 { margin: 6px 0 0; font-size: var(--hr-h1-size, clamp(30px, 4.5vw, 50px)); font-weight: 800; line-height: 1.15; letter-spacing: -.02em; color: var(--hr-navy, #1b3c74); text-transform: none; max-width: 820px; }
	.author-hub .hub-hero h1 span { color: var(--brand); }
	.author-hub .hub-hero .hero-sub { max-width: 680px; margin: 18px 0 28px; }
	.author-hub .hub-stats { display: flex; flex-wrap: wrap; gap: 12px; margin: 0; padding: 0; list-style: none; }
	.author-hub .hub-stats li { display: flex; align-items: baseline; gap: 8px; padding: 12px 18px; background: #fff; border: 1px solid var(--line); border-radius: 14px; box-shadow: var(--hub-shadow); font-size: .9rem; color: var(--ink-soft); }
	.author-hub .hub-stats strong { font-size: 1.35rem; font-weight: 800; color: var(--hr-navy, #1b3c74); }

	.author-hub .hub-list { padding: 56px 0 24px; }
	.author-hub .hub-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 28px; }
	.author-hub .hub-toolbar h2 { margin: 0; font-size: var(--hr-h2-size, clamp(24px, 4vw, 36px)); font-weight: 800; color: var(--hr-navy, #1b3c74); text-transform: none; }
	.author-hub .hub-controls { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
	.author-hub .hub-search { position: relative; }
	.author-hub .hub-search input { width: 280px; max-width: 100%; height: 46px; padding: 0 42px 0 16px; border: 1.5px solid var(--line); border-radius: 12px; background: #fff; font: inherit; font-size: .93rem; color: var(--ink); transition: border-color .15s, box-shadow .15s; }
	.author-hub .hub-search input:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-glow); }
	.author-hub .hub-search svg { position: absolute; right: 14px; top: 50%; width: 18px; height: 18px; transform: translateY(-50%); stroke: var(--ink-soft); pointer-events: none; }
	.author-hub .hub-sort { height: 46px; padding: 0 36px 0 14px; border: 1.5px solid var(--line); border-radius: 12px; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%234B5B70' stroke-width='2.5'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 14px center; font: inherit; font-size: .93rem; color: var(--ink); appearance: none; cursor: pointer; }
	.author-hub .hub-sort:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-glow); }
	.author-hub .hub-count { margin: -14px 0 22px; font-size: .88rem; color: var(--ink-soft); min-height: 1.3em; }

	.author-hub .hub-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; margin: 0; padding: 0; list-style: none; }
	.author-hub .hub-card { display: flex; flex-direction: column; height: 100%; padding: 26px 24px 22px; background: #fff; border: 1px solid var(--hr-line, #e2e8f0); border-radius: var(--hub-radius); box-shadow: var(--hub-shadow); transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
	.author-hub .hub-card:hover { transform: translateY(-3px); border-color: var(--hr-line-strong, #d9e2f2); box-shadow: 0 16px 34px rgba(var(--hr-ink-strong-rgb, 15, 23, 42), .10); }
	.author-hub .hub-card[hidden] { display: none; }
	.author-hub .hub-card-head { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
	.author-hub .hub-avatar { position: relative; flex: 0 0 auto; width: 84px; height: 84px; padding: 3px; border-radius: 50%; background: #fff; border: 1px solid var(--hr-line-strong, #d9e2f2); box-shadow: var(--hr-shadow-soft, 0 14px 30px rgba(15, 23, 42, .06)); }
	.author-hub .hub-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
	.author-hub .hub-avatar .hub-badge { position: absolute; right: -2px; bottom: -2px; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--hr-blue, #0f68ea); border: 3px solid #fff; }
	.author-hub .hub-avatar .hub-badge svg { width: 12px; height: 12px; fill: none; stroke: #fff; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; }
	.author-hub .hub-name { margin: 0 0 4px; font-size: 1.2rem; font-weight: 800; line-height: 1.3; text-transform: none; }
	.author-hub .hub-name a { color: var(--hr-ink-strong, #0f172a); }
	.author-hub .hub-name a:hover { color: var(--brand); }
	.author-hub .hub-role { margin: 0; font-size: .86rem; font-weight: 600; color: var(--brand-deep, #0F2A66); line-height: 1.4; }
	.author-hub .hub-bio { margin: 0 0 16px; font-size: .93rem; line-height: 1.65; color: var(--ink-soft); }
	.author-hub .hub-topics { display: flex; flex-wrap: wrap; gap: 6px; margin: 0 0 16px; padding: 0; list-style: none; }
	.author-hub .hub-topics a { display: inline-block; padding: 4px 11px; border-radius: 999px; background: var(--brand-tint); border: 1px solid var(--hr-line-blue, #cfdcfa); font-size: .76rem; font-weight: 700; color: var(--brand-deep, #0F2A66); transition: background .15s, color .15s; }
	.author-hub .hub-topics a:hover { background: var(--brand); border-color: var(--brand); color: #fff; }
	.author-hub .hub-latest { margin: 0 0 18px; padding: 12px 14px; border-radius: 12px; background: var(--hr-wash-sky, #f4f7ff); font-size: .86rem; line-height: 1.45; }
	.author-hub .hub-latest span { display: block; margin-bottom: 3px; font-size: .7rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--ink-soft); }
	.author-hub .hub-latest a { font-weight: 700; color: var(--hr-navy, #1b3c74); }
	.author-hub .hub-latest a:hover { color: var(--brand); text-decoration: underline; }
	.author-hub .hub-card-foot { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: auto; padding-top: 16px; border-top: 1px solid var(--hr-line-faint, #f0f3f8); }
	.author-hub .hub-posts { font-size: .86rem; color: var(--ink-soft); }
	.author-hub .hub-posts strong { color: var(--ink); }
	.author-hub .hub-actions { display: flex; align-items: center; gap: 8px; }
	.author-hub .hub-profile { display: inline-flex; align-items: center; gap: 6px; padding: 9px 14px; border-radius: 10px; background: var(--brand); color: #fff; font-size: .85rem; font-weight: 700; box-shadow: 0 6px 18px var(--brand-glow); transition: background .15s, transform .15s; }
	.author-hub .hub-profile:hover { background: var(--brand-dark); color: #fff; transform: translateY(-1px); }
	.author-hub .hub-profile svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2.5; }
	.author-hub .hub-li { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 10px; border: 1px solid var(--hr-line, #e2e8f0); background: #fff; color: #0a66c2; transition: background .15s, color .15s, border-color .15s; }
	.author-hub .hub-li:hover { background: #0a66c2; border-color: #0a66c2; color: #fff; }
	.author-hub .hub-li svg { width: 17px; height: 17px; fill: currentColor; }
	.author-hub .hub-empty { padding: 40px 20px; text-align: center; border: 1px dashed var(--line); border-radius: var(--hub-radius); color: var(--ink-soft); }
	.author-hub .hub-empty button { margin-top: 10px; padding: 8px 16px; border: 1.5px solid var(--line); border-radius: 10px; background: #fff; font: inherit; font-weight: 700; color: var(--brand); cursor: pointer; }
	.author-hub mark { background: #FFF1B8; color: inherit; border-radius: 3px; padding: 0 2px; }

	.author-hub .hub-articles { padding: 40px 0 16px; }
	.author-hub .hub-articles h2 { margin: 0 0 24px; font-size: var(--hr-h2-size, clamp(24px, 4vw, 36px)); font-weight: 800; color: var(--hr-navy, #1b3c74); text-transform: none; }
	.author-hub .hub-article-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
	.author-hub .hub-article { display: flex; flex-direction: column; padding: 14px; background: #fff; border: 1px solid var(--hr-line, #e2e8f0); border-radius: var(--hub-radius); box-shadow: var(--hub-shadow); transition: transform .2s ease, box-shadow .2s ease; }
	.author-hub .hub-article:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(var(--hr-ink-strong-rgb, 15, 23, 42), .10); }
	.author-hub .hub-article-media { display: block; overflow: hidden; border-radius: 12px; aspect-ratio: 16 / 10; background: var(--brand-tint); }
	.author-hub .hub-article-media img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s ease; }
	.author-hub .hub-article:hover .hub-article-media img { transform: scale(1.03); }
	.author-hub .hub-article h3 { margin: 14px 4px 10px; font-size: 1.05rem; font-weight: 700; line-height: 1.4; text-transform: none; }
	.author-hub .hub-article h3 a { color: var(--hr-ink-strong, #0f172a); }
	.author-hub .hub-article h3 a:hover { color: var(--brand); }
	.author-hub .hub-article-meta { margin: auto 4px 2px; display: flex; flex-wrap: wrap; gap: 6px 12px; font-size: .82rem; color: var(--ink-soft); }
	.author-hub .hub-article-meta a { font-weight: 700; color: var(--hr-navy, #1b3c74); }

	.author-hub .hub-cta { padding: 48px 0 72px; }
	.author-hub .hub-cta-card { position: relative; overflow: hidden; text-align: center; padding: clamp(28px, 4vw, 48px); background: var(--hr-tint, #f3f6ff); border: 1px solid var(--hr-line-strong, #d9e2f2); border-radius: 16px; box-shadow: 0 4px 18px rgba(var(--hr-ink-strong-rgb, 15, 23, 42), .06); }
	.author-hub .hub-cta-card::before { content: ""; position: absolute; right: -80px; top: -80px; width: 260px; height: 260px; border-radius: 50%; background: radial-gradient(circle, rgba(var(--hr-blue-rgb, 15, 104, 234), .12), transparent 70%); pointer-events: none; }
	.author-hub .hub-cta-card > * { position: relative; }
	.author-hub .hub-cta-kicker { display: inline-block; margin-bottom: 10px; font-size: 11px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--hr-blue, #0f68ea); }
	.author-hub .hub-cta-card h2 { margin: 0 0 12px; font-size: clamp(24px, 3vw, 32px); font-weight: 800; line-height: 1.25; letter-spacing: -.02em; color: var(--hr-ink-strong, #0f172a); text-transform: none; }
	.author-hub .hub-cta-card p { max-width: 640px; margin: 0 auto 22px; font-size: 1.05rem; line-height: 1.7; }
	.author-hub .hub-cta-btns { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }

	@media (max-width: 1080px) {
		.author-hub .hub-grid,
		.author-hub .hub-article-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
	}
	@media (max-width: 720px) {
		.author-hub .hub-hero { padding: 20px 0 40px; }
		.author-hub .hub-list { padding-top: 40px; }
		.author-hub .hub-grid,
		.author-hub .hub-article-grid { grid-template-columns: 1fr; gap: 18px; }
		.author-hub .hub-controls,
		.author-hub .hub-search,
		.author-hub .hub-search input { width: 100%; }
		.author-hub .hub-sort { flex: 1; }
		.author-hub .hub-stats li { flex: 1 1 calc(50% - 12px); }
		.author-hub .hub-card { padding: 22px 18px 18px; }
	}
	@media (prefers-reduced-motion: reduce) {
		.author-hub .hub-card,
		.author-hub .hub-article,
		.author-hub .hub-article-media img { transition: none; }
	}
</style>

<main class="author-hub">

	<section class="hub-hero" aria-labelledby="hub-title">
		<div class="wrap">
			<nav class="crumbs" aria-label="Breadcrumb">
				<ol>
					<li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
					<li aria-current="page">Our Authors</li>
				</ol>
			</nav>
			<span class="eyebrow">Our Authors</span>
			<h1 id="hub-title">The people behind <span>Healthray's insights</span></h1>
			<p class="hero-sub">Every article on the Healthray blog is written by someone who works on hospital software every day - founders, product managers, implementation leads and specialist writers. Meet them, see what they cover and read their latest work.</p>
			<ul class="hub-stats" aria-label="Author hub at a glance">
				<li><strong><?php echo esc_html(number_format_i18n(count($hub_authors))); ?></strong> authors</li>
				<li><strong><?php echo esc_html(number_format_i18n($hub_total_posts)); ?></strong> published articles</li>
				<li><strong><?php echo esc_html(number_format_i18n(count($hub_all_topics))); ?></strong> topics covered</li>
			</ul>
		</div>
	</section>

	<section class="hub-list" aria-labelledby="hub-list-title">
		<div class="wrap">
			<div class="hub-toolbar">
				<h2 id="hub-list-title">Meet our authors</h2>
				<div class="hub-controls">
					<div class="hub-search">
						<input type="search" id="hubSearch" placeholder="Search by name, role or topic" aria-label="Search authors by name, role or topic" autocomplete="off">
						<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2" /><path d="M21 21l-4.3-4.3" stroke-width="2" stroke-linecap="round" /></svg>
					</div>
					<select class="hub-sort" id="hubSort" aria-label="Sort authors">
						<option value="posts">Most articles</option>
						<option value="name">Name A-Z</option>
						<option value="recent">Recently published</option>
					</select>
				</div>
			</div>
			<p class="hub-count" id="hubCount" aria-live="polite"></p>

			<?php if ($hub_authors) : ?>
				<ul class="hub-grid" id="hubGrid">
					<?php foreach ($hub_authors as $i => $a) :
						$topic_names = implode(' ', wp_list_pluck($a['topics'], 'name'));
						$latest_ts   = $a['latest'] ? (int) get_post_time('U', true, $a['latest']) : 0;
						$bio         = $a['bio'] !== '' ? wp_trim_words($a['bio'], 28, '…') : '';
					?>
						<li class="hub-card"
							data-name="<?php echo esc_attr($a['name']); ?>"
							data-posts="<?php echo esc_attr($a['count']); ?>"
							data-recent="<?php echo esc_attr($latest_ts); ?>"
							data-order="<?php echo esc_attr($i); ?>"
							data-search="<?php echo esc_attr(strtolower($a['name'] . ' ' . $a['role'] . ' ' . $topic_names . ' ' . $a['bio'])); ?>">
							<div class="hub-card-head">
								<a class="hub-avatar" href="<?php echo esc_url($a['url']); ?>" tabindex="-1" aria-hidden="true">
								    <img src="<?php echo esc_url($a['photo']); ?>" alt="" width="84" height="84" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='https://secure.gravatar.com/avatar/00000000000000000000000000000000?s=240&d=mp&f=y';">
									<span class="hub-badge"><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5" /></svg></span>
								</a>
								<div>
									<h3 class="hub-name"><a href="<?php echo esc_url($a['url']); ?>" data-hl><?php echo esc_html($a['name']); ?></a></h3>
									<?php if ($a['role'] !== '') : ?>
										<p class="hub-role" data-hl><?php echo esc_html($a['role']); ?></p>
									<?php endif; ?>
								</div>
							</div>

							<?php if ($bio !== '') : ?>
								<p class="hub-bio"><?php echo esc_html($bio); ?></p>
							<?php endif; ?>

							<?php if ($a['topics']) : ?>
								<ul class="hub-topics" aria-label="Topics <?php echo esc_attr($a['name']); ?> writes about">
									<?php foreach ($a['topics'] as $t) : ?>
										<li><a href="<?php echo esc_url(get_category_link($t['id'])); ?>" data-hl><?php echo esc_html($t['name']); ?></a></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<?php if ($a['latest']) : ?>
								<p class="hub-latest">
									<span>Latest article</span>
									<a href="<?php echo esc_url(get_permalink($a['latest'])); ?>"><?php echo esc_html(get_the_title($a['latest'])); ?></a>
								</p>
							<?php endif; ?>

							<div class="hub-card-foot">
								<span class="hub-posts"><strong><?php echo esc_html(number_format_i18n($a['count'])); ?></strong> <?php echo $a['count'] === 1 ? 'article' : 'articles'; ?></span>
								<div class="hub-actions">
									<?php if ($a['linkedin'] !== '') : ?>
										<a class="hub-li" href="<?php echo esc_url($a['linkedin']); ?>" target="_blank" rel="nofollow noopener noreferrer" aria-label="<?php echo esc_attr('LinkedIn profile of ' . $a['name']); ?>">
											<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z" /></svg>
										</a>
									<?php endif; ?>
									<a class="hub-profile" href="<?php echo esc_url($a['url']); ?>" aria-label="<?php echo esc_attr('View profile and articles of ' . $a['name']); ?>">
										View profile
										<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" /></svg>
									</a>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="hub-empty" id="hubEmpty" hidden>
					<p>No author matches "<strong id="hubEmptyTerm"></strong>".</p>
					<button type="button" id="hubClear">Show all authors</button>
				</div>
			<?php else : ?>
				<p class="hub-empty">Author profiles will appear here as soon as articles are published.</p>
			<?php endif; ?>
		</div>
	</section>

	<?php if ($hub_latest) : ?>
		<section class="hub-articles" aria-labelledby="hub-articles-title">
			<div class="wrap">
				<h2 id="hub-articles-title">Latest from our authors</h2>
				<div class="hub-article-grid">
					<?php foreach ($hub_latest as $p) :
						$author_id = (int) $p->post_author;
					?>
						<article class="hub-article">
							<a class="hub-article-media" href="<?php echo esc_url(get_permalink($p)); ?>" tabindex="-1" aria-hidden="true">
								<?php
								if (has_post_thumbnail($p)) {
									echo get_the_post_thumbnail($p, 'medium_large', ['loading' => 'lazy', 'decoding' => 'async']);
								}
								?>
							</a>
							<h3><a href="<?php echo esc_url(get_permalink($p)); ?>"><?php echo esc_html(get_the_title($p)); ?></a></h3>
							<p class="hub-article-meta">
								<span>By <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>"><?php echo esc_html(get_the_author_meta('display_name', $author_id)); ?></a></span>
								<time datetime="<?php echo esc_attr(get_the_date('c', $p)); ?>"><?php echo esc_html(get_the_date('', $p)); ?></time>
							</p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="hub-cta" aria-label="Contact Healthray">
		<div class="wrap">
			<div class="hub-cta-card">
				<span class="hub-cta-kicker">AI-powered · ABDM compliant · Safe &amp; Secure</span>
				<h2>See your hospital on Healthray</h2>
				<p>A 30-minute demo on your own workflows &ndash; OPD, billing, pharmacy and ABHA &ndash; with the cloud HMS trusted by 2,500+ hospitals across India.</p>
				<div class="hub-cta-btns">
					<a class="btn btn-primary" href="https://calendly.com/healthray/healthray-technologies-lead-meeting" target="_blank" rel="noopener noreferrer">Book a Free Demo</a>
					<a class="btn btn-ghost" href="<?php echo esc_url(home_url('/contact/')); ?>">Contact Us</a>
				</div>
			</div>
		</div>
	</section>

</main>

<?php
/* Structured data: the page as a list of Person entities (Yoast already outputs the WebPage/breadcrumb graph). */
if ($hub_authors) {
    $items = [];
    foreach ($hub_authors as $pos => $a) {
        $person = [
            '@type'    => 'Person',
            '@id'      => $a['url'] . '#person',
            'name'     => $a['name'],
            'url'      => $a['url'],
            'image'    => $a['photo'],
            'worksFor' => ['@type' => 'Organization', 'name' => 'Healthray', 'url' => home_url('/')],
        ];
        if ($a['role'] !== '') {
            $person['jobTitle'] = $a['role'];
        }
        if ($a['bio'] !== '') {
            $person['description'] = wp_trim_words($a['bio'], 40, '…');
        }
        if ($a['linkedin'] !== '') {
            $person['sameAs'] = [$a['linkedin']];
        }
        $items[] = ['@type' => 'ListItem', 'position' => $pos + 1, 'item' => $person];
    }
    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        '@id'             => $hub_page_url . '#authors',
        'name'            => 'Healthray authors',
        'url'             => $hub_page_url,
        'numberOfItems'   => count($items),
        'itemListElement' => $items,
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) . '</script>' . "\n";
}
?>

<script>
	/* Author Hub: search (name, role, topic, bio) + sort. No reveal-on-scroll here - script.js handles .reveal sitewide. */
	(function () {
		var grid = document.getElementById('hubGrid');
		if (!grid) return;
		var input = document.getElementById('hubSearch');
		var sort = document.getElementById('hubSort');
		var count = document.getElementById('hubCount');
		var empty = document.getElementById('hubEmpty');
		var emptyTerm = document.getElementById('hubEmptyTerm');
		var clear = document.getElementById('hubClear');
		var cards = Array.prototype.slice.call(grid.children);
		var total = cards.length;

		/* Remember each highlightable element's plain text once, so highlighting never stacks. */
		cards.forEach(function (card) {
			card.querySelectorAll('[data-hl]').forEach(function (el) { el.setAttribute('data-text', el.textContent); });
		});

		function highlight(el, q) {
			var text = el.getAttribute('data-text');
			var i = q ? text.toLowerCase().indexOf(q) : -1;
			if (i < 0) { el.textContent = text; return; }
			el.textContent = '';
			el.appendChild(document.createTextNode(text.slice(0, i)));
			var m = document.createElement('mark');
			m.textContent = text.slice(i, i + q.length);
			el.appendChild(m);
			el.appendChild(document.createTextNode(text.slice(i + q.length)));
		}

		function render() {
			var q = input.value.trim().toLowerCase();
			var shown = 0;
			cards.forEach(function (card) {
				var match = !q || card.getAttribute('data-search').indexOf(q) !== -1;
				card.hidden = !match;
				if (match) shown++;
				card.querySelectorAll('[data-hl]').forEach(function (el) { highlight(el, q); });
			});
			count.textContent = q
				? shown + ' of ' + total + ' authors match "' + input.value.trim() + '"'
				: 'Showing all ' + total + ' authors';
			empty.hidden = shown !== 0;
			if (!shown) emptyTerm.textContent = input.value.trim();
		}

		function reorder() {
			var key = sort.value;
			cards.sort(function (a, b) {
				if (key === 'name') return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
				if (key === 'recent') return (+b.getAttribute('data-recent')) - (+a.getAttribute('data-recent'));
				return (+a.getAttribute('data-order')) - (+b.getAttribute('data-order'));
			});
			cards.forEach(function (card) { grid.appendChild(card); });
		}

		input.addEventListener('input', render);
		sort.addEventListener('change', reorder);
		clear.addEventListener('click', function () { input.value = ''; render(); input.focus(); });
		render();
	})();
</script>