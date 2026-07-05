<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['client_name'] ?? '');
    $msg   = trim($_POST['message'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    $order = (int)($_POST['sort_order'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $image = uploadImage('image');

    if (!empty($_POST['id'])) {
        if ($image) {
            $stmt = $pdo->prepare("UPDATE reviews SET client_name=?, message=?, rating=?, sort_order=?, status=?, image=? WHERE id=?");
            $stmt->execute([$name, $msg, $rating, $order, $status, $image, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE reviews SET client_name=?, message=?, rating=?, sort_order=?, status=? WHERE id=?");
            $stmt->execute([$name, $msg, $rating, $order, $status, $_POST['id']]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO reviews (client_name, message, rating, image, sort_order, status) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$name, $msg, $rating, $image, $order, $status]);
    }
    header('Location: reviews.php');
    exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM reviews WHERE id=?")->execute([$_GET['delete']]);
    header('Location: reviews.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch();
}

$items = $pdo->query("SELECT * FROM reviews ORDER BY sort_order")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reviews - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Reviews</h1></div>

    <div class="admin-card">
      <h3 style="margin-bottom:16px;"><?= $editItem ? 'Edit Review' : 'Add New Review' ?></h3>
      <form method="POST" enctype="multipart/form-data">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>"><?php endif; ?>
        <div class="form-group"><label>Client Name</label>
          <input type="text" name="client_name" required value="<?= e($editItem['client_name'] ?? '') ?>"></div>
        <div class="form-group"><label>Message</label>
          <textarea name="message"><?= e($editItem['message'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Rating (1-5)</label>
          <input type="number" name="rating" min="1" max="5" value="<?= e($editItem['rating'] ?? 5) ?>"></div>
        <div class="form-group"><label>Client Photo</label>
          <input type="file" name="image">
          <?php if (!empty($editItem['image'])): ?><p style="font-size:0.85rem;color:var(--color-muted);">Current: <?= e($editItem['image']) ?></p><?php endif; ?>
        </div>
        <div class="form-group"><label>Sort Order</label>
          <input type="number" name="sort_order" value="<?= e($editItem['sort_order'] ?? 0) ?>"></div>
        <div class="form-group"><label>Status</label>
          <select name="status" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="active" <?= (($editItem['status'] ?? '')=='active')?'selected':'' ?>>Active</option>
            <option value="inactive" <?= (($editItem['status'] ?? '')=='inactive')?'selected':'' ?>>Inactive</option>
          </select></div>
        <button type="submit" class="btn"><?= $editItem ? 'Update' : 'Add' ?> Review</button>
        <?php if ($editItem): ?><a href="reviews.php" class="btn" style="background:#9ca3af;">Cancel</a><?php endif; ?>
      </form>
    </div>

    <div class="admin-card">
      <table class="admin-table">
        <tr><th>Client</th><th>Rating</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= e($it['client_name']) ?></td>
            <td><?= str_repeat('★', (int)$it['rating']) ?></td>
            <td><span class="badge badge-<?= $it['status'] ?>"><?= e($it['status']) ?></span></td>
            <td class="action-links">
              <a href="?edit=<?= (int)$it['id'] ?>">Edit</a>
              <a href="?delete=<?= (int)$it['id'] ?>" class="delete" onclick="return confirm('Delete this review?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>
</body>
</html>
