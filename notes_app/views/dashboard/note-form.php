<?php $pageTitle='Note'; require __DIR__.'/layout_start.php';
require_once __DIR__.'/../../models/Note.php';
require_once __DIR__.'/../../models/Tag.php';
$id = (int)($_GET['id'] ?? 0);
$note = $id ? (new Note())->find($id, current_user_id()) : null;
$tags = (new Tag())->forUser(current_user_id());
$noteTagIds = $note ? array_map(fn($t)=>(int)$t['id'], $note['tags']) : [];
$action = $note ? 'update' : 'create';
?>
<div class="card-soft p-4">
  <form method="post" action="<?= APP_URL ?>/controllers/NoteController.php?action=<?= $action ?>">
    <?= csrf_field() ?>
    <?php if($note): ?><input type="hidden" name="id" value="<?= $note['id'] ?>"><?php endif; ?>
    <input required name="title" class="form-control form-control-lg mb-3" placeholder="Note title" value="<?= e($note['title'] ?? '') ?>" maxlength="255">
    <textarea name="content" class="form-control mb-3" rows="10" placeholder="Write your note…"><?= e($note['content'] ?? '') ?></textarea>
    <label class="form-label fw-bold">Tags</label>
    <div class="d-flex flex-wrap gap-2 mb-3">
      <?php foreach($tags as $t): $checked = in_array((int)$t['id'], $noteTagIds); ?>
        <label class="tag-chip" style="background:<?= e($t['color']) ?>;cursor:pointer;opacity:<?= $checked?1:.55 ?>">
          <input type="checkbox" name="tags[]" value="<?= $t['id'] ?>" <?= $checked?'checked':'' ?> hidden onchange="this.parentElement.style.opacity=this.checked?1:.55">
          <?= e($t['name']) ?>
        </label>
      <?php endforeach; ?>
      <a href="<?= APP_URL ?>/views/dashboard/tags.php" class="small text-decoration-none">+ Manage tags</a>
    </div>
    <button class="btn btn-gradient px-4"><i class="fa-solid fa-check"></i> Save note</button>
    <a href="<?= APP_URL ?>/views/dashboard/notes.php" class="btn btn-outline-secondary">Cancel</a>
  </form>
</div>
<?php require __DIR__.'/layout_end.php'; ?>
