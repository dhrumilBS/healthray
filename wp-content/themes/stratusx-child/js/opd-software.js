/* OPD page (82363): page-only script. The shared parts (GTM events, demo popup, hero form, reviews, video, FAQ)
   are in js/modules-common.js.
   Speciality picker above the hero form ([data-pick]): fills the form's "business type" select and keeps the choice
   in main[data-hr-place], which modules-common.js sends as place_type with form_start / demo_form_success. */
(function(){
  function init(){
    var root = document.querySelector('.opd-landing .hx'); if (!root) return;
    if (!document.getElementById('demo')) return;

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
      var picked = a.dataset.pick;
      root.setAttribute('data-hr-place', picked);
      if (BUSINESS[picked]) { fill(BUSINESS[picked]); setTimeout(function(){ fill(BUSINESS[picked]); }, 400); }
    }); });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
