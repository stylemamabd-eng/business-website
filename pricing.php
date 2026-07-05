<?php
$page_title = "Pricing Plans";
$page_description = "Affordable and flexible pricing tiers for our web development services, CRM integrations, and digital enterprise software.";
$page_keywords = "crm pricing, website cost, custom software rates, startup packages";
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <h1>Our Pricing Plans</h1>
    <p>Choose the perfect package to scale your business operations and customer relationships.</p>
  </div>
</section>

<section>
  <div class="container">
    <h2 class="section-title">Transparent Pricing</h2>
    <p class="section-sub">No hidden fees, cancel or change plans anytime</p>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:30px;">
      
      <!-- Tier 1 -->
      <div class="card pricing-card" style="display:flex; flex-direction:column; text-align:center; position:relative;">
        <h3 style="font-size:1.5rem; margin-bottom:10px;">Startup Plan</h3>
        <p style="color:var(--color-muted); margin-bottom:20px;">For small teams & local businesses</p>
        <div style="margin-bottom:24px;">
          <span style="font-size:2.5rem; font-weight:700; color:var(--color-dark);">$49</span>
          <span style="color:var(--color-muted);">/ month</span>
        </div>
        <ul style="text-align:left; margin-bottom:30px; line-height:2.2; flex-grow:1;">
          <li>✓ Core CRM Access (up to 5 users)</li>
          <li>✓ Basic Contact & Lead Tracking</li>
          <li>✓ Responsive Business Website</li>
          <li>✓ Email & Forms Integration</li>
          <li>✗ Advanced Reports & Analytics</li>
          <li>✗ Dedicated Support Manager</li>
        </ul>
        <a href="contact.php?plan=startup" class="btn" style="width:100%;">Get Started</a>
      </div>

      <!-- Tier 2 (Featured) -->
      <div class="card pricing-card featured" style="display:flex; flex-direction:column; text-align:center; position:relative; border-color:var(--color-primary); box-shadow:0 10px 25px rgba(29, 78, 216, 0.15);">
        <div style="position:absolute; top:-15px; left:50%; transform:translateX(-50%); background:var(--color-primary); color:#fff; padding:4px 15px; border-radius:20px; font-size:0.8rem; font-weight:600;">MOST POPULAR</div>
        <h3 style="font-size:1.5rem; margin-bottom:10px;">Professional</h3>
        <p style="color:var(--color-muted); margin-bottom:20px;">For growing companies & digital brands</p>
        <div style="margin-bottom:24px;">
          <span style="font-size:2.5rem; font-weight:700; color:var(--color-dark);">$99</span>
          <span style="color:var(--color-muted);">/ month</span>
        </div>
        <ul style="text-align:left; margin-bottom:30px; line-height:2.2; flex-grow:1;">
          <li>✓ Premium CRM Access (up to 20 users)</li>
          <li>✓ Full Lead & Inventory Pipelines</li>
          <li>✓ Custom Portal & Dashboard</li>
          <li>✓ API & Third-party Integrations</li>
          <li>✓ Detailed Reports & Automations</li>
          <li>✓ Priority Email & Chat Support</li>
        </ul>
        <a href="contact.php?plan=professional" class="btn" style="width:100%; background:var(--color-primary-dark);">Get Professional</a>
      </div>

      <!-- Tier 3 -->
      <div class="card pricing-card" style="display:flex; flex-direction:column; text-align:center; position:relative;">
        <h3 style="font-size:1.5rem; margin-bottom:10px;">Enterprise</h3>
        <p style="color:var(--color-muted); margin-bottom:20px;">For large scale operations & agency needs</p>
        <div style="margin-bottom:24px;">
          <span style="font-size:2.5rem; font-weight:700; color:var(--color-dark);">Custom</span>
        </div>
        <ul style="text-align:left; margin-bottom:30px; line-height:2.2; flex-grow:1;">
          <li>✓ Unlimited Users & Database Space</li>
          <li>✓ Dedicated Cloud Server Instance</li>
          <li>✓ Fully Customized Software Modules</li>
          <li>✓ On-site Team Training</li>
          <li>✓ Custom SLA & 99.9% Uptime Guarantee</li>
          <li>✓ 24/7 Phone & Technical Support</li>
        </ul>
        <a href="contact.php?plan=enterprise" class="btn" style="width:100%;">Contact Sales</a>
      </div>

    </div>
  </div>
</section>

<style>
  .pricing-card {
    transition: transform 0.3s, box-shadow 0.3s;
  }
  .pricing-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12) !important;
  }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
