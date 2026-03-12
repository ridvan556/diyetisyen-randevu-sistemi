<?php
session_start();
require_once 'baglanti.php';

$hata = "";
$basari = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kayit_ol'])) {
    // Formdan gelen verileri alıp sağdaki soldaki boşlukları temizliyoruz
    $ad_soyad = trim($_POST['ad_soyad']);
    $telefon = trim($_POST['telefon']);
    $eposta = trim($_POST['eposta']);
    $sifre = $_POST['sifre'];

    if (empty($ad_soyad) || empty($telefon) || empty($eposta) || empty($sifre)) {
        $hata = "Lütfen tüm alanları eksiksiz doldurun.";
    } else {
        // 1. KONTROL: Bu e-posta ile daha önce kayıt olunmuş mu? (Aynı kişinin 2 hesabı olmasın)
        $kontrol_sorgu = $db->prepare("SELECT id FROM danisanlar WHERE eposta = :eposta");
        $kontrol_sorgu->execute(['eposta' => $eposta]);
        
        if ($kontrol_sorgu->rowCount() > 0) {
            $hata = "Bu e-posta adresi sistemimizde zaten kayıtlı. Lütfen giriş yapmayı deneyin.";
        } else {
            // 2. KAYIT İŞLEMİ: Her şey yolundaysa şifreyi kriptola ve veritabanına kaydet
            $kriptolu_sifre = password_hash($sifre, PASSWORD_DEFAULT);
            
            $kayit_sorgu = $db->prepare("INSERT INTO danisanlar (ad_soyad, telefon, eposta, sifre) VALUES (:ad_soyad, :telefon, :eposta, :sifre)");
            $kayit_sorgu->execute([
                'ad_soyad' => $ad_soyad,
                'telefon' => $telefon,
                'eposta' => $eposta,
                'sifre' => $kriptolu_sifre
            ]);
            
            $basari = "Kaydınız başarıyla oluşturuldu! Şimdi giriş yapabilirsiniz.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danışan Kaydı | Diyetisyen Otomasyonu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-teal-50 font-sans h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-teal-100">
        
        <div class="text-center mb-8">
            <div class="bg-teal-100 text-teal-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                <i class="fa-solid fa-leaf"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Danışan Kaydı</h2>
            <p class="text-sm text-gray-500 mt-1">Randevu almak için lütfen hesap oluşturun</p>
        </div>

        <?php if(!empty($hata)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 rounded text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $hata; ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($basari)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-6 rounded text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> <?php echo $basari; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Adınız Soyadınız</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-user"></i></span>
                    <input type="text" name="ad_soyad" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Örn: Ayşe Yılmaz" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Cep Telefonu</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-phone"></i></span>
                    <input type="tel" name="telefon" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="05XX XXX XX XX" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">E-posta Adresi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="eposta" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="ornek@email.com" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Şifre Belirleyin</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="sifre" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" name="kayit_ol" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 shadow-md">
                Kayıt İşlemini Tamamla
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            Zaten hesabınız var mı? <a href="giris.php" class="text-teal-600 font-bold hover:underline">Giriş Yapın</a>
        </div>

    </div>

</body>
</html>