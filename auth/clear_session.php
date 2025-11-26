<?php
session_start();
session_destroy();
header('Location: /ub-lrc-dims/index.php');
?>