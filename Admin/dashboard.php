<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

$pageTitle = 'Dashboard | Restaurant Management';

$todaySales = 0;
$todayOrders = 0;
$totalFoods = 0;
$totalCustomers = 0;

$result = mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) AS total FROM orders WHERE order_status='Completed' AND DATE(order_date)=CURDATE()");
if ($result) $todaySales = (float)(mysqli_fetch_assoc($result)['total'] ?? 0);

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE order_status='Completed' AND DATE(order_date)=CURDATE()");
if ($result) $todayOrders = (int)(mysqli_fetch_assoc($result)['total'] ?? 0);

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM foods");
if ($result) $totalFoods = (int)(mysqli_fetch_assoc($result)['total'] ?? 0);

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM customers");
if ($result) $totalCustomers = (int)(mysqli_fetch_assoc($result)['total'] ?? 0);

$recentOrders = [];
$sql = "SELECT o.order_number, o.order_date, o.total_amount, o.payment_method, o.order_status,
        u.full_name AS cashier, COALESCE(c.customer_name,'Walk-in Customer') AS customer
        FROM orders o INNER JOIN users u ON o.user_id=u.user_id
        LEFT JOIN customers c ON o.customer_id=c.customer_id
        ORDER BY o.order_id DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
if ($result) while ($row = mysqli_fetch_assoc($result)) $recentOrders[] = $row;

require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h2>Dashboard</h2>
        <p class="text-muted mb-0">Restaurant overview for today</p>
    </div>
    <span class="badge text-bg-primary p-2"><?= h($_SESSION['role'] ?? 'admin') ?></span>
</div>
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <p>Today's Sales</p>
            <h3>৳ <?= number_format($todaySales, 2) ?></h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <p>Today's Orders</p>
            <h3><?= number_format($todayOrders) ?></h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <p>Total Food Items</p>
            <h3><?= number_format($totalFoods) ?></h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <p>Total Customers</p>
            <h3><?= number_format($totalCustomers) ?></h3>
        </div>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Recent Orders</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order No.</th>
                    <th>Customer</th>
                    <th>Cashier</th>
                    <th>Date</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recentOrders): foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?= h($order['order_number']) ?></td>
                            <td><?= h($order['customer']) ?></td>
                            <td><?= h($order['cashier']) ?></td>
                            <td><?= h(date('d M Y, h:i A', strtotime($order['order_date']))) ?></td>
                            <td><?= h($order['payment_method']) ?></td>
                            <td>৳ <?= number_format((float)$order['total_amount'], 2) ?></td>
                            <td><span class="badge text-bg-success"><?= h($order['order_status']) ?></span></td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>