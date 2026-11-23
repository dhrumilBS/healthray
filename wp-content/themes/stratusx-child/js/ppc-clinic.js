(function () {
  /* 1. CF7 2-step form "PPC - Clinic"; step 1 answer goes in bed_size; mobile sent as +91 + 10 digits */
  window.dataLayer = window.dataLayer || [];
  function pushEvent(name, extra) { var d = { event: name, form: 'clinic_software_demo_ppc' }; for (var k in (extra || {})) d[k] = extra[k]; window.dataLayer.push(d); }
  (function () {
    var box = document.getElementById('hr-clinic-form'), f = box && box.querySelector('form.wpcf7-form');
    var s1 = f && f.querySelector('.hr-s1'), s2 = f && f.querySelector('.hr-s2');
    if (!s1 || !s2) return;
    var bed = f.querySelector('input[name="bed_size"]'), opts = [].slice.call(s1.querySelectorAll('.opt'));
    var err1 = s1.querySelector('.err'), err2 = s2.querySelector('.err'), pv = s2.querySelector('.pv'), back = s2.querySelector('.back');
    var mob = f.querySelector('input[name="your-number"]'), stepno = box.querySelector('.stepno'), sentKind = '';
    function where() { return box.closest('#hr-pop') ? 'popup' : 'hero'; }
    var live = document.createElement('span'); live.className = 'hr-sr'; live.setAttribute('role', 'status'); live.setAttribute('aria-live', 'polite'); box.appendChild(live);

    function step(n, focus) {
      s1.hidden = n !== 1; s2.hidden = n !== 2;
      box.classList.toggle('step2', n === 2);
      if (stepno) stepno.textContent = n;
      live.textContent = 'Step ' + n + ' of 2';
      if (pv) pv.textContent = bed && bed.value ? 'Doctors: ' + bed.value : '';
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
        pushEvent('clinic_form_start', { doctors: bed.value, form_position: where() });
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
      mob.addEventListener('input', function () { mob.value = digits(mob.value); if (err2) err2.hidden = true; });
    }
    f.addEventListener('formdata', function (e) {
      var d = mob ? digits(mob.value) : '';
      e.formData.set('your-number', /^\d{10}$/.test(d) ? '+91 ' + d : '');
    });

    /* capture: runs before CF7's submit */
    f.addEventListener('submit', function (e) {
      function stop() { e.preventDefault(); e.stopImmediatePropagation(); }
      if (!bed || !bed.value) { stop(); if (err1) err1.hidden = false; step(1, true); return; }
      if (mob && !/^[6-9]\d{9}$/.test(digits(mob.value))) {
        stop();
        if (err2) { err2.textContent = 'Please write a 10-digit mobile number.'; err2.hidden = false; }
        mob.focus(); return;
      }
      if (err2) err2.hidden = true;
      sentKind = bed.value;
    }, true);

    box.addEventListener('wpcf7mailsent', function () { pushEvent('clinic_form_sent', { doctors: sentKind, form_position: where() }); });
    box.addEventListener('wpcf7invalid', function (e) {
      var bad = (e.detail && e.detail.apiResponse && e.detail.apiResponse.invalid_fields) || [];
      step(bad.some(function (x) { return x.field === 'bed_size'; }) ? 1 : 2, false);
    });
    /* clear bed_size on reset */
    f.addEventListener('reset', function () { setTimeout(function () { pick(null); step(1, false); }, 0); });

    step(1, false);
  })();

  /* 2. demo popup: moves the hero form card in, back on close */
  (function () {
    var pop = document.getElementById('hr-pop'), card = document.getElementById('hr-clinic-form');
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
      var t = card.querySelector('.hr-s1:not([hidden]) .opt[aria-pressed="true"], .hr-s1:not([hidden]) .opt, .hr-s2:not([hidden]) input[name="your-name"]');
      if (t) t.focus({ preventScroll: true });
      pushEvent('clinic_popup_open', { button: (from.textContent || '').trim() });
    }
    function close() {
      if (pop.hidden) return;
      hold.parentNode.insertBefore(card, hold);
      hold.parentNode.removeChild(hold);
      pop.hidden = true;
      document.documentElement.classList.remove('hr-pop-on');
      if (from) from.focus({ preventScroll: true });
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
  (function(){
    var root=document.getElementById('desks'), pin=document.getElementById('dp-pin'), grid=root.querySelector('.dp'),
        shot=document.getElementById('dp-shot'), dl=document.getElementById('dp-list'), pg=document.getElementById('hr-page');
    var db=[].slice.call(dl.querySelectorAll('button')), ds=[].slice.call(shot.querySelectorAll('img')), dbod=[].slice.call(document.querySelectorAll('#dp-bodies .dp-body'));
    var N=db.length, cur=-1, pinned=false, STEP=400, pinTop=0, ticking=false, stopped=false, seen=false, lastSize='', rt=0;
    var calm=window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    function wide(){ return pg.clientWidth>820; }
    function revealChip(){
      if(dl.scrollWidth<=dl.clientWidth+2) return;
      var b=db[cur], left=b.getBoundingClientRect().left-dl.getBoundingClientRect().left+dl.scrollLeft-16;
      try{ dl.scrollTo({left:Math.max(0,left),behavior:calm?'auto':'smooth'}); }catch(e){ dl.scrollLeft=Math.max(0,left); }
    }
    function show(i){
      if(i===cur) return; cur=i;
      db.forEach(function(b,k){ b.classList.toggle('on',k===i); b.setAttribute('aria-selected',k===i?'true':'false'); });
      ds.forEach(function(s,k){ s.classList.toggle('on',k===i); });
      dbod.forEach(function(b,k){ b.classList.toggle('on',k===i); });
      revealChip();
    }
    function startY(){ return pin.getBoundingClientRect().top+scrollY-pinTop; }
    function unpin(){ pinned=false; root.classList.remove('pinned','pin-compact'); ['--pin-h','--step-p','--shot-w','--pin-top','--dp-i'].forEach(function(k){ root.style.removeProperty(k); }); }
    function layout(){
      var gap=wide()?16:12;
      root.style.setProperty('--shot-w','none');
      root.classList.add('pinned'); root.classList.remove('pin-compact');
      pinTop=gap;
      var picH=shot.offsetHeight, picW=shot.offsetWidth, over=grid.offsetHeight+pinTop+gap-innerHeight;
      if(over>0 && !wide()){ root.classList.add('pin-compact'); over=grid.offsetHeight+pinTop+gap-innerHeight; }
      var newH=over>0?picH-over:picH;
      /* The picture may get smaller so it all fits, but never below 70% of its size. */
      if(!picH || newH<picH*0.7){ unpin(); dl.classList.add('auto'); show(Math.max(cur,0)); return; }
      if(over>0) root.style.setProperty('--shot-w',Math.floor(newH*picW/picH)+'px');
      if(!wide() && over<0) pinTop+=Math.floor(-over/2);
      STEP=Math.max(wide()?320:260, Math.round(innerHeight*(wide()?0.5:0.45)));
      root.style.setProperty('--pin-top',pinTop+'px');
      root.style.setProperty('--pin-h',(grid.offsetHeight+N*STEP)+'px');
      pinned=true; dl.classList.remove('auto');
      onScroll();
    }
    function onScroll(){
      if(!pinned) return;
      var p=(scrollY-startY())/STEP, i=Math.min(N-1,Math.max(0,Math.floor(p))), frac=p<0?0:Math.min(1,Math.max(0,p-i));
      show(i);
      root.style.setProperty('--dp-i',i);
      root.style.setProperty('--step-p',frac.toFixed(3));
    }
    addEventListener('scroll',function(){ if(!pinned||ticking) return; ticking=true; requestAnimationFrame(function(){ ticking=false; onScroll(); }); },{passive:true});
    function goTo(i){
      i=Math.min(N-1,Math.max(0,i)); show(i); root.style.setProperty('--step-p','0');
      var y=Math.round(startY()+i*STEP+2);
      try{ scrollTo({top:y,behavior:calm?'auto':'smooth'}); }catch(e){ scrollTo(0,y); }
    }
    function select(i,byHand){
      if(pinned){ goTo(i); return; }
      i=(i+N)%N; if(byHand){ stopped=true; dl.classList.remove('auto'); } show(i);
    }
    db.forEach(function(b,k){ b.addEventListener('click',function(){ select(k,true); }); });
    dl.addEventListener('keydown',function(e){
      var fwd=e.key==='ArrowDown'||e.key==='ArrowRight', back=e.key==='ArrowUp'||e.key==='ArrowLeft';
      if(!fwd && !back) return; e.preventDefault();
      var next=cur+(fwd?1:-1); if(pinned) next=Math.min(N-1,Math.max(0,next));
      select(next,true); try{ db[(next+N)%N].focus({preventScroll:true}); }catch(err){}
    });
    if('IntersectionObserver' in window){ new IntersectionObserver(function(es){ es.forEach(function(e){ seen=e.isIntersecting; }); },{threshold:.4}).observe(shot); }
    setInterval(function(){ if(!pinned && seen && !stopped && !calm && !document.hidden) select(cur+1,false); },6000);
    /* Re-measure when the screen size changes (on a phone only when the width changes or the height changes a lot,
       so the address bar sliding in and out does not make it jump), and when the pictures and fonts have loaded. */
    function relayout(force){
      var size=innerWidth+'x'+innerHeight;
      if(force!==true && size===lastSize) return;
      if(force!==true && !wide() && lastSize){ var q=lastSize.split('x'); if(+q[0]===innerWidth && Math.abs(+q[1]-innerHeight)<120) return; }
      lastSize=size; clearTimeout(rt); rt=setTimeout(layout,120);
    }
    addEventListener('resize',relayout);
    addEventListener('load',function(){ relayout(true); });
    if(document.fonts && document.fonts.ready) document.fonts.ready.then(function(){ relayout(true); });
    ds.forEach(function(im){ if(!im.complete) im.addEventListener('load',function(){ relayout(true); },{once:true}); });
    show(0); lastSize=innerWidth+'x'+innerHeight; layout();
    window.__desks={pinned:function(){return pinned;}, step:function(){return STEP;}, startY:startY, cur:function(){return cur;}};
  })();

  /* ---- the video: starts without sound when seen; the steps under it follow the video ---- */
  var v=document.getElementById('s2a-v'), pp=document.getElementById('s2a-pp'), wrap=document.getElementById('s2a-wrap');
  var chaps=[].slice.call(document.querySelectorAll('#s2a-chap button')), times=chaps.map(function(b){return parseFloat(b.dataset.t);}), userPaused=false;
  function mark(){
    var t=v.currentTime, cur=-1; times.forEach(function(x,i){if(t>=x) cur=i;});
    chaps.forEach(function(b,i){
      b.classList.toggle('on',i===cur); b.classList.toggle('done',i<cur);
      var end=(i+1<times.length?times[i+1]:(v.duration||78.9)), f=(i===cur)?Math.max(0,Math.min(1,(t-times[i])/(end-times[i]))):0;
      if(i>=cur) b.firstElementChild.style.width=(f*100)+'%'; else b.firstElementChild.style.width='';
    });
    if(cur>=0 && wrap.scrollWidth>wrap.clientWidth){var li=chaps[cur].parentElement; wrap.scrollLeft=Math.max(0,li.offsetLeft-20);}
  }
  v.addEventListener('timeupdate',mark);
  v.addEventListener('play',function(){pp.textContent='❚❚';}); v.addEventListener('pause',function(){pp.textContent='▶';});
  v.addEventListener('ended',function(){v.currentTime=0; userPaused=true;});
  pp.addEventListener('click',function(){ if(v.paused){userPaused=false; v.play().catch(function(){});} else {userPaused=true; v.pause();} });
  chaps.forEach(function(b){b.addEventListener('click',function(){v.currentTime=parseFloat(b.dataset.t); userPaused=false; v.play().catch(function(){});});});
  if('IntersectionObserver' in window){
    new IntersectionObserver(function(es){es.forEach(function(e){ if(e.isIntersecting && e.intersectionRatio>=.6){ if(!userPaused) v.play().catch(function(){}); } else { v.pause(); } });},{threshold:[0,.6]}).observe(v);
  }
})();

/* 5. reviews auto-slide */
(function(){
  var rail=document.getElementById('rv-rail'); if(!rail) return;
  function step(d){ var card=rail.querySelector('.rv'); var w=card.getBoundingClientRect().width+20; var max=rail.scrollWidth-rail.clientWidth; var x=rail.scrollLeft+d*w; if(x>max+4) x=0; if(x<-4) x=max; rail.scrollTo({left:x,behavior:'smooth'}); }
  var touched=false, seen=false;
  document.getElementById('rv-next').addEventListener('click',function(){touched=true; step(1);});
  document.getElementById('rv-prev').addEventListener('click',function(){touched=true; step(-1);});
  rail.addEventListener('touchstart',function(){touched=true;},{passive:true});
  if('IntersectionObserver' in window){ new IntersectionObserver(function(es){es.forEach(function(e){seen=e.isIntersecting;});},{threshold:.4}).observe(rail); }
  setInterval(function(){ if(seen && !touched) step(1); },5000);
})();

/* 6. phone bottom bar */
(function(){
  var bar=document.getElementById('mbar'), form=document.getElementById('hr-clinic-form'); if(!bar||!form||!('IntersectionObserver' in window)) return;
  /* hidden while the hero form, the "every desk" section (it fills the whole screen, like on the home page) or the last section is on screen */
  var formSeen=true, endSeen=false, desksSeen=false, last=document.querySelector('#hr-page section:last-of-type'), desks=document.getElementById('dp-pin');
  function upd(){ var on=!formSeen && !endSeen && !desksSeen; bar.classList.toggle('show',on); bar.setAttribute('aria-hidden',on?'false':'true'); bar.firstElementChild.tabIndex=on?0:-1; }
  new IntersectionObserver(function(es){ es.forEach(function(e){ formSeen=e.isIntersecting; }); upd(); },{threshold:0}).observe(form);
  if(last) new IntersectionObserver(function(es){ es.forEach(function(e){ endSeen=e.isIntersecting; }); upd(); },{threshold:.2}).observe(last);
  if(desks) new IntersectionObserver(function(es){ es.forEach(function(e){ desksSeen=e.isIntersecting; }); upd(); },{threshold:0}).observe(desks);
})();
