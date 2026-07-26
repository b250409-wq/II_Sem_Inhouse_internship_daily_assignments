<nav class="navbar navbar-expand-lg navbar-glass fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2 brand" href="<?= APP_URL ?>/index.php">
      <span class="brand-logo"><i class="fa-solid fa-note-sticky"></i></span>
      <span class="fw-bold">NoteFlow</span>
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php#home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php#features">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/index.php#contact">Contact</a></li>
        <li class="nav-item"><button class="btn btn-sm btn-outline-secondary" onclick="toggleTheme()"><i class="fa-solid fa-moon"></i></button></li>
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="btn btn-sm btn-gradient" href="<?= APP_URL ?>/views/dashboard/index.php">Dashboard</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/views/auth/login.php">Login</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-gradient" href="<?= APP_URL ?>/views/auth/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
