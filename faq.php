<?php
$page_title = "Frequently Asked Questions";
$page_description = "Find answers to frequently asked questions about our CRM solutions, web development services, pricing, and technical support.";
$page_keywords = "crm faq, web design support, software questions, apex digital faq";
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <h1>Frequently Asked Questions</h1>
    <p>Got questions? We've got answers. Explore common queries about our services and CRM platform.</p>
  </div>
</section>

<section>
  <div class="container" style="max-width: 800px;">
    <h2 class="section-title">Common Questions</h2>
    <p class="section-sub">Quick help for our clients and partners</p>

    <div class="faq-container">
      <details class="faq-card" style="background:#fff; border:1px solid var(--color-border); border-radius:var(--radius); padding:16px 20px; margin-bottom:15px; cursor:pointer; transition: 0.2s;">
        <summary style="font-weight:600; font-size:1.1rem; list-style:none; outline:none; display:flex; justify-content:space-between; align-items:center;">
          <span>What is a CRM and how can it benefit my business?</span>
          <span style="font-size:0.8rem; color:var(--color-primary);">▼</span>
        </summary>
        <p style="margin-top:12px; color:var(--color-text); line-height:1.6;">
          A Customer Relationship Management (CRM) system helps businesses manage interactions with customers, streamline sales pipelines, track customer details, and automate tasks. It improves sales efficiency, organizes database storage, and enables better customer service.
        </p>
      </details>

      <details class="faq-card" style="background:#fff; border:1px solid var(--color-border); border-radius:var(--radius); padding:16px 20px; margin-bottom:15px; cursor:pointer; transition: 0.2s;">
        <summary style="font-weight:600; font-size:1.1rem; list-style:none; outline:none; display:flex; justify-content:space-between; align-items:center;">
          <span>Do you provide custom CRM integrations?</span>
          <span style="font-size:0.8rem; color:var(--color-primary);">▼</span>
        </summary>
        <p style="margin-top:12px; color:var(--color-text); line-height:1.6;">
          Yes! We build fully customized CRM systems tailored to your specific workflow, inventory tracking, lead generation, and team management requirements. We also integrate existing CRMs with your website or database.
        </p>
      </details>

      <details class="faq-card" style="background:#fff; border:1px solid var(--color-border); border-radius:var(--radius); padding:16px 20px; margin-bottom:15px; cursor:pointer; transition: 0.2s;">
        <summary style="font-weight:600; font-size:1.1rem; list-style:none; outline:none; display:flex; justify-content:space-between; align-items:center;">
          <span>How long does it take to develop a custom business website?</span>
          <span style="font-size:0.8rem; color:var(--color-primary);">▼</span>
        </summary>
        <p style="margin-top:12px; color:var(--color-text); line-height:1.6;">
          A standard business website typically takes 2-3 weeks to build. For complex web applications, enterprise portals, or custom CRM solutions, the development timeline ranges between 4-8 weeks depending on the requirements.
        </p>
      </details>

      <details class="faq-card" style="background:#fff; border:1px solid var(--color-border); border-radius:var(--radius); padding:16px 20px; margin-bottom:15px; cursor:pointer; transition: 0.2s;">
        <summary style="font-weight:600; font-size:1.1rem; list-style:none; outline:none; display:flex; justify-content:space-between; align-items:center;">
          <span>Is technical support and maintenance included?</span>
          <span style="font-size:0.8rem; color:var(--color-primary);">▼</span>
        </summary>
        <p style="margin-top:12px; color:var(--color-text); line-height:1.6;">
          All our plans include 30 days of free post-launch support. Afterward, you can opt for our monthly maintenance package which covers database backups, software updates, security audits, and dedicated bug fixing.
        </p>
      </details>

      <details class="faq-card" style="background:#fff; border:1px solid var(--color-border); border-radius:var(--radius); padding:16px 20px; margin-bottom:15px; cursor:pointer; transition: 0.2s;">
        <summary style="font-weight:600; font-size:1.1rem; list-style:none; outline:none; display:flex; justify-content:space-between; align-items:center;">
          <span>Can I migrate my existing customer data to your CRM?</span>
          <span style="font-size:0.8rem; color:var(--color-primary);">▼</span>
        </summary>
        <p style="margin-top:12px; color:var(--color-text); line-height:1.6;">
          Absolutely. We support migrating contact details, sales history, and files from spreadsheets (CSV/Excel) or other CRM tools safely into our database with zero downtime.
        </p>
      </details>
    </div>
  </div>
</section>

<style>
  details[open] summary span:last-child {
    transform: rotate(180deg);
  }
  .faq-card:hover {
    border-color: var(--color-primary) !important;
    box-shadow: var(--shadow);
  }
  details summary::-webkit-details-marker {
    display: none;
  }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
