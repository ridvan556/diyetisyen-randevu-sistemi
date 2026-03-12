<?php
session_start();
require_once 'baglanti.php';

$mesaj = "";

// 1. RANDEVU ONAYLAMA (POST) VE İPTAL ETME (GET) İŞLEMLERİ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['islem']) && $_POST['islem'] == 'onayla') {
    $randevu_id = $_POST['id'];
    $uzman_notu = trim($_POST['uzman_notu']); // Uzmanın yazdığı notu alıyoruz
    
    $guncelle = $db->prepare("UPDATE randevular SET durum = 'Onaylandi', uzman_notu = :not WHERE id = :id");
    $guncelle->execute(['not' => $uzman_notu, 'id' => $randevu_id]);
    $mesaj = "<div class='bg-green-100 text-green-700 p-3 mb-4 rounded text-sm font-bold'><i class='fa-solid fa-check mr-2'></i> Randevu onaylandı ve notunuz hastaya iletildi.</div>";
}

if (isset($_GET['islem']) && $_GET['islem'] == 'iptal' && isset($_GET['id'])) {
    $randevu_id = $_GET['id'];
    $guncelle = $db->prepare("UPDATE randevular SET durum = 'Iptal' WHERE id = :id");
    $guncelle->execute(['id' => $randevu_id]);
    $mesaj = "<div class='bg-red-100 text-red-700 p-3 mb-4 rounded text-sm font-bold'><i class='fa-solid fa-xmark mr-2'></i> Randevu iptal edildi.</div>";
}

// 2. INNER JOIN İLE TABLOLARI BİRLEŞTİRME (uzman_notu SÜTUNU EKLENDİ)
$sorgu_metni = "
    SELECT 
        r.id AS randevu_id, 
        r.randevu_tarihi, 
        r.randevu_saati, 
        r.durum, 
        r.sikayet, 
        r.uzman_notu,
        d.ad_soyad, 
        d.telefon 
    FROM randevular r
    INNER JOIN danisanlar d ON r.danisan_id = d.id
    ORDER BY r.randevu_tarihi ASC, r.randevu_saati ASC
";

$randevular = $db->query($sorgu_metni);
$tum_randevular = $randevular->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uzman Paneli | Diyetisyen Otomasyonu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans text-gray-800">

    <nav class="bg-slate-800 text-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-stethoscope text-teal-400"></i> Yönetim<span class="font-light">Paneli</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-slate-300 text-sm hidden md:block">Yetki: <b>Uzman Diyetisyen</b></span>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-700"><i class="fa-solid fa-calendar-check mr-2 text-teal-600"></i>Tüm Randevu Talepleri</h1>
        </div>

        <?php echo $mesaj; ?>

        <div class="bg-white rounded-xl shadow-md p-6 border border-slate-200">
            <div class="overflow-x-auto">
                <?php if(empty($tum_randevular)): ?>
                    <div class="text-center py-8 text-slate-500">Sistemde henüz bir randevu kaydı bulunmuyor.</div>
                <?php else: ?>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 uppercase text-xs leading-normal">
                                <th class="py-3 px-4 font-semibold w-2/5">Danışan Bilgisi & Notu</th>
                                <th class="py-3 px-4 font-semibold">Tarih / Saat</th>
                                <th class="py-3 px-4 font-semibold">Durum</th>
                                <th class="py-3 px-4 font-semibold text-right w-1/4">Aksiyon / Yanıtınız</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-700 text-sm">
                            <?php foreach($tum_randevular as $randevu): 
                                $formatli_tarih = date('d.m.Y', strtotime($randevu['randevu_tarihi']));
                                $formatli_saat = date('H:i', strtotime($randevu['randevu_saati']));
                            ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition align-top">
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-800 text-base"><?php echo htmlspecialchars($randevu['ad_soyad']); ?></div>
                                    <div class="text-xs text-slate-500 mb-2"><i class="fa-solid fa-phone text-slate-400 mr-1"></i><?php echo htmlspecialchars($randevu['telefon']); ?></div>
                                    
                                    <?php if(!empty($randevu['sikayet'])): ?>
                                        <div class="bg-slate-100 border-l-2 border-teal-400 p-2 rounded text-xs text-slate-600 italic">
                                            <i class="fa-solid fa-comment-medical text-teal-500 mr-1"></i> "<?php echo htmlspecialchars($randevu['sikayet']); ?>"
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-semibold"><?php echo $formatli_tarih; ?></div>
                                    <div class="text-xs text-teal-600 font-bold"><?php echo $formatli_saat; ?></div>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if($randevu['durum'] == 'Bekliyor'): ?>
                                        <span class="bg-yellow-100 text-yellow-700 py-1 px-3 rounded-full text-xs font-bold">Bekliyor</span>
                                    <?php elseif($randevu['durum'] == 'Onaylandi'): ?>
                                        <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-bold">Onaylandı</span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-bold">İptal Edildi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <?php if($randevu['durum'] == 'Bekliyor'): ?>
                                        <form method="POST" action="uzman.php" class="flex flex-col items-end gap-2">
                                            <input type="hidden" name="id" value="<?php echo $randevu['randevu_id']; ?>">
                                            <input type="hidden" name="islem" value="onayla">
                                            <textarea name="uzman_notu" rows="2" class="w-full text-xs p-2 border border-slate-300 rounded focus:outline-none focus:border-teal-500 resize-none" placeholder="Hastaya tavsiye veya diyet listesi notu yazın..."></textarea>
                                            <div class="flex gap-2">
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-2 rounded text-xs transition shadow-sm"><i class="fa-solid fa-check mr-1"></i>Onayla & Gönder</button>
                                                <a href="uzman.php?islem=iptal&id=<?php echo $randevu['randevu_id']; ?>" onclick="return confirm('Bu randevuyu iptal etmek istediğinize emin misiniz?');" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded text-xs transition shadow-sm inline-block"><i class="fa-solid fa-xmark mr-1"></i>Reddet</a>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <?php if(!empty($randevu['uzman_notu'])): ?>
                                            <div class="text-xs text-slate-600 text-left mb-2 bg-slate-100 p-2 border border-slate-200 rounded">
                                                <b class="text-teal-700"><i class="fa-solid fa-user-doctor mr-1"></i>Yanıtınız:</b><br>
                                                <?php echo htmlspecialchars($randevu['uzman_notu']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <span class="text-slate-400 text-xs italic">İşlem Yapıldı</span>
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

</body>
</html>