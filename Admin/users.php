<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

$pageTitle = 'Users | Restaurant Management';
$error = '';
$editUser = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT user_id, full_name, username, role, status FROM users WHERE user_id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $editUser = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['user_id'] ?? 0);
    $name = post_value('full_name');
    $username = post_value('username');
    $password = $_POST['password'] ?? '';
    $role = post_value('role');
    $status = (int)($_POST['status'] ?? 1);

    if ($name === '' || $username === '' || !in_array($role, ['admin', 'cashier'], true)) {
        $error = 'Please fill all required fields.';
    } elseif ($id > 0) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET full_name=?, username=?, password=?, role=?, status=? WHERE user_id=?");
            mysqli_stmt_bind_param($stmt, "ssssii", $name, $username, $hash, $role, $status, $id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET full_name=?, username=?, role=?, status=? WHERE user_id=?");
            mysqli_stmt_bind_param($stmt, "sssii", $name, $username, $role, $status, $id);
        }
        if (mysqli_stmt_execute($stmt)) admin_message('User updated successfully.');
        else $error = 'Could not update user. Username may already exist.';
        mysqli_stmt_close($stmt);
        if (!$error) admin_redirect('users.php');
    } else {
        if ($password === '') $error = 'Password is required for a new user.';
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, username, password, role, status) VALUES (?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "ssssi", $name, $username, $hash, $role, $status);
            if (mysqli_stmt_execute($stmt)) admin_message('User added successfully.');
            else $error = 'Could not add user. Username may already exist.';
            mysqli_stmt_close($stmt);
            if (!$error) admin_redirect('users.php');
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id === (int)($_SESSION['user_id'] ?? 0)) admin_message('You cannot delete your own account.', 'warning');
    else {
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) admin_message('User deleted successfully.');
        else admin_message('Cannot delete this user because the user has existing orders.', 'danger');
        mysqli_stmt_close($stmt);
    }
    admin_redirect('users.php');
}

$users = mysqli_query($conn, "SELECT user_id, full_name, username, role, status, created_at FROM users ORDER BY user_id DESC");
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2>Users</h2>
        <p class="text-muted">Manage system users</p>
    </div>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><?= $editUser ? 'Edit User' : 'Add User' ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <input type="hidden" name="user_id" value="<?= h($editUser['user_id'] ?? 0) ?>">
            <div class="col-md-6"><label class="form-label">Full Name</label><input class="form-control" name="full_name" required value="<?= h($editUser['full_name'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Username</label><input class="form-control" name="username" required value="<?= h($editUser['username'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Password <?= $editUser ? '(leave blank to keep current)' : '' ?></label><input type="password" class="form-control" name="password" <?= $editUser ? '' : 'required' ?>></div>
            <div class="col-md-3"><label class="form-label">Role</label><select class="form-select" name="role">
                    <option value="cashier" <?= ($editUser['role'] ?? '') === 'cashier' ? 'selected' : '' ?>>Cashier</option>
                    <option value="admin" <?= ($editUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select></div>
            <div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status">
                    <option value="1" <?= (string)($editUser['status'] ?? '1') === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= (string)($editUser['status'] ?? '') === '0' ? 'selected' : '' ?>>Inactive</option>
                </select></div>
            <div class="col-12"><button class="btn btn-primary">Save User</button> <a href="users.php" class="btn btn-secondary">Clear</a></div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users && mysqli_num_rows($users)): while ($u = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?= h($u['full_name']) ?></td>
                            <td><?= h($u['username']) ?></td>
                            <td><?= h($u['role']) ?></td>
                            <td><?= $u['status'] ? 'Active' : 'Inactive' ?></td>
                            <td><a class="btn btn-sm btn-warning" href="users.php?edit=<?= (int)$u['user_id'] ?>">Edit</a> <a class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')" href="users.php?delete=<?= (int)$u['user_id'] ?>">Delete</a></td>
                        </tr>
                    <?php endwhile;
                else: ?><tr>
                        <td colspan="5" class="text-center p-3">No users found.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>