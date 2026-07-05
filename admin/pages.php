<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
function slugify($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = trim($text, '-');
    return empty($text) ? 'page-' . time() : $text;
}

// ---- handle page actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_page') {
    $title       = trim($_POST['title'] ?? '');
    $subtitle    = trim($_POST['subtitle'] ?? '');
    $content     = trim($_POST['content'] ?? '');
    $parent_menu = trim($_POST['parent_menu'] ?? '');
    if ($parent_menu === 'root' || $parent_menu === '') {
        $parent_menu = null;
    }
    
    $meta_desc = trim($_POST['meta_description'] ?? '');
    $meta_key  = trim($_POST['meta_keywords'] ?? '');
    $status    = $_POST['status'] ?? 'active';

    // Auto generate or use custom slug
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }

    $image = uploadImage('image');

    if (!empty($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];

        // Ensure slug is unique (excluding current page)
        $chk = $pdo->prepare("SELECT id FROM pages WHERE slug=? AND id!=?");
        $chk->execute([$slug, $id]);
        if ($chk->fetch()) {
            $slug .= '-' . time();
        }

        if ($image) {
            $stmt = $pdo->prepare("UPDATE pages SET title=?, subtitle=?, slug=?, content=?, parent_menu=?, meta_description=?, meta_keywords=?, status=?, image=? WHERE id=?");
            $stmt->execute([$title, $subtitle, $slug, $content, $parent_menu, $meta_desc, $meta_key, $status, $image, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE pages SET title=?, subtitle=?, slug=?, content=?, parent_menu=?, meta_description=?, meta_keywords=?, status=? WHERE id=?");
            $stmt->execute([$title, $subtitle, $slug, $content, $parent_menu, $meta_desc, $meta_key, $status, $id]);
        }
        header("Location: pages.php?edit=" . $id);
    } else {
        // Insert
        // Ensure slug is unique
        $chk = $pdo->prepare("SELECT id FROM pages WHERE slug=?");
        $chk->execute([$slug]);
        if ($chk->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $pdo->prepare("INSERT INTO pages (title, subtitle, slug, content, parent_menu, meta_description, meta_keywords, status, image) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$title, $subtitle, $slug, $content, $parent_menu, $meta_desc, $meta_key, $status, $image]);
        header("Location: pages.php");
    }
    exit;
}

// ---- handle page delete ----
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM pages WHERE id=?")->execute([$_GET['delete']]);
    header('Location: pages.php');
    exit;
}

// ---- handle page sections actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_section') {
    $page_id     = (int)$_POST['page_id'];
    $sec_type    = $_POST['section_type'] ?? 'content';
    $sec_title   = trim($_POST['section_title'] ?? '');
    $sec_content = trim($_POST['section_content'] ?? '');
    $sec_order   = (int)($_POST['section_sort_order'] ?? 0);
    $sec_image   = uploadImage('section_image');

    if (!empty($_POST['section_id'])) {
        // Update Section
        $sec_id = (int)$_POST['section_id'];
        if ($sec_image) {
            $stmt = $pdo->prepare("UPDATE page_sections SET section_type=?, title=?, content=?, sort_order=?, image=? WHERE id=? AND page_id=?");
            $stmt->execute([$sec_type, $sec_title, $sec_content, $sec_order, $sec_image, $sec_id, $page_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE page_sections SET section_type=?, title=?, content=?, sort_order=? WHERE id=? AND page_id=?");
            $stmt->execute([$sec_type, $sec_title, $sec_content, $sec_order, $sec_id, $page_id]);
        }
    } else {
        // Insert Section
        $stmt = $pdo->prepare("INSERT INTO page_sections (page_id, section_type, title, content, image, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$page_id, $sec_type, $sec_title, $sec_content, $sec_image, $sec_order]);
    }
    header("Location: pages.php?edit=" . $page_id);
    exit;
}

// ---- handle page section delete ----
if (isset($_GET['delete_section']) && isset($_GET['edit'])) {
    $page_id = (int)$_GET['edit'];
    $sec_id = (int)$_GET['delete_section'];
    $pdo->prepare("DELETE FROM page_sections WHERE id=? AND page_id=?")->execute([$sec_id, $page_id]);
    header("Location: pages.php?edit=" . $page_id);
    exit;
}

// ---- load data for form ----
$editItem = null;
$sections = [];
$editSection = null;

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch();

    if ($editItem) {
        // Load sections for this page
        $stmt = $pdo->prepare("SELECT * FROM page_sections WHERE page_id=? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$editItem['id']]);
        $sections = $stmt->fetchAll();

        // Load specific section for editing
        if (isset($_GET['edit_section'])) {
            $stmt = $pdo->prepare("SELECT * FROM page_sections WHERE id=? AND page_id=?");
            $stmt->execute([(int)$_GET['edit_section'], $editItem['id']]);
            $editSection = $stmt->fetch();
        }
    }
}

$items = $pdo->query("SELECT * FROM pages ORDER BY title ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Custom Pages & Builder - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Custom Pages Manager</h1></div>

    <!-- MAIN PAGE FORM -->
    <div class="admin-card">
      <h3 style="margin-bottom:16px;"><?= $editItem ? 'Edit Page Settings' : 'Add New Custom Page' ?></h3>
      <form method="POST" enctype="multipart/form-data" id="page-form">
        <input type="hidden" name="action" value="save_page">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>"><?php endif; ?>
        
        <div class="form-group"><label>Page Title</label>
          <input type="text" name="title" required value="<?= e($editItem['title'] ?? '') ?>"></div>
        
        <div class="form-group"><label>Hero Subtitle / Tagline (appears in blue header banner)</label>
          <input type="text" name="subtitle" value="<?= e($editItem['subtitle'] ?? '') ?>"></div>
        
        <div class="form-group"><label>URL Slug (Optional - auto-generated if left blank)</label>
          <input type="text" name="slug" placeholder="e.g. crm-software-development" value="<?= e($editItem['slug'] ?? '') ?>"></div>
        
        <div class="form-group"><label>Parent Menu Placement (Dropdown Location)</label>
          <select name="parent_menu" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="root" <?= (($editItem['parent_menu'] ?? '')=='')?'selected':'' ?>>Top Level Menu (Root Navbar)</option>
            <option value="services" <?= (($editItem['parent_menu'] ?? '')=='services')?'selected':'' ?>>Under Services Dropdown</option>
            <option value="work" <?= (($editItem['parent_menu'] ?? '')=='work')?'selected':'' ?>>Under Work Dropdown</option>
            <option value="resources" <?= (($editItem['parent_menu'] ?? '')=='resources')?'selected':'' ?>>Under Resources Dropdown</option>
          </select></div>

        <div class="form-group">
          <label>Default Page Content (fallback if no blocks are added below)</label>
          <div class="editor-container" style="border: 1px solid var(--color-border); border-radius: var(--radius); overflow: hidden; margin-top: 6px;">
            <div class="editor-tabs" style="display: flex; background: var(--color-bg-alt); border-bottom: 1px solid var(--color-border); padding: 8px 10px; gap: 10px;">
              <button type="button" id="btn-visual" onclick="setMode('visual')" style="padding: 6px 14px; border: 1px solid var(--color-border); background: #fff; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem; outline: none; transition: 0.2s;">Visual</button>
              <button type="button" id="btn-html" onclick="setMode('html')" style="padding: 6px 14px; border: 1px solid transparent; background: transparent; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem; color: var(--color-muted); outline: none; transition: 0.2s;">HTML (Code)</button>
            </div>
            <div id="visual-editor" contenteditable="true" style="padding: 16px; min-height: 200px; outline: none; background: #fff; line-height: 1.6; font-size: 1.05rem;" oninput="syncToTextarea()"></div>
            <textarea id="html-editor" name="content" style="display: none; width: 100%; min-height: 200px; padding: 16px; border: none; outline: none; font-family: monospace; font-size: 0.95rem; line-height: 1.5; background: #fafafa; box-sizing: border-box;"><?= e($editItem['content'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="form-group"><label>Default Section Image (fallback)</label>
          <input type="file" name="image">
          <?php if (!empty($editItem['image'])): ?><p style="font-size:0.85rem;color:var(--color-muted);">Current: <?= e($editItem['image']) ?></p><?php endif; ?>
        </div>

        <div class="form-group"><label>Meta Description (SEO)</label>
          <textarea name="meta_description" style="min-height:70px;"><?= e($editItem['meta_description'] ?? '') ?></textarea></div>

        <div class="form-group"><label>Meta Keywords (SEO)</label>
          <input type="text" name="meta_keywords" placeholder="e.g. crm service, custom crm, web crm" value="<?= e($editItem['meta_keywords'] ?? '') ?>"></div>

        <div class="form-group"><label>Status</label>
          <select name="status" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="active" <?= (($editItem['status'] ?? '')=='active')?'selected':'' ?>>Active</option>
            <option value="inactive" <?= (($editItem['status'] ?? '')=='inactive')?'selected':'' ?>>Inactive</option>
          </select></div>
        
        <button type="submit" class="btn"><?= $editItem ? 'Save Settings' : 'Create Page' ?></button>
        <?php if ($editItem): ?><a href="pages.php" class="btn" style="background:#9ca3af;">Cancel / Close</a><?php endif; ?>
      </form>
    </div>

    <!-- PAGE SECTIONS BUILDER (Only visible when editing an existing page) -->
    <?php if ($editItem): ?>
      <div class="admin-card" style="border: 2px solid var(--color-primary); box-shadow: 0 8px 30px rgba(29, 78, 216, 0.08);">
        <h2 style="font-size:1.5rem; margin-bottom:10px; color:var(--color-primary);">🧱 Page Layout Blocks (Sections)</h2>
        <p style="color:var(--color-muted); margin-bottom:24px;">Build this page using block sections. Add grids (Services, Reviews, Jobs, Team), custom HTML code blocks, or Text/Image blocks.</p>

        <!-- Current Layout List -->
        <?php if ($sections): ?>
          <div style="margin-bottom:30px; border:1px solid var(--color-border); border-radius:var(--radius); overflow:hidden;">
            <table class="admin-table" style="margin:0;">
              <tr style="background:var(--color-bg-alt);"><th>Section Block</th><th>Heading Title</th><th>Sort Order</th><th>Actions</th></tr>
              <?php foreach ($sections as $s): ?>
                <tr>
                  <td>
                    <strong style="text-transform: capitalize; color:var(--color-primary);">
                      [<?= e($s['section_type']) ?> Block]
                    </strong>
                  </td>
                  <td><?= e($s['title'] ?: '(No heading)') ?></td>
                  <td><?= (int)$s['sort_order'] ?></td>
                  <td class="action-links">
                    <a href="?edit=<?= (int)$editItem['id'] ?>&edit_section=<?= (int)$s['id'] ?>#section-form">Edit Block</a>
                    <a href="?edit=<?= (int)$editItem['id'] ?>&delete_section=<?= (int)$s['id'] ?>" class="delete" onclick="return confirm('Delete this block from the page layout?')">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>
        <?php else: ?>
          <p style="text-align:center; padding:30px; background:var(--color-bg-alt); border-radius:var(--radius); color:var(--color-muted); margin-bottom:30px;">
            No blocks added yet. The default page content will be displayed. Add some blocks below to customize the layout!
          </p>
        <?php endif; ?>

        <!-- Add/Edit Section Block Form -->
        <div style="background:var(--color-bg-alt); padding:20px; border-radius:var(--radius); border:1px solid var(--color-border);" id="section-form">
          <h4 style="margin-bottom:16px;"><?= $editSection ? '✏ Edit Layout Block' : '➕ Add Layout Block' ?></h4>
          <form method="POST" enctype="multipart/form-data" id="section-post-form">
            <input type="hidden" name="action" value="save_section">
            <input type="hidden" name="page_id" value="<?= (int)$editItem['id'] ?>">
            <?php if ($editSection): ?><input type="hidden" name="section_id" value="<?= (int)$editSection['id'] ?>"><?php endif; ?>

            <div class="form-group">
              <label>Block Type</label>
              <select name="section_type" required style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
                <option value="content" <?= (($editSection['section_type'] ?? '')=='content')?'selected':'' ?>>Text & Image Section (Rich Text)</option>
                <option value="services" <?= (($editSection['section_type'] ?? '')=='services')?'selected':'' ?>>Services Grid Cards Block</option>
                <option value="jobs" <?= (($editSection['section_type'] ?? '')=='jobs')?'selected':'' ?>>Completed Jobs Grid Cards Block</option>
                <option value="reviews" <?= (($editSection['section_type'] ?? '')=='reviews')?'selected':'' ?>>Client Reviews Testimonial Block</option>
                <option value="team" <?= (($editSection['section_type'] ?? '')=='team')?'selected':'' ?>>Team Profiles Grid Block</option>
                <option value="contact" <?= (($editSection['section_type'] ?? '')=='contact')?'selected':'' ?>>Contact Input Form Block</option>
                <option value="html" <?= (($editSection['section_type'] ?? '')=='html')?'selected':'' ?>>Raw Custom HTML Code Block</option>
              </select>
            </div>

            <div class="form-group">
              <label>Block Heading Title (Optional)</label>
              <input type="text" name="section_title" value="<?= e($editSection['title'] ?? '') ?>" placeholder="e.g. Meet Our Experts, What We Offer">
            </div>

            <div class="form-group">
              <label>Block Body Content / Raw HTML Code (Optional)</label>
              <div class="editor-container" style="border: 1px solid var(--color-border); border-radius: var(--radius); overflow: hidden; margin-top: 6px;">
                <div class="editor-tabs" style="display: flex; background: var(--color-bg-alt); border-bottom: 1px solid var(--color-border); padding: 8px 10px; gap: 10px;">
                  <button type="button" id="btn-sec-visual" onclick="setSecMode('visual')" style="padding: 6px 14px; border: 1px solid var(--color-border); background: #fff; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem; outline: none; transition: 0.2s;">Visual</button>
                  <button type="button" id="btn-sec-html" onclick="setSecMode('html')" style="padding: 6px 14px; border: 1px solid transparent; background: transparent; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem; color: var(--color-muted); outline: none; transition: 0.2s;">HTML (Code)</button>
                </div>
                <div id="visual-sec-editor" contenteditable="true" style="padding: 16px; min-height: 200px; outline: none; background: #fff; line-height: 1.6; font-size: 1.05rem;" oninput="syncToSecTextarea()"></div>
                <textarea id="html-sec-editor" name="section_content" style="display: none; width: 100%; min-height: 200px; padding: 16px; border: none; outline: none; font-family: monospace; font-size: 0.95rem; line-height: 1.5; background: #fafafa; box-sizing: border-box;"><?= e($editSection['content'] ?? '') ?></textarea>
              </div>
            </div>

            <div class="form-group">
              <label>Section Image (Optional, only for Text & Image block)</label>
              <input type="file" name="section_image">
              <?php if (!empty($editSection['image'])): ?><p style="font-size:0.85rem;color:var(--color-muted);">Current: <?= e($editSection['image']) ?></p><?php endif; ?>
            </div>

            <div class="form-group">
              <label>Block Sort Order</label>
              <input type="number" name="section_sort_order" value="<?= e($editSection['sort_order'] ?? 0) ?>">
            </div>

            <button type="submit" class="btn"><?= $editSection ? 'Update Layout Block' : 'Add Layout Block' ?></button>
            <?php if ($editSection): ?>
              <a href="?edit=<?= (int)$editItem['id'] ?>" class="btn" style="background:#9ca3af;">Cancel Edit</a>
            <?php endif; ?>
          </form>
        </div>

      </div>
    <?php endif; ?>

    <!-- LIST OF ALL PAGES (only visible when not editing a block) -->
    <?php if (!$editSection): ?>
      <div class="admin-card">
        <h3 style="margin-bottom:16px;">All Dynamic Custom Pages</h3>
        <table class="admin-table">
          <tr><th>Title</th><th>Slug</th><th>Menu Parent</th><th>Status</th><th>Actions</th></tr>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= e($it['title']) ?></td>
              <td><code>page.php?slug=<?= e($it['slug']) ?></code></td>
              <td><span style="text-transform: capitalize;"><?= e($it['parent_menu'] ?? 'Top Level') ?></span></td>
              <td><span class="badge badge-<?= $it['status'] ?>"><?= e($it['status']) ?></span></td>
              <td class="action-links">
                <a href="<?= $it['slug']=='home' ? '../index.php' : '../page.php?slug=' . urlencode($it['slug']) ?>" target="_blank" style="color: #059669; font-weight: 600;">View Page</a>
                <a href="?edit=<?= (int)$it['id'] ?>">Edit & Build Layout</a>
                <a href="?delete=<?= (int)$it['id'] ?>" class="delete" onclick="return confirm('Delete this page and all its layout blocks?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>

  </div>
</div>

<script>
// ==================== EDITOR 1: PAGE DEFAULT CONTENT ====================
let currentMode = 'visual';
const visualEditor = document.getElementById('visual-editor');
const htmlEditor = document.getElementById('html-editor');
const btnVisual = document.getElementById('btn-visual');
const btnHtml = document.getElementById('btn-html');

visualEditor.innerHTML = htmlEditor.value;

function setMode(mode) {
    if (mode === currentMode) return;
    if (mode === 'html') {
        htmlEditor.value = visualEditor.innerHTML;
        visualEditor.style.display = 'none';
        htmlEditor.style.display = 'block';
        btnVisual.style.background = 'transparent';
        btnVisual.style.borderColor = 'transparent';
        btnVisual.style.color = 'var(--color-muted)';
        btnHtml.style.background = '#fff';
        btnHtml.style.borderColor = 'var(--color-border)';
        btnHtml.style.color = 'var(--color-text)';
    } else {
        visualEditor.innerHTML = htmlEditor.value;
        htmlEditor.style.display = 'none';
        visualEditor.style.display = 'block';
        btnHtml.style.background = 'transparent';
        btnHtml.style.borderColor = 'transparent';
        btnHtml.style.color = 'var(--color-muted)';
        btnVisual.style.background = '#fff';
        btnVisual.style.borderColor = 'var(--color-border)';
        btnVisual.style.color = 'var(--color-text)';
    }
    currentMode = mode;
}
function syncToTextarea() {
    if (currentMode === 'visual') htmlEditor.value = visualEditor.innerHTML;
}
document.getElementById('page-form').addEventListener('submit', function() {
    if (currentMode === 'visual') htmlEditor.value = visualEditor.innerHTML;
});

// ==================== EDITOR 2: SECTION BLOCK CONTENT ====================
<?php if ($editItem): ?>
let currentSecMode = 'visual';
const visualSecEditor = document.getElementById('visual-sec-editor');
const htmlSecEditor = document.getElementById('html-sec-editor');
const btnSecVisual = document.getElementById('btn-sec-visual');
const btnSecHtml = document.getElementById('btn-sec-html');

visualSecEditor.innerHTML = htmlSecEditor.value;

function setSecMode(mode) {
    if (mode === currentSecMode) return;
    if (mode === 'html') {
        htmlSecEditor.value = visualSecEditor.innerHTML;
        visualSecEditor.style.display = 'none';
        htmlSecEditor.style.display = 'block';
        btnSecVisual.style.background = 'transparent';
        btnSecVisual.style.borderColor = 'transparent';
        btnSecVisual.style.color = 'var(--color-muted)';
        btnSecHtml.style.background = '#fff';
        btnSecHtml.style.borderColor = 'var(--color-border)';
        btnSecHtml.style.color = 'var(--color-text)';
    } else {
        visualSecEditor.innerHTML = htmlSecEditor.value;
        htmlSecEditor.style.display = 'none';
        visualSecEditor.style.display = 'block';
        btnSecHtml.style.background = 'transparent';
        btnSecHtml.style.borderColor = 'transparent';
        btnSecHtml.style.color = 'var(--color-muted)';
        btnSecVisual.style.background = '#fff';
        btnSecVisual.style.borderColor = 'var(--color-border)';
        btnSecVisual.style.color = 'var(--color-text)';
    }
    currentSecMode = mode;
}
function syncToSecTextarea() {
    if (currentSecMode === 'visual') htmlSecEditor.value = visualSecEditor.innerHTML;
}
document.getElementById('section-post-form').addEventListener('submit', function() {
    if (currentSecMode === 'visual') htmlSecEditor.value = visualSecEditor.innerHTML;
});
<?php endif; ?>
</script>
</body>
</html>
