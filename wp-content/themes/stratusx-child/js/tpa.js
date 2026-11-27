(function(){
  window.dataLayer=window.dataLayer||[];
  function pushEvent(name,extra){var d={event:name,form:'tpa_insurance_software_demo'};for(var k in (extra||{}))d[k]=extra[k];window.dataLayer.push(d);}

  [].forEach.call(document.querySelectorAll('.hr-tpa .hr-demo-form'),function(box){
    var where=box.closest('#hr-pop')?'popup':'hero', wrap=box.closest('.wpcf7')||box,
        s1=box.querySelector('.hr-step-1'), s2=box.querySelector('.hr-step-2'),
        bedIn=box.querySelector('[name="bed_size"]'), bedOut=box.querySelector('.hr-selected-bed-value'), started=false;
    function step(n){ if(s1)s1.style.display=(n===1?'':'none'); if(s2)s2.style.display=(n===2?'':'none'); }
    [].forEach.call(box.querySelectorAll('.hr-bed-option'),function(b){ b.addEventListener('click',function(){
      var v=b.getAttribute('data-bed')||'';
      if(bedIn)bedIn.value=v; if(bedOut)bedOut.textContent=v;
      [].forEach.call(box.querySelectorAll('.hr-bed-option'),function(x){x.classList.toggle('is-picked',x===b);});
      step(2);
      var n=box.querySelector('[name="your-name"]'); if(n)n.focus({preventScroll:true});
      if(!started){started=true;pushEvent('tpa_form_start',{bed_size:v,form_position:where});}
    });});
    [].forEach.call(box.querySelectorAll('.hr-back-step,.hr-change-bed'),function(b){ b.addEventListener('click',function(){
      step(1); var f=box.querySelector('.hr-bed-option.is-picked')||box.querySelector('.hr-bed-option'); if(f)f.focus({preventScroll:true});
    });});

    var mob=box.querySelector('[name="your-number"]');
    if(mob)mob.addEventListener('input',function(){var v=this.value.replace(/\D/g,'');if(v.length>10&&v.indexOf('91')===0)v=v.slice(2);if(v.length>10&&v.charAt(0)==='0')v=v.slice(1);this.value=v.slice(0,10);});
    wrap.addEventListener('wpcf7mailsent',function(){
      pushEvent('tpa_form_sent',{bed_size:bedIn?bedIn.value:'',form_position:where});
    });
  });

  var pop=document.getElementById('hr-pop');
  if(pop&&typeof pop.showModal==='function'){
    var lastBtn=null;
    function openPop(from){
      lastBtn=from||null;
      if(!pop.open)pop.showModal();
      document.documentElement.classList.add('hr-pop-open');
      var f=pop.querySelector('.hr-step-1')&&pop.querySelector('.hr-step-1').style.display!=='none'?pop.querySelector('.hr-bed-option'):pop.querySelector('[name="your-name"]');
      if(f)f.focus({preventScroll:true});
      pushEvent('tpa_popup_open',{button:from?(from.textContent||'').trim().slice(0,60):''});
    }
    function closePop(){ if(pop.open)pop.close(); }
    pop.addEventListener('close',function(){ document.documentElement.classList.remove('hr-pop-open'); if(lastBtn&&lastBtn.focus)lastBtn.focus({preventScroll:true}); });
    pop.querySelector('.hr-pop-x').addEventListener('click',closePop);
    pop.addEventListener('click',function(e){ if(e.target===pop)closePop(); });
    document.addEventListener('click',function(e){
      var a=e.target.closest&&e.target.closest('.hr-tpa a[href="#a-form"]');
      if(!a||pop.contains(a))return;
      e.preventDefault(); openPop(a);
    });
  }

  (function(){var v=document.getElementById('walkVideo'),sent=false;if(v)v.addEventListener('playing',function(){if(!sent){sent=true;pushEvent('tpa_video_view');}});})();

  var bed='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>';
  document.querySelectorAll('#lglist .lg-badge').forEach(function(b){ b.insertAdjacentHTML('afterbegin',bed); });
  var copy=document.getElementById('lglist').cloneNode(true); copy.removeAttribute('id'); copy.setAttribute('aria-hidden','true');
  copy.querySelectorAll('img').forEach(function(i){ i.alt=''; });
  document.getElementById('lgtrack').appendChild(copy);

  var player=document.getElementById('player'), vid=document.getElementById('walkVideo'), src=vid.querySelector('source'), btn=document.getElementById('walkBtn'), userPaused=false;
  var still=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function load(){ if(!src.src){ src.src=src.getAttribute('data-src'); vid.load(); } }

  function play(){ load(); var p=vid.play(); if(p&&p.catch) p.catch(function(){}); }
  vid.addEventListener('playing',function(){ player.classList.add('is-ready','is-playing'); btn.setAttribute('aria-label','Pause the video tour'); });
  vid.addEventListener('pause',function(){ player.classList.remove('is-playing'); btn.setAttribute('aria-label','Play the video tour'); });
  btn.addEventListener('click',function(){ if(vid.paused){ userPaused=false; play(); } else { userPaused=true; vid.pause(); } });
  document.getElementById('walkFs').addEventListener('click',function(){ play(); var el=player; (el.requestFullscreen||el.webkitRequestFullscreen||function(){}).call(el); });
  if(!still && 'IntersectionObserver' in window){
    new IntersectionObserver(function(es){ es.forEach(function(e){ if(e.isIntersecting){ if(!userPaused) play(); } else if(!vid.paused){ vid.pause(); } }); },{threshold:.4}).observe(player);
  }

  function onScreen(el){ var r=el.getBoundingClientRect(), vh=window.innerHeight||document.documentElement.clientHeight; return !document.hidden && r.bottom>vh*.25 && r.top<vh*.75; }

  var m4list=document.getElementById('m4a-list'), m4items=[].slice.call(m4list.querySelectorAll('li')), m4pics=document.querySelectorAll('#m4a-pic img'), m4box=m4list.parentNode, m4At=0, m4El=0, m4Mouse=false;
  function m4show(i){
    m4At=i; m4El=0;
    m4items.forEach(function(x){ x.classList.remove('on'); x.querySelector('button').setAttribute('aria-expanded','false'); });
    void m4list.offsetWidth;
    m4items[i].classList.add('on'); m4items[i].querySelector('button').setAttribute('aria-expanded','true');
    m4pics.forEach(function(p,j){ p.classList.toggle('on',j===i); });
  }
  function m4fit(){
    m4list.style.minHeight='';

    if(getComputedStyle(document.getElementById('m4a-pic')).display==='none') return;
    var keep=m4At, h=0;
    m4items.forEach(function(x,i){ m4items.forEach(function(y,j){ y.classList.toggle('on',j===i); }); h=Math.max(h,m4list.offsetHeight); });
    m4items.forEach(function(y,j){ y.classList.toggle('on',j===keep); });
    m4list.style.minHeight=h+'px';
  }
  m4items.forEach(function(li,i){ li.querySelector('button').addEventListener('click',function(){ m4show(i); }); });
  m4box.addEventListener('mouseenter',function(){ m4Mouse=true; }); m4box.addEventListener('mouseleave',function(){ m4Mouse=false; });
  m4fit(); addEventListener('resize',m4fit); addEventListener('load',m4fit);
  if(still){ m4list.classList.add('calm'); }
  else{
    m4list.classList.add('hold');
    setInterval(function(){
      var go=onScreen(m4box) && !m4Mouse;
      m4list.classList.toggle('hold',!go);
      if(!go) return;
      m4El+=100;
      if(m4El>=6000) m4show((m4At+1)%m4items.length);
    },100);
  }

  var trk=document.getElementById('o6a-trk'), cards=trk.querySelectorAll('.cd'), dots=document.getElementById('o6a-dots'), pv=document.getElementById('o6a-prev'), nx=document.getElementById('o6a-next');
  cards.forEach(function(c,i){ var d=document.createElement('button'); d.type='button'; d.setAttribute('aria-label','Show '+(i+1)+' of '+cards.length); d.addEventListener('click',function(){ trk.scrollTo({left:c.offsetLeft-trk.offsetLeft,behavior:'smooth'}); }); dots.appendChild(d); });
  function cur(){ var x=trk.scrollLeft,b=0,bd=1e9; cards.forEach(function(c,i){ var d=Math.abs(c.offsetLeft-trk.offsetLeft-x); if(d<bd){bd=d;b=i;} }); return b; }
  function paint(){ var k=cur(); [].forEach.call(dots.children,function(d,i){ d.setAttribute('aria-current',i===k?'true':'false'); }); pv.disabled=trk.scrollLeft<4; nx.disabled=trk.scrollLeft+trk.clientWidth>=trk.scrollWidth-4; }
  function go(s){ var k=Math.max(0,Math.min(cards.length-1,cur()+s)); trk.scrollTo({left:cards[k].offsetLeft-trk.offsetLeft,behavior:'smooth'}); }
  pv.addEventListener('click',function(){go(-1);}); nx.addEventListener('click',function(){go(1);});
  trk.addEventListener('scroll',function(){requestAnimationFrame(paint);},{passive:true}); addEventListener('resize',paint); paint();

  var o6Hold=0;
  function o6user(){ o6Hold=Date.now()+8000; }
  [pv,nx].forEach(function(b){ b.addEventListener('click',o6user); });
  dots.addEventListener('click',o6user);
  ['pointerdown','touchstart','wheel','focusin'].forEach(function(ev){ trk.addEventListener(ev,o6user,{passive:true}); });
  trk.addEventListener('mouseenter',function(){ o6Hold=Infinity; }); trk.addEventListener('mouseleave',function(){ o6Hold=Date.now()+1500; });
  if(!still) setInterval(function(){
    if(Date.now()<o6Hold || !onScreen(trk)) return;
    if(trk.scrollLeft+trk.clientWidth>=trk.scrollWidth-4) trk.scrollTo({left:0,behavior:'smooth'}); else go(1);
  },4000);
  })();