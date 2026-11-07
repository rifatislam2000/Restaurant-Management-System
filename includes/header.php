<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/admin_helpers.php';

$pageTitle = $pageTitle ?? 'Restaurant Management System';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="dashboard.php">Restaurant Management</a>
        <div class="d-flex align-items-center gap-3 text-white">
            <span class="small d-none d-sm-inline"><?= h($_SESSION['full_name'] ?? 'Admin') ?></span>
            <form method="POST" action="../logout.php" class="m-0">
                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <aside class="col-md-2 sidebar py-4">
            <div class="px-3 mb-3 text-secondary small fw-bold">MAIN MENU</div>
            <a href="dashboard.php" class="sidebar-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                Dashboard
            </a>
        </aside>
        <main class="col-md-10 p-4">
            <?php show_admin_message(); ?>
