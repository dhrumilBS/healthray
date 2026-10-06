<?php
$faq_query = new WP_Query([
    'post_type' => 'faq',       // ← your CPT slug
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'menu_order title',
    'order' => 'ASC',
]);

$faqs = [];
if ($faq_query->have_posts()):
    while ($faq_query->have_posts()):
        $faq_query->the_post();
        $faqs[] = [
            'id' => get_the_ID(),
            'title' => get_the_title(),
            'link' => get_permalink(),
            'desc' => get_the_excerpt()
        ];
    endwhile;
    wp_reset_postdata();
endif;

// Page description
global $post;
$pg = get_queried_object();
$desc = 'Browse our most commonly asked questions below';
?>
<style>
    /* ════════════════ TOKENS ════════════════ */
    .fmc {
        --ink: #0d0d14;
        --ink-2: #5a5a74;
        --ink-3: #9898aa;
        --bg: #f2f2f7;
        --white: #ffffff;
        --border: #e2e2ec;
        --accent: #4f46e5;
        --acc-lt: #eef2ff;
        --acc-glow: rgba(79, 70, 229, .10);
        --acc-ring: rgba(79, 70, 229, .22);
        --hl: #fde68a;
        --shadow-sm: 0 2px 10px rgba(13, 13, 20, .06);
        --shadow-md: 0 8px 32px rgba(13, 13, 20, .10);
        --ease: cubic-bezier(.4, 0, .2, 1);
        -webkit-font-smoothing: antialiased;
        color: var(--ink);
        background: var(--bg);
    }

    /* ════════════════ HERO ════════════════ */
    .fmc-hero-in {
        position: relative;
        z-index: 1;
        max-width: 640px;
        margin: 0 auto;
    }

    .fmc-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: #a5b4fc;
        background: rgba(165, 180, 252, .1);
        border: 1px solid rgba(165, 180, 252, .22);
        padding: 5px 16px;
        border-radius: 100px;
        margin-bottom: 22px;
    }

    .fmc-hero h1 {
        font-size: clamp(36px, 6vw, 60px);
    }

    /* ════════════════ BODY ════════════════ */
    .fmc-body {
        max-width: 1200px;
        margin: 0 auto;
        padding: 60px 10px;
    }

    .fmc-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 32px;
    }

    .fmc-topbar h2 {
        font-size: 20px;
    }

    .fmc-count {
        font-size: 12px;
        font-weight: 500;
        color: var(--ink-2);
        background: var(--white);
        border: 1px solid var(--border);
        padding: 5px 15px;
        border-radius: 100px;
        box-shadow: var(--shadow-sm);
    }

    /* ════════════════ MASONRY GRID ════════════════ */
    .fmc-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        column-gap: 18px;
    }

    .fmc-card {
        padding: 22px 22px 20px;
        break-inside: avoid;
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 18px;
        margin-bottom: 18px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: border-color .22s var(--ease), box-shadow .22s var(--ease), transform .22s var(--ease);
        position: relative;
        cursor: pointer;
    }

    .fmc-card:hover {
        border-color: #c7d2fe;
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    /* Card header */
    .fmc-card-head {
        display: flex;
        align-items: center;
        gap: 14px;
        user-select: none;
    }

    .fmc-q-badge {
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        border-radius: 9px;
        background: var(--bg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: var(--ink-2);
        margin-top: 1px;
        transition: background .2s, color .2s;
    }

    .fmc-q-title {
        flex: 1;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.45;
    }

    .fmc-q-text p {
        font-size: 15px;
        margin-top: 12px;
    }

    .fmc-card-link {
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    .fmc-card-btn {
        margin-top: 12px;
    }

    /* ── Animation ── */
    @keyframes fmcUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── Responsive ── */
    @media (max-width: 780px) {
        .fmc-grid {
            grid-template-columns: repeat(1, 1fr);
        }

        .fmc-hero {
            padding: 60px 20px 54px;
        }
    }

    @media (max-width: 500px) {
        .fmc-card-body-in {
            padding-left: 22px;
        }

        .fmc-body {
            padding: 30px 10px;
        }
    }
</style>

<div class="fmc">

    <!-- ══ HERO ══════════════════════════════════════════════ -->
    <section class="blog-hero hero-section fmc-hero">
        <div class="container">
            <div class="heading text-center">
                <div class="fmc-eyebrow">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                    Help Center
                </div>
                <h1>Frequently<br><em>Asked Questions</em></h1>
                <p><?= esc_html($desc) ?></p>
            </div>
        </div>
    </section>

    <!-- ══ MASONRY GRID ══════════════════════════════════════ -->
    <div class="fmc-body">

        <?php if (!empty($faqs)): ?>
            <div class="fmc-topbar">
                <h2>All Questions</h2>
                <span class="fmc-count" id="fmc-count"><?= count($faqs) ?> questions</span>
            </div>

            <div class="fmc-grid" id="fmc-grid">
                <?php foreach ($faqs as $idx => $faq): ?>
                    <div class="fmc-card" role="button" tabindex="0" aria-expanded="false">
                        <div class="fmc-card-head">
                            <span class="fmc-q-badge"><?= $idx + 1 ?> </span>
                            <span class="fmc-q-title"><?= esc_html($faq['title']) ?> </span>
                        </div>
                        <div class="fmc-q-text">
                            <p class=""><?= esc_html($faq['desc']) ?></p>
                        </div>
                        <a href="<?= $faq['link']; ?>" class="fmc-card-btn btn-home"> View Q&amp;A </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>