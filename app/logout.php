<?php
/**
 * Logout Handler
 */

require_once __DIR__ . '/Auth.php';

Auth::logout();
header('Location: /public/login.php');
exit;
