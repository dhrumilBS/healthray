(function(){
  function init(){
    var root = document.querySelector('.modules-landing .hx'); if (!root) return;
    var dl = window.dataLayer = window.dataLayer || [];
    var FORM = 'modules_demo';

    root.querySelectorAll('[data-wa]').forEach(function(a){ a.addEventListener('click', function(){ dl.push({event:'whatsapp_click', form:FORM}); }); });
    root.querySelectorAll('a[href^="tel:"]').forEach(function(a){ a.addEventListener('click', function(){ dl.push({event:'phone_click', form:FORM}); }); });
    root.querySelectorAll('[data-module]').forEach(function(a){ a.addEventListener('click', function(){ dl.push({event:'module_click', form:FORM, module:a.dataset.module}); }); });

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
      var started = false;
      box.addEventListener('focusin', function(e){
        if (started || !e.target.closest('.wpcf7 form')) return;
        started = true; dl.push({event:'form_start', form:FORM, form_position:'hero'});
      });
      document.addEventListener('wpcf7mailsent', function(e){
        if (!box.contains(e.target)) return;
        dl.push({event:'demo_form_success', form:FORM, form_position:'hero'});
      });
    }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();