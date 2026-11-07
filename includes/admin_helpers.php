<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function h($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function admin_redirect($url) {
    header("Location: " . $url);
    exit;
}

function admin_message($message, $type = 'success') {
    $_SESSION['admin_message'] = $message;
    $_SESSION['admin_message_type'] = $type;
}

function show_admin_message() {
    if (!empty($_SESSION['admin_message'])) {
        $type = $_SESSION['admin_message_type'] ?? 'success';
        echo '<div class="alert alert-' . h($type) . ' alert-dismissible fade show" role="alert">';
        echo h($_SESSION['admin_message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        unset($_SESSION['admin_message'], $_SESSION['admin_message_type']);
    }
}

function db_query_or_die($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Database error: " . h(mysqli_error($conn)));
    }
    return $result;
}

function post_value($key) {
    return trim($_POST[$key] ?? '');
}
