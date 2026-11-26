/* Healthray: Hospital Billing Software page. No jQuery. */
(function(){
  window.dataLayer=window.dataLayer||[];
  function pushEvent(name,extra){var d={event:name,form:'hospital_billing_software_demo'};for(var k in (extra||{}))d[k]=extra[k];window.dataLayer.push(d);}

  /* Lead form: the PPC Form (CF7 11eec7d) in the hero card (#a-form) and in the site popup (#myPopup).
     Its own script (js/ppc-hims.js) loads only on PPC pages, so its 2 steps run here (same as the Hospital Chains page):
     a bed button fills the hidden bed_size field and opens step 2, "change" goes back. */
  document.querySelectorAll('#a-form .hr-demo-form, #myPopup .hr-demo-form').forEach(function(box){
    var s1=box.querySelector('.hr-step-1'),s2=box.querySelector('.hr-step-2'),wrap=box.closest('.wpcf7');
    if(!s1||!s2) return;
    var bed=box.querySelector('[name="bed_size"]'),pick=box.querySelector('.hr-selected-bed-value'); // by name: CF7 drops the id on the popup copy
    function step(n){s1.style.display=n===1?'':'none';s2.style.display=n===2?'':'none';}
    box.querySelectorAll('.hr-bed-option').forEach(function(b){b.addEventListener('click',function(){
      var val=b.getAttribute('data-bed'); if(!val) return;
      if(bed) bed.value=val; if(pick) pick.textContent=val;
      pushEvent('billing_form_start',{bed_size:val,place:box.closest('#myPopup')?'popup':'hero'});
      step(2); var first=s2.querySelector('input:not([type="hidden"])'); if(first) first.focus({preventScroll:true});
    });});
    box.querySelectorAll('.hr-back-step, .hr-change-bed').forEach(function(b){b.addEventListener('click',function(){
      step(1); var opt=s1.querySelector('.hr-bed-option'); if(opt) opt.focus();
    });});
    /* the form's labels are not tied to their fields: give each field its label as its name */
    box.querySelectorAll('.field').forEach(function(f){
      var l=f.querySelector('label'),i=f.querySelector('input:not([type="hidden"])');
      if(l&&i&&!i.hasAttribute('aria-label')) i.setAttribute('aria-label',l.textContent.replace('*','').trim());
    });
    if(wrap){
      /* a server-side error on a step-1 answer would be hidden on step 1: show step 2 */
      wrap.addEventListener('wpcf7invalid',function(){step(2);});
      wrap.addEventListener('wpcf7mailsent',function(){pushEvent('billing_form_sent',{bed_size:bed?bed.value:''});});
      /* CF7 empties the fields after a sent form: start again at step 1 (a hidden field keeps its value, so clear it) */
      wrap.addEventListener('wpcf7reset',function(){if(bed) bed.value='';if(pick) pick.textContent='';step(1);});
    }
  });

  /* Mobile number, as in js/ppc-hims.js: "+91" in its own box, the field takes the 10-digit number, and "+91 " is
     added to the value CF7 sends (the server check needs the country code). Pasted numbers keep their last 10 digits. */
  var CODE='+91',MAX=10;
  function digits(val){var d=val.replace(/\D/g,'').replace(/^0+/,'');return d.length>MAX?d.slice(-MAX):d;}
  document.querySelectorAll('#a-form .hr-demo-form input[name="your-number"], #myPopup .hr-demo-form input[name="your-number"]').forEach(function(input){
    var wrapPh=document.createElement('span'),cc=document.createElement('span'),f=input.closest('form');
    wrapPh.className='hr-phone';cc.className='hr-cc';cc.textContent=CODE;cc.setAttribute('aria-hidden','true');
    input.parentNode.insertBefore(wrapPh,input);wrapPh.appendChild(cc);wrapPh.appendChild(input);
    input.value=digits(input.value);input.placeholder='98765 43210';
    input.setAttribute('inputmode','numeric');input.setAttribute('autocomplete','tel-national');input.setAttribute('aria-label','Mobile number, India '+CODE);
    input.addEventListener('input',function(e){
      var next=e.inputType==='insertText'?input.value.replace(/\D/g,'').replace(/^0+/,'').slice(0,MAX):digits(input.value);
      if(next!==input.value) input.value=next;
    });
    if(f) f.addEventListener('formdata',function(e){var d=digits(input.value);e.formData.set('your-number',d?CODE+' '+d:'');});
  });
  /* video: count a view once it starts */
  (function(){var v=document.getElementById('walkVideo'),sent=false;if(v)v.addEventListener('playing',function(){if(!sent){sent=true;pushEvent('billing_video_view');}});})();

  // Section 2: bed icon on every chip, then a hidden copy of the list so the strip loops without a gap
  var bed='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>';
  document.querySelectorAll('#lglist .lg-badge').forEach(function(b){ b.insertAdjacentHTML('afterbegin',bed); });
  var copy=document.getElementById('lglist').cloneNode(true); copy.removeAttribute('id'); copy.setAttribute('aria-hidden','true');
  copy.querySelectorAll('img').forEach(function(i){ i.alt=''; });
  document.getElementById('lgtrack').appendChild(copy);

  // Section 3: video loads when it comes on screen, plays muted on a loop; play/pause and full screen buttons
  var player=document.getElementById('player'), vid=document.getElementById('walkVideo'), src=vid.querySelector('source'), btn=document.getElementById('walkBtn'), userPaused=false;
  var still=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function load(){ if(!src.src){ src.src=src.getAttribute('data-src'); vid.load(); } }
  // the billing cut has no title card, so the video plays from 0 (no skip needed)
  function play(){ load(); var p=vid.play(); if(p&&p.catch) p.catch(function(){}); }
  vid.addEventListener('playing',function(){ player.classList.add('is-ready','is-playing'); btn.setAttribute('aria-label','Pause the video tour'); });
  vid.addEventListener('pause',function(){ player.classList.remove('is-playing'); btn.setAttribute('aria-label','Play the video tour'); });
  btn.addEventListener('click',function(){ if(vid.paused){ userPaused=false; play(); } else { userPaused=true; vid.pause(); } });
  document.getElementById('walkFs').addEventListener('click',function(){ play(); var el=player; (el.requestFullscreen||el.webkitRequestFullscreen||function(){}).call(el); });
  if(!still && 'IntersectionObserver' in window){
    new IntersectionObserver(function(es){ es.forEach(function(e){ if(e.isIntersecting){ if(!userPaused) play(); } else if(!vid.paused){ vid.pause(); } }); },{threshold:.4}).observe(player);
  }

  // Section 4: click a leak, its screen shows (on a phone the list opens one leak at a time)
  var items=document.querySelectorAll('#lklist .lk'), aimgs=document.querySelectorAll('#rbshot img');
  items.forEach(function(li,i){ li.querySelector('button').addEventListener('click',function(){
    items.forEach(function(x,j){ x.classList.toggle('on', j===i); x.querySelector('button').setAttribute('aria-expanded', j===i?'true':'false'); });
    aimgs.forEach(function(im,j){ im.classList.toggle('on', j===i); });
  }); });

  // Section 5: centre carousel. Middle card in front, one card each side, loops; slides every 4.5 s while on screen
  var s5still=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var cm=document.getElementById('cm5'), cms=[].slice.call(cm.querySelectorAll('.cm-card')), dotsBox=document.getElementById('cmdots'), cmCur=0, cmTimer=null, cmSeen=false;
  cms.forEach(function(c,i){ var d=document.createElement('button'); d.type='button'; d.setAttribute('aria-label','Show bill '+(i+1)); d.addEventListener('click',function(){cmGo(i,true);}); dotsBox.appendChild(d); });
  function cmPaint(){
    var n=cms.length, W=cms[0].offsetWidth, gap=Math.max(16,W*0.06), h=0;
    cms.forEach(function(c,i){ var o=((i-cmCur+n+1)%n)-1; var x=o*(W+gap);
      c.style.transform='translateX(calc(-50% + '+x+'px)) scale('+(o===0?1:0.88)+')';
      c.style.opacity=o===0?1:(o===2?0:0.5); c.style.zIndex=o===0?3:(o===2?1:2); c.style.pointerEvents=o===2?'none':'auto';
      c.classList.toggle('on',o===0); c.setAttribute('aria-hidden',o===0?'false':'true'); h=Math.max(h,c.offsetHeight); });
    cm.style.height=h+'px';
    [].forEach.call(dotsBox.children,function(d,i){ d.setAttribute('aria-current',i===cmCur?'true':'false'); });
  }
  function cmGo(i,byHand){ cmCur=(i+cms.length)%cms.length; cmPaint(); if(byHand) cmStart(); }
  function cmStart(){ clearInterval(cmTimer); if(!s5still && cmSeen) cmTimer=setInterval(function(){cmGo(cmCur+1);},4500); }
  cms.forEach(function(c,i){ c.addEventListener('click',function(){ if(i!==cmCur) cmGo(i,true); }); });
  document.querySelector('.cm-prev').addEventListener('click',function(){cmGo(cmCur-1,true);});
  document.querySelector('.cm-next').addEventListener('click',function(){cmGo(cmCur+1,true);});
  var sx=null; cm.addEventListener('pointerdown',function(e){sx=e.clientX;}); cm.addEventListener('pointerup',function(e){ if(sx===null)return; var dx=e.clientX-sx; sx=null; if(Math.abs(dx)>40) cmGo(cmCur+(dx<0?1:-1),true); });
  cm.addEventListener('mouseenter',function(){clearInterval(cmTimer);}); cm.addEventListener('mouseleave',cmStart);
  addEventListener('resize',cmPaint);
  cm.querySelectorAll('img').forEach(function(im){ im.addEventListener('load',cmPaint); });
  cmPaint();
  if('IntersectionObserver' in window){ new IntersectionObserver(function(es){ es.forEach(function(e){ cmSeen=e.isIntersecting; if(cmSeen){cmPaint(); cmStart();} else clearInterval(cmTimer); }); },{threshold:.4}).observe(cm); }

  // Section 7: arrows move the photo cards one card; hidden when all cards fit
  var deck7=document.getElementById('deck7'), pv7=document.querySelector('.p7'), nx7=document.querySelector('.n7');
  function step7(){ var c=deck7.querySelector('.dc7'); return c ? c.offsetWidth+20 : 300; }
  function arrows7(){ var fits=deck7.scrollWidth<=deck7.clientWidth+2; document.querySelector('.ar7').style.visibility=fits?'hidden':'visible'; pv7.disabled=deck7.scrollLeft<4; nx7.disabled=deck7.scrollLeft+deck7.clientWidth>=deck7.scrollWidth-4; }
  pv7.addEventListener('click',function(){deck7.scrollBy({left:-step7(),behavior:'smooth'});});
  nx7.addEventListener('click',function(){deck7.scrollBy({left:step7(),behavior:'smooth'});});
  deck7.addEventListener('scroll',function(){requestAnimationFrame(arrows7);},{passive:true});
  addEventListener('resize',arrows7); arrows7();
})();
