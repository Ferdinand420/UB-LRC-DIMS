<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UB LRC-DIMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="has-bg">
    <!-- Background Video Layer -->
    <div class="bg-video-container">
        <video preload="metadata" autoplay muted loop playsinline>
            <!-- Prefer WebM (smaller) first; fallback to MP4 -->
            <source src="assets/media/UB-Homepage-Video-w-text.webm" type="video/webm">
            <source src="assets/media/UB-Homepage-Video-w-text.mp4" type="video/mp4">
        </video>
    </div>
    <div class="site-topbar">
        <div class="topbar-inner">
            <div class="brand"><a href="index.php" class="brand-link" aria-label="UB LRC-DIMS Home"><img src="assets/media/DIMS_logo.png" alt="DIMS Logo" class="brand-logo" height="56" width="56"> UB LRC-DIMS</a></div>
            <div class="actions">
                <a href="#" class="btn btn-primary" data-open="student">Student Login</a>
                <a href="#" class="btn btn-primary" data-open="librarian">Librarian Login</a>
            </div>
        </div>
    </div>
    <div class="landing-wrapper">
        <section class="landing-hero">
            <div class="landing-hero-inner">
                <h1>UB Learning Resource Center: Discussion Room Integrated Management System</h1>
            </div>
        </section>
        <section class="feature-bar">
            <div class="feature-bar-inner">
                <h2 class="feature-heading">Access LRC Services and Room Reservations here!</h2>
                <div class="feature-actions">
                    <a href="pages/news.php" class="btn btn-primary feature-btn">News</a>
                    <a href="pages/support.php" class="btn btn-primary feature-btn">Support</a>
                    <a href="pages/learn-more.php" class="btn btn-primary feature-btn">Learn More</a>
                </div>
            </div>
        </section>
        <!-- Login Modals (hidden by default) -->
        <div class="login-modal" id="student-modal" aria-hidden="true" role="dialog" aria-labelledby="studentModalTitle">
            <div class="login-modal-dialog">
                <button class="modal-close" data-close>&times;</button>
                <h2 id="studentModalTitle" class="login-title">Student Login</h2>
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message">
                        <?php 
                        if ($_GET['error'] === 'invalid') {
                            echo '⚠ Invalid email or password. Please try again.';
                        } elseif ($_GET['error'] === 'missing') {
                            echo '⚠ Please enter both email and password.';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="auth/login.php" class="login-form">
                    <input type="hidden" name="role" value="student">
                    <label for="student-email">UB Mail</label>
                    <input id="student-email" type="email" name="email" placeholder="student@ub.edu.ph" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" required>
                    <label for="student-password">Password</label>
                    <input id="student-password" type="password" name="password" placeholder="••••••••" required>
                    <button type="submit" class="btn btn-primary full">Sign In</button>
                    <div class="login-meta">
                        <a href="#" class="mini-link" data-open="forgot">Forgot password?</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="login-modal" id="librarian-modal" aria-hidden="true" role="dialog" aria-labelledby="librarianModalTitle">
            <div class="login-modal-dialog">
                <button class="modal-close" data-close>&times;</button>
                <h2 id="librarianModalTitle" class="login-title">Librarian Login</h2>
                <p class="login-sub">Restricted access portal for authorized library staff.</p>
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message">
                        <?php 
                        if ($_GET['error'] === 'invalid') {
                            echo '⚠ Invalid email or password. Please try again.';
                        } elseif ($_GET['error'] === 'missing') {
                            echo '⚠ Please enter both email and password.';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="auth/login.php" class="login-form">
                    <input type="hidden" name="role" value="librarian">
                    <label for="librarian-email">UB Mail</label>
                    <input id="librarian-email" type="email" name="email" placeholder="staff@ub.edu.ph" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" required>
                    <label for="librarian-password">Password</label>
                    <input id="librarian-password" type="password" name="password" placeholder="••••••••" required>
                    <button type="submit" class="btn btn-primary full">Sign In</button>
                    <div class="login-meta">
                        <a href="#" class="mini-link" data-open="forgot">Forgot password?</a>
                    </div>
                </form>
            </div>
        </div>
        <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS. All rights reserved.</footer>
    </div>
    <!-- Forgot Password Step 1: Enter Email -->
    <div class="login-modal" id="forgot-modal" aria-hidden="true" role="dialog" aria-labelledby="forgotModalTitle">
        <div class="login-modal-dialog">
            <button class="modal-close" data-close>&times;</button>
            <h2 id="forgotModalTitle" class="login-title">Forgot Password</h2>
            <p>Enter your email address, then click Reset Password.</p>
            <form id="request-reset-form" style="margin-top: 12px;">
                <label for="reset-email">Email</label>
                <input id="reset-email" name="email" type="email" placeholder="you@ub.edu.ph" required />
                <button type="submit" class="btn btn-primary" style="margin-top: 12px;">Reset Password</button>
                <div id="request-reset-msg" class="mini-note" style="margin-top: 10px;"></div>
            </form>
        </div>
    </div>
    <!-- Forgot Password Step 2: Set New Password -->
    <div class="login-modal" id="forgot-reset-modal" aria-hidden="true" role="dialog" aria-labelledby="forgotResetModalTitle">
        <div class="login-modal-dialog">
            <button class="modal-close" data-close>&times;</button>
            <h2 id="forgotResetModalTitle" class="login-title">Set New Password</h2>
            <form id="perform-reset-form" style="margin-top: 12px;">
                <input id="reset-token-input" name="token" type="hidden" />
                <label for="new-password">New Password</label>
                <input id="new-password" name="password" type="password" placeholder="At least 8 characters" required />
                <label for="confirm-password">Confirm New Password</label>
                <input id="confirm-password" name="confirm" type="password" required />
                <button type="submit" class="btn btn-primary" style="margin-top: 12px;">Update Password</button>
                <div id="perform-reset-msg" class="mini-note" style="margin-top: 10px;"></div>
            </form>
        </div>
    </div>
    <script src="assets/js/background.js"></script>
    <script src="assets/js/login-modal.js"></script>
    <script src="assets/js/login-error.js"></script>
</body>
</html>
