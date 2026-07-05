<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
// ---- Filters configuration ----
$time_range = $_GET['time_range'] ?? '7days';
$selected_page = $_GET['page_url'] ?? '';
$selected_country = $_GET['country'] ?? '';

// Build SQL where clauses
$where_clauses = ["1=1"];
$params = [];

if ($time_range === 'today') {
    $where_clauses[] = "created_at >= ?";
    $params[] = date('Y-m-d 00:00:00');
} elseif ($time_range === 'yesterday') {
    $where_clauses[] = "created_at >= ? AND created_at < ?";
    $params[] = date('Y-m-d 00:00:00', time() - 86400);
    $params[] = date('Y-m-d 00:00:00');
} elseif ($time_range === '7days') {
    $where_clauses[] = "created_at >= ?";
    $params[] = date('Y-m-d 00:00:00', time() - 7 * 86400);
} elseif ($time_range === '30days') {
    $where_clauses[] = "created_at >= ?";
    $params[] = date('Y-m-d 00:00:00', time() - 30 * 86400);
}

if (!empty($selected_page)) {
    $where_clauses[] = "page_url = ?";
    $params[] = $selected_page;
}

if (!empty($selected_country)) {
    $where_clauses[] = "country = ?";
    $params[] = $selected_country;
}

$where_sql = implode(" AND ", $where_clauses);

// ---- Real-time active users (active in the last 5 minutes) ----
// Since SQlite uses UTC or local depending on config, let's use a 5 minutes threshold
$active_threshold = date('Y-m-d H:i:s', time() - 300);
// Wait, SQLite CURRENT_TIMESTAMP is UTC. So we check last_activity based on SQLite's datetime
$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
$time_clause = ($driver === 'pgsql') ? "NOW() - INTERVAL '5 minutes'" : "datetime('now', '-5 minutes')";

$active_stmt = $pdo->prepare("SELECT COUNT(DISTINCT session_id) count FROM page_visits WHERE last_activity >= $time_clause");
$active_stmt->execute();
$active_users_count = $active_stmt->fetch()['count'];

$active_loc_stmt = $pdo->prepare("SELECT country, city, COUNT(DISTINCT session_id) count FROM page_visits WHERE last_activity >= $time_clause GROUP BY country, city ORDER BY count DESC");
$active_loc_stmt->execute();
$active_locations = $active_loc_stmt->fetchAll();

// ---- Calculated Metrics with Filters ----
$metrics_stmt = $pdo->prepare("SELECT COUNT(*) pageviews, COUNT(DISTINCT session_id) visitors FROM page_visits WHERE $where_sql");
$metrics_stmt->execute($params);
$metrics = $metrics_stmt->fetch();
$total_pageviews = $metrics['pageviews'] ?? 0;
$unique_visitors = $metrics['visitors'] ?? 0;

// ---- Top Visited Pages ----
$pages_stmt = $pdo->prepare("SELECT page_url, COUNT(*) views, COUNT(DISTINCT session_id) unique_visitors FROM page_visits WHERE $where_sql GROUP BY page_url ORDER BY views DESC LIMIT 15");
$pages_stmt->execute($params);
$top_pages = $pages_stmt->fetchAll();

// ---- Top Locations ----
$loc_stmt = $pdo->prepare("SELECT country, city, COUNT(*) views, COUNT(DISTINCT session_id) unique_visitors FROM page_visits WHERE $where_sql GROUP BY country, city ORDER BY views DESC LIMIT 15");
$loc_stmt->execute($params);
$top_locations = $loc_stmt->fetchAll();

// ---- Recent Visitors Log ----
$recent_stmt = $pdo->prepare("SELECT * FROM page_visits WHERE $where_sql ORDER BY created_at DESC LIMIT 15");
$recent_stmt->execute($params);
$recent_visits = $recent_stmt->fetchAll();

// ---- Retrieve Filter Dropdown Data ----
$filter_pages = $pdo->query("SELECT DISTINCT page_url FROM page_visits ORDER BY page_url ASC")->fetchAll();
$filter_countries = $pdo->query("SELECT DISTINCT country FROM page_visits ORDER BY country ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Analytics Dashboard - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    
    <div class="admin-topbar" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <h1>Site Analytics Dashboard (Google Analytics Lite)</h1>
      <a href="../index.php" target="_blank" class="btn" style="background:var(--color-accent); font-weight:600;">View Website &rarr;</a>
    </div>

    <!-- REAL-TIME STATUS BLOCK -->
    <div class="admin-card" style="border-left: 5px solid #10b981; background: #f0fdf4;">
      <div style="display:flex; align-items:center; gap:10px;">
        <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#10b981; animation: pulse 1.5s infinite;"></span>
        <h3 style="color:#065f46; margin:0;">Real-Time Active Visitors (Last 5 Minutes)</h3>
      </div>
      <div style="font-size:3rem; font-weight:700; color:#047857; margin:10px 0;"><?= (int)$active_users_count ?></div>
      <div style="font-size:0.95rem; color:#065f46;">
        <?php if ($active_locations): ?>
          <strong>Current Locations:</strong>
          <ul style="margin:5px 0 0 16px; padding:0; list-style:disc;">
            <?php foreach ($active_locations as $al): ?>
              <li><?= e($al['city']) ?>, <?= e($al['country']) ?> (<?= (int)$al['count'] ?> visitor<?= $al['count'] > 1 ? 's' : '' ?>)</li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          No active visitors on the site right now.
        <?php endif; ?>
      </div>
    </div>

    <!-- FILTERS CONTROL PANEL -->
    <div class="admin-card">
      <h3 style="margin-bottom:16px;">Filter Statistics</h3>
      <form method="GET" style="display:flex; gap:16px; flex-wrap:wrap; align-items:flex-end;">
        <div class="form-group" style="flex:1; min-width:180px; margin:0;">
          <label>Time Range</label>
          <select name="time_range" style="width:100%;padding:10px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="today" <?= $time_range === 'today' ? 'selected' : '' ?>>Today</option>
            <option value="yesterday" <?= $time_range === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
            <option value="7days" <?= $time_range === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
            <option value="30days" <?= $time_range === '30days' ? 'selected' : '' ?>>Last 30 Days</option>
            <option value="all" <?= $time_range === 'all' ? 'selected' : '' ?>>All Time</option>
          </select>
        </div>

        <div class="form-group" style="flex:1.5; min-width:200px; margin:0;">
          <label>Page URL</label>
          <select name="page_url" style="width:100%;padding:10px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="">-- All Pages --</option>
            <?php foreach ($filter_pages as $fp): ?>
              <option value="<?= e($fp['page_url']) ?>" <?= $selected_page === $fp['page_url'] ? 'selected' : '' ?>><?= e($fp['page_url']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group" style="flex:1.2; min-width:180px; margin:0;">
          <label>Country</label>
          <select name="country" style="width:100%;padding:10px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="">-- All Countries --</option>
            <?php foreach ($filter_countries as $fc): ?>
              <option value="<?= e($fc['country']) ?>" <?= $selected_country === $fc['country'] ? 'selected' : '' ?>><?= e($fc['country']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn" style="padding:12px 24px;">Apply Filters</button>
        <a href="analytics.php" class="btn" style="background:#9ca3af; padding:12px 24px;">Reset</a>
      </form>
    </div>

    <!-- MAIN STATISTICS GRID -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:20px; margin-bottom:24px;">
      <div class="admin-card" style="margin:0; text-align:center;">
        <h4 style="color:var(--color-muted); margin-bottom:8px;">Total Pageviews</h4>
        <div style="font-size:2.5rem; font-weight:700; color:var(--color-primary);"><?= (int)$total_pageviews ?></div>
      </div>
      <div class="admin-card" style="margin:0; text-align:center;">
        <h4 style="color:var(--color-muted); margin-bottom:8px;">Unique Visitors</h4>
        <div style="font-size:2.5rem; font-weight:700; color:var(--color-dark);"><?= (int)$unique_visitors ?></div>
      </div>
    </div>

    <!-- DATA REPORT COLUMNS -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap:20px; margin-bottom:24px;">
      
      <!-- TOP PAGES -->
      <div class="admin-card" style="margin:0;">
        <h3 style="margin-bottom:16px;">Top Pages</h3>
        <table class="admin-table" style="font-size:0.92rem;">
          <tr style="background:#f9fafb;"><th>Page URL</th><th>Views</th><th>Visitors</th></tr>
          <?php foreach ($top_pages as $tp): ?>
            <tr>
              <td><code><?= e($tp['page_url']) ?></code></td>
              <td><strong><?= (int)$tp['views'] ?></strong></td>
              <td><?= (int)$tp['unique_visitors'] ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$top_pages): ?>
            <tr><td colspan="3" style="text-align:center; color:var(--color-muted);">No pageview data found matching filters.</td></tr>
          <?php endif; ?>
        </table>
      </div>

      <!-- TOP LOCATIONS -->
      <div class="admin-card" style="margin:0;">
        <h3 style="margin-bottom:16px;">Top Geographies</h3>
        <table class="admin-table" style="font-size:0.92rem;">
          <tr style="background:#f9fafb;"><th>Location</th><th>Views</th><th>Visitors</th></tr>
          <?php foreach ($top_locations as $tl): ?>
            <tr>
              <td><?= e($tl['city']) ?>, <?= e($tl['country']) ?></td>
              <td><strong><?= (int)$tl['views'] ?></strong></td>
              <td><?= (int)$tl['unique_visitors'] ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$top_locations): ?>
            <tr><td colspan="3" style="text-align:center; color:var(--color-muted);">No location data found matching filters.</td></tr>
          <?php endif; ?>
        </table>
      </div>

    </div>

    <!-- LATEST VISITOR LOGS -->
    <div class="admin-card">
      <h3 style="margin-bottom:16px;">Latest Visitor Sessions</h3>
      <table class="admin-table" style="font-size:0.9rem;">
        <tr style="background:#f9fafb;"><th>Date & Time</th><th>IP Address</th><th>Location</th><th>Page URL</th><th>User Agent</th></tr>
        <?php foreach ($recent_visits as $rv): ?>
          <tr>
            <td style="white-space:nowrap;"><?= e($rv['created_at']) ?></td>
            <td><code><?= e($rv['ip_address']) ?></code></td>
            <td><?= e($rv['city']) ?>, <?= e($rv['country']) ?></td>
            <td><code><?= e($rv['page_url']) ?></code></td>
            <td style="max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= e($rv['user_agent']) ?>"><?= e($rv['user_agent']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$recent_visits): ?>
          <tr><td colspan="5" style="text-align:center; color:var(--color-muted);">No visitor log found matching filters.</td></tr>
        <?php endif; ?>
      </table>
    </div>

  </div>
</div>

<style>
@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
</style>
</body>
</html>
