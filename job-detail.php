<?php
require_once __DIR__ . '/includes/db.php';

$job = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM completed_jobs WHERE id = ? AND status='active'");
    $stmt->execute([(int)$_GET['id']]);
    $job = $stmt->fetch();
}

if (!$job) {
    $page_title = "Job Not Found";
    require_once __DIR__ . '/includes/header.php';
    echo '<section><div class="container" style="text-align:center; padding:100px 20px;">';
    echo '<h2>Project Not Found</h2>';
    echo '<p style="margin:20px 0; color:var(--color-muted);">The project you are looking for does not exist or has been removed.</p>';
    echo '<a href="page.php?slug=completed-jobs" class="btn">Back to Completed Jobs</a>';
    echo '</div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Injects SEO values before header is loaded
$page_seo_title = $job['title'] . " - Case Study";
$page_description = substr(strip_tags($job['description']), 0, 155);
$page_keywords = "completed job, portfolio case study, apex digital work";
if ($job['image']) {
    $page_image = "uploads/" . $job['image'];
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="blog-detail-sec" style="padding:50px 0;">
  <div class="container" style="max-width: 800px;">
    
    <div style="margin-bottom:20px;">
      <a href="page.php?slug=completed-jobs" style="color:var(--color-primary); font-weight:600; display:inline-flex; align-items:center; gap:6px; margin-bottom:20px;">
        &larr; Back to Portfolio (Completed Jobs)
      </a>
      
      <?php if (!empty($job['job_date'])): ?>
        <div style="font-size:0.9rem; color:var(--color-muted); margin-bottom:10px;">
          Completed on <?= date('F d, Y', strtotime($job['job_date'])) ?>
        </div>
      <?php endif; ?>
      
      <h1 style="font-size:2.5rem; line-height:1.2; color:var(--color-dark); margin-bottom:20px; font-weight:700;">
        <?= e($job['title']) ?>
      </h1>
    </div>

    <!-- Featured Image -->
    <?php if ($job['image']): ?>
      <div style="margin-bottom:40px; border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow);">
        <img src="<?= e(img_url($job['image'])) ?>" alt="<?= e($job['title']) ?>" style="width:100%; max-height:450px; object-fit:cover; display:block;">
      </div>
    <?php endif; ?>

    <!-- Content -->
    <div style="font-size:1.1rem; line-height:1.8; color:var(--color-text);">
      <h3>Project Details:</h3>
      <p style="margin-top:10px;"><?= nl2br(e($job['description'])) ?></p>
    </div>

    <!-- Divider & CTA -->
    <hr style="border:0; border-top:1px solid var(--color-border); margin:50px 0;">
    
    <div style="background:var(--color-bg-alt); padding:30px; border-radius:var(--radius); border:1px solid var(--color-border); text-align:center;">
      <h3 style="margin-bottom:10px;">Looking for similar results?</h3>
      <p style="color:var(--color-muted); margin-bottom:20px; font-size:0.95rem;">Contact us today to discuss your CRM project, website development, or IT infrastructure.</p>
      <a href="page.php?slug=contact" class="btn">Get in Touch</a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
