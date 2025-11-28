(function(){
  // Use data attributes to target specific login triggers so Home buttons using .btn-primary are not intercepted.
  const studentBtn = document.querySelector('[data-open="student"]');
  const librarianBtn = document.querySelector('[data-open="librarian"]');
  const studentModal = document.getElementById('student-modal');
  const librarianModal = document.getElementById('librarian-modal');
  const focusableSelectors = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
  let lastFocused = null;

  function openModal(modal){
    if(!modal) return;
    lastFocused = document.activeElement;
    modal.classList.add('active');
    modal.setAttribute('aria-hidden','false');
    const firstInput = modal.querySelector('input');
    if(firstInput) firstInput.focus();
    document.body.style.overflow = 'hidden';
    trapFocus(modal);
  }
  function closeModal(modal){
    if(!modal) return;
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden','true');
    document.body.style.overflow = '';
    releaseFocusTrap();
    if(lastFocused) lastFocused.focus();
  }

  studentBtn?.addEventListener('click', e => { e.preventDefault(); openModal(studentModal); });
  librarianBtn?.addEventListener('click', e => { e.preventDefault(); openModal(librarianModal); });

  document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', e => {
      const modal = e.target.closest('.login-modal');
      closeModal(modal);
    });
  });
  document.addEventListener('keydown', e => {
    if(e.key === 'Escape'){
      [studentModal, librarianModal].forEach(m => { if(m?.classList.contains('active')) closeModal(m); });
    }
  });
  // Close when clicking backdrop
  [studentModal, librarianModal].forEach(m => {
    m?.addEventListener('click', e => { if(e.target === m) closeModal(m); });
  });

  // Focus trap
  let currentTrapModal = null;
  function trapFocus(modal){
    currentTrapModal = modal;
    document.addEventListener('keydown', handleTrap, true);
  }
  function releaseFocusTrap(){
    document.removeEventListener('keydown', handleTrap, true);
    currentTrapModal = null;
  }
  function handleTrap(e){
    if(!currentTrapModal) return;
    if(e.key !== 'Tab') return;
    const focusable = Array.from(currentTrapModal.querySelectorAll(focusableSelectors)).filter(el => !el.disabled && el.offsetParent !== null);
    if(focusable.length === 0) return;
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if(e.shiftKey && document.activeElement === first){
      e.preventDefault(); last.focus();
    } else if(!e.shiftKey && document.activeElement === last){
      e.preventDefault(); first.focus();
    }
  }
})();