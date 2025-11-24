// Background video accessibility & reduced motion handling
(function() {
  var video = document.querySelector('.bg-video-container video');
  if (!video) return;

  var motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

  function applyReducedMotion() {
    if (motionQuery.matches) {
      video.pause();
      video.removeAttribute('autoplay');
      var container = document.querySelector('.bg-video-container');
      if (container) { container.style.display = 'none'; }
      document.body.classList.add('bg-static');
    }
  }

  applyReducedMotion();
  if (motionQuery.addEventListener) {
    motionQuery.addEventListener('change', applyReducedMotion);
  } else if (motionQuery.addListener) {
    motionQuery.addListener(applyReducedMotion);
  }

  // Debug logging if ?debugVideo=1 in URL
  if (window.location.search.indexOf('debugVideo=1') !== -1) {
    function log(ev){ console.log('[BG Video]', ev.type, 'currentTime=', video.currentTime); }
    ['loadedmetadata','loadeddata','canplay','play','pause','error','stalled','waiting'].forEach(function(evt){ video.addEventListener(evt, log); });
    if (video.error) console.log('[BG Video] initial error', video.error);
  }

  // Fallback if video errors or fails to load within timeout
  var fallbackApplied = false;
  function applyVideoFallback(reason) {
    if (fallbackApplied) return;
    fallbackApplied = true;
    var container = document.querySelector('.bg-video-container');
    if (container) { container.style.display = 'none'; }
    document.body.classList.add('bg-static');
    if (window.location.search.indexOf('debugVideo=1') !== -1) {
      console.warn('[BG Video] Fallback applied:', reason);
    }
  }

  video.addEventListener('error', function(){ applyVideoFallback('error event'); });
  // If not ready to play after 5s, fallback
  setTimeout(function(){
    if (video.readyState < 2) { // HAVE_CURRENT_DATA
      applyVideoFallback('load timeout');
    }
  }, 5000);

  // (Removed badge display code per request)

  // Query param videoTest=1 forces video-test class for overlay reduction
  if (window.location.search.indexOf('videoTest=1') !== -1) {
    document.body.classList.add('video-test');
  }

  // Force video even if reduced motion (forceVideo=1)
  if (window.location.search.indexOf('forceVideo=1') !== -1) {
    var container = document.querySelector('.bg-video-container');
    if (container) { container.style.display = ''; }
    document.body.classList.remove('bg-static');
    var playPromise = video.play();
    if (playPromise && playPromise.catch) {
      playPromise.catch(function(e){ console.warn('[BG Video] force play blocked', e); });
    }
  }
})();
