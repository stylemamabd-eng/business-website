<?php
require_once __DIR__ . '/includes/db.php';

$post = null;
if (isset($_GET['slug'])) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ? AND status='active'");
    $stmt->execute([$_GET['slug']]);
    $post = $stmt->fetch();
} elseif (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ? AND status='active'");
    $stmt->execute([$_GET['id']]);
    $post = $stmt->fetch();
}

if (!$post) {
    // If post not found, redirect to blog or show error
    $page_title = "Post Not Found";
    require_once __DIR__ . '/includes/header.php';
    echo '<section><div class="container" style="text-align:center; padding:100px 20px;">';
    echo '<h2>Article Not Found</h2>';
    echo '<p style="margin:20px 0; color:var(--color-muted);">The article you are looking for does not exist or has been removed.</p>';
    echo '<a href="blog.php" class="btn">Back to Blog</a>';
    echo '</div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Define SEO metadata BEFORE including header.php!
$page_seo_title = $post['title'] . " - Blog";
$page_description = substr(strip_tags($post['content']), 0, 155);
$page_keywords = implode(", ", array_slice(explode(" ", strtolower(preg_replace('/[^\w\s]/', '', $post['title']))), 0, 8));
if ($post['image']) {
    $page_image = "uploads/" . $post['image'];
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="blog-detail-sec" style="padding:50px 0;">
  <div class="container" style="max-width: 800px;">
    
    <!-- Meta Info -->
    <div style="margin-bottom:20px;">
      <a href="blog.php" style="color:var(--color-primary); font-weight:600; display:inline-flex; align-items:center; gap:6px; margin-bottom:20px;">
        &larr; Back to Blog
      </a>
      <div style="font-size:0.9rem; color:var(--color-muted); margin-bottom:10px;">
        Published on <?= date('F d, Y', strtotime($post['created_at'])) ?> by <?= e($post['author'] ?? 'Admin') ?>
      </div>
      <h1 style="font-size:2.5rem; line-height:1.2; color:var(--color-dark); margin-bottom:20px; font-weight:700;">
        <?= e($post['title']) ?>
      </h1>
    </div>

    <!-- Featured Image -->
    <?php if ($post['image']): ?>
      <div style="margin-bottom:40px; border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow);">
        <img src="<?= e(img_url($post['image'])) ?>" alt="<?= e($post['title']) ?>" style="width:100%; max-height:450px; object-fit:cover; display:block;">
      </div>
    <?php endif; ?>

    <!-- Article Content -->
    <div class="blog-article-content" style="font-size:1.1rem; line-height:1.8; color:var(--color-text);">
      <?= nl2br($post['content']) // Using nl2br for simple formatted text, or HTML if WYSIWYG was used ?>
    </div>

    <!-- Divider & CTA -->
    <hr style="border:0; border-top:1px solid var(--color-border); margin:50px 0;">
    
    <div style="background:var(--color-bg-alt); padding:30px; border-radius:var(--radius); border:1px solid var(--color-border); text-align:center;">
      <h3 style="margin-bottom:10px;">Need a custom solution for your business?</h3>
      <p style="color:var(--color-muted); margin-bottom:20px; font-size:0.95rem;">Contact us today to discuss your CRM project, website development, or IT infrastructure.</p>
      <a href="contact.php" class="btn">Get in Touch</a>
    </div>

  </div>
</section>

<style>
  .blog-article-content p {
    margin-bottom: 24px;
  }
  .blog-article-content h2, .blog-article-content h3 {
    color: var(--color-dark);
    margin: 40px 0 16px;
    font-weight: 600;
  }
  .blog-article-content h2 { font-size: 1.8rem; }
  .blog-article-content h3 { font-size: 1.4rem; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
