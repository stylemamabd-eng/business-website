<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
// ---- handle actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $label       = trim($_POST['label'] ?? '');
    $link        = trim($_POST['link'] ?? '');
    $link_type   = $_POST['link_type'] ?? 'custom';
    $custom_page = $_POST['custom_page'] ?? '';
    $blog_post   = $_POST['blog_post'] ?? '';
    $parent_id   = $_POST['parent_id'] ?? '';
    $sort_order  = (int)($_POST['sort_order'] ?? 0);

    // Determine final link based on selection type
    if ($link_type === 'page' && !empty($custom_page)) {
        $link = 'page.php?slug=' . $custom_page;
    } elseif ($link_type === 'blog' && !empty($blog_post)) {
        $link = 'blog-detail.php?slug=' . $blog_post;
    }

    if (empty($parent_id) || $parent_id === 'none') {
        $parent_id = null;
    } else {
        $parent_id = (int)$parent_id;
    }

    if (!empty($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];
        
        // Prevent setting parent_id to itself
        if ($parent_id === $id) {
            $parent_id = null;
        }
        
        $stmt = $pdo->prepare("UPDATE menus SET label=?, link=?, parent_id=?, sort_order=? WHERE id=?");
        $stmt->execute([$label, $link, $parent_id, $sort_order, $id]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO menus (label, link, parent_id, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$label, $link, $parent_id, $sort_order]);
    }
    header('Location: menus.php');
    exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM menus WHERE id=?")->execute([$_GET['delete']]);
    header('Location: menus.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch();
}

// Fetch all menus for rendering and selection
$items = $pdo->query("SELECT * FROM menus ORDER BY sort_order ASC, id ASC")->fetchAll();

// Group into hierarchy
$top_level_menus = [];
$sub_menus_map = [];
foreach ($items as $it) {
    if (empty($it['parent_id'])) {
        $top_level_menus[] = $it;
    } else {
        $sub_menus_map[$it['parent_id']][] = $it;
    }
}

// Fetch pages and blogs for link selectors
$custom_pages = [];
try { $custom_pages = $pdo->query("SELECT title, slug FROM pages WHERE status='active'")->fetchAll(); } catch (PDOException $e) {}

$blog_posts = [];
try { $blog_posts = $pdo->query("SELECT title, slug FROM blogs WHERE status='active'")->fetchAll(); } catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Navigation Menu Manager - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Navigation Menu Manager</h1></div>

    <div class="admin-card">
      <h3 style="margin-bottom:16px;"><?= $editItem ? 'Edit Menu Item' : 'Add New Menu Item' ?></h3>
      <form method="POST">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>"><?php endif; ?>
        
        <div class="form-group"><label>Menu Label / Text (e.g. "Services", "Contact Us")</label>
          <input type="text" name="label" required value="<?= e($editItem['label'] ?? '') ?>"></div>

        <div class="form-group"><label>Link Type</label>
          <select id="link_type" name="link_type" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);" onchange="toggleLinkInputs()">
            <option value="custom" <?= !$editItem ? 'selected' : '' ?>>Custom URL or Page Link</option>
            <option value="page">Select Custom Page</option>
            <option value="blog">Select Blog Post</option>
          </select></div>

        <!-- Option A: Custom Link input -->
        <div class="form-group" id="group-custom"><label>Link URL</label>
          <input type="text" id="link-field" name="link" placeholder="e.g. index.php, services.php, #, or https://google.com" value="<?= e($editItem['link'] ?? '') ?>"></div>

        <!-- Option B: Select Custom Page dropdown -->
        <div class="form-group" id="group-page" style="display:none;"><label>Custom Page</label>
          <select name="custom_page" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="">-- Choose Page --</option>
            <?php foreach ($custom_pages as $p): ?>
              <option value="<?= e($p['slug']) ?>"><?= e($p['title']) ?></option>
            <?php endforeach; ?>
          </select></div>

        <!-- Option C: Select Blog Post dropdown -->
        <div class="form-group" id="group-blog" style="display:none;"><label>Blog Post</label>
          <select name="blog_post" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="">-- Choose Blog --</option>
            <?php foreach ($blog_posts as $b): ?>
              <option value="<?= e($b['slug']) ?>"><?= e($b['title']) ?></option>
            <?php endforeach; ?>
          </select></div>

        <div class="form-group"><label>Parent Menu placement (Nest under another menu to create dropdown)</label>
          <select name="parent_id" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="none" <?= (($editItem['parent_id'] ?? '')=='')?'selected':'' ?>>None (Top Level Navbar link)</option>
            <?php foreach ($top_level_menus as $m): ?>
              <?php if (!$editItem || $editItem['id'] != $m['id']): ?>
                <option value="<?= (int)$m['id'] ?>" <?= (($editItem['parent_id'] ?? '')==$m['id'])?'selected':'' ?>>Under: <?= e($m['label']) ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select></div>

        <div class="form-group"><label>Sort Order</label>
          <input type="number" name="sort_order" value="<?= e($editItem['sort_order'] ?? 0) ?>"></div>
        
        <button type="submit" class="btn"><?= $editItem ? 'Update' : 'Add to Menu' ?></button>
        <?php if ($editItem): ?><a href="menus.php" class="btn" style="background:#9ca3af;">Cancel</a><?php endif; ?>
      </form>
    </div>

    <!-- Menu Structure Representation -->
    <div class="admin-card">
      <h3 style="margin-bottom:16px;">Current Navbar Layout (Hierarchy)</h3>
      <table class="admin-table">
        <tr><th>Menu Link Label</th><th>Target URL</th><th>Sort Order</th><th>Actions</th></tr>
        <?php foreach ($top_level_menus as $m): ?>
          <tr style="background:#f9fafb; font-weight:600;">
            <td>📁 <?= e($m['label']) ?></td>
            <td><code><?= e($m['link']) ?></code></td>
            <td><?= (int)$m['sort_order'] ?></td>
            <td class="action-links">
              <a href="?edit=<?= (int)$m['id'] ?>">Edit</a>
              <a href="?delete=<?= (int)$m['id'] ?>" class="delete" onclick="return confirm('Delete this menu? All submenu dropdown items under it will become top-level.')">Delete</a>
            </td>
          </tr>
          <!-- Submenu items -->
          <?php if (!empty($sub_menus_map[$m['id']])): ?>
            <?php foreach ($sub_menus_map[$m['id']] as $sub): ?>
              <tr>
                <td style="padding-left:40px;">↳ 📄 <?= e($sub['label']) ?></td>
                <td><code><?= e($sub['link']) ?></code></td>
                <td><?= (int)$sub['sort_order'] ?></td>
                <td class="action-links">
                  <a href="?edit=<?= (int)$sub['id'] ?>">Edit</a>
                  <a href="?delete=<?= (int)$sub['id'] ?>" class="delete" onclick="return confirm('Delete this submenu link?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </table>
    </div>

  </div>
</div>

<script>
function toggleLinkInputs() {
    const type = document.getElementById('link_type').value;
    const gCustom = document.getElementById('group-custom');
    const gPage = document.getElementById('group-page');
    const gBlog = document.getElementById('group-blog');

    gCustom.style.display = 'none';
    gPage.style.display = 'none';
    gBlog.style.display = 'none';

    if (type === 'custom') {
        gCustom.style.display = 'block';
    } else if (type === 'page') {
        gPage.style.display = 'block';
    } else if (type === 'blog') {
        gBlog.style.display = 'block';
    }
}

// Check on edit if link starts with page.php or blog-detail.php
<?php if ($editItem): ?>
window.onload = function() {
    const link = "<?= e($editItem['link']) ?>";
    const selectType = document.getElementById('link_type');
    
    if (link.startsWith('page.php?slug=')) {
        selectType.value = 'page';
        const slug = link.replace('page.php?slug=', '');
        const selectPage = document.querySelector('select[name="custom_page"]');
        if (selectPage) selectPage.value = slug;
    } else if (link.startsWith('blog-detail.php?slug=')) {
        selectType.value = 'blog';
        const slug = link.replace('blog-detail.php?slug=', '');
        const selectBlog = document.querySelector('select[name="blog_post"]');
        if (selectBlog) selectBlog.value = slug;
    } else {
        selectType.value = 'custom';
    }
    toggleLinkInputs();
}
<?php endif; ?>
</script>
</body>
</html>
