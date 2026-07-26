<div class="row g-3">
<?php if(empty($notes)): ?><div class="col-12 text-center text-muted py-5">Nothing here yet.</div><?php endif;
foreach($notes as $n): ?>
  <div class="col-md-6 col-lg-4"><div class="note-card">
    <h6 class="fw-bold"><?= e($n['title']) ?></h6>
    <p class="small text-muted"><?= e(mb_substr(strip_tags($n['content']),0,140)) ?></p>
    <div class="d-flex flex-wrap gap-1">
      <?php foreach($n['tags'] as $t): ?><span class="tag-chip" style="background:<?= e($t['color']) ?>"><?= e($t['name']) ?></span><?php endforeach; ?>
    </div>
  </div></div>
<?php endforeach; ?>
</div>
