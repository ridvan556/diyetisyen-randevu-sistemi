<?php
require_once 'baglanti.php';

try {
    // randevular tablomuza uzmanın notlarını tutacak 'uzman_notu' adında yeni bir sütun ekliyoruz
    $db->exec("ALTER TABLE randevular ADD uzman_notu TEXT NULL AFTER sikayet");
    
    echo "<h3 style='color:green;'>Süper! Veritabanına 'Uzman Notu' sütunu başarıyla eklendi.</h3>";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
         echo "<h3 style='color:blue;'>Sütun zaten mevcut! İşleme devam edebiliriz.</h3>";
    } else {
         echo "Veritabanı Hatası: " . $e->getMessage();
    }
}
?>