<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar">
      <h1>Dashboard</h1>
      <span>Welcome, <?= e($_SESSION['admin_username']) ?></span>
    </div>

    <div class="grid">
      <?php
      $counts = [
        'Services'        => $pdo->query("SELECT COUNT(*) c FROM services")->fetch()['c'],
        'Completed Jobs'  => $pdo->query("SELECT COUNT(*) c FROM completed_jobs")->fetch()['c'],
        'Reviews'         => $pdo->query("SELECT COUNT(*) c FROM reviews")->fetch()['c'],
        'Team Members'    => $pdo->query("SELECT COUNT(*) c FROM teams")->fetch()['c'],
        'Custom Sections' => $pdo->query("SELECT COUNT(*) c FROM custom_sections")->fetch()['c'],
        'Blog Posts'      => $pdo->query("SELECT COUNT(*) c FROM blogs")->fetch()['c'],
        'Custom Pages'    => $pdo->query("SELECT COUNT(*) c FROM pages")->fetch()['c'],
        'Menu Links'      => $pdo->query("SELECT COUNT(*) c FROM menus")->fetch()['c'],
        'New Messages'    => $pdo->query("SELECT COUNT(*) c FROM messages WHERE is_read=0")->fetch()['c'],
      ];
      foreach ($counts as $label => $count):
      ?>
        <div class="admin-card" style="text-align:center;">
          <h2 style="font-size:2rem; color:var(--color-primary);"><?= (int)$count ?></h2>
          <p><?= e($label) ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Real-Time Visitors widget on Dashboard Home -->
    <?php
    $active_stmt = $pdo->prepare("SELECT COUNT(DISTINCT session_id) count FROM page_visits WHERE last_activity >= datetime('now', '-5 minutes')");
    $active_stmt->execute();
    $active_users_count = $active_stmt->fetch()['count'];
    ?>
    <div class="admin-card" style="margin-top:20px; border-left: 5px solid #10b981; background: #f0fdf4; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <div>
        <div style="display:flex; align-items:center; gap:10px;">
          <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#10b981; animation: dashboard-pulse 1.5s infinite;"></span>
          <h3 style="color:#065f46; margin:0;">Real-Time Active Visitors (Last 5 Minutes)</h3>
        </div>
        <p style="color:#047857; margin-top:5px; font-size:0.95rem;">Keep track of users currently active on your website.</p>
      </div>
      <div style="display:flex; align-items:center; gap:20px;">
        <div style="font-size:2.8rem; font-weight:700; color:#047857; line-height:1;"><?= (int)$active_users_count ?> Active</div>
        <a href="analytics.php" class="btn" style="background:#10b981; color:#fff; font-weight:600; text-decoration:none;">View Full Analytics &rarr;</a>
      </div>
    </div>
  </div>
</div>

<style>
@keyframes dashboard-pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
</style>
</body>
</html>
