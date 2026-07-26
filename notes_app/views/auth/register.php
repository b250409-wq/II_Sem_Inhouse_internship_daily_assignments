<?php $pageTitle='Register'; require __DIR__.'/../partials/head.php'; require __DIR__.'/../partials/navbar.php'; ?>
<div class="container" style="padding-top:120px">
  <div class="row justify-content-center"><div class="col-md-6 col-lg-5">
    <?php require __DIR__.'/../partials/flash.php'; ?>
    <div class="card-soft p-4 p-md-5" data-aos>
      <h3 class="fw-bold mb-1">Create your account</h3>
      <p class="text-muted">Free forever. No credit card.</p>
      <form method="post" action="<?= APP_URL ?>/controllers/AuthController.php?action=register">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label">Username</label><input required name="username" minlength="3" maxlength="50" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Email</label><input required type="email" name="email" maxlength="150" class="form-control"></div>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Password</label><input required type="password" name="password" minlength="6" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Confirm</label><input required type="password" name="confirm" minlength="6" class="form-control"></div>
        </div>
        <button class="btn btn-gradient w-100 mt-3">Create account</button>
      </form>
      <p class="text-center text-muted mt-3 mb-0">Already have one? <a href="<?= APP_URL ?>/views/auth/login.php">Login</a></p>
    </div>
  </div></div>
</div>
<?php require __DIR__.'/../partials/footer.php'; ?>
