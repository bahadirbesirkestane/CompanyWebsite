# Hosting Kurulum ve Yayına Alma Rehberi

Bu doküman, `wordpress-tema/cikolata-makine` temasıyla çalışan bu WordPress sitesinin **gerçek bir hosting ortamında** (yerel Docker test ortamından çıkıp) nasıl yayına alınacağını anlatır: hangi hosting paketinin seçileceği, sunucuda hangi teknik ayarların olması gerektiği ve dosya/veritabanı taşıma (migration) adımları.

---

## 1) Özet — Gereksinim Tablosu

| Gereksinim | Minimum | Önerilen |
|---|---|---|
| PHP sürümü | 7.4 (temanın teknik alt sınırı) | **8.1 veya 8.2** |
| MySQL / MariaDB | MySQL 5.7 / MariaDB 10.3 | **MariaDB 10.11** veya MySQL 8.0 |
| `upload_max_filesize` | 8M (WP varsayılanı çoğu yerde 2M'dir) | **64M** |
| `post_max_size` | `upload_max_filesize`'dan büyük | **64M** |
| `memory_limit` (PHP) | 128M | **256M** |
| `max_execution_time` | 60 sn | **300 sn** |
| `max_input_vars` | 1000 | **3000** |
| Disk alanı | 2 GB | **10 GB+** (ürün fotoğrafları + PDF kataloglar zamanla büyür) |
| SSL sertifikası | Zorunlu | Let's Encrypt (ücretsiz, otomatik yenilenen) |
| Günlük yedekleme | Önerilir | Otomatik günlük + manuel "yayına almadan önce" yedeği |
| E-posta gönderimi | PHP `mail()` yeterli olabilir ama spam'e düşebilir | SMTP (cPanel e-posta hesabı veya harici SMTP) |

Bu proje **özel bir sunucu, Node.js, Docker, vb. gerektirmez** — standart, ucuz bir Linux paylaşımlı hosting (shared hosting) paketi teknik olarak yeterlidir. Önemli olan paketin yukarıdaki PHP ayarlarına **izin vermesi** (çoğu paylaşımlı hosting bunu değiştirmenize izin verir, aşağıda nasıl olduğu anlatılıyor).

---

## 2) Sunucu Ortamı — Detaylı Gereksinimler

### 2.1 PHP sürümü ve eklentiler (extensions)

- **PHP 8.1 veya 8.2** önerilir (tema PHP 7.4+ ile de çalışır ama WordPress'in kendisi ve ACF/Polylang gibi eklentiler artık 8.x ile test ediliyor, uzun vadede 7.4 desteği tamamen kesilecek).
- Standart bir WordPress hosting paketinde zaten bulunması gereken PHP eklentileri: `mysqli`, `gd` **veya** `imagick` (görsel yeniden boyutlandırma için — bu tema `add_image_size()` ile birden çok görsel boyutu üretir, bu eklenti olmadan görseller bozuk/eksik boyutlanır), `mbstring`, `curl`, `xml`, `zip`, `exif` (opsiyonel ama önerilir). Hemen hemen tüm "WordPress uyumlu" olarak pazarlanan paylaşımlı hosting paketleri bunları zaten kurulu getirir — kontrol etmeniz gerekmez, sadece paket açıklamasında "WordPress uyumlu" / "PHP 8.x destekli" yazdığından emin olun.

### 2.2 PHP `.ini` ayarları — neden önemli

- **`upload_max_filesize` / `post_max_size` (64M önerilir):** wp-admin'deki medya yükleme ekranında gördüğünüz "Maksimum yükleme boyutu" WordPress'in değil, **hosting'in PHP ayarının** sınırıdır. Varsayılan 2MB, gerçek ürün fotoğrafları ve özellikle **PDF kataloglar** için yetersizdir. Bu, hostinge geçtikten sonra karşılaşılacak ilk pratik sorundur — aşağıdaki §5.5'te nasıl yükseltileceği var.
- **`memory_limit` (256M önerilir):** ACF + Polylang + tema birlikte çalışırken, özellikle çoklu dil (4 dil) ve çok sayıda görsel/PDF olan sayfalarda 128M yetersiz kalıp "Allowed memory size exhausted" hatası verebilir.
- **`max_execution_time` (300 sn önerilir):** Büyük PDF yüklemelerinde veya eklenti güncellemelerinde varsayılan 30 sn'lik süre zaman aşımına neden olabilir.
- **`max_input_vars` (3000 önerilir):** Bu temada makine başına 8 ek görsel + teknik özellik metinleri + ACF grup alanları (hero slaytları, istatistikler) var; sayfa düzenleme ekranında POST edilen alan sayısı standart 1000 sınırını aşabilir, aşarsa **bazı alanlar sessizce kaydedilmez** (hata vermez, sadece o alan boş kalır) — bu yüzden bu ayar özellikle önemlidir.

### 2.3 Veritabanı

- **MariaDB 10.11** (yerel test ortamında kullanılan) veya en az **MySQL 5.7 / MariaDB 10.3** — WordPress'in resmi minimum gereksinimi. Neredeyse tüm hosting sağlayıcıları bunu karşılar.
- Karakter seti: `utf8mb4` (Türkçe/Rusça/İspanyolca karakterlerin doğru saklanması için — WordPress kurulumu bunu otomatik ayarlar, veritabanını elle oluştururken `utf8mb4_unicode_ci` collation seçin).

### 2.4 SSL Sertifikası (HTTPS)

Zorunlu kabul edin — hem Google sıralaması hem tarayıcı güven uyarıları hem de WhatsApp/harici link paylaşımlarının düzgün görünmesi için. Neredeyse tüm cPanel tabanlı hostingler **Let's Encrypt** sertifikasını ücretsiz ve otomatik yenilemeli sağlar (cPanel → SSL/TLS Status → "Yükle" ya da "AutoSSL çalıştır").

### 2.5 Cron (zamanlanmış görevler)

WordPress'in "WP-Cron" sistemi varsayılan olarak **siteye her ziyaret geldiğinde** tetiklenir — düşük-orta trafikli bir kurumsal/ürün sitesi için bu genelde yeterlidir, ekstra bir şey kurmanıza gerek yoktur. Trafik çok düşükse (örn. sadece B2B, nadiren ziyaret ediliyor) ve zamanlanmış yazı yayınlama gibi özellikler kullanılacaksa, hosting'in cPanel'inden gerçek bir "Cron Job" kurup (`wget -q -O /dev/null https://siteadresi.com/wp-cron.php?doing_wp_cron` komutunu her 15 dakikada çalıştırmak) WP-Cron'un ziyaretçiye bağımlı olmasını önleyebilirsiniz — bu proje için zorunlu değil, opsiyonel bir iyileştirme.

### 2.6 E-posta gönderimi

İletişim formu (Contact Form 7/WPForms) kurulduğunda formdan gelen e-postaların spam'e düşmemesi için hosting'in **SMTP** ile e-posta göndermesi önerilir (PHP'nin native `mail()` fonksiyonu birçok e-posta sağlayıcısı tarafından spam olarak işaretlenir). Çoğu cPanel hosting'i domain'inize özel bir e-posta hesabı (örn. `bilgi@firmaniz.com`) oluşturmanıza izin verir; bunu **WP Mail SMTP** gibi ücretsiz bir eklentiyle WordPress'e bağlamak, form e-postalarının müşterinin gelen kutusuna (spam'e değil) düşmesini sağlar.

---

## 3) Hangi Hosting Paketi Seçilmeli

### 3.1 Paylaşımlı hosting (shared hosting) yeterli mi?

**Evet.** Bu site; büyük bir e-ticaret veya yüksek trafikli bir platform değil, kurumsal + ürün katalog sitesidir. Standart bir "WordPress Hosting" veya "Linux Paylaşımlı Hosting" paketi teknik olarak fazlasıyla yeterlidir. Özel sunucu (VPS/Dedicated) veya bulut (AWS/Azure) gereksinimi **yoktur** — bu tür seçenekler gereksiz maliyet ve yönetim yükü getirir.

### 3.2 Paket seçerken nelere bakılmalı

- **"WordPress Hosting" veya "WordPress Uyumlu" etiketli paket** tercih edin — bu paketler genelde yukarıdaki §2'deki PHP ayarlarını zaten uygun getirir ve genelde 1-tık WordPress kurulumu (Softaculous vb.) sunar.
- **cPanel (veya benzeri bir kontrol paneli) içeren paket** tercih edin — dosya yöneticisi, phpMyAdmin, PHP ayarları, SSL kurulumu gibi işlemlerin hepsi cPanel üzerinden kolayca yapılır (bu rehberdeki adımların çoğu cPanel varsayımıyla yazıldı). cPanel olmayan (yalnızca FTP + destek bileti ile yönetilen) ucuz paketlerden kaçının, teknik olmayan biri için yönetimi zorlaşır.
- **NVMe SSD disk** sunan paketler tercih edilmeli — sayfa yükleme hızını doğrudan etkiler (SEO ve kullanıcı deneyimi için önemli), günümüzde çoğu güncel hosting paketi zaten bunu standart sunuyor.
- **LiteSpeed web sunucusu + LSCache** sunan paketler bonus — Apache'ye göre WordPress'te belirgin hız artışı sağlar, ekstra ücretsiz önbellekleme eklentisiyle (LiteSpeed Cache) birleşir.
- **Aylık trafik/bant genişliği "sınırsız" veya en az birkaç yüz GB** olan paket seçin — ürün fotoğrafları ve PDF kataloglar indirildikçe bant genişliği tüketimi artar.
- **Disk alanı en az 10GB** — çok sayıda yüksek çözünürlüklü ürün fotoğrafı + çok dilli içerik + PDF kataloglar birikince 2-5GB'lık "başlangıç" paketleri hızla dolabilir.
- **Günlük otomatik yedekleme** özelliği olan paket tercih edin (birçok orta-üst seviye paket bunu sunar) — yoksa ayrıca bir yedekleme eklentisi (UpdraftPlus gibi, ücretsiz) kurulmalı.
- **Sunucu konumu:** Site şu an Türkçe (varsayılan) + İngilizce/Rusça/İspanyolca dillerinde de yayında olacağı için, **Türkiye'de veya Avrupa'da** konumlanmış bir sunucu (Türk hosting sağlayıcılarının çoğu bunu sağlar) hem yerel hem yurt dışı ziyaretçiler için makul bir gecikme (latency) dengesi sunar. Gerçekten çok uluslu/yüksek trafikli bir yapıya evrilirse ileride bir CDN (Cloudflare — ücretsiz planı da var) eklemek, statik dosyaların (görsel/CSS/JS) dünya genelinde hızlı servis edilmesini sağlar; başlangıç için zorunlu değildir.

### 3.3 Türkiye'de yaygın örnek sağlayıcılar (bilgi amaçlı)

Aşağıdakiler örnek olarak yaygın kullanılan, cPanel + WordPress hosting sunan Türk sağlayıcılardır (öneri/onay niteliğinde değil, sadece "bu tür paketleri arayın" için referans):

- Doğan Hosting, Natro, Turhost, GoDaddy Türkiye, Hostinger, Ihost — bunların "WordPress Hosting" veya "Linux Paylaşımlı Hosting — Orta/Gelişmiş" paketleri genelde §2'deki gereksinimleri karşılar.

Karar verirken bu rehberdeki **teknik gereksinim tablosunu (§1)** referans alıp, paket açıklamasında bu değerlerin karşılandığını (veya sonradan değiştirilebilir olduğunu) kontrol edin — marka/fiyat karşılaştırması bu dokümanın kapsamı dışındadır.

---

## 4) Domain ve DNS

1. Domain'iniz (örn. `firmaniz.com`) hosting hesabınızla aynı sağlayıcıda değilse, domain'in DNS ayarlarından **A kaydını** hosting'in verdiği sunucu IP adresine yönlendirin (veya sağlayıcı "Nameserver" (isim sunucusu) değişikliği istiyorsa, hosting panelinde verilen nameserver'ları (örn. `ns1.hostingadi.com`, `ns2.hostingadi.com`) domain sağlayıcınızda güncelleyin).
2. DNS değişikliklerinin dünya genelinde yayılması (propagation) birkaç saatten 24-48 saate kadar sürebilir — bu süre boyunca site bazı bölgelerde eski, bazı bölgelerde yeni sunucudan görünebilir, normaldir.
3. `www.firmaniz.com` ve `firmaniz.com` adreslerinden hangisinin "asıl" (canonical) olacağına karar verip (genelde `www`'siz tercih edilir), diğerinin ona yönlendirilmesini (301 redirect) cPanel → "Domains" veya WordPress'in "Ayarlar → Genel" kısmındaki Site Adresi/WordPress Adresi alanlarından sağlayın.

---

## 5) Dosyaların ve Veritabanının Hostinge Taşınması (Migration)

Bu proje şu an yerel bir Docker ortamında (`local-dev/`) çalışıyor. Hostinge geçiş, özünde şu 4 parçanın taşınmasıdır: **(1) tema dosyaları, (2) eklentiler, (3) medya/yüklemeler, (4) veritabanı.**

### 5.1 Hosting'de WordPress'i kurun

Çoğu cPanel hosting'i "Softaculous" veya "Installatron" gibi bir 1-tık kurulum aracı sunar (cPanel ana ekranında "WordPress" ikonu). Bunu kullanarak temiz bir WordPress kurulumu yapın — domain'i, admin kullanıcı adı/şifresini burada belirlersiniz. (Elle kurmak isterseniz: veritabanı oluşturup wordpress.org'dan indirdiğiniz dosyaları FTP ile yükleyip `wp-config.php`'yi elle düzenlemeniz gerekir — 1-tık kurulum çok daha az hataya açıktır, mümkünse onu tercih edin.)

### 5.2 Tema dosyalarını yükleyin

1. `wordpress-tema/cikolata-makine/` klasörünü **zip'leyin** (klasörün kendisini değil, İÇİNDEKİ dosyaları seçip zip'lemeye dikkat edin — zip açıldığında doğrudan `style.css`, `functions.php` vb. görünmeli, `cikolata-makine/cikolata-makine/...` gibi çift klasör OLMAMALI).
2. wp-admin → **Görünüm → Temalar → Yeni Ekle → Tema Yükle** ile bu zip dosyasını yükleyip **Etkinleştir**'e basın. (Alternatif: cPanel Dosya Yöneticisi veya FTP ile zip'i `/public_html/wp-content/themes/` altına yükleyip oradan zip'i açıp (extract) etkinleştirebilirsiniz.)

### 5.3 Eklentileri kurun

Sırasıyla, wp-admin → **Eklentiler → Yeni Ekle** ekranından arayıp kurup etkinleştirin:

1. **Advanced Custom Fields** (ücretsiz sürüm — PRO değil, bu tema PRO'ya ihtiyaç duymayacak şekilde tasarlandı)
2. **Polylang** (çoklu dil için — kurulum sırası ve yapılandırma detayları için theme'in kendi `README.md` dosyasındaki "Çoklu Dil Kurulumu" bölümüne bakın)
3. İsteğe bağlı: **Contact Form 7** veya **WPForms** (iletişim formu için), **WP Mail SMTP** (form e-postalarının spam'e düşmemesi için, bkz. §2.6)

> Not: Yerel test ortamınızda zaten kurulu olan bu eklentilerin AYARLARINI (ACF alan tanımları kod içinde olduğu için otomatik gelir; ama Polylang'in dil listesi, menü atamaları, Dize Çevirisi metinleri gibi VERİTABANI'nda saklanan ayarları) aşağıdaki §5.4'teki veritabanı taşıma işlemiyle birlikte otomatik gelir — eklentiyi hostingde ayrıca sıfırdan yapılandırmanıza GEREK YOKTUR, veritabanını doğru taşırsanız.

### 5.4 Veritabanını taşıyın (en kritik adım)

**A) Yerel ortamdan dışa aktarma (export):**

```bash
docker compose exec wpcli wp db export /var/www/html/wp-content/site-export.sql --path=/var/www/html --allow-root
```

Bu, `local-dev/` altındaki (bind-mount sayesinde host makinenizde de erişilebilir) bir `.sql` dosyası oluşturur.

**B) Hosting'e içe aktarma (import):**

1. cPanel → **phpMyAdmin**'i açın, hosting'de WordPress kurulumu sırasında oluşturulan veritabanını seçin.
2. **İçe Aktar (Import)** sekmesinden yukarıdaki `.sql` dosyasını yükleyin. (Dosya çok büyükse — birkaç yüz MB'ı aşarsa — cPanel'in "phpMyAdmin dosya boyutu sınırı" engelleyebilir; bu durumda cPanel → "Uzak MySQL"/SSH erişiminiz varsa `mysql -u kullanici -p veritabani < site-export.sql` komutunu kullanın, ya da hosting desteğinden büyük dosya importu için yardım isteyin.)

**C) Adres (URL) değişikliğini düzeltin — search-replace:**

Veritabanı içinde `http://localhost:8090` gibi yerel adresler, sayfa içerikleri, ayarlar ve (Polylang dahil) birçok yerde hard-coded olarak geçer. Bunları gerçek domain'inizle değiştirmeniz **zorunludur**, yoksa site "localhost"a yönlenmeye çalışır ve çalışmaz:

- **En güvenli yöntem — wp-cli ile (hosting'de SSH/wp-cli erişiminiz varsa):**
  ```bash
  wp search-replace 'http://localhost:8090' 'https://www.firmaniz.com' --all-tables
  ```
- **SSH/wp-cli erişiminiz yoksa:** İçe aktarmadan ÖNCE, yerel makinenizde ücretsiz **"Better Search Replace"** eklentisiyle (veya wp-cli ile yerel ortamda) aynı search-replace işlemini yapıp, ONDAN SONRA `.sql` dosyasını export edip hostinge aktarın — bu, "önce taşı sonra düzelt" yerine "önce düzelt sonra taşı" mantığıdır ve teknik bilgisi az kullanıcılar için daha az hataya açıktır.
- Domain'i **`https://`** ile (SSL kurulduktan sonra) ve **www'li/www'siz** hangi hâliyle kalıcı kullanacaksanız o hâliyle yazın (bkz. §4.3) — sonradan değiştirmek yeni bir search-replace gerektirir.

### 5.5 Medya dosyalarını (yüklenen görseller/PDF'ler) taşıyın

Yerel ortamda `wp-content/uploads/` klasörü altında biriken ürün fotoğrafları ve PDF kataloglar, veritabanı export'unda YER ALMAZ (veritabanı sadece dosya YOLLARINI/referanslarını tutar, dosyaların kendisini değil). Bu klasörü ayrıca taşımanız gerekir:

1. Docker'da: `docker compose exec wordpress bash -c "tar czf /tmp/uploads.tar.gz -C /var/www/html/wp-content uploads"` ile sıkıştırıp `docker cp` komutuyla host makinenize çıkarın (veya doğrudan bind-mount edilmiş volume'den kopyalayın).
2. Hosting'de cPanel Dosya Yöneticisi veya FTP ile bu `uploads` klasörünün TÜM içeriğini `/public_html/wp-content/uploads/` altına yükleyin (mevcut boş `uploads` klasörünün üzerine, aynı klasör yapısını koruyarak — yıl/ay alt klasörleri dahil).

### 5.6 PHP yükleme limitlerini hostingde yükseltin

wp-admin → Medya ekranında "Maksimum yükleme boyutu" hâlâ düşükse (örn. 2M/8M), cPanel üzerinden yükseltin:

- **cPanel → "MultiPHP INI Editor"** (veya "Select PHP Version → Options" sekmesi): `upload_max_filesize`, `post_max_size`, `memory_limit`, `max_execution_time`, `max_input_vars` değerlerini §1'deki tabloya göre girip kaydedin.
- Bu seçenek yoksa, `public_html/.htaccess` dosyasının EN BAŞINA şunu eklemeyi deneyin (sadece "mod_php" çalıştıran hostinglerde işe yarar, LiteSpeed/PHP-FPM'de etkisi olmayabilir):
  ```
  php_value upload_max_filesize 64M
  php_value post_max_size 64M
  php_value memory_limit 256M
  php_value max_execution_time 300
  ```
- Hiçbiri işe yaramazsa, hosting desteğine bu değerleri yükseltmelerini yazın — standart bir istektir.

### 5.7 Kalıcı Bağlantıları (permalinks) tazeleyin

Taşıma sonrası, wp-admin → **Ayarlar → Kalıcı Bağlantılar**'ı açıp (hiçbir şeyi değiştirmeden) tekrar **Kaydet**'e basın — bu, kategori/ürün/katalog URL yapılarını ve Polylang'in dil URL öneklerini (`/en/`, `/ru/`, `/es/`) yeniden oluşturur. Bu adım atlanırsa taşıma sonrası bazı sayfalar 404 verebilir.

---

## 6) Yayına Alma Sonrası Kontrol Listesi

- [ ] Site `https://` ile açılıyor, tarayıcıda "güvenli değil" uyarısı yok (SSL doğru kurulu)
- [ ] `http://` ile girildiğinde otomatik `https://`'e yönleniyor (cPanel → "Force HTTPS Redirect" veya `.htaccess` üzerinden)
- [ ] Anasayfa, Ürünler, kategori, makine detay, Kataloglar, İletişim sayfaları TR'de sorunsuz açılıyor
- [ ] `/en/`, `/ru/`, `/es/` adresleri de sorunsuz açılıyor, dil değiştirici doğru çalışıyor
- [ ] Mobilde hamburger menü ve tüm sayfalar test edildi (gerçek telefonunuzdan bakın, sadece tarayıcı simülatöründen değil)
- [ ] wp-admin → Medya'dan gerçek boyutlu bir PDF (10-20MB) test amaçlı yüklenip başarılı olduğu doğrulandı
- [ ] İletişim formu test edilip e-postanın gerçekten geldiği (spam'e düşmediği) doğrulandı
- [ ] WhatsApp butonu doğru numarayı açıyor (Görünüm → Özelleştir → İletişim & WhatsApp)
- [ ] Otomatik yedekleme aktif (hosting panelinden veya UpdraftPlus gibi bir eklentiden)
- [ ] wp-admin şifresi güçlü bir şifreyle değiştirildi, gereksiz/varsayılan "admin" kullanıcı adı kullanılmıyor
- [ ] Google Search Console'a site eklendi, `sitemap.xml` (SEO eklentisi kurulacaksa — Yoast/RankMath — onun ürettiği sitemap) gönderildi

---

## 7) Bakım ve Güvenlik Önerileri (yayın sonrası, sürekli)

- **WordPress çekirdeği, tema ve eklentileri düzenli güncelleyin** — güvenlik yamaları çoğunlukla bu güncellemelerle gelir. Büyük bir güncellemeden önce mutlaka yedek alın.
- **Yedekleme:** En az günlük otomatik yedek (hosting panelinden veya UpdraftPlus) + büyük bir içerik/tasarım değişikliğinden hemen önce manuel bir yedek daha alma alışkanlığı edinin.
- **Güvenlik eklentisi (opsiyonel ama önerilir):** Wordfence veya benzeri ücretsiz bir güvenlik eklentisi, brute-force giriş denemelerine karşı temel bir koruma sağlar.
- **Performans:** Trafik arttıkça bir önbellekleme eklentisi (WP Super Cache, LiteSpeed Cache — hosting LiteSpeed ise) ve görsel sıkıştırma eklentisi (ShortPixel/Imagify ücretsiz katmanları) sayfa hızını korumaya yardımcı olur — başlangıç için zorunlu değildir.

---

## Özet — En Kısa Yol

1. cPanel + "WordPress Hosting" etiketli bir paylaşımlı hosting paketi satın alın (§3).
2. Domain'in DNS'ini hosting'e yönlendirin (§4).
3. 1-tık kurulumla (Softaculous) temiz bir WordPress kurun, SSL'i etkinleştirin.
4. Tema zip'ini yükleyip etkinleştirin, ACF + Polylang eklentilerini kurun (§5.2-5.3).
5. Yerel veritabanınızı export edip, adresleri (`localhost:8090` → gerçek domain) değiştirip hostinge import edin (§5.4).
6. `uploads/` klasörünü taşıyın, PHP yükleme limitlerini yükseltin, Kalıcı Bağlantıları tazeleyin (§5.5-5.7).
7. §6'daki kontrol listesini uygulayın.
