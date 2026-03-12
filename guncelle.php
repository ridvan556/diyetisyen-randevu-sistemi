<?php
require_once 'baglanti.php';

try {
    // randevular tablomuza 'sikayet' adında uzun metin (TEXT) alabilen bir sütun ekliyoruz
    $db->exec("ALTER TABLE randevular ADD sikayet TEXT NULL AFTER randevu_saati");
    
    echo "<h3 style='color:green;'>Harika! Veritabanına 'Şikayet/Not' sütunu başarıyla eklendi.</h3>";
    echo "<p>Artık index.php dosyasını güncelleyebiliriz.</p>";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
         echo "<h3 style='color:blue;'>Sütun zaten mevcut! İşleme devam edebilirsiniz.</h3>";
    } else {
         echo "Veritabanı Hatası: " . $e->getMessage();
    }
}
?>