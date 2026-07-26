<?php $pageTitle='Admin'; require_once __DIR__.'/../../includes/helpers.php'; require_admin();
require __DIR__.'/../partials/head.php';
require_once __DIR__.'/../../models/User.php';
require_once __DIR__.'/../../models/Contact.php';
require_once __DIR__.'/../../config/database.php';
$db = Database::getInstance();
$userCount   = (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$noteCount   = (int)$db->query('SELECT COUNT(*) FROM notes')->fetchColumn();
$tagCount    = (int)$db->query('SELECT COUNT(*) FROM tags')->fetchColumn();
$msgCount    = (int)$db->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
$users = (new User())->all();
$msgs  = (new Contact())->all();
?>
<div class="d-flex">
  <?php require __DIR__.'/../partials/sidebar.php'; ?>
  <main class="flex-grow-1">
    <?php require __DIR__.'/../partials/topbar.php'; ?>
    <div class="p-4">
      <div class="row g-3 mb-4">
        <div class="col-md-3 col-6"><div class="stat-card"><small>Users</small><h3><?= $userCount ?></h3></div></div>
        <div class="col-md-3 col-6"><div class="stat-card alt"><small>Notes</small><h3><?= $noteCount ?></h3></div></div>
        <div class="col-md-3 col-6"><div class="stat-card alt2"><small>Tags</small><h3><?= $tagCount ?></h3></div></div>
        <div class="col-md-3 col-6"><div class="stat-card alt3"><small>Messages</small><h3><?= $msgCount ?></h3></div></div>
      </div>
      <div class="card-soft p-4 mb-4">
        <h6 class="fw-bold">Users</h6>
        <table class="table align-middle">
          <thead><tr><th>#</th><th>User</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead>
          <tbody>
          <?php foreach($users as $u): ?>
            <tr><td><?= $u['id'] ?></td><td><?= e($u['username']) ?></td><td><?= e($u['email']) ?></td><td><span class="badge bg-<?= $u['role']==='admin'?'primary':'secondary' ?>"><?= e($u['role']) ?></span></td><td><?= date('M d, Y', strtotime($u['created_at'])) ?></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="card-soft p-4">
        <h6 class="fw-bold">Contact messages</h6>
        <?php if(!$msgs): ?><p class="text-muted mb-0">No messages.</p><?php endif; ?>
        <?php foreach($msgs as $m): ?>
          <div class="border-top py-2"><strong><?= e($m['name']) ?></strong> <small class="text-muted">&lt;<?= e($m['email']) ?>&gt; · <?= date('M d, Y H:i',strtotime($m['created_at'])) ?></small><p class="mb-0 small"><?= nl2br(e($m['message'])) ?></p></div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</div>
<?php require __DIR__.'/../partials/footer.php'; ?>
