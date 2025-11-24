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
            <div class="brand"><img src="assets/media/UB_logo.png" alt="University of Batangas Logo" class="brand-logo" height="56" width="56"> UB LRC-DIMS</div>
            <div class="actions">
                <a href="pages/student-login.php" class="btn btn-outline light">Student Login</a>
                <a href="pages/librarian-login.php" class="btn btn-primary">Librarian Login</a>
            </div>
        </div>
    </div>
    <div class="landing-wrapper">
        <section class="landing-hero">
            <div class="landing-hero-inner">
                <h1>UB Learning Resource Center Discussion Integrated Management System</h1>
            </div>
        </section>
        <section class="feature-bar">
            <div class="feature-bar-inner">
                <h2 class="feature-heading">Access LRC Services and Room Reservations here!</h2>
                <div class="feature-actions">
                    <a href="#news" class="btn btn-primary feature-btn">News</a>
                    <a href="#support" class="btn btn-primary feature-btn">Support</a>
                    <a href="pages/reservations.php" class="btn btn-primary feature-btn">Learn More</a>
                </div>
            </div>
        </section>
        <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS. All rights reserved.</footer>
    </div>
    <script src="assets/js/background.js"></script>
</body>
</html>
