<style>
  @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap');

  :root {
    --bg: #f0f4f8;
    --surface: rgba(255,255,255,0.72);
    --surface-border: rgba(255,255,255,0.9);
    --text-primary: #0f1923;
    --text-muted: #5a6a7a;
    --accent: #2563eb;
    --accent-light: rgba(37,99,235,0.08);
    --green: #16a34a;
    --green-bg: #dcfce7;
    --red: #dc2626;
    --red-bg: #fee2e2;
    --amber: #b45309;
    --amber-bg: #fef3c7;
    --radius: 16px;
    --shadow: 0 8px 32px rgba(15,25,35,0.08);
    --shadow-hover: 0 20px 48px rgba(15,25,35,0.14);
  }

  .cpt-browser * { box-sizing: border-box; margin: 0; padding: 0; }

  .cpt-browser {
    font-family: Mulish, system-ui, sans-serif;
    background: var(--bg);
    min-height: 100vh;
    padding: 48px 24px 64px;
  }

  /* Header */
  .cpt-header { text-align: center; margin-bottom: 36px; }
  .cpt-header h1 { font-size: clamp(26px,4vw,40px); font-weight: 700; color: var(--text-primary); letter-spacing: -0.5px; }
  .cpt-header p { color: var(--text-muted); margin-top: 6px; font-size: 15px; }

  /* Controls */
  .cpt-controls { max-width: 900px; margin: 0 auto 32px; display: flex; flex-direction: column; gap: 16px; align-items: center; }

  .cpt-search { width: 100%; max-width: 440px; position: relative; }
  .cpt-search svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; }
  .cpt-search input { width: 100%; padding: 12px 16px 12px 42px; border-radius: 12px; border: 1.5px solid #dde3ec; background: var(--surface); font-family: 'Sora', sans-serif; font-size: 14px; color: var(--text-primary); outline: none; transition: border-color .2s, box-shadow .2s; }
  .cpt-search input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-light); }
  .cpt-search input::placeholder { color: #9ba8b5; }

  /* Tabs */
  .cpt-tabs { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; }
  .cpt-tab { padding: 7px 16px; border-radius: 999px; border: 1.5px solid #dde3ec; background: var(--surface); font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 600; color: var(--text-muted); cursor: pointer; transition: all .2s; }
  .cpt-tab:hover { border-color: var(--accent); color: var(--accent); }
  .cpt-tab.active { background: var(--accent); border-color: var(--accent); color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,.25); }

  /* ── Toolbar ── */
  .cpt-toolbar {
    max-width: 1200px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding: 0 4px;
  }
  .cpt-stats { font-size: 13px; color: var(--text-muted); flex: 1; }

  /* Filter toggle */
  .cpt-filter-btn {
    padding: 7px 14px;
    border-radius: 999px;
    border: 1.5px solid #dde3ec;
    background: var(--surface);
    font-family: Mulish, system-ui, sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .cpt-filter-btn:hover { border-color: var(--amber); color: var(--amber); }
  .cpt-filter-btn.active { background: var(--amber-bg); border-color: var(--amber); color: var(--amber); }

  /* Bulk assign button */
  .cpt-bulk-btn {
    padding: 8px 18px;
    border-radius: 999px;
    border: none;
    background: var(--accent);
    font-family: Mulish, system-ui, sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    gap: 7px;
    box-shadow: 0 4px 12px rgba(37,99,235,.22);
  }
  .cpt-bulk-btn:hover:not(:disabled) { background: #1d4ed8; box-shadow: 0 6px 18px rgba(37,99,235,.32); transform: translateY(-1px); }
  .cpt-bulk-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

  /* Progress bar */
  .cpt-progress-wrap {
    max-width: 1200px;
    margin: 0 auto 18px;
    padding: 0 4px;
    display: none;
  }
  .cpt-progress-wrap.visible { display: block; }
  .cpt-progress-bar-bg {
    background: #e2e8f0;
    border-radius: 999px;
    height: 8px;
    overflow: hidden;
    margin-bottom: 6px;
  }
  .cpt-progress-bar-fill {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #2563eb, #38bdf8);
    width: 0%;
    transition: width .3s ease;
  }
  .cpt-progress-label { font-size: 12px; color: var(--text-muted); }

  /* Toast */
  .cpt-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 8px;
    pointer-events: none;
  }
  .cpt-toast-item {
    padding: 12px 18px;
    border-radius: 12px;
    font-family: Mulish, system-ui, sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
    animation: toastIn .3s ease forwards;
    pointer-events: auto;
  }
  .cpt-toast-item.success { background: #16a34a; }
  .cpt-toast-item.error   { background: #dc2626; }
  .cpt-toast-item.info    { background: #2563eb; }
  @keyframes toastIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
  @keyframes toastOut { from { opacity:1; } to { opacity:0; transform:translateY(8px); } }

  /* Grid */
  .cpt-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px,1fr)); gap: 22px; max-width: 1200px; margin: 0 auto; }

  /* Card */
  .cpt-card { background: var(--surface); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border: 1px solid var(--surface-border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; display: flex; flex-direction: column; transition: transform .28s ease, box-shadow .28s ease; }
  .cpt-card:hover { transform: translateY(-4px) scale(1.01); box-shadow: var(--shadow-hover); }
  .cpt-card.hidden { display: none; }

  /* Missing-thumb highlight */
  .cpt-card.no-thumb { border: 2px dashed #fbbf24; }
  .cpt-card.thumb-assigned { border: 2px solid var(--green); animation: cardGlow .6s ease; }
  @keyframes cardGlow { 0%,100% { box-shadow: var(--shadow); } 50% { box-shadow: 0 0 0 4px rgba(22,163,74,.25), var(--shadow-hover); } }

  .cpt-card-thumb { width: 100%; height: 155px; overflow: hidden; background: #e8edf3; flex-shrink: 0; position: relative; }
  .cpt-card-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s ease; }
  .cpt-card:hover .cpt-card-thumb img { transform: scale(1.04); }

  /* "No image" overlay */
  .cpt-no-img-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: repeating-linear-gradient(45deg, #f1f5f9, #f1f5f9 8px, #e8edf3 8px, #e8edf3 16px);
    color: #94a3b8;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
  }
  .cpt-no-img-overlay svg { opacity: .45; }

  /* Per-card assign btn */
  .cpt-card-assign-btn {
    position: absolute;
    bottom: 8px;
    right: 8px;
    padding: 5px 12px;
    border-radius: 999px;
    border: none;
    background: var(--accent);
    color: #fff;
    font-family: Mulish, system-ui, sans-serif;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(37,99,235,.35);
    display: flex;
    align-items: center;
    gap: 4px;
  }
  .cpt-card-assign-btn:hover:not(:disabled) { background: #1d4ed8; transform: scale(1.05); }
  .cpt-card-assign-btn:disabled { opacity: .55; cursor: not-allowed; }

  .cpt-card-body { padding: 16px 18px 18px; display: flex; flex-direction: column; flex: 1; gap: 8px; }

  .cpt-badge-row { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
  .cpt-badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; letter-spacing: .3px; text-transform: uppercase; }
  .cpt-badge-index   { background: var(--green-bg); color: var(--green); }
  .cpt-badge-noindex { background: var(--red-bg); color: var(--red); }
  .cpt-badge-type    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
  .cpt-badge-nothumb { background: var(--amber-bg); color: var(--amber); }

  .cpt-card-title { font-size: 16px; font-weight: 700; color: var(--text-primary); line-height: 1.35; }
  .cpt-card-title a { text-decoration: none; color: inherit; transition: color .2s; }
  .cpt-card-title a:hover { color: var(--accent); }

  .cpt-meta { display: flex; flex-direction: column; gap: 4px; font-size: 12px; color: var(--text-muted); }
  .cpt-meta code { font-family: 'JetBrains Mono', monospace; font-size: 11px; background: #f1f5f9; border: 1px solid #dde3ec; border-radius: 6px; padding: 2px 7px; display: inline-block; color: #4a5a6a; }

  .cpt-excerpt { font-size: 13px; color: var(--text-muted); line-height: 1.55; flex: 1; }

  .cpt-no-results { display: none; text-align: center; color: var(--text-muted); font-size: 15px; grid-column: 1 / -1; padding: 48px 0; }
  .cpt-no-results svg { display: block; margin: 0 auto 12px; opacity: .35; }
</style>

<?php

/* ── Query all public post types ───────────────────────────────────── */
$public_post_types = get_post_types(['public' => true], 'objects');
$all_type_slugs    = array_keys($public_post_types);

$args = [
  'post_type'      => $all_type_slugs,
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'date',
  'order'          => 'DESC',
];
$query       = new WP_Query($args);
$total       = $query->found_posts;
$default_img = 79101; // fallback image attachment ID

/* Collect used post types */
$used_types   = [];
$no_thumb_ids = [];
if ($query->have_posts()) {
  foreach ($query->posts as $p) {
    $used_types[$p->post_type] = true;
    if (!has_post_thumbnail($p->ID)) {
      $no_thumb_ids[] = $p->ID;
    }
  }
}
$no_thumb_count = count($no_thumb_ids);
?>

<div class="cpt-browser">

  <!-- Header -->
  <div class="cpt-header">
    <h1>Content Browser</h1>
    <p><?php echo esc_html($total); ?> published entries across all post types</p>
  </div>

  <!-- Search + Tabs -->
  <div class="cpt-controls">
    <div class="cpt-search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <input type="text" id="cptSearchInput" placeholder="Search by title or slug…" autocomplete="off">
    </div>
    <div class="cpt-tabs" id="cptTabs">
      <button class="cpt-tab active" data-type="all">All</button>
      <?php foreach ($used_types as $type_slug => $v):
        $type_obj = $public_post_types[$type_slug] ?? null;
        $label    = $type_obj ? esc_html($type_obj->labels->name) : esc_html(ucfirst($type_slug));
      ?>
        <button class="cpt-tab" data-type="<?php echo esc_attr($type_slug); ?>"><?php echo $label; ?></button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="cpt-toolbar">
    <div class="cpt-stats" id="cptStats"></div>

    <?php if ($no_thumb_count > 0): ?>
      <!-- "Show missing only" toggle -->
      <button class="cpt-filter-btn" id="cptMissingFilter" title="Show only entries missing a featured image">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
        Missing Image
        <span id="cptMissingCount" style="background:rgba(0,0,0,.12);border-radius:999px;padding:1px 7px;"><?php echo $no_thumb_count; ?></span>
      </button>

      <!-- Bulk assign button -->
      <button class="cpt-bulk-btn" id="cptBulkAssign" data-default-img="<?php echo esc_attr($default_img); ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        Assign to All Missing
        <span style="background:rgba(255,255,255,.22);border-radius:999px;padding:1px 8px;font-size:11px;" id="cptBulkCount"><?php echo $no_thumb_count; ?></span>
      </button>
    <?php endif; ?>
  </div>

  <!-- Progress bar -->
  <div class="cpt-progress-wrap" id="cptProgressWrap">
    <div class="cpt-progress-bar-bg"><div class="cpt-progress-bar-fill" id="cptProgressFill"></div></div>
    <div class="cpt-progress-label" id="cptProgressLabel">0 / 0 processed…</div>
  </div>

  <!-- Grid -->
  <?php if ($query->have_posts()): ?>
  <div class="cpt-grid" id="cptGrid">

    <?php while ($query->have_posts()): $query->the_post();
      $post_id     = get_the_ID();
      $post_type   = get_post_type();
      $type_obj    = $public_post_types[$post_type] ?? null;
      $type_label  = $type_obj ? $type_obj->labels->singular_name : ucfirst($post_type);
      $slug        = get_post_field('post_name', $post_id);
      $date        = get_the_date('d M Y');
      $noindex     = (get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true) == '1');
      $index_label = $noindex ? 'Noindex' : 'Index';
      $has_thumb   = has_post_thumbnail($post_id);
    ?>
    <div class="cpt-card <?php echo !$has_thumb ? 'no-thumb' : ''; ?>"
         data-post-id="<?php echo esc_attr($post_id); ?>"
         data-title="<?php echo esc_attr(strtolower(get_the_title())); ?>"
         data-slug="<?php echo esc_attr(strtolower($slug)); ?>"
         data-type="<?php echo esc_attr($post_type); ?>"
         data-has-thumb="<?php echo $has_thumb ? '1' : '0'; ?>">

      <div class="cpt-card-thumb">
        <?php if ($has_thumb): ?>
          <?php the_post_thumbnail('medium', ['loading' => 'lazy']); ?>
        <?php else: ?>
          <div class="cpt-no-img-overlay">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            No featured image
          </div>
          <!-- Per-card assign button -->
          <button class="cpt-card-assign-btn"
                  data-post-id="<?php echo esc_attr($post_id); ?>"
                  data-img-id="<?php echo esc_attr($default_img); ?>"
                  title="Assign default featured image (ID: <?php echo esc_attr($default_img); ?>)">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Assign Image
          </button>
        <?php endif; ?>
      </div>

      <div class="cpt-card-body">
        <div class="cpt-badge-row">
          <span class="cpt-badge cpt-badge-type"><?php echo esc_html($type_label); ?></span>
          <span class="cpt-badge <?php echo $noindex ? 'cpt-badge-noindex' : 'cpt-badge-index'; ?>"><?php echo $index_label; ?></span>
          <?php if (!$has_thumb): ?>
            <span class="cpt-badge cpt-badge-nothumb" id="no-thumb-badge-<?php echo $post_id; ?>">No Image</span>
          <?php endif; ?>
        </div>

        <div class="cpt-card-title">
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </div>

        <div class="cpt-meta">
          <code><?php echo esc_html($slug); ?></code>
          <span><?php echo esc_html($date); ?></span>
        </div>

        <div class="cpt-excerpt">
          <?php echo wp_kses_post(wp_trim_words(get_the_excerpt(), 14)); ?>
        </div>
      </div>
    </div>
    <?php endwhile; wp_reset_postdata(); ?>

    <div class="cpt-no-results" id="cptNoResults">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      No entries match your search.
    </div>
  </div>
  <?php else: ?>
    <p style="text-align:center;color:#64748b;">No published content found.</p>
  <?php endif; ?>

</div>

<!-- Toast container -->
<div class="cpt-toast" id="cptToast"></div>

<script>
(function () {
  /* ── DOM refs ── */
  const searchInput   = document.getElementById('cptSearchInput');
  const tabs          = document.querySelectorAll('.cpt-tab');
  const cards         = document.querySelectorAll('.cpt-card');
  const noResults     = document.getElementById('cptNoResults');
  const statsEl       = document.getElementById('cptStats');
  const missingBtn    = document.getElementById('cptMissingFilter');
  const bulkBtn       = document.getElementById('cptBulkAssign');
  const bulkCountEl   = document.getElementById('cptBulkCount');
  const progressWrap  = document.getElementById('cptProgressWrap');
  const progressFill  = document.getElementById('cptProgressFill');
  const progressLabel = document.getElementById('cptProgressLabel');
  const toastWrap     = document.getElementById('cptToast');
  const ajaxUrl       = '<?php echo esc_js(admin_url("admin-ajax.php")); ?>';
  const nonce         = '<?php echo esc_js(wp_create_nonce("cpt_assign_thumb_nonce")); ?>';
  const defaultImgId  = <?php echo intval($default_img); ?>;

  let activeType    = 'all';
  let searchTerm    = '';
  let showMissing   = false;
  let isBulkRunning = false;

  /* ── Toast ── */
  function showToast(msg, type = 'info', duration = 3500) {
    const el = document.createElement('div');
    el.className = `cpt-toast-item ${type}`;
    el.textContent = msg;
    toastWrap.appendChild(el);
    setTimeout(() => {
      el.style.animation = 'toastOut .3s ease forwards';
      setTimeout(() => el.remove(), 320);
    }, duration);
  }

  /* ── Filter logic ── */
  function applyFilters() {
    let visible = 0, total = 0;
    cards.forEach(card => {
      total++;
      const title    = card.dataset.title   || '';
      const slug     = card.dataset.slug    || '';
      const type     = card.dataset.type    || '';
      const hasThumb = card.dataset.hasThumb === '1';

      const matchType    = activeType === 'all' || type === activeType;
      const matchSearch  = !searchTerm || title.includes(searchTerm) || slug.includes(searchTerm);
      const matchMissing = !showMissing || !hasThumb;

      if (matchType && matchSearch && matchMissing) {
        card.classList.remove('hidden');
        visible++;
      } else {
        card.classList.add('hidden');
      }
    });

    noResults.style.display = visible === 0 ? 'block' : 'none';
    statsEl.textContent = `Showing ${visible} of ${total} entries`;
  }

  /* ── Tabs ── */
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      activeType = tab.dataset.type;
      applyFilters();
    });
  });

  /* ── Search ── */
  searchInput.addEventListener('input', () => {
    searchTerm = searchInput.value.trim().toLowerCase();
    applyFilters();
  });

  /* ── Missing filter toggle ── */
  if (missingBtn) {
    missingBtn.addEventListener('click', () => {
      showMissing = !showMissing;
      missingBtn.classList.toggle('active', showMissing);
      applyFilters();
    });
  }

  /* ── Single-card assign ── */
  async function assignThumb(postId, imgId, card, btn) {
    if (btn) { btn.disabled = true; btn.textContent = '…'; }

    try {
      const fd = new FormData();
      fd.append('action',   'cpt_assign_thumb');
      fd.append('nonce',    nonce);
      fd.append('post_id',  postId);
      fd.append('image_id', imgId);

      const res  = await fetch(ajaxUrl, { method: 'POST', body: fd });
      const data = await res.json();

      if (data.success) {
        /* Swap out the placeholder with the real image */
        const thumbWrap = card.querySelector('.cpt-card-thumb');
        thumbWrap.innerHTML = `<img src="${data.data.thumb_url}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">`;

        /* Update card state */
        card.dataset.hasThumb = '1';
        card.classList.remove('no-thumb');
        card.classList.add('thumb-assigned');

        /* Remove "No Image" badge */
        const badge = card.querySelector('[id^="no-thumb-badge-"]');
        if (badge) badge.remove();

        /* Refresh missing count */
        updateMissingCount();
        showToast(`✅ Image assigned to "${card.dataset.title.slice(0,30)}…"`, 'success');
        return true;
      } else {
        if (!data.data?.skipped) {
          showToast(`❌ ${data.data?.message || 'Failed'}`, 'error');
        }
        return false;
      }
    } catch (e) {
      showToast('❌ Network error. Please try again.', 'error');
      return false;
    } finally {
      if (btn) { btn.disabled = false; }
    }
  }

  /* ── Per-card assign buttons (delegated) ── */
  document.getElementById('cptGrid')?.addEventListener('click', async (e) => {
    const btn = e.target.closest('.cpt-card-assign-btn');
    if (!btn) return;
    const card   = btn.closest('.cpt-card');
    const postId = btn.dataset.postId;
    const imgId  = btn.dataset.imgId || defaultImgId;
    await assignThumb(postId, imgId, card, btn);
  });

  /* ── Bulk assign ── */
  function getMissingCards() {
    return [...cards].filter(c => c.dataset.hasThumb === '0');
  }

  function updateMissingCount() {
    const count = getMissingCards().length;
    if (bulkCountEl)  bulkCountEl.textContent = count;
    const missingCountEl = document.getElementById('cptMissingCount');
    if (missingCountEl) missingCountEl.textContent = count;
    if (bulkBtn && count === 0) {
      bulkBtn.disabled = true;
      bulkBtn.title    = 'All entries already have a featured image';
    }
  }

  if (bulkBtn) {
    bulkBtn.addEventListener('click', async () => {
      if (isBulkRunning) return;

      const missing = getMissingCards();
      if (missing.length === 0) {
        showToast('ℹ️ All entries already have a featured image.', 'info');
        return;
      }

      isBulkRunning        = true;
      bulkBtn.disabled     = true;
      progressWrap.classList.add('visible');

      let done = 0, succeeded = 0, failed = 0;
      const total = missing.length;

      for (const card of missing) {
        const postId = card.dataset.postId;
        const ok     = await assignThumb(postId, defaultImgId, card, null);
        ok ? succeeded++ : failed++;
        done++;

        const pct = Math.round((done / total) * 100);
        progressFill.style.width  = pct + '%';
        progressLabel.textContent = `${done} / ${total} processed…`;
      }

      /* Done */
      progressLabel.textContent = `✅ Done — ${succeeded} assigned, ${failed} failed`;
      showToast(`Bulk assign complete: ${succeeded} assigned, ${failed} skipped/failed.`, succeeded > 0 ? 'success' : 'info', 5000);
      isBulkRunning = false;

      setTimeout(() => progressWrap.classList.remove('visible'), 4000);
    });
  }

  /* ── Init ── */
  applyFilters();
  updateMissingCount();
})();
</script>