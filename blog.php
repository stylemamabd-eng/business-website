<?php
$page_title = "Blog & Articles";
$page_description = "Read our latest news, technology updates, business articles, and tutorials on CRM and software optimization.";
$page_keywords = "crm blog, software development blog, apex digital articles, business tech news";
require_once __DIR__ . '/includes/header.php';

// Fetch all active blog posts
try {
    $blogs = $pdo->query("SELECT * FROM blogs WHERE status='active' ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) {
    $blogs = []; // Handle if table doesn't exist yet
}
?>

<section class="hero">
  <div class="container">
    <h1>Our Blog & Articles</h1>
    <p>Insights, guides, and tips on CRM optimization, web development, and digital marketing.</p>
  </div>
</section>

<section>
  <div class="container">
    <?php if ($blogs): ?>
      <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:35px;">
        <?php foreach ($blogs as $b): ?>
          <article class="card blog-card" style="display:flex; flex-direction:column; padding:0; overflow:hidden;">
            <?php if ($b['image']): ?>
              <img src="<?= e(img_url($b['image'])) ?>" alt="<?= e($b['title']) ?>" style="width:100%; height:200px; object-fit:cover;">
            <?php else: ?>
              <div style="width:100%; height:200px; background:linear-gradient(135deg, #e0f2fe, #bae6fd); display:flex; align-items:center; justify-content:center; color:var(--color-primary); font-weight:600; font-size:1.5rem;">
                <?= e($b['title'][0] ?? 'B') ?>
              </div>
            <?php endif; ?>
            
            <div style="padding:24px; display:flex; flex-direction:column; flex-grow:1;">
              <div style="font-size:0.85rem; color:var(--color-muted); margin-bottom:10px;">
                <span>By <?= e($b['author'] ?? 'Admin') ?></span> | 
                <span><?= date('M d, Y', strtotime($b['created_at'])) ?></span>
              </div>
              <h3 style="font-size:1.3rem; margin-bottom:12px; line-height:1.4;">
                <a href="blog-detail.php?slug=<?= urlencode($b['slug']) ?>" style="color:var(--color-dark); transition:color 0.2s;"><?= e($b['title']) ?></a>
              </h3>
              <p style="color:var(--color-text); margin-bottom:20px; font-size:0.95rem; line-height:1.6; flex-grow:1;">
                <?= e(substr(strip_tags($b['content']), 0, 120)) ?>...
              </p>
              <a href="blog-detail.php?slug=<?= urlencode($b['slug']) ?>" class="btn-read" style="font-weight:600; color:var(--color-primary); display:inline-flex; align-items:center; gap:6px; transition:color 0.2s;">
                Read Article &rarr;
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div style="text-align:center; padding:40px 0;">
        <p style="font-size:1.2rem; color:var(--color-muted);">No blog posts found. Check back later or add some from the Admin Dashboard!</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<style>
  .blog-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid var(--color-border);
  }
  .blog-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.1);
  }
  .blog-card:hover h3 a {
    color: var(--color-primary) !important;
  }
  .btn-read:hover {
    color: var(--color-primary-dark) !important;
  }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
