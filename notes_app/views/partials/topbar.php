<div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom" style="background:var(--surface)">
  <h5 class="mb-0"><?= e($pageTitle ?? 'Dashboard') ?></h5>
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-sm btn-outline-secondary" onclick="toggleTheme()"><i class="fa-solid fa-circle-half-stroke"></i></button>
    <i class="fa-regular fa-bell text-muted"></i>
    <div class="d-flex align-items-center gap-2">
      <span class="brand-logo" style="width:32px;height:32px"><?= strtoupper(substr($_SESSION['username'] ?? 'U',0,1)) ?></span>
      <strong class="small"><?= e($_SESSION['username'] ?? 'User') ?></strong>
    </div>
  </div>
</div>
