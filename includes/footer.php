</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-col">
      <h3><?= e($settings['site_name'] ?? 'My Business') ?></h3>
      <p><?= e($settings['site_tagline'] ?? '') ?></p>
    </div>
    <div class="footer-col">
      <h4>Contact</h4>
      <p>📞 <?= e($settings['phone'] ?? '') ?></p>
      <p>✉️ <?= e($settings['email'] ?? '') ?></p>
      <p>📍 <?= e($settings['address'] ?? '') ?></p>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <a href="services.php">Services</a>
      <a href="completed-jobs.php">Completed Jobs</a>
      <a href="reviews.php">Reviews</a>
      <a href="contact.php">Contact</a>
    </div>
  </div>
  <div class="footer-bottom">
    <p><?= e($settings['footer_text'] ?? '') ?></p>
  </div>
</footer>

<script src="js/script.js"></script>
</body>
</html>
