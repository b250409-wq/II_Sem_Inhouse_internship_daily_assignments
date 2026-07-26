<?php $pageTitle='Forgot Password'; require __DIR__.'/../partials/head.php'; require __DIR__.'/../partials/navbar.php'; ?>
<div class="container" style="padding-top:120px">
  <div class="row justify-content-center"><div class="col-md-5">
    <div class="card-soft p-4 p-md-5">
      <h4 class="fw-bold">Reset your password</h4>
      <p class="text-muted small">Enter your email and we'll send you a reset link (demo: emailing is stubbed).</p>
      <form method="post"><?= csrf_field() ?>
        <input type="email" name="email" class="form-control mb-3" required>
        <button class="btn btn-gradient w-100">Send reset link</button>
      </form>
    </div>
  </div></div>
</div>
<?php require __DIR__.'/../partials/footer.php'; ?>
