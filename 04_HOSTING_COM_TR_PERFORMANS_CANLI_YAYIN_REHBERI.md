# hosting.com.tr "Performans" Paketi ile Canlı Yayına Alma Rehberi

Bu doküman, genel-amaçlı [02_HOSTING_KURULUM_REHBERI.md](02_HOSTING_KURULUM_REHBERI.md)'ndeki gereksinimleri **spesifik olarak hosting.com.tr'nin "Performans" paketine ve gerçek cPanel arayüzüne** uyarlar; ayrıca siteyi **demo değil, tam/canlı** olarak yayına almak için A'dan Z'ye yapılacakları, kimin (siz mi, ben mi) yapacağını ve şu an elimde hazır bekleyen yayın paketini anlatır.

**Araştırma tarihi:** 2026-08-26, hosting.com.tr'nin genel web hosting sayfası, bilgi bankası makaleleri ve blog rehberleri üzerinden.

---

## 1) Sonuç — Destekliyor mu?

**Evet, "Performans" paketi bu WordPress sitesini tam/canlı olarak yayınlamak için teknik olarak yeterli ve uygun.** Aşağıdaki tablo, projenin gerçek gereksinimlerini paketin doğrulanmış özellikleriyle karşılaştırıyor:

| Gereksinim | Projenin ihtiyacı | Performans paketi | Durum |
|---|---|---|---|
| Kontrol paneli | cPanel (Softaculous, phpMyAdmin, Dosya Yöneticisi) | cPanel **veya** Plesk (Linux Hosting ailesi → cPanel) | ✅ |
| PHP sürümü | 8.1/8.2 önerilen | cPanel "Select PHP Version" ile alan adı bazında seçilebilir (güncel EA-PHP sürümleri, 8.x dahil) | ✅ |
| Veritabanı | MariaDB/MySQL, `utf8mb4` | MariaDB/MySQL (Linux hosting standardı) | ✅ |
| Disk alanı | ~230 MB mevcut veri (§4), büyüyebilir | **Sınırsız SSD** | ✅ (fazlasıyla) |
| Aylık trafik | Düşük-orta (kurumsal site) | **Sınırsız** | ✅ |
| SSL | Zorunlu | **Ücretsiz SSL** (paketle birlikte) | ✅ |
| Tek-tık WordPress kurulumu | Softaculous vb. | **Softaculous Apps Installer** (cPanel → Yazılım) | ✅ |
| E-posta hesabı (form bildirimleri için) | En az 1 | **Sınırsız** | ✅ |
| Alan adı sayısı | 1 (belki +1 demo/test için) | **3 Site Barındırma** (addon domain/subdomain) | ✅ |
| CPU/RAM | Orta trafikli kurumsal site için yeterli | 2 Core CPU, 2 GB RAM | ✅ |
| Ücretsiz taşıma desteği | — | Hosting.com.tr'nin teknik ekibi ücretsiz taşıma sunuyor | ✅ (bonus, isterseniz bu rehberi hiç kullanmadan onlara da yaptırabilirsiniz) |
| SSH / WP-CLI (sunucu üzerinde) | Zorunlu değil, olsa kolaylaştırır | **Doğrulanamadı** — paylaşımlı hosting paketlerinde SSH genelde kapalı gelir, destek talebiyle açılabilir | ⚠️ Aşağıda §3'te detay |

**Kaynaklar (doğrulanan sayfalar):**
- [Web Hosting paketleri ve karşılaştırma](https://www.hosting.com.tr/hosting/)
- [Linux Hosting (cPanel altyapısı, MariaDB/MySQL, Softaculous)](https://www.hosting.com.tr/hosting/linux-hosting/)
- [cPanel Üzerinde PHP Sürümü Değiştirme — Select PHP Version](https://www.hosting.com.tr/bilgi-bankasi/cpanel-uzerinde-php-surumu-degistirme-ve-uygun-eklenti-secilmesi/)
- [WordPress Kurulumu — Adım Adım Resimli Rehber](https://www.hosting.com.tr/blog/wordpress-kurulum-nasil-yapilir/)

---

## 2) Şu an elimde hazır bekleyen "yayın paketi"

**2026-08-28'de TAMAMEN YENİDEN üretildi** (aşağıdaki tablo günceldir — 26 Ağustos'taki ilk paket artık silindi, Arapça 5. dil ve o tarihten sonraki tüm düzeltmeleri içermiyordu). Bu sefer paketten önce ayrıca 5 dilin (TR/EN/RU/ES/AR) TÜM içeriği — ürün görselleri, sayfa ACF alanları, arayüz metinleri, haberler — tek tek karşılaştırılıp eksik/tutarsız olanlar giderildi (57 üründe eksik olan 459 görsel bağlantısı, anasayfa/iletişim sayfalarındaki eksik alanlar, 15+ eksik arayüz metni çevirisi, TR-only kalmış tek haber yazısının 4 dile çevirisi dahil). `deployment-package/` klasöründe hazır (bu klasör `.gitignore`'a eklendi, git'e gitmiyor çünkü büyük ve yeniden üretilebilir):

| Dosya | İçerik | Boyut |
|---|---|---|
| `deployment-package/cikolata-makine-tema.zip` | Tema dosyaları (kök seviyede, çift-klasör yok — doğrudan wp-admin'den yüklenebilir) | ~93 KB |
| **`deployment-package/site-export-demofreme.sql`** | **`https://demofreme.com` için import'a tamamen hazır veritabanı** — bkz. aşağıda | ~7,1 MB |
| `deployment-package/site-export-localhost.sql` | Ham export (hâlâ `localhost:8090` içerir) — sadece referans, importta bunu DEĞİL üsttekini kullanın | ~7,0 MB |
| **`deployment-package/uploads.zip`** | Tüm ürün görselleri + PDF kataloglar — **cPanel Dosya Yöneticisi'nde kullanın** (§5.5) | ~232 MB |
| **`deployment-package/languages.zip`** | WordPress çekirdek dil paketleri (`es_ES`, `ru_RU` — bunlar olmadan İspanyolca/Rusça sayfalarda tarih ayı isimleri İngilizce kalıyordu, 2026-08-28'de fark edilip düzeltildi) — `wp-content/languages/` içine açılmalı | ~5 MB |

**Alan adı `https://demofreme.com` olarak onaylandı ve `site-export-demofreme.sql` zaten hazırlandı.** Bu, düz metin "bul-değiştir" ile DEĞİL, `wp search-replace` ile **serialization-safe** şekilde yapıldı — WordPress'te ACF alanları, tema ayarları gibi birçok veri PHP `serialize()` formatında saklanır ve içinde karakter-uzunluğu ön eki taşır (`s:21:"http://localhost:8090";` gibi); düz metin değiştirme bu uzunluğu bozup veriyi görünmez biçimde çöp haline getirirdi (URL 21 karakterden 22 karaktere çıktığı için tam olarak bu projede karşılaşılacak bir tuzaktı). `wp search-replace` bunu otomatik telafi ediyor — toplam **819 değişiklik** yapıldı. İşlem yerel geliştirme veritabanınıza hiç dokunmadan (`--export` bayrağıyla salt-okunur bir dışa aktarım) yapıldı — `localhost:8090` adresinden geliştirmeye kaldığınız yerden devam edebilirsiniz.

**Yeni adım — `languages.zip`:** cPanel Dosya Yöneticisi'nde `public_html/wp-content/` içine yükleyip Extract edin (§5.5'teki `uploads.zip` ile aynı yöntem) — `wp-content/languages/` klasörü oluşur/güncellenir. Bu adım atlanırsa site çalışır ama İspanyolca/Rusça sayfalarda tarih gösterimleri (haber tarihleri gibi) İngilizce ay ismiyle kalır.

`site-export-localhost.sql` artık sadece referans amaçlı duruyor, **importta mutlaka `site-export-demofreme.sql`'i kullanın**.

**Elimde olmayan / yapamayacağım şeyler:** Hosting satın alma işlemi bir ödeme/finansal işlemdir — bunu sizin adınıza yapamam, hosting.com.tr üzerinden paketi ve alan adını **siz** satın almanız gerekiyor. Ayrıca cPanel'e gerçek erişim (kullanıcı adı/şifre) olmadan dosya yükleme/veritabanı import gibi adımları benim tarafımdan otomatik yapmam mümkün değil — bunlar ya sizin cPanel arayüzünden elle yapmanız, ya da (isterseniz, güvenlik notunu okuduktan sonra — bkz. §6 sonu) bana FTP bilgilerini vererek yaptırmanız gereken adımlar.

---

## 3) SSH/WP-CLI hakkında önemli not

Araştırmam sırasında hosting.com.tr'nin paylaşımlı ("Linux Hosting") paketlerinde SSH erişiminin **varsayılan olarak açık geldiğini teyit edemedim** — bilgi bankalarında genel SSH kullanım makaleleri var ama bunlar VPS/sunucu ürünlerine de hitap ediyor olabilir. Sektör genelinde paylaşımlı hosting paketlerinde SSH çoğunlukla kapalıdır, destek talebiyle (canlı destek/ticket) açılabilir.

**Bu bir engel değil** — aşağıdaki tüm adımlar SSH olmadan, sadece **Softaculous + phpMyAdmin + Dosya Yöneticisi** ile yapılabilir (yerel Docker ortamınızda WP-CLI kullanıyor olmanız sadece geliştirme kolaylığıydı, üretimde şart değil). SSH isterseniz hosting.com.tr canlı destekten talep edebilirsiniz — WP-CLI ile bazı bakım işlemleri (örn. toplu arama-değiştirme, önbellek temizleme) daha hızlı olur ama zorunlu değildir.

---

## 4) Sizin yapmanız gerekenler (satın alma / karar aşaması)

Bunlar hesap, ödeme ve alan adı kararları olduğu için **sadece siz yapabilirsiniz**:

1. **Alan adı kararı:** Bu site için kullanılacak gerçek alan adı ne olacak? Zaten sahip olduğunuz bir domain mi var, yoksa hosting.com.tr'den yeni mi alacaksınız (paket 1+ yıllık alımda ücretsiz `.com.tr` hediye ediyor)? Bu, hem SQL dosyasının son hâlini hazırlamam hem de rehberin geri kalanı için gerekli — **bana söylemeniz gereken ilk şey bu.**
2. **Performans paketini satın alın:** [hosting.com.tr/hosting](https://www.hosting.com.tr/hosting/) sayfasından "Performans" paketini seçip ödemeyi tamamlayın. Kontrol panelini (cPanel) seçme ekranı çıkarsa **cPanel** seçin (bu rehber cPanel'e göre yazıldı; Plesk seçerseniz adımlar farklılaşır).
3. **Alan adını bağlayın:**
   - Domain'i hosting.com.tr'den yeni aldıysanız bu otomatik olur.
   - Mevcut bir domain'iniz varsa: domain'in kayıtlı olduğu yerden (Nic.tr, GoDaddy, başka bir sağlayıcı vb.) nameserver'ları hosting.com.tr'nin size vereceği değerlerle (genelde `ns1.hosting.com.tr` / `ns2.hosting.com.tr` gibi, sipariş sonrası e-postada gelir) güncelleyin. DNS yayılması birkaç saat–48 saat sürebilir.
4. **Hosting.com.tr Müşteri Paneli'nden cPanel'e ilk girişi yapın** ("Hostinglerim → Yönet → cPanel Giriş") ve cPanel kullanıcı adı/şifresini not edin.

Bu 4 adımı tamamlayıp bana **alan adını** verdiğinizde, ben SQL dosyasını finalize eder ve §5'teki adımları birlikte (veya isterseniz FTP bilgisiyle benim tarafımdan) yürütürüz.

---

## 5) cPanel'de A'dan Z'ye yapılacaklar

**Manuel kurulum yolunu izliyorsanız (önerilen, bkz. aşağıdaki kutu) gerçek sıra şu şekilde olur:** önce §5.1 (dosyalar + boş veritabanı), sonra doğrudan **§5.4 (veritabanı import)** — çünkü wp-admin'e girebilmek için önce gerçek admin hesabının veritabanından gelmesi gerekiyor — ardından §5.2 (tema) ve §5.3 (eklentiler) wp-admin üzerinden, en son §5.6-5.8.

> **Durum (2026-08-26):** `https://demofreme.com` kontrol edildi — WordPress çekirdeği artık gerçekten kurulu ve çalışıyor (varsayılan "Hello World" içeriğiyle, SSL sorunsuz). §5.1'in "mail() hatası" kutusundaki sorun bu seferki denemede yaşanmadı — WP Toolkit/Softaculous bir şekilde başarıyla tamamlanmış. **Kaldığımız yer: §5.4 (veritabanı import)'tan devam.**

### ⚠️ Bilinen sorun — WP Toolkit / Softaculous "mail() undefined" hatası

demofreme.com'da denendiğinde, cPanel'in "WordPress'i kur" penceresi (WP Toolkit) kurulumun son adımında (`wp_new_blog_notification` → `wp_mail()` → PHPMailer) şu hatayla çöktü:

```
Fatal error: Uncaught Error: Call to undefined function PHPMailer\PHPMailer\mail() ...
```

**Sebep:** Kurulum aracı arka planda `wp-cli`'yi bir PHP **CLI** süreci olarak çalıştırıyor ve bu sunucuda CLI PHP'de `mail()` fonksiyonu devre dışı bırakılmış (paylaşımlı hostinglerde spam script'lerini önlemek için sık yapılan bir sertleştirme). WordPress kurulumu bittikten hemen sonra "yeni site oluşturuldu" bildirim e-postası göndermeye çalışırken bu fonksiyon çağrısı patlıyor ve **tüm kurulum "başarısız" olarak işaretleniyor** — sitenizle/ayarlarınızla ilgili bir sorun değil. Kontrol: `https://demofreme.com` adresi kurulumdan sonra "Index of /" (boş dizin listesi) gösteriyorsa, WordPress dosyaları hiç yazılmamış demektir.

**Çözüm — otomatik kurucuyu tamamen atlayıp manuel kurulum (önerilen, zaten elimizdeki hazır veritabanıyla daha da kısa):**

### 5.1 WordPress'i manuel kurun (WP Toolkit/Softaculous'u atlayarak)

1. **Veritabanı oluşturun** — cPanel → **MySQL® Databases**: yeni bir veritabanı (örn. `demofrem_wp`) ve yeni bir kullanıcı oluşturup kullanıcıyı veritabanına **"ALL PRIVILEGES"** ile ekleyin. Veritabanı adı/kullanıcı adı/şifreyi not edin.
2. **WordPress çekirdek dosyalarını yükleyin** — [wordpress.org/latest.zip](https://wordpress.org/latest.zip) dosyasını indirin, cPanel **Dosya Yöneticisi**'nde `public_html` içine yükleyin, sağ tık → **Extract**. Çıkan `wordpress/` klasörünün İÇİNDEKİ tüm dosya/klasörleri seçip `public_html`'in **köküne** taşıyın (klasörün kendisini değil — yoksa site `demofreme.com/wordpress` adresinde açılır), ardından boş `wordpress/` klasörünü ve zip'i silin.
3. **`wp-config.php` oluşturun** — `public_html` içinde `wp-config-sample.php`'yi bulup **Rename** ile `wp-config.php` yapın, sağ tık → **Edit** ile açıp `DB_NAME`, `DB_USER`, `DB_PASSWORD` alanlarını 1. adımdaki bilgilerle doldurun (`DB_HOST` genelde `localhost` kalır). İsteğe bağlı ama önerilir: Authentication Unique Keys bölümünü [api.wordpress.org/secret-key/1.1/salt/](https://api.wordpress.org/secret-key/1.1/salt/) çıktısıyla değiştirin. Kaydedin.
4. Bu adımdan sonra **install.php'yi HİÇ ÇALIŞTIRMAYIN** — doğrudan §5.4'e (veritabanı import) geçin; import işlemi kurulum sihirbazının yapacağı her şeyi (+ gerçek içeriğimizi) zaten getirecek, böylece hatalı bildirim-e-postası adımına hiç uğramayız.

> **Alternatif (denemek isterseniz):** cPanel → "Yazılım" bölümünde WP Toolkit'ten ayrı bir **Softaculous Apps Installer** ikonu varsa, o farklı bir mekanizma kullandığı için sorunsuz çalışabilir — 2 dakikanızı alır, denemeye değer. Ama yukarıdaki manuel yöntem garantili ve zaten elimizdeki hazır veritabanıyla daha az adım gerektiriyor, bu yüzden birincil önerimiz bu.

### 5.2 Tema dosyalarını yükleyin

**Yöntem A (önerilen — wp-admin üzerinden, en az hata payı):**
1. §5.4'teki veritabanı import'unu tamamladıktan sonra, yerel ortamdaki gerçek admin hesabınızla (`admin`/`admin123`) wp-admin'e girin.
2. **Görünüm → Temalar → Yeni Ekle → Tema Yükle**.
3. Elimde hazır bekleyen `deployment-package/cikolata-makine-tema.zip` dosyasını seçip yükleyin, **Etkinleştir**'e basın.

**Yöntem B (cPanel Dosya Yöneticisi ile):**
1. cPanel → **Dosya Yöneticisi** → `public_html/wp-content/themes/` klasörüne gidin.
2. `cikolata-makine-tema.zip`'i yükleyin (Upload butonu), yüklendikten sonra sağ tık → **Extract**.
3. wp-admin → Görünüm → Temalar'dan "Cikolata Makine" temasını etkinleştirin.

### 5.3 Gerekli eklentileri kurun

wp-admin → **Eklentiler → Yeni Ekle** ekranından sırayla arayıp kurup **etkinleştirin** (§5.4'te veritabanını import ettiğinizde bu eklentilerin AYARLARI otomatik gelecek, şimdilik sadece eklentinin kendisinin kurulu/etkin olması yeterli):

1. **Advanced Custom Fields** (ücretsiz sürüm — PRO gerekmiyor)
2. **Polylang** (çoklu dil)
3. **Contact Form 7** (iletişim formu için — tema bunu kullanıyorsa)
4. Önerilir: **WP Mail SMTP** (form e-postalarının spam'e düşmemesi için — cPanel'de domain'inize özel bir e-posta hesabı oluşturup buna bağlayın)

### 5.4 Veritabanını import edin (en kritik adım)

0. **Tablo öneki kontrolü (import'tan ÖNCE, atlamayın):** cPanel → Dosya Yöneticisi → `public_html/wp-config.php`'yi Edit ile açın, `$table_prefix` satırını bulun. Bizim `.sql` dosyamız `wp_` önekiyle üretildi — eğer buradaki değer de `'wp_'` ise sorun yok. Farklıysa (örn. `'wpxx_'` gibi kurulum aracının rastgele ürettiği bir değer), **`'wp_'` olarak değiştirip kaydedin** (taze/varsayılan içerikli bir kurulum olduğu için bunu değiştirmek veri kaybettirmez).
1. `deployment-package/site-export-demofreme.sql` dosyasını kullanın — `https://demofreme.com` için zaten hazırlandı (bkz. §2).
2. cPanel → **phpMyAdmin**'i açın, §5.1'de oluşturulan/WP Toolkit'in otomatik oluşturduğu veritabanını (sol menüden) seçin — hangisi olduğundan emin değilseniz cPanel → "MySQL® Databases" ekranında veya `wp-config.php`'deki `DB_NAME` değerinden görebilirsiniz.
3. Üstteki **"İçe Aktar" (Import)** sekmesine geçin, `site-export-demofreme.sql` dosyasını seçip **Git (Go)**'ya basın. (Dosya ~6-7 MB olduğu için standart phpMyAdmin yükleme limitini rahatça karşılar, ekstra bir işlem gerekmez.) Bu, mevcut varsayılan 12 WordPress tablosunu (`DROP TABLE IF EXISTS` ile) silip yerine gerçek içeriğimizi kuracak.
4. Import bitince veritabanında gerçek admin hesabınız (yerel Docker ortamındakiyle aynı: `admin`/`admin123`) hazır olacak — `https://demofreme.com/wp-admin` adresinden bu bilgiyle giriş yapıp **hemen** Kullanıcılar → Profil'den şifreyi güçlü bir şifreyle değiştirin (bkz. §6 kontrol listesi). `admin123` canlıda asla kalmamalı.

   > **Not — veritabanı adı/kullanıcı adı uyuşmazlığı:** §5.1'de oluşturduğunuz veritabanı adı/kullanıcı adı, yerel Docker ortamınızdaki (`wordpress`/`wordpress`) ile birebir aynı olmak zorunda değil. Bu sorun değil — `.sql` dosyası tablo verisini taşır, bağlantı bilgilerini değil; WordPress zaten hostingdeki kendi `wp-config.php`'nizdeki bağlantı bilgilerini kullanır.

### 5.5 Medya dosyalarını (uploads) yükleyin

1. cPanel → **Dosya Yöneticisi** → `public_html/wp-content/` klasörüne gidin.
2. **`deployment-package/uploads.zip`** dosyasını (~232 MB) buraya yükleyin. Büyük dosya olduğu için **FTP** (FileZilla vb.) ile yüklemek tarayıcı üzerinden yüklemekten daha güvenilir olur; cPanel → "FTP Hesapları"ndan bir FTP kullanıcısı oluşturup FileZilla'ya bağlanabilirsiniz.
3. Yükleme bitince Dosya Yöneticisi'nde `uploads.zip`'e sağ tıklayıp **Extract**. Zip'in içi doğrudan `uploads/...` şeklinde yapılandırıldığı için, `wp-content/` içinde extract ettiğinizde otomatik olarak `wp-content/uploads/` oluşur (fresh kurulumun boş `uploads/` klasörü varsa üzerine birleşir) — **ekstra bir taşıma işlemi gerekmez.**
4. Çift `uploads/uploads/` klasörü OLUŞMADIĞINI doğrulayın (extract sonrası `wp-content/uploads/2024/...` gibi görmelisiniz, `wp-content/uploads/uploads/2024/...` değil).
5. **Aynı şekilde `deployment-package/languages.zip`'i de yükleyip Extract edin** (~5 MB, küçük) — İspanyolca/Rusça sayfalarda tarih gösteriminin doğru dilde çıkması için gerekli (bkz. §2).

### 5.6 PHP ayarlarını yükseltin

cPanel → **"Software"** altında **"Select PHP Version"** (doğrulanan gerçek yol — bkz. §1 kaynak linki):
1. **PHP Version** açılır menüsünden **8.1 veya 8.2** seçin, **Apply**.
2. Aynı ekranda **"Extensions"** (veya "Switch to PHP Options" ile eski arayüz) altında şu değerleri (varsayılanları düşükse) [02_HOSTING_KURULUM_REHBERI.md](02_HOSTING_KURULUM_REHBERI.md) §1'deki tabloya göre yükseltin: `upload_max_filesize` (64M), `post_max_size` (64M), `memory_limit` (256M), `max_execution_time` (300), `max_input_vars` (3000).
3. Bu ekranda gelmiyorsa/kilitliyse, aynı sayfanın altındaki eklenti listesinden `mysqli`, `gd`, `mbstring`, `curl`, `xml`, `zip`, `exif`'in **işaretli (aktif)** olduğunu doğrulayın (çoğu zaten varsayılan aktif gelir).

### 5.7 SSL kurun

cPanel ana ekranında **"SSL/TLS Status"** (veya "Let's Encrypt™ SSL") bölümüne gidin, alan adınızın yanındaki **"AutoSSL Çalıştır"** / **"Run AutoSSL"** butonuna basın — hosting.com.tr paket açıklamasında "Ücretsiz SSL" zaten dahil, birkaç dakika içinde sertifika otomatik kurulur. Ardından WordPress → **Ayarlar → Genel**'den Site Adresi ve WordPress Adresi'nin `https://` ile başladığını doğrulayın.

### 5.8 Kalıcı Bağlantıları tazeleyin

wp-admin → **Ayarlar → Kalıcı Bağlantılar** → hiçbir şeyi değiştirmeden tekrar **Kaydet**. Bu adım atlanırsa ürün/kategori sayfaları ve `/en/`, `/ru/`, `/es/` dil adresleri 404 verebilir.

---

## 6) Yayına alma sonrası kontrol listesi

- [ ] `https://` ile açılıyor, tarayıcıda güvenlik uyarısı yok
- [ ] `http://` girildiğinde otomatik `https://`'e yönleniyor
- [ ] Anasayfa, Ürünler, kategori, makine detay, Kataloglar, İletişim TR'de sorunsuz açılıyor
- [ ] `/en/`, `/ru/`, `/es/` adresleri açılıyor, dil değiştirici doğru çalışıyor
- [ ] Ürün görselleri ve PDF kataloglar (uploads klasörü doğru taşınmışsa) görünüyor/indirilebiliyor
- [ ] Mobilde gerçek bir telefondan test edildi (hamburger menü, mega menü)
- [ ] İletişim formu test edilip e-posta gerçekten geldi (spam'e düşmedi)
- [ ] WhatsApp butonu doğru numarayı açıyor
- [ ] **wp-admin şifresi Softaculous/import sonrası güçlü bir şifreyle değiştirildi**, "admin" kullanıcı adı kullanılmıyor
- [ ] cPanel → otomatik yedekleme aktif olduğu teyit edildi (hosting.com.tr haftalık yedek sunuyor — büyük bir değişiklikten önce manuel ek yedek alma alışkanlığı edinin)
- [ ] Google Search Console'a site eklendi

---

## 7) Sırada ne var

**Güncelleme (2026-08-28):** `https://demofreme.com` artık zaten canlıda ve çalışıyor — bu, artık bir "ilk kurulum" değil, **"eski canlı içeriği yeni yerel içerikle değiştirme"** işlemi. Bu senaryoya özel adım adım rehber (yedek alma, veritabanı/uploads/languages güncelleme, Kalıcı Bağlantıları tazeleme, kontrol listesi) için bkz. **[`05_CANLI_TASIMA_VE_GUNCELLEME_REHBERI.md`](05_CANLI_TASIMA_VE_GUNCELLEME_REHBERI.md) §3** — bu dosyadaki §5.1-5.8 hâlâ genel cPanel ekranlarının (phpMyAdmin, Dosya Yöneticisi, PHP sürümü, SSL) nasıl kullanılacağını anlatmak için referans olarak geçerli.

---

## 8) Canlıya geçtikten sonra: kod ve içerik güncelleme iş akışı

Site yayına girdikten sonra artık **iki ayrı ortam** var: yerel Docker (`localhost:8090`, geliştirme) ve canlı (`https://demofreme.com`, kendi veritabanı). Bu ikisi otomatik senkron DEĞİL — hangi değişikliğin nereden yapılacağını netleştirmek önemli.

### 8.1 Tema/kod değişiklikleri (PHP, CSS, JS)

1. Değişikliği **her zaman önce yerelde** (`localhost:8090`) yapıp test edin — canlıda doğrudan kod düzenlemeyin (hata ayıklaması zor, ziyaretçi hata görebilir).
2. Her zamanki gibi git'e commit edin (mevcut kural geçerli: **push etmeden önce mutlaka sorun**, bkz. `CLAUDE.md`).
3. Değişen dosyaları canlıya taşıyın — SSH/wp-cli erişiminiz olmadığı için (bkz. §1) en pratik yöntem:
   - **FTP (önerilen, günlük kullanım için):** cPanel → "FTP Hesapları"ndan bir FTP kullanıcısı oluşturup FileZilla gibi bir istemciyle bağlanın. FileZilla'nın "Dizin karşılaştırma" (directory comparison) özelliğiyle yerel `wordpress-tema/cikolata-makine/` ile sunucudaki `wp-content/themes/cikolata-makine/`'i karşılaştırıp sadece **değişen dosyaları** üzerine yükleyin.
   - **cPanel Dosya Yöneticisi:** Tek-iki dosyalık küçük düzeltmeler için, dosyayı bulup **Edit** ile içeriği doğrudan yapıştırabilirsiniz.
   - **Zip ile tam yeniden yükleme:** Çok sayıda dosya değiştiyse, temayı yeniden zip'lememi isteyin (artık doğru format — tek klasörle sarmalanmış — biliniyor), Görünüm → Temalar → Tema Yükle ile üzerine yazdırın.
4. **Cache temizliği:** LiteSpeed altyapısı var (§1) — LiteSpeed Cache eklentisi kuruluysa değişiklik sonrası **Purge All** yapın, yoksa eski dosya bir süre önbellekten servis edilmeye devam edebilir.

### 8.2 İçerik değişiklikleri (ürün ekleme/düzenleme, sayfa metinleri, ACF alanları)

Canlı site artık **kendi** veritabanına sahip. **Önerilen kural:** canlıya geçtikten sonra günlük içerik işlerini (yeni ürün, fiyat/metin güncelleme, kategori düzenleme) doğrudan `https://demofreme.com/wp-admin` üzerinden yapın; yerel Docker ortamı sadece **kod** geliştirme/testi için kalsın. Yerelde de içerik girip sonradan "senkronize" etmeye çalışmayın — iki veritabanı birbirinden hızla uzaklaşır ve elle birleştirmek pratik değildir.

### 8.3 Eklenti / WordPress çekirdek güncellemeleri

wp-admin → Panel → Güncellemeler'den canlıda bağımsız güncellenebilir (yerel ortamı etkilemez). **Büyük bir güncellemeden hemen önce cPanel'den manuel bir yedek alın** (§6'daki otomatik yedeğe ek olarak).

### 8.4 Her deploy öncesi kısa kontrol

- [ ] Değişiklik yerelde (`localhost:8090`) test edildi mi?
- [ ] git commit edildi mi?
- [ ] Veritabanını etkileyen bir değişiklikse cPanel'den güncel bir yedek alındı mı?
- [ ] Deploy sonrası canlıda gözle kontrol edildi mi (özellikle mobil + TR/EN/RU/ES 4 dil)?

### 8.5 Daha rahat bir uzun vadeli seçenek: cPanel Git Version Control

cPanel ana ekranında **"Git™ Version Control"** ikonu olup olmadığına bakın (güncel cPanel kurulumlarında standart gelir, henüz bu hesapta doğrulanmadı). Varsa, GitHub reponuzdan (`bahadirbesirkestane/CompanyWebsite`) SSH gerekmeden — cPanel arayüzünün kendi "Update from Remote" + "Deploy HEAD Commit" butonlarıyla — otomatik pull/deploy kurulabilir. Bunun için hangi dosyaların nereye kopyalanacağını tanımlayan bir `.cpanel.yml` betiği yazmak gerekir. Şu an için §8.1'deki FTP yöntemi yeterli; isterseniz ileride bunu birlikte kurup manuel dosya taşımayı tamamen ortadan kaldırabiliriz.
