<?php
require_once __DIR__ . '/../includes/header.php';
?>
<main class="container" style="max-width: 520px; margin: 40px auto;">
    <h2>Forgot Password</h2>
    <p>Enter your email to receive a password reset link. In this demo, we will show the reset token directly.</p>

    <form id="request-reset-form" style="margin-top: 16px;">
        <label for="reset-email">Email</label>
        <input id="reset-email" name="email" type="email" placeholder="you@ub.edu.ph" required />
        <button type="submit" class="btn-primary" style="margin-top: 12px;">Send Reset Link</button>
        <div id="request-reset-msg" class="mini-note" style="margin-top: 10px;"></div>
    </form>

    <div id="token-section" style="display:none; margin-top: 20px;">
        <p><strong>Reset Token (demo):</strong> <code id="reset-token"></code></p>
        <p>Use the form below to set a new password.</p>
    </div>

    <hr style="margin: 24px 0;" />

    <h3>Set New Password</h3>
    <form id="perform-reset-form" style="margin-top: 12px;">
        <label for="reset-token-input">Token</label>
        <input id="reset-token-input" name="token" type="text" placeholder="Paste token here" required />

        <label for="new-password">New Password</label>
        <input id="new-password" name="password" type="password" placeholder="At least 8 characters" required />

        <label for="confirm-password">Confirm Password</label>
        <input id="confirm-password" name="confirm" type="password" required />

        <button type="submit" class="btn-primary" style="margin-top: 12px;">Reset Password</button>
        <div id="perform-reset-msg" class="mini-note" style="margin-top: 10px;"></div>
    </form>

    <p style="margin-top: 20px;"><a href="/ub-lrc-dims/index.php" class="mini-link">Back to login</a></p>
</main>

<script>
document.getElementById('request-reset-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = (document.getElementById('reset-email').value || '').trim();
    const msg = document.getElementById('request-reset-msg');
    msg.textContent = 'Sending…';
    try {
        const form = new FormData();
        form.append('email', email);
        const res = await fetch('/ub-lrc-dims/api/request_password_reset.php', { method: 'POST', body: form });
        const data = await res.json();
        if (data.ok) {
            msg.textContent = 'If the email exists, a reset link was sent.';
            if (data.token) {
                document.getElementById('token-section').style.display = 'block';
                document.getElementById('reset-token').textContent = data.token;
                document.getElementById('reset-token-input').value = data.token;
            }
        } else {
            msg.textContent = data.error || 'Request failed';
        }
    } catch (err) {
        msg.textContent = 'Network error';
    }
});

document.getElementById('perform-reset-form').addEventListener('submit', async (e) => {
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
</script>

<?php
// No custom sidebar on this page
?>