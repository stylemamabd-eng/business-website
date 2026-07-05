<?php $cur = basename($_SERVER['PHP_SELF']); ?>
<div class="admin-sidebar">
  <h2>⚙ Admin Panel</h2>
  <a href="../index.php" target="_blank" style="background:#1e293b; color:#10b981; font-weight:600; border-bottom:1px solid #334155;">💻 View Website &rarr;</a>
  <a href="index.php" class="<?= $cur=='index.php'?'active':'' ?>">Dashboard</a>
  <a href="analytics.php" class="<?= $cur=='analytics.php'?'active':'' ?>">Analytics Dashboard</a>
  <a href="services.php" class="<?= $cur=='services.php'?'active':'' ?>">Services</a>
  <a href="jobs.php" class="<?= $cur=='jobs.php'?'active':'' ?>">Completed Jobs</a>
  <a href="reviews.php" class="<?= $cur=='reviews.php'?'active':'' ?>">Reviews</a>
  <a href="teams.php" class="<?= $cur=='teams.php'?'active':'' ?>">Team</a>
  <a href="sections.php" class="<?= $cur=='sections.php'?'active':'' ?>">Custom Sections</a>
  <a href="blogs.php" class="<?= $cur=='blogs.php'?'active':'' ?>">Blog Posts</a>
  <a href="pages.php" class="<?= $cur=='pages.php'?'active':'' ?>">Custom Pages</a>
  <a href="menus.php" class="<?= $cur=='menus.php'?'active':'' ?>">Menu Manager</a>
  <a href="messages.php" class="<?= $cur=='messages.php'?'active':'' ?>">Messages</a>
  <a href="settings.php" class="<?= $cur=='settings.php'?'active':'' ?>">Site Settings</a>
  <a href="users.php" class="<?= $cur=='users.php'?'active':'' ?>">Manage Admins</a>
  <a href="logout.php">Logout</a>
</div>
