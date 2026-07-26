<?php $pageTitle='Dashboard'; require __DIR__.'/layout_start.php';
require_once __DIR__.'/../../models/Note.php';
require_once __DIR__.'/../../models/Tag.php';
$stats = (new Note())->stats(current_user_id());
$recent = (new Note())->forUser(current_user_id(), ['sort'=>'newest']);
$pinned = array_filter($recent, fn($n)=>$n['is_pinned']);
?>
<div class="row g-3 mb-4">
  <div class="col-md-3 col-6"><div class="stat-card"><div class="d-flex justify-content-between"><div><small class="opacity-75">Total Notes</small><h3 class="mb-0"><?= $stats['total'] ?></h3></div><i class="fa-solid fa-note-sticky fs-2 opacity-50"></i></div></div></div>
  <div class="col-md-3 col-6"><div class="stat-card alt3"><div class="d-flex justify-content-between"><div><small class="opacity-75">Tags</small><h3 class="mb-0"><?= $stats['tags'] ?></h3></div><i class="fa-solid fa-tags fs-2 opacity-50"></i></div></div></div>
  <div class="col-md-3 col-6"><div class="stat-card alt"><div class="d-flex justify-content-between"><div><small class="opacity-75">Favorites</small><h3 class="mb-0"><?= $stats['favorite'] ?></h3></div><i class="fa-solid fa-star fs-2 opacity-50"></i></div></div></div>
  <div class="col-md-3 col-6"><div class="stat-card alt2"><div class="d-flex justify-content-between"><div><small class="opacity-75">Archived</small><h3 class="mb-0"><?= $stats['archived'] ?></h3></div><i class="fa-solid fa-box-archive fs-2 opacity-50"></i></div></div></div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card-soft p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Notes activity</h5>
        <a href="<?= APP_URL ?>/views/dashboard/note-form.php" class="btn btn-sm btn-gradient"><i class="fa-solid fa-plus"></i> Quick add</a>
      </div>
      <canvas id="chart" height="120"></canvas>
    </div>
    <div class="card-soft p-4 mt-4">
      <h6 class="fw-bold"><i class="fa-solid fa-thumbtack text-primary"></i> Pinned</h6>
      <?php if(!$pinned): ?><p class="text-muted mb-0">No pinned notes yet.</p><?php else: foreach(array_slice($pinned,0,4) as $n): ?>
        <div class="border-top py-2"><strong><?= e($n['title']) ?></strong><div class="small text-muted"><?= e(mb_substr(strip_tags($n['content']),0,120)) ?></div></div>
      <?php endforeach; endif; ?>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card-soft p-4">
      <h6 class="fw-bold">Quick actions</h6>
      <div class="d-grid gap-2 mt-3">
        <a class="btn btn-outline-secondary" href="<?= APP_URL ?>/views/dashboard/note-form.php"><i class="fa-solid fa-plus"></i> New note</a>
        <a class="btn btn-outline-secondary" href="<?= APP_URL ?>/views/dashboard/tags.php"><i class="fa-solid fa-tag"></i> Manage tags</a>
        <a class="btn btn-outline-secondary" href="<?= APP_URL ?>/controllers/NoteController.php?action=export"><i class="fa-solid fa-file-export"></i> Export notes</a>
      </div>
    </div>
    <div class="card-soft p-4 mt-4">
      <h6 class="fw-bold">Recent</h6>
      <?php foreach(array_slice($recent,0,5) as $n): ?>
        <div class="d-flex justify-content-between border-top py-2 small"><span><?= e($n['title']) ?></span><span class="text-muted"><?= date('M d', strtotime($n['created_at'])) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<script>
new Chart(document.getElementById('chart'),{type:'bar',data:{labels:['Total','Favorites','Pinned','Archived','Tags'],datasets:[{data:[<?= $stats['total'] ?>,<?= $stats['favorite'] ?>,<?= $stats['pinned'] ?>,<?= $stats['archived'] ?>,<?= $stats['tags'] ?>],backgroundColor:['#6366f1','#f59e0b','#8b5cf6','#10b981','#06b6d4'],borderRadius:8}]},options:{plugins:{legend:{display:false}}}});
</script>
<?php require __DIR__.'/layout_end.php'; ?>
