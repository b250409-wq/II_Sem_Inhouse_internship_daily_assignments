<?php $pageTitle='Archive'; require __DIR__.'/layout_start.php';
require_once __DIR__.'/../../models/Note.php';
$notes = (new Note())->forUser(current_user_id(), ['archived'=>1]);
?>
<div class="row g-3">
<?php if(!$notes): ?><div class="col-12 text-center text-muted py-5">Nothing archived.</div><?php endif;
foreach($notes as $n): ?>
  <div class="col-md-6 col-lg-4"><div class="note-card">
    <h6 class="fw-bold"><?= e($n['title']) ?></h6>
    <p class="small text-muted"><?= e(mb_substr(strip_tags($n['content']),0,140)) ?></p>
    <a class="btn btn-sm btn-outline-primary" href="<?= APP_URL ?>/controllers/NoteController.php?action=restore&id=<?= $n['id'] ?>"><i class="fa-solid fa-rotate-left"></i> Restore</a>
  </div></div>
<?php endforeach; ?>
</div>
<?php require __DIR__.'/layout_end.php'; ?>
