<?php $pageTitle='Tags'; require __DIR__.'/layout_start.php';
require_once __DIR__.'/../../models/Tag.php';
$tags = (new Tag())->forUser(current_user_id());
?>
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card-soft p-4">
      <h6 class="fw-bold">Create tag</h6>
      <form method="post" action="<?= APP_URL ?>/controllers/TagController.php?action=create">
        <?= csrf_field() ?>
        <input required name="name" class="form-control mb-2" placeholder="Tag name" maxlength="50">
        <input type="color" name="color" value="#6366f1" class="form-control form-control-color mb-3" style="width:60px">
        <button class="btn btn-gradient w-100">Add tag</button>
      </form>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card-soft p-4">
      <h6 class="fw-bold mb-3">Your tags</h6>
      <?php if(!$tags): ?><p class="text-muted">No tags yet.</p><?php endif; ?>
      <div class="d-flex flex-wrap gap-2">
        <?php foreach($tags as $t): ?>
          <div class="d-inline-flex align-items-center gap-2 border rounded-pill ps-2 pe-1 py-1">
            <span class="tag-chip" style="background:<?= e($t['color']) ?>"><?= e($t['name']) ?></span>
            <a class="btn btn-sm btn-link text-danger p-0" href="<?= APP_URL ?>/controllers/TagController.php?action=delete&id=<?= $t['id'] ?>" onclick="return confirm('Delete tag?')"><i class="fa-solid fa-xmark"></i></a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__.'/layout_end.php'; ?>
