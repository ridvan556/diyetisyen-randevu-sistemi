<?php
session_start();
require_once 'baglanti.php';

// Eğer kullanıcı zaten giriş yapmışsa, onu direkt panele (index.php) gönderelim
if (isset($_SESSION['oturum_acik']) && $_SESSION['oturum_acik'] === true) {
    header("Location: index.php");
    exit;
}

$hata = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['giris_yap'])) {
    $eposta = trim($_POST['eposta']);
    $sifre = $_POST['sifre'];

    if (empty($eposta) || empty($sifre)) {
        $hata = "Lütfen e-posta ve şifrenizi girin.";
    } else {
        // 1. Veritabanında bu e-postaya sahip bir kullanıcı var mı diye bakıyoruz
        $sorgu = $db->prepare("SELECT * FROM danisanlar WHERE eposta = :eposta");
        $sorgu->execute(['eposta' => $eposta]);
        $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

        // 2. Kullanıcı varsa ve girdiği şifre veritabanındaki kriptolu şifreyle eşleşiyorsa
        if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
            // Oturum (Session) değişkenlerini oluşturuyoruz
            $_SESSION['oturum_acik'] = true;
            $_SESSION['danisan_id'] = $kullanici['id']; // Randevu alırken bu ID'yi kullanacağız
            $_SESSION['ad_soyad'] = $kullanici['ad_soyad']; // Ekranda adını göstermek için
            
            // Başarılı giriş, ana panele yönlendir
            header("Location: index.php");
            exit;
        } else {
            $hata = "E-posta adresi veya şifre hatalı!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sisteme Giriş | Diyetisyen Otomasyonu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-teal-50 font-sans h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-teal-100">
        
        <div class="text-center mb-8">
            <div class="bg-teal-100 text-teal-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Sisteme Giriş</h2>
            <p class="text-sm text-gray-500 mt-1">Randevularınızı yönetmek için giriş yapın</p>
        </div>

        <?php if(!empty($hata)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 rounded text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $hata; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">E-posta Adresi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="eposta" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="ornek@email.com" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Şifre</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-key"></i></span>
                    <input type="password" name="sifre" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" name="giris_yap" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 shadow-md">
                Giriş Yap <i class="fa-solid fa-arrow-right-to-bracket ml-2"></i>
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            Henüz hesabınız yok mu? <a href="kayit.php" class="text-teal-600 font-bold hover:underline">Hemen Kayıt Olun</a>
        </div>

    </div>

</body>
</html>