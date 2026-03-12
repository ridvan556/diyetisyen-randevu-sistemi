<?php
$host = 'localhost';
$kullanici = 'root';
$sifre = 'mysql'; // Ampps kullandığın için varsayılan şifre 'mysql'dir.
$veritabani = 'klinik_db';

try {
    // 1. Önce MySQL sunucusuna bağlanıyoruz (Henüz veritabanı seçmeden)
    $db = new PDO("mysql:host=$host;charset=utf8", $kullanici, $sifre);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Eğer 'klinik_db' adında bir veritabanı yoksa, otomatik olarak oluştur!
    $db->exec("CREATE DATABASE IF NOT EXISTS `$veritabani` DEFAULT CHARACTER SET utf8 COLLATE utf8_turkish_ci");

    // 3. Oluşturduğumuz bu yeni veritabanının içine giriyoruz
    $db->exec("USE `$veritabani`");

} catch (PDOException $e) {
    die("Kritik Hata! Veritabanına ulaşılamadı: " . $e->getMessage());
}
?>