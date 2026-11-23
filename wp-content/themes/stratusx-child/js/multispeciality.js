/* Healthray solutions page: multispeciality hospitals. Loaded only on this page template. */
(function(){
  var ROOT=document.querySelector('.msh'); if(!ROOT) return;
  var BASE=ROOT.getAttribute('data-base')||'';
  window.dataLayer=window.dataLayer||[];
  function push(ev,extra){var o={event:ev,form_name:'multispeciality_demo'};for(var k in (extra||{}))o[k]=extra[k];window.dataLayer.push(o);}
  /* logo strip: a second copy makes the slide loop without a gap */
  var ll=document.getElementById('logoList'),cp=ll.cloneNode(true);cp.removeAttribute('id');cp.setAttribute('aria-hidden','true');cp.querySelectorAll('img').forEach(function(i){i.alt='';});ll.parentNode.appendChild(cp);

  /* scroll story: the screen on the right follows the step in the middle of the screen */
  var sImgs=document.querySelectorAll('#screens img'),sCnt=document.getElementById('count'),sSteps=document.querySelectorAll('.step'),sCur=-1,sTick=false;
  function sPick(){
    sTick=false;
    var mid=innerHeight/2,best=0,bd=1e9;
    sSteps.forEach(function(s,k){var r=s.getBoundingClientRect();var d=Math.abs(r.top+r.height/2-mid);if(d<bd){bd=d;best=k;}});
    if(best===sCur)return; sCur=best;
    sSteps.forEach(function(s,k){s.classList.toggle('on',k===best);});
    sImgs.forEach(function(m,k){m.classList.toggle('on',k===best);});
    sCnt.textContent=(best+1)+' of 4';
  }
  addEventListener('scroll',function(){if(!sTick){sTick=true;requestAnimationFrame(sPick);}},{passive:true});
  addEventListener('resize',sPick); sPick();

  /* results deck: arrows move one card */
  var dk=document.getElementById('deck');function dStep(d){var c=dk.querySelector('.dc');dk.scrollBy({left:d*(c.getBoundingClientRect().width+18),behavior:'smooth'});}
  document.getElementById('rdPrev').addEventListener('click',function(){dStep(-1);});document.getElementById('rdNext').addEventListener('click',function(){dStep(1);});

  /* product video: no controls, plays when on screen */
  var v=document.getElementById('walk'),fr=v.closest('.frame'),pb=document.getElementById('vplay');
  var calm=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches, userPaused=false;
  function go(){v.preload='auto';v.play().catch(function(){fr.classList.add('paused');});}
  function toggle(){if(v.paused){userPaused=false;go();}else{userPaused=true;v.pause();fr.classList.add('paused');}}
  v.addEventListener('click',toggle); pb.addEventListener('click',toggle);
  v.addEventListener('playing',function(){fr.classList.remove('paused');});
  if(calm) fr.classList.add('paused');
  if('IntersectionObserver' in window){
    new IntersectionObserver(function(es){es.forEach(function(e){
      if(e.isIntersecting){ if(!calm&&!userPaused) go(); } else if(!v.paused){v.pause();}
    });},{threshold:.45}).observe(v);
  }

  /* customer interview: plays in place with sound and controls */
  document.getElementById('playStory').addEventListener('click',function(){push('video_play',{video:'universal_interview'});
    var c=document.getElementById('vstory'), r=document.createElement('video');
    r.src=c.getAttribute('data-src')||(BASE+'video/universal-review-web.mp4');r.controls=true;r.autoplay=true;r.playsInline=true;r.setAttribute('aria-label','Video interview with Divyesh Gandhi, Universal Hospital');
    c.querySelectorAll('img,.play').forEach(function(n){n.remove();});c.appendChild(r);r.play().catch(function(){});
  });

  /* WhatsApp share for each role card, and for the whole page */
  var base=location.href.split('#')[0];
  function wa(text,hash){return 'https://wa.me/?text='+encodeURIComponent(text+'\n'+base+(hash?'#'+hash:''));}
  var sa=document.getElementById('shareAll'); sa.addEventListener('click',function(){push('whatsapp_share',{place:'final_section'});}); sa.href=wa('Have a look: Healthray for multispeciality hospitals. Every department on one patient file.');sa.target='_blank';sa.rel='noopener';

  /* FAQ: first one open by default, only one open at a time */
  var fq=document.querySelectorAll('.msh .faq details');
  fq.forEach(function(d){d.addEventListener('toggle',function(){if(d.open)fq.forEach(function(o){if(o!==d)o.open=false;});});});
})();
