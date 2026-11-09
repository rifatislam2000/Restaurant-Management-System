<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_helpers.php';
$pageTitle = 'Foods | Restaurant Management';
$error = '';
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $s = mysqli_prepare($conn, "SELECT * FROM foods WHERE food_id=?");
    mysqli_stmt_bind_param($s, "i", $id);
    mysqli_stmt_execute($s);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
    mysqli_stmt_close($s);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['food_id'] ?? 0);
    $cat = (int)($_POST['category_id'] ?? 0);
    $name = post_value('food_name');
    $price = (float)($_POST['price'] ?? 0);
    $desc = post_value('description');
    $availability = post_value('availability');
    if (!$cat || $name === '' || $price < 0) $error = 'Please enter valid food details.';
    elseif ($id) {
        $s = mysqli_prepare($conn, "UPDATE foods SET category_id=?,food_name=?,price=?,description=?,availability=? WHERE food_id=?");
        mysqli_stmt_bind_param($s, "isdssi", $cat, $name, $price, $desc, $availability, $id);
        if (mysqli_stmt_execute($s)) admin_message('Food updated.');
        else $error = 'Could not update food.';
        mysqli_stmt_close($s);
        if (!$error) admin_redirect('foods.php');
    } else {
        $s = mysqli_prepare($conn, "INSERT INTO foods(category_id,food_name,price,description,availability) VALUES(?,?,?,?,?)");
        mysqli_stmt_bind_param($s, "isdss", $cat, $name, $price, $desc, $availability);
        if (mysqli_stmt_execute($s)) admin_message('Food added.');
        else $error = 'Could not add food.';
        mysqli_stmt_close($s);
        if (!$error) admin_redirect('foods.php');
    }
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $s = mysqli_prepare($conn, "DELETE FROM foods WHERE food_id=?");
    mysqli_stmt_bind_param($s, "i", $id);
    if (mysqli_stmt_execute($s)) admin_message('Food deleted.');
    else admin_message('Cannot delete food already used in an order.', 'danger');
    mysqli_stmt_close($s);
    admin_redirect('foods.php');
}
$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
$list = mysqli_query($conn, "SELECT f.*,c.category_name FROM foods f JOIN categories c ON f.category_id=c.category_id ORDER BY f.food_id DESC");
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Food Management</h2>
<p class="text-muted">Add, edit and delete food items</p>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5><?= $edit ? 'Edit Food' : 'Add Food' ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <input type="hidden" name="food_id" value="<?= h($edit['food_id'] ?? 0) ?>">
            <div class="col-md-3"><label class="form-label">Food Name</label><input class="form-control" name="food_name" required value="<?= h($edit['food_name'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Category</label><select class="form-select" name="category_id" required>
                    <option value="">Choose</option><?php if ($cats) while ($c = mysqli_fetch_assoc($cats)): ?><option value="<?= $c['category_id'] ?>" <?= ($edit['category_id'] ?? '') == $c['category_id'] ? 'selected' : '' ?>><?= h($c['category_name']) ?></option><?php endwhile; ?>
                </select></div>
            <div class="col-md-2"><label class="form-label">Price</label><input type="number" step="0.01" min="0" class="form-control" name="price" required value="<?= h($edit['price'] ?? '') ?>"></div>
            <div class="col-md-2"><label class="form-label">Availability</label><select class="form-select" name="availability">
                    <option <?= ($edit['availability'] ?? 'Available') === 'Available' ? 'selected' : '' ?>>Available</option>
                    <option <?= ($edit['availability'] ?? '') === 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                </select></div>
            <div class="col-md-2"><label class="form-label">Description</label><input class="form-control" name="description" value="<?= h($edit['description'] ?? '') ?>"></div>
            <div class="col-12"><button class="btn btn-primary">Save Food</button> <a href="foods.php" class="btn btn-secondary">Clear</a></div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Food</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Availability</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($list && mysqli_num_rows($list)): while ($r = mysqli_fetch_assoc($list)): ?><tr>
                            <td><?= h($r['food_name']) ?></td>
                            <td><?= h($r['category_name']) ?></td>
                            <td>৳ <?= number_format($r['price'], 2) ?></td>
                            <td><?= h($r['availability']) ?></td>
                            <td><a class="btn btn-sm btn-warning" href="?edit=<?= $r['food_id'] ?>">Edit</a> <a class="btn btn-sm btn-danger" onclick="return confirm('Delete food?')" href="?delete=<?= $r['food_id'] ?>">Delete</a></td>
                        </tr><?php endwhile;
                        else: ?><tr>
                        <td colspan="5" class="text-center p-3">No foods found.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>