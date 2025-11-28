(function(){
  // Use data attributes to target specific login triggers so Home buttons using .btn-primary are not intercepted.
  const studentBtn = document.querySelector('[data-open="student"]');
  const librarianBtn = document.querySelector('[data-open="librarian"]');
  const forgotBtns = document.querySelectorAll('[data-open="forgot"]');
  const resetModal = document.getElementById('reset-modal');
  const studentModal = document.getElementById('student-modal');
  const librarianModal = document.getElementById('librarian-modal');
  const forgotModal = document.getElementById('forgot-modal');
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
  forgotBtns.forEach(btn => btn.addEventListener('click', e => { e.preventDefault(); openModal(forgotModal); }));

  document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', e => {
      const modal = e.target.closest('.login-modal');
      closeModal(modal);
    });
  });
  document.addEventListener('keydown', e => {
    if(e.key === 'Escape'){
      [studentModal, librarianModal, forgotModal, resetModal].forEach(m => { if(m?.classList.contains('active')) closeModal(m); });
    }
  });
  // Close when clicking backdrop
  [studentModal, librarianModal, forgotModal, resetModal].forEach(m => {
    m?.addEventListener('click', e => { if(e.target === m) closeModal(m); });
  });

  // AJAX: Forgot Password form
  const forgotForm = document.getElementById('forgot-form');
  const forgotMsg = document.getElementById('forgot-message');
  forgotForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    forgotMsg.textContent = '';
    const email = (document.getElementById('forgot-email')?.value || '').trim();
    if(!email){ forgotMsg.textContent = 'Please enter your email.'; return; }
    const btn = forgotForm.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    try {
      const res = await fetch('api/request_password_reset.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email })
      });
      const data = await res.json();
      if(data.success){
        forgotMsg.innerHTML = (data.message || 'If that email exists, a reset link was sent.') + (data.link ? ` <a href="${data.link}">Reset now</a>` : '');
      } else {
        forgotMsg.textContent = data.message || 'Failed to send reset link.';
      }
    } catch (err){
      forgotMsg.textContent = 'Network error. Please try again.';
    } finally {
      btn.disabled = false;
      btn.textContent = 'Send Reset Link';
    }
  });

  // If URL contains ?token=..., open Reset modal and handle submit
  function getQueryParam(name){
    const m = new URLSearchParams(window.location.search).get(name);
    return m ? decodeURIComponent(m) : '';
  }
  const token = getQueryParam('token');
  const resetForm = document.getElementById('reset-form');
  const resetMsg = document.getElementById('reset-message');
  const resetTokenInput = document.getElementById('reset-token');
  if(token && resetModal){
    resetTokenInput && (resetTokenInput.value = token);
    openModal(resetModal);
  }
  resetForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    resetMsg.textContent = '';
    const password = (document.getElementById('reset-password')?.value || '');
    const tokenVal = (resetTokenInput?.value || '');
    if(password.length < 6){ resetMsg.textContent = 'Password must be at least 6 characters.'; return; }
    const btn = resetForm.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Resetting...';
    try {
      const res = await fetch('api/reset_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token: tokenVal, password })
      });
      const data = await res.json();
      if(data.success){
        resetMsg.textContent = 'Password reset successful. You may now log in.';
      } else {
        resetMsg.textContent = data.message || 'Failed to reset password.';
      }
    } catch(err){
      resetMsg.textContent = 'Network error. Please try again.';
    } finally {
      btn.disabled = false; btn.textContent = 'Reset Password';
    }
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