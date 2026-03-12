<?php
require_once 'baglanti.php';

try {
    // 1. Danışanlar (Hastalar) Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS danisanlar (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ad_soyad VARCHAR(100) NOT NULL,
        telefon VARCHAR(20) NOT NULL,
        eposta VARCHAR(100) NOT NULL,
        sifre VARCHAR(255) NOT NULL,
        kayit_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Randevular Tablosu (İlişkisel Tablo)
    $db->exec("CREATE TABLE IF NOT EXISTS randevular (
        id INT AUTO_INCREMENT PRIMARY KEY,
        danisan_id INT NOT NULL,
        randevu_tarihi DATE NOT NULL,
        randevu_saati TIME NOT NULL,
        durum ENUM('Bekliyor', 'Onaylandi', 'Iptal') DEFAULT 'Bekliyor',
        olusturulma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (danisan_id) REFERENCES danisanlar(id) ON DELETE CASCADE
    )");

    echo "<h3 style='color:green;'>Harika! Diyetisyen/Klinik Randevu Sistemi veritabanı ve tabloları başarıyla kuruldu.</h3>";
    echo "<p>Artık kurulum.php dosyasını silebilirsiniz.</p>";

} catch (PDOException $e) {
    echo "Veritabanı Hatası: " . $e->getMessage();
}
?>