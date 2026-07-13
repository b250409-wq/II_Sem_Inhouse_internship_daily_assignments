<?php
/**
 * api/logout.php
 * Destroys the current session.
 * POST (no body needed)
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

$_SESSION = [];
session_destroy();

respondSuccess(['message' => 'Logged out.']);
