<?php $pageTitle='All Notes'; require __DIR__.'/layout_start.php';
require_once __DIR__.'/../../models/Note.php';
require_once __DIR__.'/../../models/Tag.php';
$filters = ['q'=>$_GET['q']??'', 'sort'=>$_GET['sort']??'newest', 'tag_id'=>$_GET['tag_id']??''];
$notes = (new Note())->forUser(current_user_id(), $filters);
$tags  = (new Tag())->forUser(current_user_id());
?>
<form class="row g-2 mb-3" method="get">
  <div class="col-md-5"><input class="form-control" name="q" value="<?= e($filters['q']) ?>" placeholder="Search notes…"></div>
  <div class="col-md-3"><select name="tag_id" class="form-select"><option value="">All tags</option><?php foreach($tags as $t): ?><option value="<?= $t['id'] ?>" <?= $filters['tag_id']==$t['id']?'selected':'' ?>><?= e($t['name']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-2"><select name="sort" class="form-select"><option value="newest" <?= $filters['sort']==='newest'?'selected':'' ?>>Newest</option><option value="oldest" <?= $filters['sort']==='oldest'?'selected':'' ?>>Oldest</option><option value="alpha" <?= $filters['sort']==='alpha'?'selected':'' ?>>A–Z</option></select></div>
  <div class="col-md-2 d-grid"><button class="btn btn-gradient"><i class="fa-solid fa-magnifying-glass"></i> Search</button></div>
</form>
<div class="row g-3">
  <?php if(!$notes): ?>
    <div class="col-12 text-center text-muted py-5"><i class="fa-regular fa-face-smile fs-1"></i><p class="mt-2">No notes yet. Create your first note!</p></div>
  <?php else: foreach($notes as $n): ?>
    <div class="col-md-6 col-lg-4">
      <div class="note-card h-100">
        <div class="actions">
          <a class="btn btn-sm btn-light" href="<?= APP_URL ?>/controllers/NoteController.php?action=toggle&field=is_pinned&id=<?= $n['id'] ?>" title="Pin"><i class="fa-solid fa-thumbtack <?= $n['is_pinned']?'text-primary':'' ?>"></i></a>
          <a class="btn btn-sm btn-light" href="<?= APP_URL ?>/controllers/NoteController.php?action=toggle&field=is_favorite&id=<?= $n['id'] ?>" title="Favorite"><i class="fa-solid fa-star <?= $n['is_favorite']?'text-warning':'' ?>"></i></a>
          <a class="btn btn-sm btn-light" href="<?= APP_URL ?>/controllers/NoteController.php?action=toggle&field=is_archived&id=<?= $n['id'] ?>" title="Archive"><i class="fa-solid fa-box-archive"></i></a>
          <a class="btn btn-sm btn-light" href="<?= APP_URL ?>/views/dashboard/note-form.php?id=<?= $n['id'] ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
          <a class="btn btn-sm btn-light text-danger" href="<?= APP_URL ?>/controllers/NoteController.php?action=delete&id=<?= $n['id'] ?>" onclick="return confirm('Delete note?')" title="Delete"><i class="fa-solid fa-trash"></i></a>
        </div>
        <h6 class="fw-bold mb-1 pe-5"><?= e($n['title']) ?></h6>
        <p class="small text-muted"><?= e(mb_substr(strip_tags($n['content']),0,140)) ?></p>
        <div class="d-flex flex-wrap gap-1 mb-2">
          <?php foreach($n['tags'] as $t): ?><span class="tag-chip" style="background:<?= e($t['color']) ?>"><?= e($t['name']) ?></span><?php endforeach; ?>
        </div>
        <small class="text-muted"><i class="fa-regular fa-clock"></i> <?= date('M d, Y', strtotime($n['created_at'])) ?></small>
      </div>
    </div>
  <?php endforeach; endif; ?>
</div>
<?php require __DIR__.'/layout_end.php'; ?>
