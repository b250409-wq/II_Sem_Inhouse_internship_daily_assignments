<?php $cur = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar p-3" style="width:260px">
  <div class="d-flex align-items-center gap-2 mb-4 px-2">
    <span class="brand-logo"><i class="fa-solid fa-note-sticky"></i></span>
    <strong>NoteFlow</strong>
  </div>
  <nav class="nav flex-column">
    <a class="nav-link <?= $cur==='index.php'?'active':'' ?>" href="<?= APP_URL ?>/views/dashboard/index.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <a class="nav-link <?= $cur==='notes.php'?'active':'' ?>" href="<?= APP_URL ?>/views/dashboard/notes.php"><i class="fa-regular fa-note-sticky"></i> All Notes</a>
    <a class="nav-link" href="<?= APP_URL ?>/views/dashboard/note-form.php"><i class="fa-solid fa-plus"></i> Add Note</a>
    <a class="nav-link <?= $cur==='tags.php'?'active':'' ?>" href="<?= APP_URL ?>/views/dashboard/tags.php"><i class="fa-solid fa-tags"></i> Tags</a>
    <a class="nav-link <?= $cur==='favorites.php'?'active':'' ?>" href="<?= APP_URL ?>/views/dashboard/favorites.php"><i class="fa-solid fa-star"></i> Favorites</a>
    <a class="nav-link <?= $cur==='archive.php'?'active':'' ?>" href="<?= APP_URL ?>/views/dashboard/archive.php"><i class="fa-solid fa-box-archive"></i> Archive</a>
    <a class="nav-link <?= $cur==='profile.php'?'active':'' ?>" href="<?= APP_URL ?>/views/dashboard/profile.php"><i class="fa-solid fa-user"></i> Profile</a>
    <a class="nav-link <?= $cur==='settings.php'?'active':'' ?>" href="<?= APP_URL ?>/views/dashboard/settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
    <?php if(current_user_role()==='admin'): ?>
      <hr><a class="nav-link" href="<?= APP_URL ?>/views/admin/index.php"><i class="fa-solid fa-shield-halved"></i> Admin</a>
    <?php endif; ?>
    <hr>
    <a class="nav-link" href="<?= APP_URL ?>/controllers/AuthController.php?action=logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </nav>
</aside>
