<?php
session_start();
$_SESSION['test'] = 'works';
echo 'Session ID: ' . session_id();
echo "\nTest value: " . $_SESSION['test'];
?>
