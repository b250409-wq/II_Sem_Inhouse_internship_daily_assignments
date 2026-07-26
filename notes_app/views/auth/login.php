<?php $pageTitle='Login'; require __DIR__.'/../partials/head.php'; require __DIR__.'/../partials/navbar.php'; ?>
<div class="container" style="padding-top:120px">
  <div class="row justify-content-center"><div class="col-md-6 col-lg-5">
    <?php require __DIR__.'/../partials/flash.php'; ?>
    <div class="card-soft p-4 p-md-5" data-aos>
      <h3 class="fw-bold mb-1">Welcome back</h3>
      <p class="text-muted">Login to continue to your notes.</p>
      <form method="post" action="<?= APP_URL ?>/controllers/AuthController.php?action=login">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label">Email</label><input required type="email" name="email" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Password</label><input required type="password" name="password" class="form-control"></div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check"><input class="form-check-input" type="checkbox" name="remember" id="rm"><label class="form-check-label" for="rm">Remember me</label></div>
          <a href="<?= APP_URL ?>/views/auth/forgot.php" class="small text-decoration-none">Forgot password?</a>
        </div>
        <button class="btn btn-gradient w-100">Login</button>
      </form>
      <p class="text-center text-muted mt-3 mb-0">No account? <a href="<?= APP_URL ?>/views/auth/register.php">Register</a></p>
    </div>
  </div></div>
</div>
<?php require __DIR__.'/../partials/footer.php'; ?>
