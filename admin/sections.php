<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key   = trim($_POST['section_key'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $page  = $_POST['page'] ?? 'home';
    $order = (int)($_POST['sort_order'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $image = uploadImage('image');

    if (!empty($_POST['id'])) {
        if ($image) {
            $stmt = $pdo->prepare("UPDATE custom_sections SET section_key=?, title=?, content=?, page=?, sort_order=?, status=?, image=? WHERE id=?");
            $stmt->execute([$key, $title, $content, $page, $order, $status, $image, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE custom_sections SET section_key=?, title=?, content=?, page=?, sort_order=?, status=? WHERE id=?");
            $stmt->execute([$key, $title, $content, $page, $order, $status, $_POST['id']]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO custom_sections (section_key, title, content, page, image, sort_order, status) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$key, $title, $content, $page, $image, $order, $status]);
    }
    header('Location: sections.php');
    exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM custom_sections WHERE id=?")->execute([$_GET['delete']]);
    header('Location: sections.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM custom_sections WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch();
}

$items = $pdo->query("SELECT * FROM custom_sections ORDER BY page, sort_order")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Custom Sections - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Custom Sections</h1></div>
    <p style="margin-bottom:20px;color:var(--color-muted);">
      Custom section jekono page e show korano jay. "Page" field e select koro ei section kon page e dekhabe (jemon: about, home, services)। Notun page e dekhate hole oi page er PHP file e query add kora lagbe — ekhon shudhu <strong>about.php</strong> page e "about" key er section gula auto show hoy.
    </p>

    <div class="admin-card">
      <h3 style="margin-bottom:16px;"><?= $editItem ? 'Edit Section' : 'Add New Section' ?></h3>
      <form method="POST" enctype="multipart/form-data">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>"><?php endif; ?>
        <div class="form-group"><label>Section Key (unique, no space)</label>
          <input type="text" name="section_key" required value="<?= e($editItem['section_key'] ?? '') ?>" placeholder="e.g. about-mission"></div>
        <div class="form-group"><label>Title</label>
          <input type="text" name="title" value="<?= e($editItem['title'] ?? '') ?>"></div>
        <div class="form-group"><label>Content</label>
          <textarea name="content"><?= e($editItem['content'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Image</label>
          <input type="file" name="image">
          <?php if (!empty($editItem['image'])): ?><p style="font-size:0.85rem;color:var(--color-muted);">Current: <?= e($editItem['image']) ?></p><?php endif; ?>
        </div>
        <div class="form-group"><label>Show on Page</label>
          <select name="page" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
            <?php foreach (['home','about','services','contact'] as $p): ?>
              <option value="<?= $p ?>" <?= (($editItem['page'] ?? '')==$p)?'selected':'' ?>><?= ucfirst($p) ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="form-group"><label>Sort Order</label>
          <input type="number" name="sort_order" value="<?= e($editItem['sort_order'] ?? 0) ?>"></div>
        <div class="form-group"><label>Status</label>
          <select name="status" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="active" <?= (($editItem['status'] ?? '')=='active')?'selected':'' ?>>Active</option>
            <option value="inactive" <?= (($editItem['status'] ?? '')=='inactive')?'selected':'' ?>>Inactive</option>
          </select></div>
        <button type="submit" class="btn"><?= $editItem ? 'Update' : 'Add' ?> Section</button>
        <?php if ($editItem): ?><a href="sections.php" class="btn" style="background:#9ca3af;">Cancel</a><?php endif; ?>
      </form>
    </div>

    <div class="admin-card">
      <table class="admin-table">
        <tr><th>Key</th><th>Title</th><th>Page</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= e($it['section_key']) ?></td>
            <td><?= e($it['title']) ?></td>
            <td><?= e($it['page']) ?></td>
            <td><span class="badge badge-<?= $it['status'] ?>"><?= e($it['status']) ?></span></td>
            <td class="action-links">
              <a href="?edit=<?= (int)$it['id'] ?>">Edit</a>
              <a href="?delete=<?= (int)$it['id'] ?>" class="delete" onclick="return confirm('Delete this section?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>
</body>
</html>
