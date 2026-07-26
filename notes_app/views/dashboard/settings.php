<?php $pageTitle='Settings'; require __DIR__.'/layout_start.php'; ?>
<div class="card-soft p-4">
  <h6 class="fw-bold">Appearance</h6>
  <div class="btn-group mt-2">
    <button class="btn btn-outline-secondary" onclick="document.documentElement.setAttribute('data-theme','light');localStorage.setItem('notes_theme','light')"><i class="fa-solid fa-sun"></i> Light</button>
    <button class="btn btn-outline-secondary" onclick="document.documentElement.setAttribute('data-theme','dark');localStorage.setItem('notes_theme','dark')"><i class="fa-solid fa-moon"></i> Dark</button>
    <button class="btn btn-outline-secondary" onclick="localStorage.removeItem('notes_theme');location.reload()"><i class="fa-solid fa-circle-half-stroke"></i> System</button>
  </div>
</div>
<?php require __DIR__.'/layout_end.php'; ?>
