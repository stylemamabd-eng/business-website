<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $date  = $_POST['job_date'] ?: null;
    $order = (int)($_POST['sort_order'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $image = uploadImage('image');

    if (!empty($_POST['id'])) {
        if ($image) {
            $stmt = $pdo->prepare("UPDATE completed_jobs SET title=?, description=?, job_date=?, sort_order=?, status=?, image=? WHERE id=?");
            $stmt->execute([$title, $desc, $date, $order, $status, $image, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE completed_jobs SET title=?, description=?, job_date=?, sort_order=?, status=? WHERE id=?");
            $stmt->execute([$title, $desc, $date, $order, $status, $_POST['id']]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO completed_jobs (title, description, job_date, image, sort_order, status) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$title, $desc, $date, $image, $order, $status]);
    }
    header('Location: jobs.php');
    exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM completed_jobs WHERE id=?")->execute([$_GET['delete']]);
    header('Location: jobs.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM completed_jobs WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch();
}

$items = $pdo->query("SELECT * FROM completed_jobs ORDER BY sort_order DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Completed Jobs - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Completed Jobs</h1></div>

    <div class="admin-card">
      <h3 style="margin-bottom:16px;"><?= $editItem ? 'Edit Job' : 'Add New Job' ?></h3>
      <form method="POST" enctype="multipart/form-data">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>"><?php endif; ?>
        <div class="form-group"><label>Title</label>
          <input type="text" name="title" required value="<?= e($editItem['title'] ?? '') ?>"></div>
        <div class="form-group"><label>Description</label>
          <textarea name="description"><?= e($editItem['description'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Job Date</label>
          <input type="date" name="job_date" value="<?= e($editItem['job_date'] ?? '') ?>"></div>
        <div class="form-group"><label>Image</label>
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
        <button type="submit" class="btn"><?= $editItem ? 'Update' : 'Add' ?> Job</button>
        <?php if ($editItem): ?><a href="jobs.php" class="btn" style="background:#9ca3af;">Cancel</a><?php endif; ?>
      </form>
    </div>

    <div class="admin-card">
      <table class="admin-table">
        <tr><th>Title</th><th>Date</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= e($it['title']) ?></td>
            <td><?= e($it['job_date']) ?></td>
            <td><span class="badge badge-<?= $it['status'] ?>"><?= e($it['status']) ?></span></td>
            <td class="action-links">
              <a href="?edit=<?= (int)$it['id'] ?>">Edit</a>
              <a href="?delete=<?= (int)$it['id'] ?>" class="delete" onclick="return confirm('Delete this job?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>
</body>
</html>
