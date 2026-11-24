(function () {
  /* 1. CF7 2-step form "PPC - Clinic": step 1 speciality (saved in bed_size), step 2 name, mobile, email.
     Mobile goes to Odoo as +91 + 10 digits. */
  window.dataLayer = window.dataLayer || [];
  function pushEvent(name, extra) { var d = { event: name, form: 'clinic_software_demo_ppc' }; for (var k in (extra || {})) d[k] = extra[k]; window.dataLayer.push(d); }
  function $(id) { return document.getElementById(id); }
  (function () {
    var box = $('hr-clinic-form'), f = box && box.querySelector('form.wpcf7-form');
    var s1 = f && f.querySelector('#a-s1'), s2 = f && f.querySelector('#a-s2');
    if (!s1 || !s2) return;
    var bed = f.querySelector('input[name="bed_size"]'), bar = $('a-bar'), lbl = $('a-lbl'), err = $('a-err');
    var otherF = $('a-otherf'), other = $('a-other'), otherBtn = $('a-other-btn');
    var name = $('a-name'), mob = $('a-mob'), mail = $('a-mail'), sentKind = '';
    function where() { return box.closest('#hr-pop') ? 'popup' : 'hero'; }

    function step(n, focus) {
      s1.hidden = n !== 1; s2.hidden = n !== 2;
      if (bar) bar.style.width = n === 2 ? '100%' : '50%';
      if (lbl) lbl.textContent = n === 2 ? 'STEP 2 OF 2 · LAST STEP' : 'STEP 1 OF 2 · TAKES 20 SECONDS';
      if (err) err.hidden = true;
      if (!focus) return;
      var t = n === 2 ? name : (s1.querySelector('.opts button.on') || s1.querySelector('.opts button'));
      if (t) t.focus({ preventScroll: true });
    }
    function pickSpec(v, word) {
      if (bed) bed.value = v;
      $('a-pick').textContent = 'Speciality: ' + v;
      var spw = $('sp-word'); if (spw && word) spw.textContent = word;
      document.dispatchEvent(new CustomEvent('hr-speciality', { detail: v }));
      pushEvent('clinic_form_start', { speciality: v, form_position: where() });
      step(2, true);
      /* phone: bring the card to the top of the screen, so the keyboard does not hide the fields */
      if (window.innerWidth <= 820 && where() === 'hero') {
        var rib = document.querySelector('.ai-rib'), y = box.getBoundingClientRect().top + scrollY - 12 - (rib ? rib.offsetHeight : 0);
        try { scrollTo({ top: y, behavior: 'smooth' }); } catch (e) { scrollTo(0, y); }
      }
    }
    [].forEach.call(s1.querySelectorAll('.opts button'), function (b) {
      b.addEventListener('click', function () {
        if (b === otherBtn) { otherF.hidden = false; b.setAttribute('aria-expanded', 'true'); b.classList.add('on'); other.focus(); return; }
        otherF.hidden = true; otherBtn.classList.remove('on'); otherBtn.setAttribute('aria-expanded', 'false');
        pickSpec(b.getAttribute('data-pick'), b.getAttribute('data-w'));
      });
    });
    function otherNext() {
      var v = other.value.trim();
      if (v.length < 2) { other.classList.add('bad'); other.focus(); return; }
      other.classList.remove('bad'); pickSpec(v, 'your clinic');
    }
    $('a-other-go').addEventListener('click', otherNext);
    other.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); otherNext(); } });
    other.addEventListener('input', function () { other.classList.remove('bad'); });
    $('a-change').addEventListener('click', function () { step(1, true); });

    /* mobile: digits only */
    function digits(v) {
      var d = String(v || '').replace(/\D/g, '');
      if (d.length === 12 && d.slice(0, 2) === '91') return d.slice(2);
      if (d.length === 11 && d.charAt(0) === '0') return d.slice(1);
      return d.slice(0, 10);
    }
    if (mob) { mob.setAttribute('inputmode', 'numeric'); mob.setAttribute('maxlength', '14'); mob.addEventListener('input', function () { mob.value = digits(mob.value); }); }
    if (mail) { mail.setAttribute('inputmode', 'email'); mail.setAttribute('autocapitalize', 'off'); mail.setAttribute('spellcheck', 'false'); }
    if (name) name.setAttribute('autocapitalize', 'words');

    /* each field is checked when the doctor leaves it: green tick when right, a short red line under it when wrong */
    var RULES = {
      'a-name': function (v) { return v.trim().length >= 2 ? '' : 'Please write your name.'; },
      'a-mob': function (v) { return /^[6-9]\d{9}$/.test(digits(v)) ? '' : 'Please write a 10-digit mobile number.'; },
      'a-mail': function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? '' : 'Please write your email, for example you@gmail.com.'; }
    };
    function fieldCheck(id, show) {
      var el = $(id); if (!el) return '';
      var fl = el.closest('.field'), msg = RULES[id](el.value), m = $(id + '-m');
      fl.classList.toggle('ok', !msg && el.value !== '');
      if (show) { fl.classList.toggle('bad', !!msg); if (m) { m.textContent = msg; m.hidden = !msg; } el.setAttribute('aria-invalid', msg ? 'true' : 'false'); }
      else if (!msg) { fl.classList.remove('bad'); if (m) m.hidden = true; el.setAttribute('aria-invalid', 'false'); }
      return msg;
    }
    Object.keys(RULES).forEach(function (id) {
      var el = $(id); if (!el) return;
      el.addEventListener('blur', function () { if (el.value !== '') fieldCheck(id, true); });
      el.addEventListener('input', function () { fieldCheck(id, el.closest('.field').classList.contains('bad')); if (id === 'a-mail') $('a-sugg').hidden = true; });
    });

    /* email typo help: "Did you mean ...@gmail.com?" */
    var DOMS = ['gmail.com', 'yahoo.com', 'yahoo.in', 'hotmail.com', 'outlook.com', 'rediffmail.com', 'icloud.com', 'live.com'];
    function editDist(a, b) { var d = [], i, j; for (i = 0; i <= a.length; i++) d[i] = [i]; for (j = 0; j <= b.length; j++) d[0][j] = j; for (i = 1; i <= a.length; i++) for (j = 1; j <= b.length; j++) d[i][j] = Math.min(d[i - 1][j] + 1, d[i][j - 1] + 1, d[i - 1][j - 1] + (a[i - 1] === b[j - 1] ? 0 : 1)); return d[a.length][b.length]; }
    if (mail) mail.addEventListener('blur', function () {
      var v = mail.value.trim().toLowerCase(), at = v.lastIndexOf('@'), s = $('a-sugg'); s.hidden = true; if (at < 1) return;
      var dom = v.slice(at + 1), best = null;
      DOMS.forEach(function (d) { var k = editDist(dom, d); if (k > 0 && k <= 2 && (!best || k < best.k)) best = { d: d, k: k }; });
      if (!best) return;
      var fixed = v.slice(0, at + 1) + best.d, b = document.createElement('button');
      s.textContent = 'Did you mean '; b.type = 'button'; b.textContent = fixed; s.appendChild(b); s.appendChild(document.createTextNode('?')); s.hidden = false;
      b.addEventListener('click', function () { mail.value = fixed; s.hidden = true; fieldCheck('a-mail', true); pushEvent('clinic_email_fixed', { form_position: where() }); });
    });

    f.addEventListener('formdata', function (e) {
      var d = mob ? digits(mob.value) : '';
      e.formData.set('your-number', /^\d{10}$/.test(d) ? '+91 ' + d : '');
    });
    /* capture: runs before CF7's submit */
    f.addEventListener('submit', function (e) {
      function stop() { e.preventDefault(); e.stopImmediatePropagation(); }
      if (!bed || !bed.value) { stop(); step(1, true); return; }
      var bad = null;
      ['a-name', 'a-mob', 'a-mail'].forEach(function (id) { if (fieldCheck(id, true)) { if (!bad) bad = id; pushEvent('clinic_form_error', { field: id.replace('a-', ''), form_position: where() }); } });
      if (bad) { stop(); $(bad).focus(); return; }
      if (err) err.hidden = true;
      sentKind = bed.value;
    }, true);

    box.addEventListener('wpcf7mailsent', function () { pushEvent('clinic_form_sent', { speciality: sentKind, form_position: where() }); });
    box.addEventListener('wpcf7invalid', function (e) {
      var bad = (e.detail && e.detail.apiResponse && e.detail.apiResponse.invalid_fields) || [];
      if (bad.some(function (x) { return x.field === 'bed_size'; })) { step(1, false); return; }
      step(2, false);
      if (err && bad.length) { err.textContent = bad[0].message || 'Please check the details.'; err.hidden = false; }
    });
    ['wpcf7spam', 'wpcf7mailfailed'].forEach(function (n) {
      box.addEventListener(n, function () { if (err) { err.textContent = 'Sorry, this did not go through. Please try again, or send us a WhatsApp message.'; err.hidden = false; } });
    });
    /* clear the speciality on reset */
    f.addEventListener('reset', function () { setTimeout(function () { if (bed) bed.value = '';[].forEach.call(f.querySelectorAll('.field'), function (x) { x.classList.remove('ok', 'bad'); }); step(1, false); }, 0); });

    step(1, false);
  })();

  /* 2. demo popup: moves the hero form card in, back on close */
  (function () {
    var pop = $('hr-pop'), card = $('hr-clinic-form');
    if (!pop || !card) return;
    var inner = pop.querySelector('.hr-pop-in'), x = pop.querySelector('.hr-pop-x'), hold = document.createElement('div'), from = null;
    hold.className = 'card-hold'; hold.setAttribute('aria-hidden', 'true');
    function open(e) {
      e.preventDefault();
      if (!pop.hidden) return;
      from = e.currentTarget;
      hold.style.height = card.offsetHeight + 'px';
      card.parentNode.insertBefore(hold, card);
      inner.appendChild(card);
      pop.hidden = false;
      document.documentElement.classList.add('hr-pop-on');
      var t = card.querySelector('#a-s1:not([hidden]) .opts button, #a-s2:not([hidden]) #a-name');
      if (t) t.focus({ preventScroll: true });
      pushEvent('clinic_popup_open', { button: (from.textContent || '').trim() });
    }
    function close() {
      if (pop.hidden) return;
      hold.parentNode.insertBefore(card, hold);
      hold.parentNode.removeChild(hold);
      pop.hidden = true;
      document.documentElement.classList.remove('hr-pop-on');
      if (from && from.offsetParent !== null) from.focus({ preventScroll: true });
    }
    [].forEach.call(document.querySelectorAll('[data-hr-pop]'), function (a) { a.addEventListener('click', open); });
    x.addEventListener('click', close);
    pop.addEventListener('click', function (e) { if (e.target === pop) close(); });
    document.addEventListener('keydown', function (e) {
      if (pop.hidden) return;
      if (e.key === 'Escape') { close(); return; }
      /* keep Tab inside */
      if (e.key !== 'Tab') return;
      var els = [].filter.call(pop.querySelectorAll('button,input:not([type=hidden]),a[href]'), function (el) { return el.offsetParent !== null; });
      if (!els.length) return;
      var a = els[0], b = els[els.length - 1];
      if (e.shiftKey && document.activeElement === a) { e.preventDefault(); b.focus(); }
      else if (!e.shiftKey && document.activeElement === b) { e.preventDefault(); a.focus(); }
    });
  })();

  /* 3. FAQ: one open at a time */
  (function () {
    var qs = [].slice.call(document.querySelectorAll('details[name="clinic-faq"]'));
    qs.forEach(function (d) {
      d.addEventListener('toggle', function () {
        if (d.open) qs.forEach(function (o) { if (o !== d && o.open) o.open = false; });
      });
    });
  })();

  [].forEach.call(document.querySelectorAll('a.btn.wa'), function (a) { a.addEventListener('click', function () { pushEvent('clinic_whatsapp_click'); }); });

  /* 4. desks (pinned scroll, like the home page departments section) */
  /* ---- one software for your whole clinic: the same behaviour as the home page's departments section ----
     Pinned (whenever it fits on the screen): the list and the picture stay in place while the page scrolls. Each step
     of scrolling opens the next desk, the line under the open name fills as you scroll, and after the last desk the
     page moves on. Phone: the row of names sits above the picture; the text keeps only the heading and one line, and the
     picture may get a little smaller so it all fits. A tap or click on a name scrolls the page to that desk.
     Not pinned (a screen too short even for that): it changes by itself every 6 seconds; a tap stops that. */
  (function () {
    var root = document.getElementById('desks'), pin = document.getElementById('dp-pin'), grid = root.querySelector('.dp'),
      shot = document.getElementById('dp-shot'), dl = document.getElementById('dp-list'), pg = document.getElementById('hr-page');
    var db = [].slice.call(dl.querySelectorAll('button')), ds = [].slice.call(shot.querySelectorAll('img')), dbod = [].slice.call(document.querySelectorAll('#dp-bodies .dp-body'));
    var N = db.length, cur = -1, pinned = false, STEP = 400, pinTop = 0, ticking = false, stopped = false, seen = false, lastSize = '', rt = 0;
    var calm = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    function wide() { return pg.clientWidth > 820; }
    function revealChip() {
      if (dl.scrollWidth <= dl.clientWidth + 2) return;
      var b = db[cur], left = b.getBoundingClientRect().left - dl.getBoundingClientRect().left + dl.scrollLeft - 16;
      try { dl.scrollTo({ left: Math.max(0, left), behavior: calm ? 'auto' : 'smooth' }); } catch (e) { dl.scrollLeft = Math.max(0, left); }
    }
    function show(i) {
      if (i === cur) return; cur = i;
      db.forEach(function (b, k) { b.classList.toggle('on', k === i); b.setAttribute('aria-selected', k === i ? 'true' : 'false'); });
      ds.forEach(function (s, k) { s.classList.toggle('on', k === i); });
      dbod.forEach(function (b, k) { b.classList.toggle('on', k === i); });
      revealChip();
    }
    function startY() { return pin.getBoundingClientRect().top + scrollY - pinTop; }
    function unpin() { pinned = false; root.classList.remove('pinned', 'pin-compact');['--pin-h', '--step-p', '--shot-w', '--pin-top', '--dp-i'].forEach(function (k) { root.style.removeProperty(k); }); }
    function layout() {
      var gap = wide() ? 16 : 12;
      root.style.setProperty('--shot-w', 'none');
      root.classList.add('pinned'); root.classList.remove('pin-compact');
      /* the sticky AI Scribe ribbon sits on top: pin the desks just under it */
      var rib = document.querySelector('.ai-rib');
      pinTop = gap + (rib ? Math.round(rib.getBoundingClientRect().height) : 0);
      var picH = shot.offsetHeight, picW = shot.offsetWidth, over = grid.offsetHeight + pinTop + gap - innerHeight;
      if (over > 0 && !wide()) { root.classList.add('pin-compact'); over = grid.offsetHeight + pinTop + gap - innerHeight; }
      var newH = over > 0 ? picH - over : picH;
      /* The picture may get smaller so it all fits, but never below 70% of its size. */
      if (!picH || newH < picH * 0.7) { unpin(); dl.classList.add('auto'); show(Math.max(cur, 0)); return; }
      if (over > 0) root.style.setProperty('--shot-w', Math.floor(newH * picW / picH) + 'px');
      if (!wide() && over < 0) pinTop += Math.floor(-over / 2);
      STEP = Math.max(wide() ? 320 : 260, Math.round(innerHeight * (wide() ? 0.5 : 0.45)));
      root.style.setProperty('--pin-top', pinTop + 'px');
      root.style.setProperty('--pin-h', (grid.offsetHeight + N * STEP) + 'px');
      pinned = true; dl.classList.remove('auto');
      onScroll();
    }
    function onScroll() {
      if (!pinned) return;
      var p = (scrollY - startY()) / STEP, i = Math.min(N - 1, Math.max(0, Math.floor(p))), frac = p < 0 ? 0 : Math.min(1, Math.max(0, p - i));
      show(i);
      root.style.setProperty('--dp-i', i);
      root.style.setProperty('--step-p', frac.toFixed(3));
    }
    addEventListener('scroll', function () { if (!pinned || ticking) return; ticking = true; requestAnimationFrame(function () { ticking = false; onScroll(); }); }, { passive: true });
    function goTo(i) {
      i = Math.min(N - 1, Math.max(0, i)); show(i); root.style.setProperty('--step-p', '0');
      var y = Math.round(startY() + i * STEP + 2);
      try { scrollTo({ top: y, behavior: calm ? 'auto' : 'smooth' }); } catch (e) { scrollTo(0, y); }
    }
    function select(i, byHand) {
      if (pinned) { goTo(i); return; }
      i = (i + N) % N; if (byHand) { stopped = true; dl.classList.remove('auto'); } show(i);
    }
    db.forEach(function (b, k) { b.addEventListener('click', function () { select(k, true); }); });
    dl.addEventListener('keydown', function (e) {
      var fwd = e.key === 'ArrowDown' || e.key === 'ArrowRight', back = e.key === 'ArrowUp' || e.key === 'ArrowLeft';
      if (!fwd && !back) return; e.preventDefault();
      var next = cur + (fwd ? 1 : -1); if (pinned) next = Math.min(N - 1, Math.max(0, next));
      select(next, true); try { db[(next + N) % N].focus({ preventScroll: true }); } catch (err) { }
    });
    if ('IntersectionObserver' in window) { new IntersectionObserver(function (es) { es.forEach(function (e) { seen = e.isIntersecting; }); }, { threshold: .4 }).observe(shot); }
    setInterval(function () { if (!pinned && seen && !stopped && !calm && !document.hidden) select(cur + 1, false); }, 6000);
    /* Re-measure when the screen size changes (on a phone only when the width changes or the height changes a lot,
       so the address bar sliding in and out does not make it jump), and when the pictures and fonts have loaded. */
    function relayout(force) {
      var size = innerWidth + 'x' + innerHeight;
      if (force !== true && size === lastSize) return;
      if (force !== true && !wide() && lastSize) { var q = lastSize.split('x'); if (+q[0] === innerWidth && Math.abs(+q[1] - innerHeight) < 120) return; }
      lastSize = size; clearTimeout(rt); rt = setTimeout(layout, 120);
    }
    addEventListener('resize', relayout);
    addEventListener('load', function () { relayout(true); });
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { relayout(true); });
    ds.forEach(function (im) { if (!im.complete) im.addEventListener('load', function () { relayout(true); }, { once: true }); });
    show(0); lastSize = innerWidth + 'x' + innerHeight; layout();
    window.__desks = { pinned: function () { return pinned; }, step: function () { return STEP; }, startY: startY, cur: function () { return cur; } };
  })();

  /* ---- the video: plays without sound, again and again, while at least half of it is on screen ---- */
  var v = $('s2a-v'), pp = $('s2a-pp'), userPaused = false;
  if (!v || !pp) return;
  v.addEventListener('play', function () { pp.textContent = '❚❚'; });
  v.addEventListener('pause', function () { pp.textContent = '▶'; });
  pp.addEventListener('click', function () { if (v.paused) { userPaused = false; v.play().catch(function () { }); } else { userPaused = true; v.pause(); } });

  /* full screen: computers and Android show the whole player (with its buttons); iPhone Safari can only show the
     <video> itself, so it opens Safari's own player (with its controls, removed again on exit) */
  var fs = $('s2a-fs'), player = v.parentNode;
  function isFull() { return !!(document.fullscreenElement || document.webkitFullscreenElement || v.webkitDisplayingFullscreen); }
  function onFs() { var on = isFull(); player.classList.toggle('is-full', on); if (fs) fs.setAttribute('aria-label', on ? 'Exit full screen' : 'Watch the video full screen'); }
  function iosFull() {
    if (!v.webkitEnterFullscreen) return;
    v.controls = true;
    try { v.webkitEnterFullscreen(); } catch (e) { v.addEventListener('loadedmetadata', function () { try { v.webkitEnterFullscreen(); } catch (e2) { } }, { once: true }); }
  }
  if (fs) {
    fs.addEventListener('click', function () {
      if (isFull()) { var x = document.exitFullscreen || document.webkitExitFullscreen; if (x) x.call(document); return; }
      userPaused = false; v.play().catch(function () { });
      if (player.requestFullscreen) player.requestFullscreen().catch(iosFull);
      else if (player.webkitRequestFullscreen) player.webkitRequestFullscreen();
      else iosFull();
      pushEvent('clinic_video_fullscreen');
    });
    document.addEventListener('fullscreenchange', onFs);
    document.addEventListener('webkitfullscreenchange', onFs);
    v.addEventListener('webkitbeginfullscreen', onFs);
    v.addEventListener('webkitendfullscreen', function () { v.controls = false; onFs(); v.play().catch(function () { }); });
  }
  /* skip the 3-second title card ("One patient. One OPD visit."): its small text looks like an empty box on a phone.
     The video starts at the reception scene and plays again from there when it ends (the <video> has no "loop"). */
  var SKIP = 3.2;
  v.addEventListener('loadedmetadata', function () { if (v.currentTime < SKIP) v.currentTime = SKIP; });
  v.addEventListener('ended', function () { v.currentTime = SKIP; v.play().catch(function () { }); });
  if ('IntersectionObserver' in window) {
    /* start downloading a little before the reader reaches it, and only after the page has loaded */
    var warm = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { if (v.preload !== 'auto') { v.preload = 'auto'; try { v.load(); } catch (err) { } } warm.disconnect(); } }); }, { rootMargin: '900px 0px' });
    if (document.readyState === 'complete') warm.observe(v); else addEventListener('load', function () { warm.observe(v); }, { once: true });
    new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting && e.intersectionRatio >= .5) { if (!userPaused) v.play().catch(function () { }); } else if (!isFull()) { v.pause(); } }); }, { threshold: [0, .5] }).observe(v);
  } else { v.preload = 'auto'; }
})();

/* 5. reviews auto-slide */
/* Slides every 5 seconds on phones (iPhone too) and computers while on screen. A touch, a swipe, the mouse over a
   card or an arrow click pauses it; it slides again 8 seconds later. Steps card by card, back to the first after the last. */
(function () {
  var rail = document.getElementById('rv-rail'); if (!rail) return;
  var cards = [].slice.call(rail.querySelectorAll('.rv')); if (cards.length < 2) return;
  var seen = false, hover = false, holdUntil = 0, snapT = 0;
  function pos(c) { return c.offsetLeft - cards[0].offsetLeft; }
  function idx() { var x = rail.scrollLeft, best = 0, bd = 1e9; cards.forEach(function (c, i) { var d = Math.abs(pos(c) - x); if (d < bd) { bd = d; best = i; } }); return best; }
  function moveTo(x) {
    /* Safari can pull a snapping rail back in the middle of a smooth scroll: switch snapping off while it moves */
    rail.style.scrollSnapType = 'none'; clearTimeout(snapT);
    try { rail.scrollTo({ left: x, behavior: 'smooth' }); } catch (e) { rail.scrollLeft = x; }
    snapT = setTimeout(function () { rail.style.scrollSnapType = ''; }, 900);
  }
  function step(d) {
    var max = rail.scrollWidth - rail.clientWidth, cur = rail.scrollLeft, i = idx();
    if (d > 0) moveTo(cur >= max - 4 ? 0 : Math.min(max, pos(cards[Math.min(cards.length - 1, i + 1)])));
    else moveTo(cur <= 4 ? max : Math.max(0, pos(cards[Math.max(0, i - 1)])));
  }
  function hold() { holdUntil = Date.now() + 8000; }
  var nx = document.getElementById('rv-next'), pv = document.getElementById('rv-prev');
  if (nx) nx.addEventListener('click', function () { hold(); step(1); });
  if (pv) pv.addEventListener('click', function () { hold(); step(-1); });
  ['touchstart', 'pointerdown', 'wheel'].forEach(function (n) { rail.addEventListener(n, hold, { passive: true }); });
  rail.addEventListener('mouseenter', function () { hover = true; });
  rail.addEventListener('mouseleave', function () { hover = false; hold(); });
  if ('IntersectionObserver' in window) { new IntersectionObserver(function (es) { es.forEach(function (e) { seen = e.isIntersecting; }); }, { threshold: .3 }).observe(rail); }
  else seen = true;
  setInterval(function () { if (seen && !hover && !document.hidden && Date.now() > holdUntil) step(1); }, 5000);
})();

/* 6. phone bottom bar: hidden while the hero form, the desks section, the last section or one of the page's own
   "Book a free demo" buttons is on screen (so two same buttons never show together) */
(function () {
  var bar = document.getElementById('mbar'), form = document.getElementById('hr-clinic-form'); if (!bar || !form || !('IntersectionObserver' in window)) return;
  var formSeen = true, endSeen = false, desksSeen = false, last = document.querySelector('#hr-page section:last-of-type'), desks = document.getElementById('dp-pin');
  var ctas = [].slice.call(document.querySelectorAll('#hr-page .cta-line .btn, #hr-page .p3 .btn')), ctaOn = [];
  function upd() { var on = !formSeen && !endSeen && !desksSeen && !ctaOn.length; bar.classList.toggle('show', on); bar.setAttribute('aria-hidden', on ? 'false' : 'true'); bar.firstElementChild.tabIndex = on ? 0 : -1; }
  new IntersectionObserver(function (es) { es.forEach(function (e) { formSeen = e.isIntersecting; }); upd(); }, { threshold: 0 }).observe(form);
  if (last) new IntersectionObserver(function (es) { es.forEach(function (e) { endSeen = e.isIntersecting; }); upd(); }, { threshold: .2 }).observe(last);
  if (desks) new IntersectionObserver(function (es) { es.forEach(function (e) { desksSeen = e.isIntersecting; }); upd(); }, { threshold: 0 }).observe(desks);
  var co = new IntersectionObserver(function (es) { es.forEach(function (e) { var i = ctaOn.indexOf(e.target); if (e.isIntersecting && i < 0) ctaOn.push(e.target); if (!e.isIntersecting && i > -1) ctaOn.splice(i, 1); }); upd(); }, { threshold: 0, rootMargin: '0px 0px -10px 0px' });
  ctas.forEach(function (c) { co.observe(c); });
})();

/* 7. AI Scribe ribbon + 10-second demo. Follows the speciality tapped in the form (or ?sp= from the ad).
   Sample talks and notes only: no medicine names anywhere (Google Ads restricted drug terms). */
(function () {
  var S = {
    general: {
      tpl: 'General physician', av: 'R', name: 'Mr. Ramesh Patel', meta: '58 Y · M · 4th visit',
      talk: [['Patient', 'Ek hafte se subah sar dard hota hai. Uthte waqt chakkar aata hai.'], ['Doctor', 'BP 150 by 92 hai. Wahi dawai chalu rakhiye. Namak kam, roz subah BP check kijiye. Ek mahine baad aaiye.']],
      note: [['cx', 'Complaints', 'Headache in the morning · 1 week · giddy on standing'], ['dx', 'Diagnosis', 'High blood pressure'], ['ad', 'Advice', 'Continue same medicine · less salt · BP every morning'], ['fu', 'Follow-up', 'After 1 month']]
    },
    skin: {
      tpl: 'Skin and hair', av: 'N', name: 'Mrs. Neha Shah', meta: '29 Y · F · 3rd visit',
      talk: [['Patient', 'Kohni aur ghutne par do mahine se khujli wale daag hain.'], ['Doctor', 'Plaque psoriasis lag raha hai. Dawai din mein do baar lagaiye. Photo le lete hain, teen hafte baad milte hain.']],
      note: [['cx', 'Complaints', 'Itchy patches on elbows and knees · 2 months'], ['dx', 'Diagnosis', 'Plaque psoriasis'], ['ad', 'Advice', 'Apply twice a day · photo at every visit'], ['fu', 'Follow-up', 'After 3 weeks']]
    },
    women: {
      tpl: "Women's health", av: 'K', name: 'Mrs. Kavya Desai', meta: '27 Y · F · antenatal visit',
      talk: [['Patient', 'Last period 12 March ko aaya tha. Pair mein thodi sujan hai.'], ['Doctor', 'BP 118 by 76, sab theek hai. Wahi dawai chalu rakhiye. Do hafte baad aana.']],
      note: [['cx', 'Complaints', 'Mild swelling of feet'], ['dx', 'Diagnosis', 'Pregnancy · 34 weeks'], ['ad', 'Advice', 'Continue same medicine · count baby movements'], ['fu', 'Follow-up', 'After 2 weeks']]
    },
    child: {
      tpl: 'Child', av: 'R', name: 'Riya Patel', meta: '15 months · F · 10.5 kg',
      talk: [['Mother', 'Do din se bukhar hai. Pandrah mahine ki hai.'], ['Doctor', 'Weight 10.5 kilo. Paani zyada pilaiye. Teen din baad dikhaiye.']],
      note: [['cx', 'Complaints', 'Fever · 2 days'], ['dx', 'Diagnosis', 'Viral fever'], ['ad', 'Advice', 'Plenty of fluids · watch for rash'], ['fu', 'Follow-up', 'After 3 days']]
    },
    diab: {
      tpl: 'Diabetes', av: 'S', name: 'Mr. Suresh Joshi', meta: '52 Y · M · follow-up',
      talk: [['Patient', 'Fasting sugar 180 aa raha hai. Pyaas zyada lagti hai.'], ['Doctor', 'HbA1c 8.1 hai. Dawai ka dose badha dete hain. Roz aadha ghanta chaliye. Teen mahine baad HbA1c.']],
      note: [['cx', 'Complaints', 'More thirst · fasting sugar 180'], ['dx', 'Diagnosis', 'Type 2 diabetes · not in control'], ['ad', 'Advice', 'Dose increased · walk 30 min daily'], ['fu', 'Follow-up', 'HbA1c after 3 months']]
    }
  };
  function extra(k) {
    if (k === 'skin') return '<div class="ais-lab">Photos · this visit and the first</div><div class="ax-photos"><div class="ax-ph"><i></i><b>Visit 1 · 15 Oct</b></div><div class="ax-ph after"><i></i><b>Visit 3 · 5 Nov</b></div></div><div class="ax-tags"><span>Laser</span><span>Chemical peel</span><span>Hair loss</span></div>';
    if (k === 'women') return '<div class="ais-lab">Pregnancy card</div><div class="ax-kv"><div>Last period<b>12 Mar 2026</b></div><div class="hi">Due date<b>17 Dec 2026</b></div><div>Weeks<b>34 of 40</b></div><div>G P L A<b>2 · 1 · 1 · 0</b></div></div>';
    if (k === 'child') return '<div class="ais-lab">Vaccine chart</div><div class="ax-vac"><div><span>Measles (MR) 1</span><span class="ok">Done · 9 months</span></div><div><span>Hepatitis A 1</span><span class="ok">Done · 12 months</span></div><div><span>MMR 2 and Chickenpox 1</span><span class="due">Due now</span></div></div>';
    if (k === 'diab') return '<div class="ais-lab">Fasting sugar · last 4 visits</div><svg class="ax-chart" viewBox="0 0 320 110" role="img" aria-label="Fasting sugar 210, 196, 188, 180"><line x1="20" y1="90" x2="310" y2="90" stroke="#e2e8f0"/><line x1="20" y1="54" x2="310" y2="54" stroke="#e2e8f0" stroke-dasharray="4 4"/><text x="22" y="49" font-size="11" fill="#64748b" font-family="Mulish">130 target</text><polyline points="40,18 120,28 200,34 290,40" fill="none" stroke="#2563eb" stroke-width="3"/><g fill="#2563eb"><circle cx="40" cy="18" r="5"/><circle cx="120" cy="28" r="5"/><circle cx="200" cy="34" r="5"/><circle cx="290" cy="40" r="6"/></g><g font-size="12" font-family="Mulish" font-weight="700" fill="#0d2a40" text-anchor="middle"><text x="40" y="106">Jul · 210</text><text x="120" y="106">Aug · 196</text><text x="200" y="106">Sep · 188</text><text x="290" y="106">Nov · 180</text></g></svg>';
    return '<div class="ais-lab">From earlier visits</div><div class="ax-kv"><div>Last visit<b>12 Sep · BP 146/90</b></div><div>Allergy<b style="color:#b91c1c">Dust</b></div></div>';
  }
  var MAP = { 'General physician': 'general', 'Dermatologist': 'skin', 'Gynecologist': 'women', 'Pediatrician': 'child', 'Cardiologist': 'general' };
  var WORD = { general: 'your general clinic', skin: 'your skin clinic', women: "your women's clinic", child: 'your child clinic', diab: 'your diabetes clinic' };
  var cur = 'general', T = [], calm = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var sp = (location.search.match(/[?&]sp=([a-z]+)/) || [])[1];
  if (S[sp]) { cur = sp; var w = document.getElementById('sp-word'); if (w) w.textContent = WORD[sp]; }
  document.addEventListener('hr-speciality', function (e) { cur = MAP[e.detail] || 'general'; });
  var sh = document.getElementById('ai-sheet'); if (!sh) return;
  function $(id) { return document.getElementById(id); }
  function at(ms, fn) { T.push(setTimeout(fn, ms)); }
  function stop() { T.forEach(clearTimeout); T = []; }
  function mmss(s) { return '00:' + (s < 10 ? '0' : '') + s; }
  function setup(k) {
    var s = S[k];
    $('ais-av').textContent = s.av; $('ais-name').textContent = s.name; $('ais-meta').textContent = s.meta; $('ais-tpl').textContent = s.tpl + ' template';
    $('ais-tr').innerHTML = s.talk.map(function (l) { return '<p><b>' + l[0] + ':</b>' + l[1].split(' ').map(function (x) { return '<span class="w"> ' + x + '</span>'; }).join('') + '</p>'; }).join('');
    $('ais-rows').innerHTML = s.note.map(function (n) { return '<div class="ais-row empty ' + n[0] + '"><b>' + n[1] + '</b><span>—</span><em class="dr">DRAFT</em></div>'; }).join('');
    $('ais-extra').innerHTML = extra(k); $('ais-extra').classList.remove('on');
    $('ais-rec').className = 'ais-rec'; $('ais-rec-t').textContent = 'Recording · 00:00';
    $('ais-status').innerHTML = ''; $('ais-go').classList.remove('ready');
  }
  function fill(k) {
    var s = S[k];
    [].forEach.call($('ais-rows').children, function (r, i) { r.classList.remove('empty'); r.classList.add('done'); r.querySelector('span').textContent = s.note[i][2]; });
    $('ais-extra').classList.add('on'); $('ais-go').classList.add('ready');
    $('ais-status').textContent = 'Draft ready · 4 lines. Check, change if needed, and save.';
    /* phone: the notes sit under the AI Scribe panel, so bring them into view when they are ready */
    if (window.innerWidth <= 760) {
      var win = sh.querySelector('.ais-win'), ch = sh.querySelector('.ais-chart');
      try { win.scrollTo({ top: ch.offsetTop - 70, behavior: calm ? 'auto' : 'smooth' }); } catch (e) { win.scrollTop = ch.offsetTop - 70; }
    }
  }
  function play() {
    stop(); setup(cur); sh.querySelector('.ais-win').scrollTop = 0;
    var words = [].slice.call($('ais-tr').querySelectorAll('.w'));
    if (calm) { words.forEach(function (w) { w.classList.add('on'); }); $('ais-rec').className = 'ais-rec off'; $('ais-rec-t').textContent = 'Recording stopped · 00:42'; fill(cur); return; }
    var rec = $('ais-rec'); rec.className = 'ais-rec live';
    var step = 150, total = words.length * step, secs = 42;
    for (var i = 1; i <= secs; i++) { (function (i) { at(Math.round(i * total / secs), function () { $('ais-rec-t').textContent = 'Recording · ' + mmss(i); }); })(i); }
    words.forEach(function (w, i) { at(300 + i * step, function () { w.classList.add('on', 'tmp'); if (i > 1) words[i - 2].classList.remove('tmp'); }); });
    var end = 300 + total + 300;
    at(end, function () {
      words.forEach(function (w) { w.classList.remove('tmp'); }); rec.className = 'ais-rec off'; $('ais-rec-t').textContent = 'Recording stopped · 00:42';
      $('ais-status').innerHTML = 'Writing the draft from the talk…<span class="bar"><i></i></span>';
      requestAnimationFrame(function () { requestAnimationFrame(function () { var b = $('ais-status').querySelector('.bar i'); if (b) b.style.width = '100%'; }); });
    });
    at(end + 1300, function () { fill(cur); });
  }
  var last = null;
  function open() { last = document.activeElement; sh.hidden = false; document.documentElement.style.overflow = 'hidden'; play(); try { $('ais-x').focus({ preventScroll: true }); } catch (e) { } window.dataLayer.push({ event: 'clinic_ai_scribe_open' }); }
  function close() { sh.hidden = true; document.documentElement.style.overflow = ''; stop(); if (last && last.focus && last.offsetParent !== null) try { last.focus({ preventScroll: true }); } catch (e) { } }
  document.addEventListener('click', function (e) { if (e.target.closest('[data-ai-open]')) open(); });
  $('ais-x').addEventListener('click', close);
  /* "Book a free demo" in the demo closes it; the form popup opens through data-hr-pop */
  $('ais-cta').addEventListener('click', function () { close(); });
  sh.addEventListener('click', function (e) { if (e.target === sh) close(); });
  addEventListener('keydown', function (e) { if (e.key === 'Escape' && !sh.hidden) close(); });
})();
