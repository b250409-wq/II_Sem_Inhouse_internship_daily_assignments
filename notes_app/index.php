<?php $pageTitle='Home'; require __DIR__.'/views/partials/head.php'; require __DIR__.'/views/partials/navbar.php'; ?>
<?php require __DIR__.'/views/partials/flash.php'; ?>

<section id="home" class="hero">
  <div class="blob b1"></div><div class="blob b2"></div>
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos>
        <span class="badge rounded-pill glass px-3 py-2 mb-3"><i class="fa-solid fa-sparkles text-warning"></i> Introducing NoteFlow 2.0</span>
        <h1 class="fw-bold">Organize Your Ideas with <span class="text-grad">Smart Notes</span></h1>
        <p class="lead text-muted mt-3">A beautifully designed workspace to capture thoughts, tag them, and find them instantly. Built for creators, teams and thinkers.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
          <a href="<?= APP_URL ?>/views/auth/register.php" class="btn btn-gradient btn-lg px-4">Get Started <i class="fa-solid fa-arrow-right ms-1"></i></a>
          <a href="#features" class="btn btn-outline-secondary btn-lg px-4">Learn More</a>
        </div>
        <div class="d-flex gap-4 mt-4 text-muted small">
          <div><i class="fa-solid fa-check text-success"></i> Free forever</div>
          <div><i class="fa-solid fa-check text-success"></i> No credit card</div>
          <div><i class="fa-solid fa-check text-success"></i> Dark mode</div>
        </div>
      </div>
      <div class="col-lg-6" data-aos>
        <div class="glass p-4 p-md-5 position-relative">
          <div class="d-flex gap-2 mb-3"><span class="rounded-circle bg-danger" style="width:10px;height:10px"></span><span class="rounded-circle bg-warning" style="width:10px;height:10px"></span><span class="rounded-circle bg-success" style="width:10px;height:10px"></span></div>
          <div class="note-card mb-2"><div class="d-flex justify-content-between"><strong>Product Roadmap Q4</strong><i class="fa-solid fa-thumbtack text-primary"></i></div><small class="text-muted">Ship search, filters, and rich text this quarter…</small><div class="mt-2"><span class="tag-chip" style="background:#6366f1">work</span> <span class="tag-chip" style="background:#10b981">planning</span></div></div>
          <div class="note-card mb-2"><strong>Book: Atomic Habits</strong><small class="d-block text-muted">Small changes compound over time…</small><div class="mt-2"><span class="tag-chip" style="background:#f59e0b">reading</span></div></div>
          <div class="note-card"><strong>Weekend recipe ideas</strong><small class="d-block text-muted">Pasta, sourdough, tiramisu…</small><div class="mt-2"><span class="tag-chip" style="background:#ef4444">personal</span></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="features" class="section">
  <div class="container">
    <div class="text-center mb-5" data-aos>
      <h2 class="fw-bold">Everything you need. <span class="text-grad">Nothing you don't.</span></h2>
      <p class="text-muted">A complete note-taking toolkit with the features that matter.</p>
    </div>
    <div class="row g-4">
      <?php
      $features = [
        ['fa-lock','Secure Login','Bcrypt hashing + CSRF + session hardening.'],
        ['fa-pen-to-square','Create & Edit','Rich text editor with autosave feel.'],
        ['fa-trash','Soft Delete','Restore anything from the archive.'],
        ['fa-tags','Multiple Tags','Colorful, filterable, unlimited.'],
        ['fa-magnifying-glass','Live Search','Find notes by title, body or tag.'],
        ['fa-filter','Smart Filters','Sort by newest, oldest, alphabetical.'],
        ['fa-thumbtack','Pin Notes','Keep the important stuff on top.'],
        ['fa-star','Favorites','One click to save your best work.'],
        ['fa-box-archive','Archive','Declutter without deleting.'],
        ['fa-chart-line','Dashboard Analytics','Charts powered by Chart.js.'],
        ['fa-user','User Profile','Avatar upload & password change.'],
        ['fa-moon','Dark Mode','Beautiful in day or night.'],
        ['fa-file-export','Export Notes','Download all notes as JSON.'],
        ['fa-mobile-screen','Responsive','Works on every device.'],
        ['fa-shield-halved','Admin Panel','Manage users, notes and messages.'],
      ];
      foreach ($features as $f): ?>
        <div class="col-md-6 col-lg-4" data-aos>
          <div class="card-soft p-4 h-100">
            <div class="feature-icon mb-3"><i class="fa-solid <?= $f[0] ?>"></i></div>
            <h5 class="fw-bold"><?= $f[1] ?></h5>
            <p class="text-muted mb-0"><?= $f[2] ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="about" class="section" style="background:color-mix(in oklab,var(--surface) 60%,transparent)">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6" data-aos>
        <h2 class="fw-bold">About <span class="text-grad">NoteFlow</span></h2>
        <p class="text-muted">NoteFlow is a modern, minimal and secure note-taking application built with PHP 8, PDO and MySQL. Designed with a SaaS-grade UI and a rock-solid backend.</p>
        <div class="row mt-4 g-3">
          <div class="col-sm-6"><div class="card-soft p-3"><h6 class="fw-bold"><i class="fa-solid fa-bullseye text-primary"></i> Mission</h6><p class="small text-muted mb-0">Turn every thought into a searchable, organized note.</p></div></div>
          <div class="col-sm-6"><div class="card-soft p-3"><h6 class="fw-bold"><i class="fa-solid fa-eye text-primary"></i> Vision</h6><p class="small text-muted mb-0">The most delightful place to think and write.</p></div></div>
          <div class="col-sm-6"><div class="card-soft p-3"><h6 class="fw-bold"><i class="fa-solid fa-gift text-primary"></i> Benefits</h6><p class="small text-muted mb-0">Fast, secure, private, and free for personal use.</p></div></div>
          <div class="col-sm-6"><div class="card-soft p-3"><h6 class="fw-bold"><i class="fa-solid fa-layer-group text-primary"></i> Stack</h6><p class="small text-muted mb-0">PHP 8, PDO, MySQL, Bootstrap 5, Chart.js.</p></div></div>
        </div>
      </div>
      <div class="col-lg-6" data-aos>
        <div class="glass p-4">
          <canvas id="aboutChart" height="220"></canvas>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="contact" class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-5" data-aos>
        <h2 class="fw-bold">Get in <span class="text-grad">touch</span></h2>
        <p class="text-muted">Questions, feedback or just want to say hi? We'd love to hear from you.</p>
        <ul class="list-unstyled text-muted mt-4">
          <li class="mb-2"><i class="fa-solid fa-envelope me-2 text-primary"></i> hello@noteflow.app</li>
          <li class="mb-2"><i class="fa-solid fa-location-dot me-2 text-primary"></i> Remote · Worldwide</li>
          <li class="mb-2"><i class="fa-solid fa-clock me-2 text-primary"></i> Mon–Fri, 9am–6pm</li>
        </ul>
      </div>
      <div class="col-lg-7" data-aos>
        <form class="card-soft p-4" method="post" action="<?= APP_URL ?>/controllers/ContactController.php">
          <?= csrf_field() ?>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Name</label><input required name="name" class="form-control" maxlength="100"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input required name="email" type="email" class="form-control" maxlength="150"></div>
            <div class="col-12"><label class="form-label">Message</label><textarea required name="message" rows="5" class="form-control" maxlength="2000"></textarea></div>
            <div class="col-12"><button class="btn btn-gradient px-4">Send message <i class="fa-solid fa-paper-plane ms-1"></i></button></div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__.'/views/partials/footer.php'; ?>
<script>
const ctx = document.getElementById('aboutChart');
if (ctx) new Chart(ctx,{type:'line',data:{labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],datasets:[{label:'Notes created',data:[3,5,7,4,9,12,8],borderColor:'#6366f1',backgroundColor:'rgba(99,102,241,.15)',tension:.4,fill:true}]},options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
</script>
