(function () {
  var CFG = window.HR_DG || {}, UP = CFG.uploads || 'https://healthray.com/wp-content/uploads';
  var calm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  window.dataLayer = window.dataLayer || [];
  function pushEvent(name, extra) { var d = { event: name, form: 'diagnostic_software_demo_ppc' }; for (var k in (extra || {})) d[k] = extra[k]; window.dataLayer.push(d); }
  function seenMid(el, a, b) { var r = el.getBoundingClientRect(), vh = window.innerHeight || document.documentElement.clientHeight; return r.width > 0 && r.bottom > vh * a && r.top < vh * b; }

  /* 1. label per ad group (?k=), for when a page cache drops ?k= (PHP prints it too) */
  (function () {
    var m = /[?&]k=([a-z]+)/.exec(location.search), t = { dc: 'Diagnostic centre software', path: 'Pathology lab and radiology software', scan: 'Radiology and USG reporting software' }, el = document.getElementById('eyebrow');
    if (m && t[m[1]] && el) el.textContent = t[m[1]];
  })();

  /* 2. lab logo row (listed twice for a seamless loop) */
  (function () {
    var track = document.getElementById('track'); if (!track) return;
    var LABS = [
      ['2024/01/Pratham-lab-1.webp', 'Pratham Laboratory', 'Surat, Gujarat'],
      ['2024/01/Diabcare-lab-1.webp', 'Diabcare Laboratory', 'Mumbai, Maharashtra'],
      ['2024/06/Om-Pathology-Lab-Healthray.webp', 'Om Pathology Lab', 'Surat, Gujarat'],
      ['2024/06/Bansal-Diagnostics-Healthray.webp', 'Bansal Diagnostics', 'Kurukshetra, Haryana'],
      ['2024/06/Omed-Diagnostics-Healthray.webp', 'Omed Diagnostics', 'Cachar, Assam'],
      ['2024/06/Chakraa-Medical-Center-Healthray.webp', 'Chakraa Medical Center', 'Tiruppur, Tamil Nadu'],
      ['2024/06/Shiv-Krishna-Hospital-Healthray.webp', 'Shiv Krishna Hospital', 'Ghaziabad, Uttar Pradesh'],
      ['2024/04/Universal-Multispecialty-Hospital.webp', 'Universal Hospital', 'Surat, Gujarat'],
      ['2024/06/Gastron.webp', 'Gastron Super Speciality Hospital', 'Surat, Gujarat'],
      ['2024/11/Sri-Manakula-Vinayagar-Hospital.webp', 'Sri Manakula Vinayagar Hospital', 'Pondicherry']
    ];
    [false, true].forEach(function (copy) {
      LABS.forEach(function (l) {
        var d = document.createElement('div'); d.className = 'lab';
        if (copy) d.setAttribute('aria-hidden', 'true');
        var g = document.createElement('span'); g.className = 'lg';
        var im = document.createElement('img'); im.src = UP + '/' + l[0]; im.alt = copy ? '' : l[1] + ' logo'; im.width = 120; im.height = 54; im.loading = 'lazy';
        g.appendChild(im); d.appendChild(g);
        var b = document.createElement('b'); b.textContent = l[1]; d.appendChild(b);
        var s = document.createElement('span'); s.textContent = l[2]; d.appendChild(s);
        track.appendChild(d);
      });
    });
  })();

  /* 3. CF7 2-step form "PPC - Diagnostic centre": step 1 scans (saved in bed_size), step 2 name, mobile, email, city (all required).
     Mobile goes to Odoo as +91 + 10 digits. */
  (function () {
    var box = document.getElementById('hr-dg-form'), f = box && box.querySelector('form.wpcf7-form');
    var s1 = f && f.querySelector('.hr-s1'), s2 = f && f.querySelector('.hr-s2');
    if (!s1 || !s2) return;
    var bed = f.querySelector('input[name="bed_size"]'), opts = [].slice.call(s1.querySelectorAll('.opt'));
    var err1 = s1.querySelector('.err'), err2 = s2.querySelector('.err'), pv = s2.querySelector('.pv'), back = s2.querySelector('.back');
    var mob = f.querySelector('input[name="your-number"]'), stepno = box.querySelector('.stepno'), sentKind = '';
    function where() { return box.closest('#dg-pop') ? 'popup' : 'hero'; }
    var live = document.createElement('span'); live.className = 'hr-sr'; live.setAttribute('role', 'status'); live.setAttribute('aria-live', 'polite'); box.appendChild(live);

    function step(n, focus) {
      s1.hidden = n !== 1; s2.hidden = n !== 2;
      box.classList.toggle('step2', n === 2);
      if (stepno) stepno.textContent = n;
      live.textContent = 'Step ' + n + ' of 2';
      if (pv) pv.textContent = bed ? bed.value : '';
      if (!focus) return;
      var t = n === 2 ? f.querySelector('input[name="your-name"]') : (s1.querySelector('.opt[aria-pressed="true"]') || opts[0]);
      if (t) t.focus({ preventScroll: true });
    }
    function pick(o) {
      opts.forEach(function (b) { b.setAttribute('aria-pressed', b === o ? 'true' : 'false'); });
      if (bed) bed.value = o ? o.getAttribute('data-v') : '';
    }
    opts.forEach(function (o) {
      o.addEventListener('click', function () {
        pick(o);
        if (err1) err1.hidden = true;
        pushEvent('diag_form_start', { scans: bed.value, form_position: where() });
        step(2, true);
      });
    });
    if (back) back.addEventListener('click', function () { step(1, true); });

    /* mobile: digits only */
    function digits(v) {
      var d = String(v || '').replace(/\D/g, '');
      if (d.length === 12 && d.slice(0, 2) === '91') return d.slice(2);
      if (d.length === 11 && d.charAt(0) === '0') return d.slice(1);
      return d.slice(0, 10);
    }
    if (mob) {
      mob.setAttribute('inputmode', 'numeric'); mob.setAttribute('maxlength', '14');
      mob.addEventListener('input', function () { mob.value = digits(mob.value); });
    }
    [].forEach.call(s2.querySelectorAll('input'), function (i) { i.addEventListener('input', function () { if (err2) err2.hidden = true; }); });
    f.addEventListener('formdata', function (e) {
      var d = mob ? digits(mob.value) : '';
      e.formData.set('your-number', /^\d{10}$/.test(d) ? '+91 ' + d : '');
    });

    /* capture: runs before CF7's submit */
    f.addEventListener('submit', function (e) {
      function stop() { e.preventDefault(); e.stopImmediatePropagation(); }
      if (!bed || !bed.value) { stop(); if (err1) err1.hidden = false; step(1, true); return; }
      /* every field is required: name, mobile, email, city */
      var nm = f.querySelector('input[name="your-name"]'), em = f.querySelector('input[name="your-email"]'), ct = f.querySelector('input[name="your-city"]'), bad = null, msg = '';
      if (nm && nm.value.trim().length < 2) { bad = nm; msg = 'Please write your name.'; }
      else if (mob && !/^[6-9]\d{9}$/.test(digits(mob.value))) { bad = mob; msg = 'Please write a 10-digit mobile number.'; }
      else if (em && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(em.value.trim())) { bad = em; msg = 'Please write your email, for example you@gmail.com.'; }
      else if (ct && ct.value.trim().length < 2) { bad = ct; msg = 'Please write your city.'; }
      if (bad) {
        stop();
        if (err2) { err2.textContent = msg; err2.hidden = false; }
        pushEvent('diag_form_error', { field: bad.name, form_position: where() });
        bad.focus(); return;
      }
      if (err2) err2.hidden = true;
      sentKind = bed.value;
    }, true);

    box.addEventListener('wpcf7mailsent', function () { pushEvent('diag_form_sent', { scans: sentKind, form_position: where() }); });
    box.addEventListener('wpcf7invalid', function (e) {
      var bad = (e.detail && e.detail.apiResponse && e.detail.apiResponse.invalid_fields) || [];
      step(bad.some(function (x) { return x.field === 'bed_size'; }) ? 1 : 2, false);
    });
    /* clear bed_size on reset */
    f.addEventListener('reset', function () { setTimeout(function () { pick(null); step(1, false); }, 0); });

    step(1, false);
  })();

  /* 4. demo popup: moves the hero form card in, back on close */
  (function () {
    var pop = document.getElementById('dg-pop'), card = document.getElementById('hr-dg-form');
    if (!pop || !card) return;
    var inner = pop.querySelector('.dg-pop-in'), x = pop.querySelector('.dg-pop-x'), hold = document.createElement('div'), from = null;
    hold.className = 'fm-hold'; hold.setAttribute('aria-hidden', 'true');
    function open(e) {
      e.preventDefault();
      if (!pop.hidden) return;
      from = e.currentTarget;
      hold.style.height = card.offsetHeight + 'px';
      card.parentNode.insertBefore(hold, card);
      inner.appendChild(card);
      pop.hidden = false;
      document.documentElement.classList.add('dg-pop-on');
      var t = card.querySelector('.hr-s1:not([hidden]) .opt[aria-pressed="true"], .hr-s1:not([hidden]) .opt, .hr-s2:not([hidden]) input[name="your-name"]');
      if (t) t.focus({ preventScroll: true });
      pushEvent('diag_popup_open', { button: (from.textContent || '').trim() });
    }
    function close() {
      if (pop.hidden) return;
      hold.parentNode.insertBefore(card, hold);
      hold.parentNode.removeChild(hold);
      pop.hidden = true;
      document.documentElement.classList.remove('dg-pop-on');
      if (from && from.offsetParent !== null) from.focus({ preventScroll: true });
    }
    [].forEach.call(document.querySelectorAll('[data-dg-pop]'), function (a) { a.addEventListener('click', open); });
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

  /* 5. FAQ: the first is open; only one open at a time */
  (function () {
    var qs = [].slice.call(document.querySelectorAll('details[name="dg-faq"]'));
    qs.forEach(function (d) {
      d.addEventListener('toggle', function () {
        if (d.open) qs.forEach(function (o) { if (o !== d && o.open) o.open = false; });
      });
    });
  })();

  /* 6. phone bottom bar: shows only when the form card and the page's other demo buttons are off screen */
  (function () {
    var bar = document.querySelector('.bar'), form = document.getElementById('hr-dg-form'), last = document.getElementById('book');
    if (!bar || !form) return;
    var btns = [].slice.call(document.querySelectorAll('main .bt'));
    function seen(el) { var r = el.getBoundingClientRect(), vh = window.innerHeight || document.documentElement.clientHeight; return (r.width > 0 || r.height > 0) && r.bottom > 70 && r.top < vh - 70; }
    function upd() {
      var hide = seen(form) || (!!last && seen(last));
      for (var i = 0; !hide && i < btns.length; i++) hide = seen(btns[i]);
      bar.classList.toggle('off', hide);
    }
    upd(); window.addEventListener('scroll', upd, { passive: true }); window.addEventListener('resize', upd);
  })();

  /* 7. the video: the still first; when half the box is on screen the video loads and plays without sound.
     It pauses when scrolled away. Play / pause and full screen buttons (iPhone: Safari's own player). */
  (function () {
    var box = document.getElementById('player'), btn = document.getElementById('playbtn'), pp = document.getElementById('playpp'), fs = document.getElementById('playfs');
    if (!box || !btn || !pp || !fs) return;
    var v = null, stopped = false, pressed = false;
    function state() {
      var going = !!v && !v.paused && !v.ended;
      box.classList.toggle('playing', going);
      btn.setAttribute('aria-label', going ? 'Pause the video' : 'Play the 1-minute video');
      pp.setAttribute('aria-label', going ? 'Pause' : 'Play');
    }
    function make() {
      if (v) return false;
      v = document.createElement('video');
      v.muted = true; v.defaultMuted = true; v.playsInline = true; v.setAttribute('muted', ''); v.setAttribute('playsinline', ''); v.preload = 'auto';
      v.src = box.getAttribute('data-src');
      ['play', 'pause', 'ended'].forEach(function (e) { v.addEventListener(e, state); });
      v.addEventListener('ended', function () { pushEvent('diag_video_done'); });
      v.addEventListener('webkitendfullscreen', function () { v.controls = false; });
      box.insertBefore(v, btn); box.classList.add('on');
      return true;
    }
    function play() { var p = v.play(); if (p && p.catch) p.catch(function () { state(); }); }
    function toggle() {
      make();
      if (v.paused || v.ended) { stopped = false; play(); if (!pressed) { pressed = true; pushEvent('diag_video_play'); } }
      else { stopped = true; v.pause(); }
    }
    btn.addEventListener('click', toggle);
    pp.addEventListener('click', toggle);

    function fsEl() { return document.fullscreenElement || document.webkitFullscreenElement || null; }
    function phoneFull() { if (v && v.webkitEnterFullscreen) { v.controls = true; try { v.webkitEnterFullscreen(); } catch (e) { } } }
    fs.addEventListener('click', function () {
      if (!v) return;
      try {
        if (fsEl()) { (document.exitFullscreen || document.webkitExitFullscreen).call(document); return; }
        var go = box.requestFullscreen || box.webkitRequestFullscreen;
        if (go) { var q = go.call(box); if (q && q.catch) q.catch(phoneFull); }
        else phoneFull();
        pushEvent('diag_video_fullscreen');
      } catch (e) { }
    });
    ['fullscreenchange', 'webkitfullscreenchange'].forEach(function (n) {
      document.addEventListener(n, function () {
        var on = fsEl() === box;
        box.classList.toggle('full', on);
        fs.setAttribute('aria-label', on ? 'Leave full screen' : 'Full screen');
      });
    });

    var saver = !!(navigator.connection && navigator.connection.saveData);
    if (!calm && !saver && 'IntersectionObserver' in window) {
      new IntersectionObserver(function (es) {
        var seen = es[es.length - 1].intersectionRatio >= .5;
        if (seen) {
          if (stopped || (v && v.ended)) return;
          if (make()) pushEvent('diag_video_view');
          if (v.paused) play();
        }
        else if (v && !v.paused && !fsEl() && !v.webkitDisplayingFullscreen) v.pause();
      }, { threshold: [0, .5] }).observe(box);
    }
  })();

  /* 8. pictures load late so the top of the page is fast; near their section, all its pictures load */
  [].forEach.call(document.querySelectorAll('.scan, .m5, .r6'), function (sec) {
    var t = setInterval(function () {
      var r = sec.getBoundingClientRect(), vh = window.innerHeight || document.documentElement.clientHeight;
      if (r.width > 0 && r.top < vh * 2 && r.bottom > -vh) { [].forEach.call(sec.querySelectorAll('img'), function (i) { i.loading = 'eager'; }); clearInterval(t); }
    }, 400);
  });

  /* 9. scan reports: moves every 6 s while on screen; a tap opens that line and it keeps moving */
  [].forEach.call(document.querySelectorAll('.v1grid'), function (g) {
    var its = [].slice.call(g.querySelectorAll('.v1it')), pics = [].slice.call(g.querySelectorAll('.v1pic picture')), cur = 0, t = 0;
    function show(i) {
      cur = i; t = 0;
      its.forEach(function (b, k) { b.classList.remove('on'); b.setAttribute('aria-pressed', k === i ? 'true' : 'false'); });
      void g.offsetWidth;
      its[i].classList.add('on');
      pics.forEach(function (p, k) { p.classList.toggle('on', k === i); });
    }
    its.forEach(function (b, k) { b.addEventListener('click', function () { show(k); }); });
    show(0);
    if (calm) return;
    g.classList.add('hold');
    setInterval(function () {
      var go = seenMid(g, .3, .7); g.classList.toggle('hold', !go); if (!go) return;
      t += 100; if (t >= 6000) show((cur + 1) % its.length);
    }, 100);
  });

  /* 10. the day's money: the middle panel in front; moves every 5 s while on screen.
     Arrows, dots, swipe, keys and a tap on a side panel move it too, and the count starts again. */
  [].forEach.call(document.querySelectorAll('.m5 .c3'), function (c) {
    var sec = c.closest('section'), sl = [].slice.call(c.querySelectorAll('.sl')), dots = [].slice.call(sec.querySelectorAll('.c3dots button')), at = 0, t = 0, x = null, swiped = false;
    function fit() { var h = 0; sl.forEach(function (s) { h = Math.max(h, s.offsetHeight); }); if (h) c.style.height = h + 'px'; }
    function show(n) {
      at = (n + sl.length) % sl.length; t = 0;
      sl.forEach(function (s, i) { var d = (i - at + sl.length) % sl.length; if (d === sl.length - 1) d = -1; s.setAttribute('data-pos', d); s.setAttribute('aria-hidden', d === 0 ? 'false' : 'true'); });
      dots.forEach(function (b, i) { b.classList.toggle('on', i === at); b.setAttribute('aria-pressed', i === at ? 'true' : 'false'); });
    }
    sl.forEach(function (s, i) { s.addEventListener('click', function () { if (swiped || i === at) return; show(i); }); });
    dots.forEach(function (b, i) { b.addEventListener('click', function () { show(i); }); });
    [].forEach.call(sec.querySelectorAll('.c3arr button'), function (b) { b.addEventListener('click', function () { show(at + (b.classList.contains('prev') ? -1 : 1)); }); });
    c.addEventListener('pointerdown', function (e) { x = e.clientX; });
    c.addEventListener('pointerup', function (e) { if (x === null) return; var dx = e.clientX - x; x = null; if (Math.abs(dx) > 40) { swiped = true; setTimeout(function () { swiped = false; }, 60); show(at + (dx < 0 ? 1 : -1)); } });
    c.addEventListener('pointercancel', function () { x = null; });
    c.addEventListener('keydown', function (e) { if (e.key === 'ArrowRight') show(at + 1); if (e.key === 'ArrowLeft') show(at - 1); });
    show(0); fit();
    window.addEventListener('resize', fit); window.addEventListener('load', fit);
    [].forEach.call(c.querySelectorAll('img'), function (i) { i.addEventListener('load', fit); });
    if (calm) return;
    setInterval(function () { if (!seenMid(c, .3, .7)) return; t += 100; if (t >= 5000) show(at + 1); }, 100);
  });

  /* 11. report on WhatsApp: the point nearest the middle of the screen sets the picture */
  [].forEach.call(document.querySelectorAll('.c6grid'), function (g) {
    var steps = [].slice.call(g.querySelectorAll('.c6steps .st')), pics = [].slice.call(g.querySelectorAll('.c6pic picture'));
    function upd() {
      if (!g.offsetParent) return;
      var mid = (window.innerHeight || document.documentElement.clientHeight) / 2, best = 0, bd = 1e9;
      steps.forEach(function (s, k) { var r = s.getBoundingClientRect(), d = Math.abs(r.top + r.height / 2 - mid); if (d < bd) { bd = d; best = k; } });
      steps.forEach(function (s, k) { s.classList.toggle('on', k === best); });
      pics.forEach(function (p, k) { p.classList.toggle('on', k === best); });
    }
    window.addEventListener('scroll', upd, { passive: true }); window.addEventListener('resize', upd); upd();
  });

  /* 12. reviews: one card every 4 s while on screen, back to the first after the last (phones and computers).
     A touch, a swipe or an arrow pauses it for 8 s; the mouse over a card pauses it. */
  [].forEach.call(document.querySelectorAll('.say9'), function (sec) {
    var row = sec.querySelector('.rv-row'), hold = 0, snapT = 0;
    if (!row) return;
    var cards = [].slice.call(row.querySelectorAll('.rv'));
    function pos(c) { return c.offsetLeft - cards[0].offsetLeft; }
    function idx() { var x = row.scrollLeft, best = 0, bd = 1e9; cards.forEach(function (c, i) { var d = Math.abs(pos(c) - x); if (d < bd) { bd = d; best = i; } }); return best; }
    function moveTo(x) {
      /* Safari can pull a snapping row back in the middle of a smooth scroll: no snapping while it moves */
      row.style.scrollSnapType = 'none'; clearTimeout(snapT);
      try { row.scrollTo({ left: x, behavior: 'smooth' }); } catch (e) { row.scrollLeft = x; }
      snapT = setTimeout(function () { row.style.scrollSnapType = ''; }, 900);
    }
    function step(d) {
      var max = row.scrollWidth - row.clientWidth, cur = row.scrollLeft, i = idx();
      if (d > 0) moveTo(cur >= max - 4 ? 0 : Math.min(max, pos(cards[Math.min(cards.length - 1, i + 1)])));
      else moveTo(cur <= 4 ? max : Math.max(0, pos(cards[Math.max(0, i - 1)])));
    }
    [].forEach.call(sec.querySelectorAll('.rv-arrows button'), function (b) { b.addEventListener('click', function () { hold = Date.now() + 8000; step(b.classList.contains('prev') ? -1 : 1); }); });
    ['pointerdown', 'touchstart', 'wheel'].forEach(function (ev) { row.addEventListener(ev, function () { hold = Date.now() + 8000; }, { passive: true }); });
    row.addEventListener('mouseenter', function () { hold = Infinity; });
    row.addEventListener('mouseleave', function () { hold = Date.now() + 1500; });
    setInterval(function () { if (Date.now() > hold && !document.hidden && seenMid(row, .2, .8)) step(1); }, 4000);
  });
})();
