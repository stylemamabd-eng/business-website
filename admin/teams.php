<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desig = trim($_POST['designation'] ?? '');
    $bio  = trim($_POST['bio'] ?? '');
    $fb   = trim($_POST['facebook_url'] ?? '');
    $order = (int)($_POST['sort_order'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $image = uploadImage('image');

    if (!empty($_POST['id'])) {
        if ($image) {
            $stmt = $pdo->prepare("UPDATE teams SET name=?, designation=?, bio=?, facebook_url=?, sort_order=?, status=?, image=? WHERE id=?");
            $stmt->execute([$name, $desig, $bio, $fb, $order, $status, $image, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE teams SET name=?, designation=?, bio=?, facebook_url=?, sort_order=?, status=? WHERE id=?");
            $stmt->execute([$name, $desig, $bio, $fb, $order, $status, $_POST['id']]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO teams (name, designation, bio, facebook_url, image, sort_order, status) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$name, $desig, $bio, $fb, $image, $order, $status]);
    }
    header('Location: teams.php');
    exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM teams WHERE id=?")->execute([$_GET['delete']]);
    header('Location: teams.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch();
}

$items = $pdo->query("SELECT * FROM teams ORDER BY sort_order")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Team - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Team</h1></div>

    <div class="admin-card">
      <h3 style="margin-bottom:16px;"><?= $editItem ? 'Edit Member' : 'Add New Member' ?></h3>
      <form method="POST" enctype="multipart/form-data">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>"><?php endif; ?>
        <div class="form-group"><label>Name</label>
          <input type="text" name="name" required value="<?= e($editItem['name'] ?? '') ?>"></div>
        <div class="form-group"><label>Designation</label>
          <input type="text" name="designation" value="<?= e($editItem['designation'] ?? '') ?>"></div>
        <div class="form-group"><label>Bio</label>
          <textarea name="bio"><?= e($editItem['bio'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Facebook URL</label>
          <input type="text" name="facebook_url" value="<?= e($editItem['facebook_url'] ?? '') ?>"></div>
        <div class="form-group"><label>Photo</label>
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
        <button type="submit" class="btn"><?= $editItem ? 'Update' : 'Add' ?> Member</button>
        <?php if ($editItem): ?><a href="teams.php" class="btn" style="background:#9ca3af;">Cancel</a><?php endif; ?>
      </form>
    </div>

    <div class="admin-card">
      <table class="admin-table">
        <tr><th>Name</th><th>Designation</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= e($it['name']) ?></td>
            <td><?= e($it['designation']) ?></td>
            <td><span class="badge badge-<?= $it['status'] ?>"><?= e($it['status']) ?></span></td>
            <td class="action-links">
              <a href="?edit=<?= (int)$it['id'] ?>">Edit</a>
              <a href="?delete=<?= (int)$it['id'] ?>" class="delete" onclick="return confirm('Delete this member?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>
</body>
</html>
