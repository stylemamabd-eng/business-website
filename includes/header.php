<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/tracker.php';
$settings = getSettings($pdo);
$current = basename($_SERVER['PHP_SELF']);

// Load all menu items dynamically from the database
$menu_items = [];
try {
    $stmt = $pdo->query("SELECT * FROM menus ORDER BY sort_order ASC, id ASC");
    $menu_items = $stmt->fetchAll();
} catch (PDOException $e) {
    // ignore
}

// Group menu items into hierarchy
$top_menus = [];
$sub_menus = [];
foreach ($menu_items as $item) {
    if (empty($item['parent_id'])) {
        $top_menus[] = $item;
    } else {
        $sub_menus[$item['parent_id']][] = $item;
    }
}

// Determine SEO tags
$site_name = $settings['site_name'] ?? 'Apex Digital';
$meta_title = isset($page_seo_title) ? $page_seo_title : ($site_name . (isset($page_title) ? ' - ' . $page_title : ''));
$meta_desc = $page_description ?? $settings['site_tagline'] ?? '';
$meta_keywords = $page_keywords ?? 'business, crm, services, web development, software';
$meta_image = $page_image ?? 'uploads/default-share.jpg';
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($meta_title) ?></title>
<meta name="description" content="<?= e($meta_desc) ?>">
<meta name="keywords" content="<?= e($meta_keywords) ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($meta_title) ?>">
<meta property="og:description" content="<?= e($meta_desc) ?>">
<meta property="og:url" content="<?= e($current_url) ?>">
<meta property="og:image" content="<?= e($meta_image) ?>">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:title" content="<?= e($meta_title) ?>">
<meta property="twitter:description" content="<?= e($meta_desc) ?>">
<meta property="twitter:image" content="<?= e($meta_image) ?>">

<link rel="stylesheet" href="css/style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <a href="index.php" class="logo"><?= e($site_name) ?></a>

    <nav class="main-nav" id="mainNav">
      <?php foreach ($top_menus as $m): ?>
        <?php if (!empty($sub_menus[$m['id']])): ?>
          <!-- Render as Dropdown -->
          <div class="dropdown">
            <?php 
              // Check if any sub-item is active
              $is_active = ($current == $m['link']);
              foreach ($sub_menus[$m['id']] as $sub) {
                  if ($current == $sub['link'] || (isset($_GET['slug']) && strpos($sub['link'], $_GET['slug']) !== false)) {
                      $is_active = true;
                  }
              }
            ?>
            <?php if ($m['link'] === '#' || empty($m['link'])): ?>
              <span class="dropdown-toggle <?= $is_active ? 'active' : '' ?>"><?= e($m['label']) ?></span>
            <?php else: ?>
              <a href="<?= e($m['link']) ?>" class="dropdown-toggle <?= $is_active ? 'active' : '' ?>"><?= e($m['label']) ?></a>
            <?php endif; ?>
            <div class="dropdown-content">
              <?php foreach ($sub_menus[$m['id']] as $sub): ?>
                <a href="<?= e($sub['link']) ?>"><?= e($sub['label']) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php else: ?>
          <!-- Render as standard link -->
          <a href="<?= e($m['link']) ?>" class="<?= ($current == $m['link'] || (isset($_GET['slug']) && strpos($m['link'], $_GET['slug']) !== false)) ? 'active' : '' ?>"><?= e($m['label']) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>

    <button class="nav-toggle" id="navToggle" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<main>
