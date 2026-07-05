<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM messages WHERE id=?")->execute([$_GET['delete']]);
    header('Location: messages.php');
    exit;
}
if (isset($_GET['read'])) {
    $pdo->prepare("UPDATE messages SET is_read=1 WHERE id=?")->execute([$_GET['read']]);
    header('Location: messages.php');
    exit;
}
$items = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Contact Messages</h1></div>
    <div class="admin-card">
      <table class="admin-table">
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th>Date</th><th>Actions</th></tr>
        <?php foreach ($items as $it): ?>
          <tr style="<?= $it['is_read'] ? '' : 'font-weight:600;background:#eff6ff;' ?>">
            <td><?= e($it['name']) ?></td>
            <td><?= e($it['email']) ?></td>
            <td><?= e($it['phone']) ?></td>
            <td><?= e(mb_strimwidth($it['message'], 0, 60, '...')) ?></td>
            <td><?= e($it['created_at']) ?></td>
            <td class="action-links">
              <?php if (!$it['is_read']): ?><a href="?read=<?= (int)$it['id'] ?>">Mark Read</a><?php endif; ?>
              <a href="?delete=<?= (int)$it['id'] ?>" class="delete" onclick="return confirm('Delete this message?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$items): ?><tr><td colspan="6">No messages yet.</td></tr><?php endif; ?>
      </table>
    </div>
  </div>
</div>
</body>
</html>
