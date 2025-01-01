<?php
session_start();
session_unset();
session_destroy();

echo "<script>window.open('AdminLogin.php','_self')</script>";
?>