# FTP ile Yedekleme ve Güncelleme — A'dan Z'ye Rehber

Bu dosya, artık çalışan FTP erişimini (`public_html`'e kilitli hesap) üç iş için nasıl kullanacağınızı anlatıyor: **manuel yedekleme**, **otomatik yedekleme** ve **güncelleme (dosya taşıma)**. `05_CANLI_TASIMA_VE_GUNCELLEME_REHBERI.md` dosyasının YERİNE GEÇMİYOR — o dosya veritabanı/içerik taşımayı (phpMyAdmin) anlatıyor, bu dosya FTP ile dosya taşımayı ve yedeklemeyi anlatıyor. İkisi birlikte kullanılır.

**En önemli kural, baştan:** FTP sadece **dosyaları** (tema, resim, PDF) taşır. Ürün/sayfa/haber gibi **içerik** veritabanında durur — FTP ile asla taşınamaz. İçerik değişikliği yaptıysanız hâlâ `05_CANLI_TASIMA...` dosyasındaki phpMyAdmin export/import adımlarını kullanacaksınız. Bu rehber sadece dosya tarafını çözüyor.

---

## Önce kısa özet (TL;DR)

| İş | Yöntem | Otomatik mi? |
|---|---|---|
| Manuel tam yedek | FileZilla ile `public_html`'i bilgisayara indir + phpMyAdmin'den DB export | Hayır, elle |
| Otomatik yedek (ana yöntem) | UpdraftPlus eklentisi (zaten kurulu) → Google Drive'a otomatik | Evet, tamamen |
| Otomatik yedek (ek/yedek katman) | WinSCP + Windows Görev Zamanlayıcı → bilgisayara otomatik indirme | Evet, tamamen |
| Tema/kod güncelleme | FileZilla ile değişen dosyaları sürükle-bırak | Hayır, elle (kasıtlı — güvenlik için) |

---

## Bölüm 1 — Manuel Yedekleme

### 1.1 — Dosyaları indir (FileZilla ile)

1. FileZilla ile FTP hesabına bağlan (zaten kurulu — Site Manager'daki kayıtlı profili kullan).
2. Sol taraftaki **"Local site"** panelinde bilgisayarında bir yedek klasörü oluştur, örn. `C:\Yedekler\demofreme\2026-08-31\`.
3. Sağ taraftaki **"Remote site"** panelinde `public_html` içeriğinin tamamını seç (Ctrl+A).
4. Sağ tık → **"Download"**. Tüm site dosyaları (232+ MB olabilir, `uploads` klasörü büyük) bilgisayarına iner.

Bunu ayda bir veya büyük bir değişiklik öncesi/sonrası yapmak yeterli — her gün elle yapmana gerek yok, onun için Bölüm 2 var.

### 1.2 — Veritabanını indir (phpMyAdmin ile)

1. cPanel → **phpMyAdmin** → veritabanını seç.
2. **Export** → **Quick** → **Go**.
3. İnen `.sql` dosyasını aynı yedek klasörüne (`C:\Yedekler\demofreme\2026-08-31\`) koy.

Dosyalar + veritabanı aynı klasörde, aynı tarihte durduğu sürece bu, o anki sitenin tam bir "anlık görüntüsü"dür — bir şey ters giderse buradan geri dönebilirsin.

---

## Bölüm 2 — Otomatik Yedekleme

### 2.1 — Ana yöntem: UpdraftPlus (zaten kurulu)

Bunu zaten kurdun. Kısa hatırlatma/kontrol listesi:

- wp-admin → **Ayarlar → UpdraftPlus Backups → Settings**
- **Files backup schedule** ve **Database backup schedule** periyotları seçili mi kontrol et (öneri: DB haftalık, dosyalar ayda bir — içerik sık değişmiyor).
- **Remote storage: Google Drive** bağlı mı, "Backup Now" ile test ettiğinde dosya gerçekten Drive'a düşüyor mu kontrol et.
- Bu, **sunucu dışına** giden tek otomatik yedek — en kritik olan bu, çünkü sunucu tekrar ele geçirilirse sunucu-içi hiçbir yedek güvenli sayılmaz.

### 2.2 — Ek katman: WinSCP ile bilgisayarına otomatik indirme (isteğe bağlı)

UpdraftPlus tek başına yeterli ama istersen ikinci, bağımsız bir yedek katmanı daha kurabiliriz — bilgisayarın her hafta otomatik olarak `public_html`'i kendi üzerine indirir. Bunun için FileZilla değil, **WinSCP** kullanacağız çünkü WinSCP script/otomasyon destekliyor, FileZilla desteklemiyor.

**Adım 1 — WinSCP'yi kur**

[winscp.net](https://winscp.net/eng/download.php) üzerinden ücretsiz indir, kur.

**Adım 2 — Script dosyasını oluştur**

Not defteri ile `C:\FTP-Yedek\yedek-script.txt` adında bir dosya oluştur, içine:

```
option batch abort
option confirm off
open ftpes://FTP_KULLANICI_ADIN:FTP_SIFREN@demofreme.com/ -certificate=*
synchronize local "C:\FTP-Yedek\public_html" /
close
exit
```

`FTP_KULLANICI_ADIN` ve `FTP_SIFREN` yerine gerçek FTP bilgilerini yaz. **Önemli:** Bu dosyada şifre düz metin olarak duruyor — bu yüzden `C:\FTP-Yedek` klasörünü kimseyle paylaşma/bulut senkronizasyonuna (OneDrive vb.) sokma.

**Adım 3 — Çalıştıran bir .bat dosyası yap**

Aynı klasörde `yedek-al.bat` adında bir dosya oluştur:

```bat
@echo off
"C:\Program Files (x86)\WinSCP\WinSCP.com" /script="C:\FTP-Yedek\yedek-script.txt" /log="C:\FTP-Yedek\son-calisma.log"
```

(WinSCP farklı bir klasöre kurulduysa yolu ona göre düzelt.)

Bu `.bat` dosyasına çift tıklayınca `public_html`'in tamamı `C:\FTP-Yedek\public_html` klasörüne iner/güncellenir — önce elle bir kere çift tıklayıp çalıştığını doğrula.

**Adım 4 — Windows Görev Zamanlayıcı ile otomatikleştir**

1. Başlat menüsünde **"Görev Zamanlayıcı"** (Task Scheduler) ara, aç.
2. Sağdan **"Temel Görev Oluştur"**.
3. İsim: `Demofreme FTP Yedek`.
4. Tetikleyici: **Haftalık**, istediğin gün/saat (örn. Pazar 03:00 — trafiğin az olduğu bir saat).
5. Eylem: **"Bir program başlat"** → Program/script alanına `C:\FTP-Yedek\yedek-al.bat` yolunu gir.
6. Bitir.

Bilgisayarın o saatte açık/uyanık olması gerektiğini unutma — bu yöntem sunucu tarafında değil, senin bilgisayarında çalışıyor. Sürekli açık bir bilgisayar/sunucu yoksa bu adımı atlayıp sadece 2.1'deki UpdraftPlus'a güven, o zaten sunucu tarafında çalışır ve bilgisayarının açık olmasını gerektirmez.

---

## Bölüm 3 — FTP ile Güncelleme (kod/tema dosyaları)

### 3.1 — Hangi dosyalar değişti?

Yerelde (`localhost:8090`, Docker) bir değişiklik yaptıktan sonra bana "hangi dosyalar değişti" diye sor — `git status` ile listelerim. Sadece o dosyaları taşıman yeterli, tüm temayı yeniden yüklemene gerek yok.

### 3.2 — FileZilla ile taşı

1. FileZilla'da sol tarafta (Local site) bilgisayarındaki `wordpress-tema/cikolata-makine/` klasörüne git.
2. Sağ tarafta (Remote site) `public_html/wp-content/themes/cikolata-makine/` klasörüne git.
3. Değişen dosyayı/dosyaları sol taraftan bulup sağa sürükle-bırak.
4. "Target file already exists" sorusu çıkarsa **"Overwrite"** (üzerine yaz) seç.

### 3.3 — Yarı-otomatik: tek tıkla senkronizasyon (isteğe bağlı)

Her seferinde dosya dosya sürüklemek istemiyorsan, WinSCP'nin **"Synchronize"** özelliğini **elle tetiklenen** bir script olarak kurabiliriz (Bölüm 2.2'deki gibi ama Görev Zamanlayıcı'ya BAĞLAMADAN — bilinçli olarak sadece çift tıklayınca çalışsın istiyoruz, otomatik/zamanlanmış olmasın, çünkü test aşamasındaki bir değişikliği yanlışlıkla canlıya atma riski var):

`C:\FTP-Guncelle\guncelle-script.txt`:
```
option batch abort
option confirm off
open ftpes://FTP_KULLANICI_ADIN:FTP_SIFREN@demofreme.com/
synchronize remote "C:\...\wordpress-tema\cikolata-makine" /wp-content/themes/cikolata-makine
close
exit
```

`C:\FTP-Guncelle\guncelle.bat`:
```bat
@echo off
"C:\Program Files (x86)\WinSCP\WinSCP.com" /script="C:\FTP-Guncelle\guncelle-script.txt"
pause
```

Yerelde değişikliği test edip onayladıktan SONRA bu `.bat`'a çift tıkla — sadece değişen dosyalar karşılaştırılıp yüklenir, elle sürüklemekten daha hızlı. **Bunu Görev Zamanlayıcı'ya eklemiyoruz** — güncelleme her zaman senin bilinçli onayınla, elle tetiklenmeli.

### 3.4 — Cache temizle

Değişiklik siteye yansımıyorsa: cPanel'de bir **LiteSpeed Cache** ikonu varsa **"Purge All"** yap. Ayrıca `04_HOSTING_COM_TR...` rehberinde bahsedilen PHP yeniden başlatma (Select PHP Version'da sürüm değiştirip geri alma) hâlâ geçerli bir çözüm.

### 3.5 — İçerik/veritabanı değişikliği mi yaptın?

Bu FTP akışı **çalışmaz** — `05_CANLI_TASIMA_VE_GUNCELLEME_REHBERI.md` dosyasındaki phpMyAdmin export/import adımlarına dön.

---

## Kontrol Listesi

- [ ] FTP hesabı `public_html`'e kilitli şekilde çalışıyor (bağlandın, doğruladık)
- [ ] En az bir kere elle tam yedek alındı (Bölüm 1)
- [ ] UpdraftPlus'ta zamanlama + Google Drive bağlantısı test edildi (Bölüm 2.1)
- [ ] (İsteğe bağlı) WinSCP otomatik yedek scripti kurulup Görev Zamanlayıcı'ya eklendi (Bölüm 2.2)
- [ ] Bir tema dosyası değişikliği FTP ile başarıyla taşınıp test edildi (Bölüm 3.2)

---

## Not — daha temiz bir alternatif: Git Version Control

Eğer cPanel'de **"Git™ Version Control"** özelliği varsa (kontrol etmeni önermiştim, henüz cevap gelmedi), tema güncellemeleri için FTP'den daha temiz bir yol kurulabilir — GitHub'a push edip cPanel'de "Deploy" butonuna basmak yeterli olur, dosya sürüklemeye hiç gerek kalmaz. İstersen bunu ayrıca kontrol edip birlikte kuralım.
