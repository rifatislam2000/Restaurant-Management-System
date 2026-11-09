<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username == '' || $password == '') {

        $error = "Please enter username and password.";
    } else {

        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");

        if (!$stmt) {
            die("Query Error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $username);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $user = mysqli_fetch_assoc($result);

        if ($user && $user['status'] == 1 && password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header('Location: Admin/dashboard.php');
            exit;
        } else {

            $error = "Invalid username or password.";
        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Restaurant Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/style.css?v=20261109">
</head>

<body class="login-page">

    <div class="container">

        <div class="login-card">

            <div class="login-brand-mark" aria-hidden="true">R</div>
            <p class="login-eyebrow">Restaurant Management System</p>
            <h1 class="login-title">Welcome back</h1>

            <p class="login-subtitle">Sign in to continue to your account.</p>

            <?php if ($error != ''): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php endif; ?>

            <form method="POST" action="">

                <div class="mb-3 login-field">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" autocomplete="username" required autofocus>
                </div>

                <div class="mb-4 login-field">
                    <label for="password" class="form-label">Password</label>
                    <div class="login-password-wrap">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                        <button class="login-password-toggle" type="button" id="toggle-password" aria-label="Show password" aria-pressed="false">Show</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Sign in
                </button>

            </form>

            <p class="login-footer">Secure access for restaurant staff</p>
        </div>

    </div>

    <script src="assets/js/login.js" defer></script>
</body>

</html>