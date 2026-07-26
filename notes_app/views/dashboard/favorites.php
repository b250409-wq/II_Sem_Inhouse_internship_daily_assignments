<?php $pageTitle='Favorites'; require __DIR__.'/layout_start.php';
require_once __DIR__.'/../../models/Note.php';
$notes = (new Note())->forUser(current_user_id(), ['favorite'=>1]);
include __DIR__.'/_notes_grid.php';
require __DIR__.'/layout_end.php';
