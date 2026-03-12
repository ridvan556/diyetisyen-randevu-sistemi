# 🍏 Diyetisyen & Klinik Randevu Otomasyonu

Bu proje, uzman diyetisyenler ve danışanlar (hastalar) arasındaki randevu alma, onaylama ve tavsiye/diyet listesi iletme süreçlerini dijitalleştiren dinamik bir arka yüz (backend) uygulamasıdır. PHP ve MySQL kullanılarak sıfırdan geliştirilmiştir.

## 🚀 Kullanılan Teknolojiler
* **Backend:** PHP 8
* **Veritabanı:** MySQL (PDO - İlişkisel Veritabanı Mimarisi)
* **Frontend:** HTML5, Tailwind CSS
* **İkonlar:** FontAwesome

## 🛠️ Öne Çıkan Özellikler ve Algoritmalar
* **Akıllı Çakışma Önleyici (Conflict Control):** Aynı tarihe ve saate ikinci bir randevu alınmasını veritabanı seviyesinde engelleyen zeki algoritma.
* **Çift Panelli Mimari:** Hem danışanlar için kişisel takip paneli (`index.php`) hem de uzmanlar için yönetim paneli (`uzman.php`).
* **İlişkisel Veri Çekme (INNER JOIN):** Uzman panelinde hastanın bilgileri (ad, telefon) ile randevu saatlerini tek potada birleştiren gelişmiş SQL sorguları.
* **Karşılıklı Not İletişimi:** Hastaların randevu alırken hedeflerini yazabilmesi ve uzmanın onay aşamasında tavsiye/diyet listesi notunu hastanın paneline iletebilmesi.
* **Kriptolu Oturum:** `password_hash()` ve `password_verify()` ile güvenli üyelik ve Session tabanlı yetkilendirme.
* **Geçmiş Zaman Kontrolü:** JavaScript kullanmadan, doğrudan form validasyonu ile geçmiş tarihlere randevu alınmasının engellenmesi.

## ⚙️ Kurulum
1. Proje dosyalarını yerel sunucunuzun (Ampps, XAMPP, MAMP) kök dizinine alın.
2. `baglanti.php` içindeki veritabanı bilgilerinizi (kullanıcı adı, şifre) kendi sisteminize göre ayarlayın.
3. Tarayıcınızda sırasıyla `kurulum.php`, `guncelle.php` ve `guncelle2.php` dosyalarını çalıştırarak veritabanı tablolarını ve yeni sütunları otomatik inşa edin.
4. `kayit.php` üzerinden ilk danışan kaydınızı oluşturup sistemi test etmeye başlayabilirsiniz!
