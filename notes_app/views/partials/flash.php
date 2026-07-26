<?php $s=flash('success'); $er=flash('error'); ?>
<?php if($s): ?><div class="alert alert-success alert-dismissible fade show m-3"><?= e($s) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if($er): ?><div class="alert alert-danger alert-dismissible fade show m-3"><?= e($er) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
