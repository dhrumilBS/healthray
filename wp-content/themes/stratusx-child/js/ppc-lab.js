(function(){
  var CFG=window.HR_LAB||{};CFG.uploads=CFG.uploads||'https://healthray.com/wp-content/uploads';
  /* 1. heading per ad group (?k=) */
  var HEAD={
    diag:['Diagnostic centre software','Bill, report and WhatsApp. One easy software for your diagnostic centre.'],
    'switch':['Pathology lab software','Changing your lab software? Bill, report and WhatsApp in one easy place.'],
    lis:['Lab information system (LIS)','Bill, report and WhatsApp. One easy software for your whole lab.']
  };
  try{
    var k=new URLSearchParams(location.search).get('k');
    if(k&&HEAD[k]){document.getElementById('eyebrow').textContent=HEAD[k][0];document.getElementById('headline').textContent=HEAD[k][1];}
  }catch(e){}

  /* 2. lab logo row (listed twice for a seamless loop) */
  var LABS=[
    ['2024/01/Pratham-lab-1.webp','Pratham Laboratory','Surat, Gujarat'],
    ['2024/01/Diabcare-lab-1.webp','Diabcare Laboratory','Mumbai, Maharashtra'],
    ['2024/06/Om-Pathology-Lab-Healthray.webp','Om Pathology Lab','Surat, Gujarat'],
    ['2024/06/Bansal-Diagnostics-Healthray.webp','Bansal Diagnostics','Kurukshetra, Haryana'],
    ['2024/06/Omed-Diagnostics-Healthray.webp','Omed Diagnostics','Cachar, Assam'],
    ['2024/06/Chakraa-Medical-Center-Healthray.webp','Chakraa Medical Center','Tiruppur, Tamil Nadu'],
    ['2024/06/Shiv-Krishna-Hospital-Healthray.webp','Shiv Krishna Hospital','Ghaziabad, Uttar Pradesh'],
    ['2024/04/Universal-Multispecialty-Hospital.webp','Universal Hospital','Surat, Gujarat'],
    ['2024/06/Gastron.webp','Gastron Super Speciality Hospital','Surat, Gujarat'],
    ['2024/11/Sri-Manakula-Vinayagar-Hospital.webp','Sri Manakula Vinayagar Hospital','Pondicherry']
  ];
  var track=document.getElementById('track');
  [false,true].forEach(function(copy){
    LABS.forEach(function(l){
      var d=document.createElement('div');d.className='lab';
      if(copy)d.setAttribute('aria-hidden','true');
      var g=document.createElement('span');g.className='lg';
      var im=document.createElement('img');im.src=CFG.uploads+'/'+l[0];im.alt=copy?'':l[1]+' logo';im.width=120;im.height=54;im.loading='lazy';
      g.appendChild(im);d.appendChild(g);
      var b=document.createElement('b');b.textContent=l[1];d.appendChild(b);
      var s=document.createElement('span');s.textContent=l[2];d.appendChild(s);
      track.appendChild(d);
    });
  });

  var calm=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function onScreen(el){var r=el.getBoundingClientRect(),vh=window.innerHeight||document.documentElement.clientHeight;return r.bottom>vh*.25&&r.top<vh*.75;}

  /* 3. gallery */
  var gal=document.getElementById('gal'),pns=[].slice.call(gal.querySelectorAll('.pn')),gdots=[].slice.call(document.querySelectorAll('#gdots button')),gAt=0,gHold=0,gSwiped=false,gx=null;
  function gShow(n){
    gAt=(n+pns.length)%pns.length;
    pns.forEach(function(p,i){var d=(i-gAt+pns.length)%pns.length;if(d===pns.length-1)d=-1;p.setAttribute('data-pos',d);});
    gdots.forEach(function(b,i){b.classList.toggle('on',i===gAt);b.setAttribute('aria-pressed',i===gAt?'true':'false');});
  }
  function gUser(n){gShow(n);gHold=Date.now()+8000;}
  pns.forEach(function(p,i){p.addEventListener('click',function(){if(gSwiped||i===gAt)return;gUser(i);});});
  gdots.forEach(function(b,i){b.addEventListener('click',function(){gUser(i);});});
  [].forEach.call(document.querySelectorAll('.arrows button'),function(b){b.addEventListener('click',function(){gUser(gAt+(b.classList.contains('prev')?-1:1));});});
  gal.addEventListener('pointerdown',function(e){gx=e.clientX;});
  gal.addEventListener('pointerup',function(e){
    if(gx===null)return;var dx=e.clientX-gx;gx=null;
    if(Math.abs(dx)>40){gSwiped=true;setTimeout(function(){gSwiped=false;},60);gUser(gAt+(dx<0?1:-1));}
  });
  gal.addEventListener('pointercancel',function(){gx=null;});
  gal.addEventListener('keydown',function(e){if(e.key==='ArrowRight')gUser(gAt+1);if(e.key==='ArrowLeft')gUser(gAt-1);});
  gal.addEventListener('mouseenter',function(){gHold=Infinity;});
  gal.addEventListener('mouseleave',function(){gHold=Date.now()+1500;});
  gShow(0);
  if(!calm)setInterval(function(){if(Date.now()>gHold&&onScreen(gal))gShow(gAt+1);},4000);

  /* 3a. reviews auto-slide */
  var rvrow=document.getElementById('rvrow'),rvHold=0;
  function rvStep(dir){
    var c=rvrow.querySelector('.rv'),step=c.offsetWidth+(parseFloat(getComputedStyle(rvrow).columnGap)||16),max=rvrow.scrollWidth-rvrow.clientWidth;
    if(dir>0&&rvrow.scrollLeft>=max-4)rvrow.scrollTo({left:0,behavior:'smooth'});
    else if(dir<0&&rvrow.scrollLeft<=4)rvrow.scrollTo({left:max,behavior:'smooth'});
    else rvrow.scrollBy({left:step*dir,behavior:'smooth'});
  }
  [].forEach.call(document.querySelectorAll('.rv-arrows button'),function(b){
    b.addEventListener('click',function(){rvHold=Date.now()+8000;rvStep(b.classList.contains('prev')?-1:1);});
  });
  ['pointerdown','touchstart','focusin'].forEach(function(ev){rvrow.addEventListener(ev,function(){rvHold=Date.now()+8000;},{passive:true});});
  rvrow.addEventListener('mouseenter',function(){rvHold=Infinity;});
  rvrow.addEventListener('mouseleave',function(){rvHold=Date.now()+1500;});
  if(!calm)setInterval(function(){if(Date.now()>rvHold&&onScreen(rvrow))rvStep(1);},4000);

  /* 3b. report lines */
  var rpnl=document.getElementById('rpnl'),rbox=document.getElementById('rtabs'),rtabs=[].slice.call(rbox.querySelectorAll('.tab')),rEl=0;
  function rshow(k){
    rEl=0;
    rpnl.setAttribute('data-on',k);
    rtabs.forEach(function(t){t.classList.remove('on');t.setAttribute('aria-pressed','false');});
    void rbox.offsetWidth; /* restart the line animation */
    rtabs.forEach(function(t){if(t.getAttribute('data-k')===k){t.classList.add('on');t.setAttribute('aria-pressed','true');}});
  }
  function rSeen(){
    var a=rbox.getBoundingClientRect(),b=rpnl.getBoundingClientRect(),vh=window.innerHeight||document.documentElement.clientHeight;
    return Math.max(a.bottom,b.bottom)>vh*.3&&Math.min(a.top,b.top)<vh*.7;
  }
  rtabs.forEach(function(t){t.addEventListener('click',function(){rshow(t.getAttribute('data-k'));});});
  if(calm){rbox.classList.add('calm');}
  else{
    rbox.classList.add('hold');
    setInterval(function(){
      var go=rSeen();
      rbox.classList.toggle('hold',!go);
      if(!go)return;
      rEl+=100;
      if(rEl>=4500){
        var keys=rtabs.map(function(t){return t.getAttribute('data-k');});
        rshow(keys[(keys.indexOf(rpnl.getAttribute('data-on'))+1)%keys.length]);
      }
    },100);
  }

  /* 4. CF7 2-step form; mobile sent as +91 + 10 digits */
  window.dataLayer=window.dataLayer||[];
  function pushEvent(name,extra){var d={event:name,form:'lab_software_demo_ppc'};for(var k in (extra||{}))d[k]=extra[k];window.dataLayer.push(d);}
  (function(){
    var box=document.getElementById('hr-lab-form'),f=box&&box.querySelector('form.wpcf7-form');
    var s1=f&&f.querySelector('.hr-s1'),s2=f&&f.querySelector('.hr-s2');
    if(!s1||!s2)return;
    var bed=f.querySelector('input[name="bed_size"]'),opts=[].slice.call(s1.querySelectorAll('.opt'));
    var err1=s1.querySelector('.err'),err2=s2.querySelector('.err'),pv=s2.querySelector('.pv'),back=s2.querySelector('.back');
    var mob=f.querySelector('input[name="your-number"]'),stepno=box.querySelector('.stepno'),sentKind='';
    function where(){return box.closest('#lab-pop')?'popup':'hero';}
    var live=document.createElement('span');live.className='hr-sr';live.setAttribute('role','status');live.setAttribute('aria-live','polite');box.appendChild(live);

    function step(n,focus){
      s1.hidden=n!==1;s2.hidden=n!==2;
      box.classList.toggle('step2',n===2);
      if(stepno)stepno.textContent=n;
      live.textContent='Step '+n+' of 2';
      if(pv)pv.textContent=bed?bed.value:'';
      if(!focus)return;
      var t=n===2?f.querySelector('input[name="your-name"]'):(s1.querySelector('.opt[aria-pressed="true"]')||opts[0]);
      if(t)t.focus({preventScroll:true});
    }
    function pick(o){
      opts.forEach(function(b){b.setAttribute('aria-pressed',b===o?'true':'false');});
      if(bed)bed.value=o?o.getAttribute('data-v'):'';
    }
    opts.forEach(function(o){
      o.addEventListener('click',function(){
        pick(o);
        if(err1)err1.hidden=true;
        pushEvent('lab_form_start',{lab_type:bed.value,form_position:where()});
        step(2,true);
      });
    });
    if(back)back.addEventListener('click',function(){step(1,true);});

    /* mobile: digits only */
    function digits(v){
      var d=String(v||'').replace(/\D/g,'');
      if(d.length===12&&d.slice(0,2)==='91')return d.slice(2);
      if(d.length===11&&d.charAt(0)==='0')return d.slice(1);
      return d.slice(0,10);
    }
    if(mob){
      mob.setAttribute('inputmode','numeric');mob.setAttribute('maxlength','14');
      mob.addEventListener('input',function(){mob.value=digits(mob.value);if(err2)err2.hidden=true;});
    }
    f.addEventListener('formdata',function(e){
      var d=mob?digits(mob.value):'';
      e.formData.set('your-number',/^\d{10}$/.test(d)?'+91 '+d:'');
    });

    /* capture: runs before CF7's submit */
    f.addEventListener('submit',function(e){
      function stop(){e.preventDefault();e.stopImmediatePropagation();}
      if(!bed||!bed.value){stop();if(err1)err1.hidden=false;step(1,true);return;}
      if(mob&&!/^[6-9]\d{9}$/.test(digits(mob.value))){
        stop();
        if(err2){err2.textContent='Please write a 10-digit mobile number.';err2.hidden=false;}
        mob.focus();return;
      }
      if(err2)err2.hidden=true;
      sentKind=bed.value;
    },true);

    box.addEventListener('wpcf7mailsent',function(){pushEvent('lab_form_sent',{lab_type:sentKind,form_position:where()});});
    box.addEventListener('wpcf7invalid',function(e){
      var bad=(e.detail&&e.detail.apiResponse&&e.detail.apiResponse.invalid_fields)||[];
      step(bad.some(function(x){return x.field==='bed_size';})?1:2,false);
    });
    /* clear bed_size on reset */
    f.addEventListener('reset',function(){setTimeout(function(){pick(null);step(1,false);},0);});

    step(1,false);
  })();

  /* 4b. preload pictures near their section */
  ['gal','rpnl'].forEach(function(id){
    var box=document.getElementById(id);if(!box)return;
    var t=setInterval(function(){
      var r=box.getBoundingClientRect(),vh=window.innerHeight||document.documentElement.clientHeight;
      if(r.top<vh*2&&r.bottom>-vh){[].forEach.call(box.querySelectorAll('img'),function(i){i.loading='eager';});clearInterval(t);}
    },400);
  });

  /* 4c. phone bottom bar */
  (function(){
    var bar=document.querySelector('.bar'),form=document.querySelector('#demo .fm')||document.getElementById('demo'),last=document.getElementById('book');
    if(!bar||!form)return;
    var wa=document.querySelector('.wa-bt');
    if(wa)wa.addEventListener('click',function(){pushEvent('lab_whatsapp_click');});
    var btns=[].slice.call(document.querySelectorAll('main .bt, main .wa-bt'));
    function seen(el){var r=el.getBoundingClientRect(),vh=window.innerHeight||document.documentElement.clientHeight;return (r.width>0||r.height>0)&&r.bottom>70&&r.top<vh-70;}
    function upd(){
      var hide=seen(form)||(!!last&&seen(last));
      for(var i=0;!hide&&i<btns.length;i++)hide=seen(btns[i]);
      bar.classList.toggle('off',hide);
    }
    upd();window.addEventListener('scroll',upd,{passive:true});window.addEventListener('resize',upd);
  })();

  /* 4d. demo popup: moves the hero form card in, back on close */
  (function(){
    var pop=document.getElementById('lab-pop'),card=document.getElementById('hr-lab-form');
    if(!pop||!card)return;
    var inner=pop.querySelector('.lab-pop-in'),x=pop.querySelector('.lab-pop-x'),hold=document.createElement('div'),from=null;
    hold.className='fm-hold';hold.setAttribute('aria-hidden','true');
    function open(e){
      e.preventDefault();
      if(!pop.hidden)return;
      from=e.currentTarget;
      hold.style.height=card.offsetHeight+'px';
      card.parentNode.insertBefore(hold,card);
      inner.appendChild(card);
      pop.hidden=false;
      document.documentElement.classList.add('lab-pop-on');
      var t=card.querySelector('.hr-s1:not([hidden]) .opt[aria-pressed="true"], .hr-s1:not([hidden]) .opt, .hr-s2:not([hidden]) input[name="your-name"]');
      if(t)t.focus({preventScroll:true});
      pushEvent('lab_popup_open',{button:(from.textContent||'').trim()});
    }
    function close(){
      if(pop.hidden)return;
      hold.parentNode.insertBefore(card,hold);
      hold.parentNode.removeChild(hold);
      pop.hidden=true;
      document.documentElement.classList.remove('lab-pop-on');
      if(from)from.focus({preventScroll:true});
    }
    [].forEach.call(document.querySelectorAll('[data-lab-pop]'),function(a){a.addEventListener('click',open);});
    x.addEventListener('click',close);
    pop.addEventListener('click',function(e){if(e.target===pop)close();});
    document.addEventListener('keydown',function(e){
      if(pop.hidden)return;
      if(e.key==='Escape'){close();return;}
      /* keep Tab inside */
      if(e.key!=='Tab')return;
      var els=[].filter.call(pop.querySelectorAll('button,input:not([type=hidden]),a[href]'),function(el){return el.offsetParent!==null;});
      if(!els.length)return;
      var a=els[0],b=els[els.length-1];
      if(e.shiftKey&&document.activeElement===a){e.preventDefault();b.focus();}
      else if(!e.shiftKey&&document.activeElement===b){e.preventDefault();a.focus();}
    });
  })();

  /* 4e. FAQ: one open at a time */
  (function(){
    var qs=[].slice.call(document.querySelectorAll('details[name="lab-faq"]'));
    qs.forEach(function(d){
      d.addEventListener('toggle',function(){
        if(d.open)qs.forEach(function(o){if(o!==d&&o.open)o.open=false;});
      });
    });
  })();

  /* 5. video */
  (function(){
    var box=document.getElementById('player'),btn=document.getElementById('playbtn'),pp=document.getElementById('playpp'),fs=document.getElementById('playfs');
    var v=null,stopped=false,pressed=false;
    function state(){
      var going=!!v&&!v.paused&&!v.ended;
      box.classList.toggle('playing',going);
      btn.setAttribute('aria-label',going?'Pause the video':'Play the 1-minute video');
      pp.setAttribute('aria-label',going?'Pause':'Play');
    }
    function make(){
      if(v)return false;
      v=document.createElement('video');
      v.muted=true;v.defaultMuted=true;v.playsInline=true;v.setAttribute('muted','');v.setAttribute('playsinline','');v.preload='auto';
      v.src=box.getAttribute('data-src');
      ['play','pause','ended'].forEach(function(e){v.addEventListener(e,state);});
      v.addEventListener('ended',function(){pushEvent('lab_video_done');});
      box.insertBefore(v,btn);box.classList.add('on');
      return true;
    }
    function play(){var p=v.play();if(p&&p.catch)p.catch(function(){state();});}
    function toggle(){
      make();
      if(v.paused||v.ended){stopped=false;play();if(!pressed){pressed=true;pushEvent('lab_video_play');}}
      else{stopped=true;v.pause();}
    }
    btn.addEventListener('click',toggle);
    pp.addEventListener('click',toggle);

    function fsEl(){return document.fullscreenElement||document.webkitFullscreenElement||null;}
    function phoneFull(){if(v&&v.webkitEnterFullscreen){try{v.webkitEnterFullscreen();}catch(e){}}}
    fs.addEventListener('click',function(){
      if(!v)return;
      try{
        if(fsEl()){(document.exitFullscreen||document.webkitExitFullscreen).call(document);return;}
        var go=box.requestFullscreen||box.webkitRequestFullscreen;
        if(go){var q=go.call(box);if(q&&q.catch)q.catch(phoneFull);}
        else phoneFull();
      }catch(e){}
    });
    ['fullscreenchange','webkitfullscreenchange'].forEach(function(n){
      document.addEventListener(n,function(){
        var on=fsEl()===box;
        box.classList.toggle('full',on);
        fs.setAttribute('aria-label',on?'Leave full screen':'Full screen');
      });
    });

    var saver=!!(navigator.connection&&navigator.connection.saveData);
    if(!calm&&!saver&&'IntersectionObserver' in window){
      new IntersectionObserver(function(es){
        var seen=es[es.length-1].intersectionRatio>=.5;
        if(seen){
          if(stopped||(v&&v.ended))return;
          if(make())pushEvent('lab_video_view');
          if(v.paused)play();
        }
        else if(v&&!v.paused&&!fsEl())v.pause();
      },{threshold:[0,.5]}).observe(box);
    }
  })();
})();