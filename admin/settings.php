<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $stmt = $pdo->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key=?");
        $stmt->execute([trim($value), $key]);
    }
    header('Location: settings.php?saved=1');
    exit;
}
$settings = [];
foreach ($pdo->query("SELECT * FROM site_settings")->fetchAll() as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Site Settings - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Site Settings</h1></div>
    <?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Settings saved. Sob page e update hoye geche.</div><?php endif; ?>

    <div class="admin-card">
      <form method="POST">
        <div class="form-group"><label>Site Name</label>
          <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>"></div>
        <div class="form-group"><label>Tagline</label>
          <input type="text" name="site_tagline" value="<?= e($settings['site_tagline'] ?? '') ?>"></div>
        <div class="form-group"><label>Phone</label>
          <input type="text" name="phone" value="<?= e($settings['phone'] ?? '') ?>"></div>
        <div class="form-group"><label>Email</label>
          <input type="text" name="email" value="<?= e($settings['email'] ?? '') ?>"></div>
        <div class="form-group"><label>Address</label>
          <input type="text" name="address" value="<?= e($settings['address'] ?? '') ?>"></div>
        <div class="form-group"><label>Facebook URL</label>
          <input type="text" name="facebook_url" value="<?= e($settings['facebook_url'] ?? '') ?>"></div>
        <div class="form-group"><label>Footer Text</label>
          <input type="text" name="footer_text" value="<?= e($settings['footer_text'] ?? '') ?>"></div>
        <button type="submit" class="btn">Save Settings</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
