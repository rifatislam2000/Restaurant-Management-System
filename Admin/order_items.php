<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_helpers.php';
$pageTitle = 'Order Items | Restaurant Management';
$error = '';
$selectedOrder = (int)($_GET['order_id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order = (int)($_POST['order_id'] ?? 0);
    $food = (int)($_POST['food_id'] ?? 0);
    $qty = (int)($_POST['quantity'] ?? 0);
    if (!$order || !$food || $qty < 1) $error = 'Choose an order, food and valid quantity.';
    else {
        $s = mysqli_prepare($conn, "SELECT price FROM foods WHERE food_id=?");
        mysqli_stmt_bind_param($s, "i", $food);
        mysqli_stmt_execute($s);
        $foodRow = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        mysqli_stmt_close($s);
        if (!$foodRow) $error = 'Food item not found.';
        else {
            $unit = (float)$foodRow['price'];
            $subtotal = $unit * $qty;
            $s = mysqli_prepare($conn, "INSERT INTO order_items(order_id,food_id,quantity,unit_price,subtotal) VALUES(?,?,?,?,?)");
            mysqli_stmt_bind_param($s, "iiidd", $order, $food, $qty, $unit, $subtotal);
            if (mysqli_stmt_execute($s)) {
                mysqli_query($conn, "UPDATE orders SET total_amount=(SELECT COALESCE(SUM(subtotal),0) FROM order_items WHERE order_id=" . (int)$order . ") WHERE order_id=" . (int)$order);
                admin_message('Order item added and order total updated.');
            } else $error = 'Could not add item. Check selected order and food.';
            mysqli_stmt_close($s);
            if (!$error) admin_redirect('order_items.php?order_id=' . $order);
        }
    }
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $order = (int)($_GET['order_id'] ?? 0);
    $s = mysqli_prepare($conn, "DELETE FROM order_items WHERE order_item_id=?");
    mysqli_stmt_bind_param($s, "i", $id);
    if (mysqli_stmt_execute($s)) {
        if ($order) mysqli_query($conn, "UPDATE orders SET total_amount=(SELECT COALESCE(SUM(subtotal),0) FROM order_items WHERE order_id=" . (int)$order . ") WHERE order_id=" . (int)$order);
        admin_message('Order item deleted.');
    } else admin_message('Could not delete item.', 'danger');
    mysqli_stmt_close($s);
    admin_redirect('order_items.php?order_id=' . $order);
}
$orders = mysqli_query($conn, "SELECT order_id,order_number FROM orders ORDER BY order_id DESC");
$foods = mysqli_query($conn, "SELECT food_id,food_name,price FROM foods WHERE availability='Available' ORDER BY food_name");
$sql = "SELECT oi.*,o.order_number,f.food_name FROM order_items oi JOIN orders o ON oi.order_id=o.order_id JOIN foods f ON oi.food_id=f.food_id";
if ($selectedOrder) $sql .= " WHERE oi.order_id=" . $selectedOrder;
$sql .= " ORDER BY oi.order_item_id DESC";
$list = mysqli_query($conn, $sql);
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Order Items</h2>
<p class="text-muted">Add food items to an order and manage quantities</p><?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5>Add Order Item</h5>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-4"><label class="form-label">Order</label><select class="form-select" name="order_id" required>
                    <option value="">Choose Order</option><?php if ($orders) while ($o = mysqli_fetch_assoc($orders)): ?><option value="<?= $o['order_id'] ?>" <?= $selectedOrder === $o['order_id'] ? 'selected' : '' ?>><?= h($o['order_number']) ?></option><?php endwhile; ?>
                </select></div>
            <div class="col-md-4"><label class="form-label">Food</label><select class="form-select" name="food_id" required>
                    <option value="">Choose Food</option><?php if ($foods) while ($f = mysqli_fetch_assoc($foods)): ?><option value="<?= $f['food_id'] ?>"><?= h($f['food_name']) ?> - ৳<?= number_format($f['price'], 2) ?></option><?php endwhile; ?>
                </select></div>
            <div class="col-md-2"><label class="form-label">Quantity</label><input type="number" min="1" value="1" class="form-control" name="quantity" required></div>
            <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Add Item</button></div>
        </form>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5>Order Item List</h5><a href="order_items.php" class="btn btn-sm btn-outline-secondary">All Orders</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order</th>
                    <th>Food</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody><?php if ($list && mysqli_num_rows($list)): while ($r = mysqli_fetch_assoc($list)): ?><tr>
                            <td><?= h($r['order_number']) ?></td>
                            <td><?= h($r['food_name']) ?></td>
                            <td><?= $r['quantity'] ?></td>
                            <td>৳ <?= number_format($r['unit_price'], 2) ?></td>
                            <td>৳ <?= number_format($r['subtotal'], 2) ?></td>
                            <td><a class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')" href="?delete=<?= $r['order_item_id'] ?>&order_id=<?= $r['order_id'] ?>">Delete</a></td>
                        </tr><?php endwhile;
                        else: ?><tr>
                        <td colspan="6" class="text-center p-3">No order items found.</td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>