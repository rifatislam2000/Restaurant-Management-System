<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
header('Location: dashboard.php');
exit;
