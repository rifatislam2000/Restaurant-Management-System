<?php
require_once __DIR__ . '/../includes/auth.php'; require_login();
require_once __DIR__ . '/../includes/db.php'; require_once __DIR__ . '/../includes/admin_helpers.php';
$pageTitle = 'Categories | Restaurant Management'; $error=''; $edit=null;
if (isset($_GET['edit'])) { $id=(int)$_GET['edit']; $s=mysqli_prepare($conn,"SELECT * FROM categories WHERE category_id=?"); mysqli_stmt_bind_param($s,"i",$id); mysqli_stmt_execute($s); $edit=mysqli_fetch_assoc(mysqli_stmt_get_result($s)); mysqli_stmt_close($s); }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=(int)($_POST['category_id']??0); $name=post_value('category_name'); $description=post_value('description');
    if ($name==='') $error='Category name is required.';
    elseif ($id) { $s=mysqli_prepare($conn,"UPDATE categories SET category_name=?, description=? WHERE category_id=?"); mysqli_stmt_bind_param($s,"ssi",$name,$description,$id); if(mysqli_stmt_execute($s)) admin_message('Category updated.'); else $error='Category name already exists.'; mysqli_stmt_close($s); if(!$error) admin_redirect('categories.php'); }
    else { $s=mysqli_prepare($conn,"INSERT INTO categories(category_name,description) VALUES(?,?)"); mysqli_stmt_bind_param($s,"ss",$name,$description); if(mysqli_stmt_execute($s)) admin_message('Category added.'); else $error='Category name already exists.'; mysqli_stmt_close($s); if(!$error) admin_redirect('categories.php'); }
}
if(isset($_GET['delete'])) { $id=(int)$_GET['delete']; $s=mysqli_prepare($conn,"DELETE FROM categories WHERE category_id=?"); mysqli_stmt_bind_param($s,"i",$id); if(mysqli_stmt_execute($s)) admin_message('Category deleted.'); else admin_message('Cannot delete: foods are using this category.','danger'); mysqli_stmt_close($s); admin_redirect('categories.php'); }
$list=mysqli_query($conn,"SELECT * FROM categories ORDER BY category_id DESC");
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Categories</h2><p class="text-muted">Add and manage food categories</p>
<?php if($error): ?><div class="alert alert-danger"><?=h($error)?></div><?php endif; ?>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h5><?= $edit?'Edit Category':'Add Category' ?></h5></div><div class="card-body">
<form method="POST" class="row g-3"><input type="hidden" name="category_id" value="<?=h($edit['category_id']??0)?>">
<div class="col-md-5"><label class="form-label">Category Name</label><input class="form-control" name="category_name" required value="<?=h($edit['category_name']??'')?>"></div>
<div class="col-md-5"><label class="form-label">Description</label><input class="form-control" name="description" value="<?=h($edit['description']??'')?>"></div>
<div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Save</button> <a class="btn btn-secondary ms-2" href="categories.php">Clear</a></div></form></div></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>ID</th><th>Category</th><th>Description</th><th>Action</th></tr></thead><tbody>
<?php if($list && mysqli_num_rows($list)): while($r=mysqli_fetch_assoc($list)): ?><tr><td><?=$r['category_id']?></td><td><?=h($r['category_name'])?></td><td><?=h($r['description'])?></td><td><a class="btn btn-sm btn-warning" href="?edit=<?=$r['category_id']?>">Edit</a> <a class="btn btn-sm btn-danger" onclick="return confirm('Delete category?')" href="?delete=<?=$r['category_id']?>">Delete</a></td></tr><?php endwhile; else: ?><tr><td colspan="4" class="text-center p-3">No categories found.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
