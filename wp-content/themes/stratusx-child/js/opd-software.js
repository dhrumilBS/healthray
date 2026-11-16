(function(){
  function init(){
    var root = document.querySelector('.opd-landing .hx'); if (!root) return;
    var dl = window.dataLayer = window.dataLayer || [];
    var FORM = 'opd_software_demo';

    root.querySelectorAll('[data-wa]').forEach(function(a){ a.addEventListener('click', function(){ dl.push({event:'whatsapp_click', form:FORM}); }); });
    root.querySelectorAll('a[href^="tel:"]').forEach(function(a){ a.addEventListener('click', function(){ dl.push({event:'phone_click', form:FORM}); }); });

    var popupTrigger = function(){
      var found = null, els = document.querySelectorAll('a, button');
      for (var i = 0; i < els.length; i++) {
        var el = els[i];
        if (root.contains(el)) continue;
        if (!/^book a demo$/i.test((el.textContent || '').replace(/\s+/g, ' ').trim())) continue;
        var href = el.tagName === 'A' ? (el.getAttribute('href') || '') : '';
        if (href && href.charAt(0) !== '#' && href.indexOf('javascript:') !== 0) continue;
        if (el.getClientRects().length) return el;
        if (!found) found = el;
      }
      return found;
    };
    root.querySelectorAll('a[href="#demo"]').forEach(function(a){ a.addEventListener('click', function(e){
      var t = popupTrigger(); if (!t) return;
      e.preventDefault();

      var h = t.tagName === 'A' ? (t.getAttribute('href') || '') : 'x';
      if (h === '#' || h === '') t.addEventListener('click', function(ev){ ev.preventDefault(); }, {capture: true, once: true});
      t.click();
      dl.push({event:'demo_popup_open', form:FORM, button:(a.textContent || '').trim()});
    }); });

    document.addEventListener('wpcf7mailsent', function(e){
      var hero = document.getElementById('demo');
      if (hero && hero.contains(e.target)) return;
      for (var n = e.target; n && n !== document.body; n = n.parentElement) {
        if (getComputedStyle(n).position === 'fixed') { dl.push({event:'demo_form_success', form:FORM, form_position:'popup'}); return; }
      }
    });

    var faqs = root.querySelectorAll('.faq details');
    faqs.forEach(function(d){ d.addEventListener('toggle', function(){
      if (!d.open) return;
      faqs.forEach(function(o){ if (o !== d && o.open) o.open = false; });
    }); });

    var box = document.getElementById('demo');
    if (box) {
      var started = false, picked = '';

      var BUSINESS = {
        'Clinic with 2 to 5 doctors': 'Clinic Management Software (CMS)',
        'Hospital with OPD and IPD': 'Hospital Management Software (HMS)'
      };
      var fill = function(label){
        var sels = document.querySelectorAll('.wpcf7 select');
        for (var s = 0; s < sels.length; s++) {
          for (var i = 0; i < sels[s].options.length; i++) {
            var o = sels[s].options[i];
            if (o.value === label || o.text.trim() === label) {
              sels[s].selectedIndex = i;
              sels[s].dispatchEvent(new Event('change', {bubbles: true}));
              break;
            }
          }
        }
        return false;
      };
      root.querySelectorAll('[data-pick]').forEach(function(a){ a.addEventListener('click', function(){
        picked = a.dataset.pick;
        if (BUSINESS[picked]) { fill(BUSINESS[picked]); setTimeout(function(){ fill(BUSINESS[picked]); }, 400); }
      }); });
      box.addEventListener('focusin', function(e){
        if (started || !e.target.closest('.wpcf7 form')) return;
        started = true; dl.push({event:'form_start', form:FORM, form_position:'hero', place_type:picked});
      });
      document.addEventListener('wpcf7mailsent', function(e){
        if (!box.contains(e.target)) return;
        dl.push({event:'demo_form_success', form:FORM, form_position:'hero', place_type:picked});
      });
    }

    var rail = document.getElementById('rail'), dots = document.getElementById('dots');
    if (rail && dots) {
      var cards = rail.querySelectorAll('.rev'), hold = false, pause;
      var step = function(){ return cards[0].offsetWidth + 20; };
      var stops = function(){ return Math.max(1, Math.round((rail.scrollWidth - rail.clientWidth) / step()) + 1); };
      var at = function(){ return Math.min(stops() - 1, Math.round(rail.scrollLeft / step())); };
      var paint = function(){ var k = at(); dots.querySelectorAll('button').forEach(function(d, j){ d.classList.toggle('on', j === k); }); };
      var go = function(k){ var m = stops() - 1; if (k < 0) k = m; if (k > m) k = 0; rail.scrollTo({left: k * step(), behavior: 'smooth'}); };
      var rest = function(){ hold = true; clearTimeout(pause); pause = setTimeout(function(){ hold = false; }, 10000); };
      var build = function(){
        dots.innerHTML = '';
        for (var i = 0; i < stops(); i++) (function(k){
          var b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', 'Reviews, set ' + (k + 1)); b.innerHTML = '<i></i>';
          b.addEventListener('click', function(){ go(k); rest(); });
          dots.appendChild(b);
        })(i);
        paint();
      };
      var prev = document.getElementById('rev-prev'), next = document.getElementById('rev-next');
      if (prev) prev.addEventListener('click', function(){ go(at() - 1); rest(); });
      if (next) next.addEventListener('click', function(){ go(at() + 1); rest(); });
      rail.addEventListener('scroll', paint, {passive: true});
      window.addEventListener('resize', build);
      ['mouseenter','touchstart','focusin'].forEach(function(ev){ rail.addEventListener(ev, function(){ hold = true; }, {passive: true}); });
      ['mouseleave','touchend','focusout'].forEach(function(ev){ rail.addEventListener(ev, function(){ hold = false; }, {passive: true}); });
      var still = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (!still) setInterval(function(){ if (!hold && document.visibilityState === 'visible') go(at() + 1); }, 7000);
      build();
    }

    var pl = document.getElementById('tour');
    if (pl) {
      var vid = pl.querySelector('video'), fsb = pl.querySelector('.full'), byHand = false, auto = false;
      var toggle = function(){ byHand = true; auto = false; if (vid.paused) vid.play(); else vid.pause(); };
      vid.addEventListener('click', toggle);
      pl.querySelector('.play').addEventListener('click', toggle);
      vid.addEventListener('play', function(){ pl.classList.remove('paused'); if (!auto) dl.push({event:'video_play', form:FORM}); });
      vid.addEventListener('pause', function(){ pl.classList.add('paused'); });
      vid.addEventListener('ended', function(){ pl.classList.add('paused'); vid.currentTime = 0; byHand = false; });
      fsb.addEventListener('click', function(){
        if (document.fullscreenElement) { document.exitFullscreen(); }
        else if (pl.requestFullscreen) { pl.requestFullscreen(); }
        else if (vid.webkitEnterFullscreen) { vid.webkitEnterFullscreen(); }
      });
      document.addEventListener('fullscreenchange', function(){ pl.classList.toggle('fs', document.fullscreenElement === pl); });
      if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        new IntersectionObserver(function(es){
          var on = es[0].isIntersecting;
          if (on && vid.paused && !(byHand && pl.classList.contains('paused'))) { auto = true; var p = vid.play(); if (p && p.catch) p.catch(function(){}); }
          else if (!on && !vid.paused) { auto = false; vid.pause(); byHand = false; }
        }, {threshold: 0.5}).observe(pl);
      }
    }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();