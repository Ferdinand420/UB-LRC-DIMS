(function(){
  // Use data attributes to target specific login triggers so Home buttons using .btn-primary are not intercepted.
  const studentBtn = document.querySelector('[data-open="student"]');
  const librarianBtn = document.querySelector('[data-open="librarian"]');
  const studentModal = document.getElementById('student-modal');
  const librarianModal = document.getElementById('librarian-modal');
  const forgotBtnLinks = document.querySelectorAll('[data-open="forgot"]');
  const forgotModal = document.getElementById('forgot-modal');
  const forgotResetModal = document.getElementById('forgot-reset-modal');
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
      [studentModal, librarianModal, forgotModal, forgotResetModal].forEach(m => { if(m?.classList.contains('active')) closeModal(m); });
    }
  });
  // Close when clicking backdrop
  [studentModal, librarianModal, forgotModal, forgotResetModal].forEach(m => {
    m?.addEventListener('click', e => { if(e.target === m) closeModal(m); });
  });

  // Open forgot modal from any trigger
  forgotBtnLinks.forEach(link => {
    link.addEventListener('click', e => { e.preventDefault(); openModal(forgotModal); });
  });

  // Forgot modal form handlers
  const requestForm = document.getElementById('request-reset-form');
  const performForm = document.getElementById('perform-reset-form');
  if (requestForm) {
    requestForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const emailInput = document.getElementById('reset-email');
      const msg = document.getElementById('request-reset-msg');
      msg.textContent = '';
      try {
        const form = new FormData();
        form.append('email', (emailInput.value || '').trim());
        const res = await fetch('/ub-lrc-dims/api/request_password_reset.php', { method: 'POST', body: form });
        const data = await res.json();
        if (data.ok) {
          // No success text shown; proceed silently to next step
          if (data.token) {
            const tokenInput = document.getElementById('reset-token-input');
            if (tokenInput) tokenInput.value = data.token;
          }
          // Proceed to step 2 modal
          closeModal(forgotModal);
          openModal(forgotResetModal);
        } else {
          msg.textContent = data.error || 'Request failed';
        }
      } catch (err) {
        msg.textContent = 'Network error';
      }
    });
  }
  if (performForm) {
    performForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const token = (document.getElementById('reset-token-input').value || '').trim();
      const password = document.getElementById('new-password').value;
      const confirm = document.getElementById('confirm-password').value;
      const msg = document.getElementById('perform-reset-msg');
      msg.textContent = 'Resetting…';
      try {
        const form = new FormData();
        form.append('token', token);
        form.append('password', password);
        form.append('confirm', confirm);
        const res = await fetch('/ub-lrc-dims/api/reset_password.php', { method: 'POST', body: form });
        const data = await res.json();
        if (data.ok) {
          msg.textContent = 'Password updated successfully. You can now log in.';
        } else {
          msg.textContent = data.error || 'Reset failed';
        }
      } catch (err) {
        msg.textContent = 'Network error';
      }
    });
  }

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