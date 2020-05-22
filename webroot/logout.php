<?php
include('inc/cfg.php');
session_name(SESSION_UNIQUE_ID);
session_start();
// Destroy the sessions
session_destroy();
// Redirect to the main page, if correctly logged out this should
// automatically redirect to login.php.
header('Location: index.php');
