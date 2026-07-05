<?php
require_once __DIR__ . '/includes/db.php';

$review = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ? AND status='active'");
    $stmt->execute([(int)$_GET['id']]);
    $review = $stmt->fetch();
}

if (!$review) {
    $page_title = "Review Not Found";
    require_once __DIR__ . '/includes/header.php';
    echo '<section><div class="container" style="text-align:center; padding:100px 20px;">';
    echo '<h2>Review Not Found</h2>';
    echo '<p style="margin:20px 0; color:var(--color-muted);">The review you are looking for does not exist or has been removed.</p>';
    echo '<a href="page.php?slug=reviews" class="btn">Back to Reviews</a>';
    echo '</div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Injects SEO values before header is loaded
$page_seo_title = "Review by " . $review['client_name'];
$page_description = substr(strip_tags($review['message']), 0, 155);
$page_keywords = "client testimonial, client review, customer rating";
if ($review['image']) {
    $page_image = "uploads/" . $review['image'];
}

require_once __DIR__ . '/includes/header.php';
?>

<section style="padding:70px 0;">
  <div class="container" style="max-width: 700px; text-align:center;">
    
    <div style="text-align:left; margin-bottom:40px;">
      <a href="page.php?slug=reviews" style="color:var(--color-primary); font-weight:600; display:inline-flex; align-items:center; gap:6px;">
        &larr; Back to Reviews
      </a>
    </div>

    <div style="background:#fff; border:1px solid var(--color-border); border-radius:var(--radius); padding:50px 40px; box-shadow:var(--shadow);">
      
      <!-- Avatar -->
      <?php if ($review['image']): ?>
        <img src="<?= e(img_url($review['image'])) ?>" alt="<?= e($review['client_name']) ?>" style="width:100px; height:100px; border-radius:50%; object-fit:cover; margin:0 auto 20px; box-shadow:var(--shadow); display:block;">
      <?php else: ?>
        <div style="width:80px; height:80px; border-radius:50%; background:var(--color-bg-alt); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; color:var(--color-primary); font-size:1.8rem; font-weight:700;">
          <?= e($review['client_name'][0] ?? 'C') ?>
        </div>
      <?php endif; ?>

      <!-- Rating Stars -->
      <div style="color:var(--color-accent); font-size:1.5rem; margin-bottom:20px;">
        <?= str_repeat('★', (int)$review['rating']) . str_repeat('☆', 5 - (int)$review['rating']) ?>
      </div>

      <!-- Testimonial Message -->
      <blockquote style="font-size:1.3rem; line-height:1.7; color:var(--color-dark); font-style:italic; margin-bottom:30px;">
        "<?= nl2br(e($review['message'])) ?>"
      </blockquote>

      <!-- Client Name -->
      <cite style="font-size:1.1rem; font-weight:600; color:var(--color-primary); font-style:normal;">
        — <?= e($review['client_name']) ?>
      </cite>
    </div>

    <!-- Divider & CTA -->
    <hr style="border:0; border-top:1px solid var(--color-border); margin:50px 0;">
    
    <div style="background:var(--color-bg-alt); padding:30px; border-radius:var(--radius); border:1px solid var(--color-border); text-align:center;">
      <h3 style="margin-bottom:10px;">Ready to get started?</h3>
      <p style="color:var(--color-muted); margin-bottom:20px; font-size:0.95rem;">Join our growing list of satisfied clients and scale your business operations.</p>
      <a href="page.php?slug=contact" class="btn">Contact Us Today</a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
