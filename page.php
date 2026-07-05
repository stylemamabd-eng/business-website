<?php
require_once __DIR__ . '/includes/db.php';

$page = null;
if (isset($_GET['slug'])) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status='active'");
    $stmt->execute([$_GET['slug']]);
    $page = $stmt->fetch();
}

if (!$page) {
    // Page not found
    $page_title = "Page Not Found";
    require_once __DIR__ . '/includes/header.php';
    echo '<section><div class="container" style="text-align:center; padding:100px 20px;">';
    echo '<h2>Page Not Found</h2>';
    echo '<p style="margin:20px 0; color:var(--color-muted);">The page you are looking for does not exist or has been removed.</p>';
    echo '<a href="index.php" class="btn">Go Home</a>';
    echo '</div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Injects SEO values before header is loaded
$page_seo_title = $page['title'];
$page_description = $page['meta_description'] ?? $page['subtitle'] ?? '';
$page_keywords = $page['meta_keywords'] ?? 'custom page, service';
if ($page['image']) {
    $page_image = "uploads/" . $page['image'];
}

// Handle Contact Form Submission
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'contact_form') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = "Name, Email, Message required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $message]);
        $success = true;
    }
}

// Fetch sections for this page
$sections = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM page_sections WHERE page_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$page['id']]);
    $sections = $stmt->fetchAll();
} catch (PDOException $e) {
    // ignore
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section (Exactly like about.php style in user screenshot) -->
<section class="hero">
  <div class="container">
    <h1><?= e($page['title']) ?></h1>
    <p><?= e($page['subtitle'] ?? '') ?></p>
  </div>
</section>

<?php if ($sections): ?>
  <?php foreach ($sections as $sec): ?>
    
    <!-- Render content section -->
    <?php if ($sec['section_type'] === 'content'): ?>
      <section>
        <div class="container">
          <?php if (!empty($sec['title'])): ?>
            <h2 class="section-title"><?= e($sec['title']) ?></h2>
          <?php endif; ?>
          <div style="display:flex; gap:40px; flex-wrap:wrap; align-items:center;">
            <?php if (!empty($sec['image'])): ?>
              <div style="flex:1; min-width:290px;">
                <img src="<?= e(img_url($sec['image'])) ?>" alt="<?= e($sec['title'] ?? '') ?>" style="width:100%; border-radius:10px; box-shadow:var(--shadow);">
              </div>
            <?php endif; ?>
            <div style="flex:<?= !empty($sec['image']) ? '2' : '1' ?>; min-width:290px; font-size:1.05rem; line-height:1.8; width:100%;">
              <?= $sec['content'] ?>
            </div>
          </div>
        </div>
      </section>

    <!-- Render services section -->
    <?php elseif ($sec['section_type'] === 'services'): ?>
      <?php $services = $pdo->query("SELECT * FROM services WHERE status='active' ORDER BY sort_order")->fetchAll(); ?>
      <section class="bg-alt">
        <div class="container">
          <h2 class="section-title"><?= e($sec['title'] ?: 'Our Services') ?></h2>
          <?php if (!empty($sec['content'])): ?><p class="section-sub"><?= e($sec['content']) ?></p><?php endif; ?>
          <div class="grid">
            <?php foreach ($services as $s): ?>
              <div class="card">
                <?php if ($s['image']): ?><img class="card-img" src="<?= e(img_url($s['image'])) ?>" alt="<?= e($s['title']) ?>"><?php endif; ?>
                <h3><?= e($s['title']) ?></h3>
                <p><?= nl2br(e($s['description'])) ?></p>
              </div>
            <?php endforeach; ?>
            <?php if (!$services): ?><p>No services added yet.</p><?php endif; ?>
          </div>
        </div>
      </section>

    <!-- Render jobs section -->
    <?php elseif ($sec['section_type'] === 'jobs'): ?>
      <?php $jobs = $pdo->query("SELECT * FROM completed_jobs WHERE status='active' ORDER BY sort_order DESC")->fetchAll(); ?>
      <section>
        <div class="container">
          <h2 class="section-title"><?= e($sec['title'] ?: 'Recently Completed Jobs') ?></h2>
          <?php if (!empty($sec['content'])): ?><p class="section-sub"><?= e($sec['content']) ?></p><?php endif; ?>
          <div class="grid">
            <?php foreach ($jobs as $j): ?>
              <div class="card">
                <?php if ($j['image']): ?><img class="card-img" src="<?= e(img_url($j['image'])) ?>" alt="<?= e($j['title']) ?>"><?php endif; ?>
                <h3><?= e($j['title']) ?></h3>
                <p><?= nl2br(e(substr(strip_tags($j['description']), 0, 100))) ?>...</p>
                <a href="job-detail.php?id=<?= (int)$j['id'] ?>" style="color: var(--color-primary); font-weight: 600; display: inline-block; margin-top: 15px;">View Case Study &rarr;</a>
              </div>
            <?php endforeach; ?>
            <?php if (!$jobs): ?><p>No completed jobs added yet.</p><?php endif; ?>
          </div>
        </div>
      </section>

    <!-- Render reviews section -->
    <?php elseif ($sec['section_type'] === 'reviews'): ?>
      <?php $reviews = $pdo->query("SELECT * FROM reviews WHERE status='active' ORDER BY sort_order")->fetchAll(); ?>
      <section class="bg-alt">
        <div class="container">
          <h2 class="section-title"><?= e($sec['title'] ?: 'Client Reviews') ?></h2>
          <?php if (!empty($sec['content'])): ?><p class="section-sub"><?= e($sec['content']) ?></p><?php endif; ?>
          <div class="grid">
            <?php foreach ($reviews as $r): ?>
              <div class="card review-card">
                <?php if ($r['image']): ?><img class="review-avatar" src="<?= e(img_url($r['image'])) ?>" alt="<?= e($r['client_name']) ?>"><?php endif; ?>
                <div class="review-stars"><?= str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5 - (int)$r['rating']) ?></div>
                <p>"<?= nl2br(e(substr(strip_tags($r['message']), 0, 120))) ?>..."</p>
                <a href="review-detail.php?id=<?= (int)$r['id'] ?>" style="color: var(--color-primary); font-weight: 600; display: inline-block; margin-top: 15px; margin-bottom: 5px;">Read Full Review &rarr;</a>
                <h4 style="margin-top:12px;"><?= e($r['client_name']) ?></h4>
              </div>
            <?php endforeach; ?>
            <?php if (!$reviews): ?><p>No reviews added yet.</p><?php endif; ?>
          </div>
        </div>
      </section>

    <!-- Render team section -->
    <?php elseif ($sec['section_type'] === 'team'): ?>
      <?php $team = $pdo->query("SELECT * FROM teams WHERE status='active' ORDER BY sort_order")->fetchAll(); ?>
      <section>
        <div class="container">
          <h2 class="section-title"><?= e($sec['title'] ?: 'Our Team') ?></h2>
          <?php if (!empty($sec['content'])): ?><p class="section-sub"><?= e($sec['content']) ?></p><?php endif; ?>
          <div class="grid">
            <?php foreach ($team as $it): ?>
              <div class="card team-card">
                <?php if ($it['image']): ?><img class="team-avatar" src="<?= e(img_url($it['image'])) ?>" alt="<?= e($it['name']) ?>"><?php endif; ?>
                <div class="team-role"><?= e($it['designation']) ?></div>
                <h4><?= e($it['name']) ?></h4>
                <p style="margin-top:10px; font-size:0.92rem; color:var(--color-muted);"><?= e($it['bio']) ?></p>
              </div>
            <?php endforeach; ?>
            <?php if (!$team): ?><p>No team members added yet.</p><?php endif; ?>
          </div>
        </div>
      </section>

    <!-- Render contact form section -->
    <?php elseif ($sec['section_type'] === 'contact'): ?>
      <section>
        <div class="container" style="max-width:700px;">
          <h2 class="section-title"><?= e($sec['title'] ?: 'Send a Message') ?></h2>
          <?php if (!empty($sec['content'])): ?><p class="section-sub"><?= e($sec['content']) ?></p><?php endif; ?>
          
          <?php if ($success): ?>
            <div class="alert alert-success">Thanks! Your message has been sent.</div>
          <?php elseif ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
          <?php endif; ?>
          
          <form method="POST">
            <input type="hidden" name="action" value="contact_form">
            <div class="form-group">
              <label>Name</label>
              <input type="text" name="name" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" required>
            </div>
            <div class="form-group">
              <label>Phone</label>
              <input type="text" name="phone">
            </div>
            <div class="form-group">
              <label>Message</label>
              <textarea name="message" required style="min-height:120px;"></textarea>
            </div>
            <button type="submit" class="btn" style="width:100%;">Send Message</button>
          </form>
        </div>
      </section>

    <!-- Render raw HTML section -->
    <?php elseif ($sec['section_type'] === 'html'): ?>
      <section>
        <div class="container">
          <?php if (!empty($sec['title'])): ?>
            <h2 class="section-title"><?= e($sec['title']) ?></h2>
          <?php endif; ?>
          <?= $sec['content'] ?>
        </div>
      </section>

    <?php endif; ?>
  <?php endforeach; ?>
<?php else: ?>
  <!-- Fallback: Render default content/image fields -->
  <section>
    <div class="container">
      <div style="display:flex; gap:40px; flex-wrap:wrap; align-items:center; min-height: 200px;">
        <?php if (!empty($page['image'])): ?>
          <div style="flex:1; min-width:290px;">
            <img src="<?= e(img_url($page['image'])) ?>" alt="<?= e($page['title']) ?>" style="width:100%; border-radius:10px; box-shadow:var(--shadow);">
          </div>
        <?php endif; ?>
        <div style="flex:<?= !empty($page['image']) ? '2' : '1' ?>; min-width:290px; font-size:1.05rem; line-height:1.8; width:100%;">
          <?= $page['content'] ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
