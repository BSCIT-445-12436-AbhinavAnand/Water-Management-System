<?php
require_once '../includes/config.php';
if(isAdmin()) redirect('dashboard.php');
redirect('login.php');
?>
