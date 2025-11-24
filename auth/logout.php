<?php
require_once __DIR__ . '/../includes/auth.php';
logout_user();
header('Location: /ub-lrc-dims/index.php');
exit;
