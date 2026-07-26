<?php $pageTitle='Profile'; require __DIR__.'/layout_start.php';
require_once __DIR__.'/../../models/User.php';
$u = (new User())->findById(current_user_id());
?>
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card-soft p-4">
      <h6 class="fw-bold">Profile info</h6>
      <form method="post" action="<?= APP_URL ?>/controllers/ProfileController.php?action=update" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label">Username</label><input required name="username" class="form-control" value="<?= e($u['username']) ?>"></div>
        <div class="mb-3"><label class="form-label">Email</label><input required type="email" name="email" class="form-control" value="<?= e($u['email']) ?>"></div>
        <div class="mb-3"><label class="form-label">Theme</label>
          <select name="theme" class="form-select">
            <?php foreach(['light','dark','system'] as $th): ?><option value="<?= $th ?>" <?= $u['theme']===$th?'selected':'' ?>><?= ucfirst($th) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3"><label class="form-label">Avatar</label><input type="file" name="avatar" accept="image/*" class="form-control"></div>
        <button class="btn btn-gradient">Save</button>
      </form>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card-soft p-4">
      <h6 class="fw-bold">Change password</h6>
      <form method="post" action="<?= APP_URL ?>/controllers/ProfileController.php?action=password">
        <?= csrf_field() ?>
        <input required type="password" name="current" class="form-control mb-2" placeholder="Current password">
        <input required type="password" name="new" minlength="6" class="form-control mb-2" placeholder="New password">
        <input required type="password" name="confirm" minlength="6" class="form-control mb-3" placeholder="Confirm new password">
        <button class="btn btn-gradient">Update password</button>
      </form>
    </div>
  </div>
</div>
<?php require __DIR__.'/layout_end.php'; ?>
