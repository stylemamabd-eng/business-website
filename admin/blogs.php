<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
function slugify($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = trim($text, '-');
    return empty($text) ? 'post-' . time() : $text;
}

// ---- handle actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author  = trim($_POST['author'] ?? 'Admin');
    $status  = $_POST['status'] ?? 'active';
    
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
        
        // Ensure slug is unique (excluding current post)
        $chk = $pdo->prepare("SELECT id FROM blogs WHERE slug=? AND id!=?");
        $chk->execute([$slug, $id]);
        if ($chk->fetch()) {
            $slug .= '-' . time();
        }

        if ($image) {
            $stmt = $pdo->prepare("UPDATE blogs SET title=?, slug=?, content=?, author=?, status=?, image=? WHERE id=?");
            $stmt->execute([$title, $slug, $content, $author, $status, $image, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE blogs SET title=?, slug=?, content=?, author=?, status=? WHERE id=?");
            $stmt->execute([$title, $slug, $content, $author, $status, $id]);
        }
    } else {
        // Insert
        // Ensure slug is unique
        $chk = $pdo->prepare("SELECT id FROM blogs WHERE slug=?");
        $chk->execute([$slug]);
        if ($chk->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $pdo->prepare("INSERT INTO blogs (title, slug, content, author, status, image) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$title, $slug, $content, $author, $status, $image]);
    }
    header('Location: blogs.php');
    exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM blogs WHERE id=?")->execute([$_GET['delete']]);
    header('Location: blogs.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch();
}

$items = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog Posts - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Blog Posts</h1></div>

    <div class="admin-card">
      <h3 style="margin-bottom:16px;"><?= $editItem ? 'Edit Blog Post' : 'Add New Blog Post' ?></h3>
      <form method="POST" enctype="multipart/form-data">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>"><?php endif; ?>
        
        <div class="form-group"><label>Title</label>
          <input type="text" name="title" required value="<?= e($editItem['title'] ?? '') ?>"></div>
        
        <div class="form-group"><label>URL Slug (Optional - auto-generated if left blank)</label>
          <input type="text" name="slug" placeholder="e.g. how-to-scale-crm" value="<?= e($editItem['slug'] ?? '') ?>"></div>
        
        <div class="form-group">
          <label>Content</label>
          <div class="editor-container" style="border: 1px solid var(--color-border); border-radius: var(--radius); overflow: hidden; margin-top: 6px;">
            <div class="editor-tabs" style="display: flex; background: var(--color-bg-alt); border-bottom: 1px solid var(--color-border); padding: 8px 10px; gap: 10px;">
              <button type="button" id="btn-visual" onclick="setMode('visual')" style="padding: 6px 14px; border: 1px solid var(--color-border); background: #fff; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem; outline: none; transition: 0.2s;">Visual</button>
              <button type="button" id="btn-html" onclick="setMode('html')" style="padding: 6px 14px; border: 1px solid transparent; background: transparent; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem; color: var(--color-muted); outline: none; transition: 0.2s;">HTML (Code)</button>
            </div>
            <div id="visual-editor" contenteditable="true" style="padding: 16px; min-height: 250px; outline: none; background: #fff; line-height: 1.6; font-size: 1.05rem;" oninput="syncToTextarea()"></div>
            <textarea id="html-editor" name="content" style="display: none; width: 100%; min-height: 250px; padding: 16px; border: none; outline: none; font-family: monospace; font-size: 0.95rem; line-height: 1.5; background: #fafafa; box-sizing: border-box;"><?= e($editItem['content'] ?? '') ?></textarea>
          </div>
        </div>
        
        <div class="form-group"><label>Author</label>
          <input type="text" name="author" value="<?= e($editItem['author'] ?? 'Admin') ?>"></div>

        <div class="form-group"><label>Featured Image</label>
          <input type="file" name="image">
          <?php if (!empty($editItem['image'])): ?><p style="font-size:0.85rem;color:var(--color-muted);">Current: <?= e($editItem['image']) ?></p><?php endif; ?>
        </div>

        <div class="form-group"><label>Status</label>
          <select name="status" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--color-border);">
            <option value="active" <?= (($editItem['status'] ?? '')=='active')?'selected':'' ?>>Active</option>
            <option value="inactive" <?= (($editItem['status'] ?? '')=='inactive')?'selected':'' ?>>Inactive</option>
          </select></div>
        
        <button type="submit" class="btn"><?= $editItem ? 'Update' : 'Publish' ?> Post</button>
        <?php if ($editItem): ?><a href="blogs.php" class="btn" style="background:#9ca3af;">Cancel</a><?php endif; ?>
      </form>
    </div>

    <div class="admin-card">
      <table class="admin-table">
        <tr><th>Title</th><th>Author</th><th>Date</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= e($it['title']) ?></td>
            <td><?= e($it['author'] ?? 'Admin') ?></td>
            <td><?= date('Y-m-d', strtotime($it['created_at'])) ?></td>
            <td><span class="badge badge-<?= $it['status'] ?>"><?= e($it['status']) ?></span></td>
            <td class="action-links">
              <a href="?edit=<?= (int)$it['id'] ?>">Edit</a>
              <a href="?delete=<?= (int)$it['id'] ?>" class="delete" onclick="return confirm('Delete this blog post?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>

<script>
let currentMode = 'visual';
const visualEditor = document.getElementById('visual-editor');
const htmlEditor = document.getElementById('html-editor');
const btnVisual = document.getElementById('btn-visual');
const btnHtml = document.getElementById('btn-html');

// Initial load: Set the visual editor's HTML to whatever is in the textarea (PHP generated)
visualEditor.innerHTML = htmlEditor.value;

function setMode(mode) {
    if (mode === currentMode) return;
    
    if (mode === 'html') {
        // Sync Visual -> HTML
        htmlEditor.value = visualEditor.innerHTML;
        
        // Toggle view
        visualEditor.style.display = 'none';
        htmlEditor.style.display = 'block';
        
        // Style tabs
        btnVisual.style.background = 'transparent';
        btnVisual.style.borderColor = 'transparent';
        btnVisual.style.color = 'var(--color-muted)';
        
        btnHtml.style.background = '#fff';
        btnHtml.style.borderColor = 'var(--color-border)';
        btnHtml.style.color = 'var(--color-text)';
    } else {
        // Sync HTML -> Visual
        visualEditor.innerHTML = htmlEditor.value;
        
        // Toggle view
        htmlEditor.style.display = 'none';
        visualEditor.style.display = 'block';
        
        // Style tabs
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
    if (currentMode === 'visual') {
        htmlEditor.value = visualEditor.innerHTML;
    }
}

// Intercept form submit to ensure final sync
document.querySelector('form').addEventListener('submit', function() {
    if (currentMode === 'visual') {
        htmlEditor.value = visualEditor.innerHTML;
    }
});
</script>
</body>
</html>
