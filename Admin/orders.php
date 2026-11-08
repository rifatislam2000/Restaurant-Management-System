<?php
require_once __DIR__ . '/../includes/auth.php'; require_login();
require_once __DIR__ . '/../includes/db.php'; require_once __DIR__ . '/../includes/admin_helpers.php';
$pageTitle='Orders | Restaurant Management';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){$number=post_value('order_number');$customer=(int)($_POST['customer_id']??0);$total=(float)($_POST['total_amount']??0);$payment=post_value('payment_method');$status=post_value('order_status');$user=(int)($_SESSION['user_id']??0);
if($number===''||$total<0||$payment===''||!$user)$error='Please enter valid order details.';
else{$customerValue=$customer?:null;$s=mysqli_prepare($conn,"INSERT INTO orders(order_number,customer_id,user_id,total_amount,payment_method,order_status) VALUES(?,?,?,?,?,?)");mysqli_stmt_bind_param($s,"siidss",$number,$customerValue,$user,$total,$payment,$status);if(mysqli_stmt_execute($s))admin_message('Order added.');else $error='Could not add order. Order number may already exist.';mysqli_stmt_close($s);if(!$error)admin_redirect('orders.php');}}
if(isset($_GET['status'])){$id=(int)$_GET['status'];$new=post_value('status');}
if(isset($_GET['delete'])){$id=(int)$_GET['delete'];$s=mysqli_prepare($conn,"DELETE FROM orders WHERE order_id=?");mysqli_stmt_bind_param($s,"i",$id);if(mysqli_stmt_execute($s))admin_message('Order deleted with its order items.');else admin_message('Could not delete order.','danger');mysqli_stmt_close($s);admin_redirect('orders.php');}
$customers=mysqli_query($conn,"SELECT customer_id,customer_name FROM customers ORDER BY customer_name");
$list=mysqli_query($conn,"SELECT o.*,u.full_name,COALESCE(c.customer_name,'Walk-in Customer') AS customer FROM orders o JOIN users u ON o.user_id=u.user_id LEFT JOIN customers c ON o.customer_id=c.customer_id ORDER BY o.order_id DESC");
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Orders</h2><p class="text-muted">Create and manage customer orders</p><?php if($error):?><div class="alert alert-danger"><?=h($error)?></div><?php endif;?>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h5>Add Order</h5></div><div class="card-body"><form method="POST" class="row g-3">
<div class="col-md-2"><label class="form-label">Order Number</label><input class="form-control" name="order_number" required placeholder="ORD-001"></div>
<div class="col-md-3"><label class="form-label">Customer</label><select class="form-select" name="customer_id"><option value="0">Walk-in Customer</option><?php if($customers)while($c=mysqli_fetch_assoc($customers)):?><option value="<?=$c['customer_id']?>"><?=h($c['customer_name'])?></option><?php endwhile;?></select></div>
<div class="col-md-2"><label class="form-label">Total Amount</label><input type="number" min="0" step="0.01" class="form-control" name="total_amount" required></div>
<div class="col-md-2"><label class="form-label">Payment</label><select class="form-select" name="payment_method"><option>Cash</option><option>Card</option><option>Mobile Banking</option></select></div>
<div class="col-md-2"><label class="form-label">Status</label><select class="form-select" name="order_status"><option>Completed</option><option>Pending</option><option>Cancelled</option></select></div>
<div class="col-md-1 d-flex align-items-end"><button class="btn btn-primary">Add</button></div></form><small class="text-muted">Cashier is taken from the currently logged-in user.</small></div></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Order No.</th><th>Customer</th><th>Cashier</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>
<?php if($list&&mysqli_num_rows($list)):while($r=mysqli_fetch_assoc($list)):?><tr><td><?=h($r['order_number'])?></td><td><?=h($r['customer'])?></td><td><?=h($r['full_name'])?></td><td>৳ <?=number_format($r['total_amount'],2)?></td><td><?=h($r['payment_method'])?></td><td><?=h($r['order_status'])?></td><td><?=h($r['order_date'])?></td><td><a class="btn btn-sm btn-info" href="order_items.php?order_id=<?=$r['order_id']?>">Items</a> <a class="btn btn-sm btn-danger" onclick="return confirm('Delete order?')" href="?delete=<?=$r['order_id']?>">Delete</a></td></tr><?php endwhile;else:?><tr><td colspan="8" class="text-center p-3">No orders found.</td></tr><?php endif;?>
</tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
