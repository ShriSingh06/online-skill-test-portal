<?php
require_once 'config.php';
session_unset();
session_destroy();
header("Location: " . $base_url);
exit;
