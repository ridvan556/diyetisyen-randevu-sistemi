<?php
session_start();
require_once 'baglanti.php';

// Güvenlik: Oturum açık değilse giriş sayfasına kışla
if (!isset($_SESSION['oturum_acik']) || $_SESSION['oturum_acik'] !== true) {
    header("Location: giris.php");
    exit;
}

$danisan_id = $_SESSION['danisan_id'];
$ad_soyad = $_SESSION['ad_soyad'];
$hata = "";
$basari = "";

// 1. YENİ RANDEVU ALMA İŞLEMİ (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['randevu_al'])) {
    $tarih = $_POST['tarih'];
    $saat = $_POST['saat'];
    $sikayet = trim($_POST['sikayet']);
    $bugun = date('Y-m-d');

    if (empty($tarih) || empty($saat)) {
        $hata = "Lütfen tarih ve saat seçiniz.";
    } elseif ($tarih < $bugun) {
        $hata = "Geçmiş bir tarihe randevu alamazsınız!";
    } else {
        $kontrol = $db->prepare("SELECT id FROM randevular WHERE randevu_tarihi = :tarih AND randevu_saati = :saat AND durum != 'Iptal'");
        $kontrol->execute(['tarih' => $tarih, 'saat' => $saat]);

        if ($kontrol->rowCount() > 0) {
            $hata = "Üzgünüz, seçtiğiniz tarih ve saatte diyetisyenimiz dolu. Lütfen başka bir saat seçin.";
        } else {
            $kaydet = $db->prepare("INSERT INTO randevular (danisan_id, randevu_tarihi, randevu_saati, sikayet) VALUES (:danisan_id, :tarih, :saat, :sikayet)");
            $kaydet->execute([
                'danisan_id' => $danisan_id,
                'tarih' => $tarih,
                'saat' => $saat,
                'sikayet' => $sikayet
            ]);
            $basari = "Randevunuz başarıyla oluşturuldu! Uzman onayından sonra kesinleşecektir.";
        }
    }
}

// 2. RANDEVU İPTAL İŞLEMİ (GET)
if (isset($_GET['iptal_id'])) {
    $iptal_id = $_GET['iptal_id'];
    $iptal = $db->prepare("UPDATE randevular SET durum = 'Iptal' WHERE id = :id AND danisan_id = :danisan_id AND durum = 'Bekliyor'");
    $iptal->execute(['id' => $iptal_id, 'danisan_id' => $danisan_id]);
    header("Location: index.php");
    exit;
}

// 3. KULLANICININ RANDEVULARINI ÇEKME (uzman_notu DAHİL)
$randevular = $db->prepare("SELECT * FROM randevular WHERE danisan_id = :danisan_id ORDER BY randevu_tarihi DESC, randevu_saati DESC");
$randevular->execute(['danisan_id' => $danisan_id]);
$randevu_listesi = $randevular->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danışan Paneli | Diyetisyen Otomasyonu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-teal-50 font-sans text-gray-800">

    <nav class="bg-teal-700 text-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-leaf text-teal-300"></i> Diyetisyen<span class="font-light">Sistemi</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-teal-100 text-sm hidden md:block">Hoş geldiniz, <b><?php echo htmlspecialchars($ad_soyad); ?></b></span>
                <a href="cikis.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-sm font-semibold transition shadow"><i class="fa-solid fa-power-off"></i> Çıkış Yap</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="grid md:grid-cols-3 gap-8">
            
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 border border-teal-100">
                    <h2 class="text-lg font-bold text-teal-800 mb-4 border-b border-teal-50 pb-2">
                        <i class="fa-solid fa-calendar-plus text-teal-500 mr-2"></i>Yeni Randevu Talebi
                    </h2>

                    <?php if(!empty($hata)): ?>
                        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded text-sm"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $hata; ?></div>
                    <?php endif; ?>
                    <?php if(!empty($basari)): ?>
                        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded text-sm"><i class="fa-solid fa-check"></i> <?php echo $basari; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Randevu Tarihi</label>
                            <input type="date" name="tarih" min="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Randevu Saati</label>
                            <select name="saat" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500 bg-white" required>
                                <option value="">-- Saat Seçin --</option>
                                <option value="09:00:00">09:00</option>
                                <option value="10:00:00">10:00</option>
                                <option value="11:00:00">11:00</option>
                                <option value="13:30:00">13:30</option>
                                <option value="14:30:00">14:30</option>
                                <option value="15:30:00">15:30</option>
                                <option value="16:30:00">16:30</option>
                            </select>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Şikayetiniz / Hedefiniz (İsteğe Bağlı)</label>
                            <textarea name="sikayet" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500 resize-none" placeholder="Örn: Kilo vermek istiyorum, insülin direncim var..."></textarea>
                        </div>

                        <button type="submit" name="randevu_al" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded transition">
                            Randevu Al
                        </button>
                    </form>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-md p-6 border border-teal-100">
                    <h2 class="text-lg font-bold text-teal-800 mb-4 border-b border-teal-50 pb-2">
                        <i class="fa-solid fa-clock-rotate-left text-teal-500 mr-2"></i>Randevu Geçmişim
                    </h2>

                    <div class="overflow-x-auto">
                        <?php if(empty($randevu_listesi)): ?>
                            <div class="text-center py-8 text-gray-500 text-sm">Henüz bir randevu almadınız.</div>
                        <?php else: ?>
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-teal-50 text-teal-800 uppercase text-xs leading-normal">
                                        <th class="py-3 px-4 font-semibold">Tarih</th>
                                        <th class="py-3 px-4 font-semibold">Saat</th>
                                        <th class="py-3 px-4 font-semibold w-2/5">Durum & Uzman Notu</th>
                                        <th class="py-3 px-4 font-semibold text-right">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm">
                                    <?php foreach($randevu_listesi as $randevu): 
                                        $formatli_tarih = date('d.m.Y', strtotime($randevu['randevu_tarihi']));
                                        $formatli_saat = date('H:i', strtotime($randevu['randevu_saati']));
                                    ?>
                                    <tr class="border-b border-gray-100 hover:bg-teal-50 transition align-top">
                                        <td class="py-4 px-4 font-semibold text-gray-800"><?php echo $formatli_tarih; ?></td>
                                        <td class="py-4 px-4 font-semibold text-teal-600"><?php echo $formatli_saat; ?></td>
                                        <td class="py-4 px-4">
                                            <?php if($randevu['durum'] == 'Bekliyor'): ?>
                                                <span class="bg-yellow-100 text-yellow-700 py-1 px-3 rounded-full text-xs font-bold inline-block mb-2"><i class="fa-regular fa-clock"></i> Onay Bekliyor</span>
                                            <?php elseif($randevu['durum'] == 'Onaylandi'): ?>
                                                <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-bold inline-block mb-2"><i class="fa-solid fa-check"></i> Onaylandı</span>
                                            <?php else: ?>
                                                <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-bold inline-block mb-2"><i class="fa-solid fa-xmark"></i> İptal Edildi</span>
                                            <?php endif; ?>

                                            <?php if(!empty($randevu['uzman_notu'])): ?>
                                                <div class="mt-2 bg-white border border-teal-200 p-2 rounded shadow-sm text-xs text-teal-800">
                                                    <b class="text-teal-700"><i class="fa-solid fa-user-doctor mr-1"></i>Uzman Notu:</b><br>
                                                    <span class="italic text-gray-600"><?php echo htmlspecialchars($randevu['uzman_notu']); ?></span>
                                                </div>
                                            <?php endif; ?>

                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <?php if($randevu['durum'] == 'Bekliyor'): ?>
                                                <a href="index.php?iptal_id=<?php echo $randevu['id']; ?>" onclick="return confirm('Randevunuzu iptal etmek istediğinize emin misiniz?');" class="text-red-500 hover:text-red-700 font-semibold text-xs border border-red-200 py-1 px-2 rounded transition shadow-sm bg-white">İptal Et</a>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-xs">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </body>
    </html>