<?php
session_start();
// Tüm oturum değişkenlerini (eski projeden kalanlar dahil) temizle
$_SESSION = array();
session_destroy();

// Temizlendikten sonra giriş sayfasına yönlendir
header("Location: giris.php");
exit;
?>