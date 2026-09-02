# Hosting Devir/Teslim — Sorumluluk Ayrımı, Olası Sorular ve Mimari Özeti

Bu doküman, hosting yönetimini **sizin değil, başka birinin** yapacağı bir senaryo için hazırlandı. Üç soruyu cevaplıyor:

1. Hangi güncellemeler **hosting tarafında** (yalnızca hosting yöneticisinin erişebileceği sunucu/panel ayarları) yapılmak ZORUNDA, hangileri **wp-admin veya tema dosyası** üzerinden (siz veya ben) yapılabiliyor?
2. Hosting aracısı size hangi soruları sorabilir, cevapları ne olmalı?
3. Bu siteyi hiç görmemiş bir hosting yöneticisine mimariyi/teknolojiyi/bağımlılıkları nasıl anlatırsınız?

**Bu belge, mevcut 4 hosting dokümanının (aşağıya bakın) YERİNE GEÇMİYOR** — onlar "nasıl kurulur/taşınır" adımlarını detaylı anlatıyor, bu belge ise "kim ne yapacak" sorumluluk çizgisini ve bir hosting görevlisiyle ilk konuşmada çıkabilecek soruları netleştiriyor. Teknik detay/adım adım ekran görüntüsü gerekiyorsa şu dosyalara bakın:

| Dosya | Ne zaman bakılır |
|---|---|
| [02_HOSTING_KURULUM_REHBERI.md](02_HOSTING_KURULUM_REHBERI.md) | Genel sunucu gereksinim tablosu, hangi hosting paketi seçilmeli |
| [04_HOSTING_COM_TR_PERFORMANS_CANLI_YAYIN_REHBERI.md](04_HOSTING_COM_TR_PERFORMANS_CANLI_YAYIN_REHBERI.md) | cPanel'de adım adım kurulum (spesifik bir sağlayıcıya göre) |
| [05_CANLI_TASIMA_VE_GUNCELLEME_REHBERI.md](05_CANLI_TASIMA_VE_GUNCELLEME_REHBERI.md) | Dosya/medya taşıma, FTP'siz yükleme, siz kendiniz güncelleme yaparken |
| [06_FTP_YEDEKLEME_VE_GUNCELLEME_REHBERI.md](06_FTP_YEDEKLEME_VE_GUNCELLEME_REHBERI.md) | Düzenli yedekleme ve FTP ile güncelleme rutini |

---

## 1) Sorumluluk Ayrımı — Kim Ne Yapabilir

Üç ayrı kategori var — ikisi net, üçüncüsü **sizin hosting yöneticisiyle netleştirmeniz gereken gri alan**:

### A) Hosting tarafında yapılması ZORUNLU (sadece hosting yöneticisi erişebilir)

Bunlar sunucu/panel seviyesinde ayarlar — tema kodundan veya wp-admin'den değiştirilemez:

- **PHP sürümü seçimi** (cPanel "Select PHP Version" veya eşdeğeri)
- **PHP `.ini` ayarları**: `upload_max_filesize`, `post_max_size`, `memory_limit`, `max_execution_time`, `max_input_vars` (bkz. §3, tam değerler)
- **SSL sertifikası** kurulumu/yenilenmesi
- **Domain/DNS** kayıtları (A/CNAME, e-posta için MX/SPF/DKIM)
- **Veritabanı** oluşturma ve erişim bilgileri (ilk kurulumda)
- **Gerçek cron job** kurulumu (opsiyonel iyileştirme, sunucu `crontab`'ı gerektirir)
- **E-posta hesabı / SMTP relay** sağlanması (iletişim formu bildirimlerinin gitmesi için)
- **Sunucu seviyesi yedekleme** politikası (günlük otomatik yedek)
- **Disk alanı / kaynak (RAM-CPU)** tahsisi
- **İlk taşıma (migration)**: veritabanı + dosyaların sunucuya yüklenmesi, ardından "Kalıcı Bağlantılar" (permalink) ayarının bir kez kaydedilmesi (bkz. §3 — bu site özel URL yapıları kullanıyor, bu adım atlanırsa ürün/kategori sayfaları 404 verir)

### B) wp-admin üzerinden yapılabilir (siz veya ben, hosting yöneticisine ihtiyaç yok)

- Tüm **içerik**: ürün, kategori, sayfa, haber, katalog, çeviri, Özelleştir ayarları
- **Eklenti güncellemeleri** (ACF/Polylang/Contact Form 7/WP Mail SMTP) — admin panelden "Güncelle" ile yapılır; TEK istisna, yeni eklenti sürümü daha yüksek bir PHP sürümü isterse, o zaman PHP yükseltmesi için hosting tarafına ihtiyaç doğar
- WP Mail SMTP'nin **kendi ayar ekranı** — hangi SMTP hesabının kullanılacağı buradan girilir, ama hesabın kendisini (kullanıcı adı/şifre) hosting yöneticisinden almanız gerekir

### C) Gri alan — netleştirilmesi gereken: dosya erişimi (tema kodu)

Bu, üzerinde en çok kafa karışıklığı çıkan nokta, o yüzden ayrı vurguluyorum: **"hosting yönetimi" ile "dosyalara erişim" AYNI ŞEY DEĞİL.**

Tema (`wordpress-tema/cikolata-makine`) tamamen özel yazılmış kod — bir eklenti gibi "tek tıkla güncelle" butonu yok. Yeni bir kod değişikliğini canlıya taşımak için dosyaların sunucuya yüklenmesi gerekiyor. Bunu iki şekilde yapabilirsiniz:

- **wp-admin → Görünüm → Tema Dosya Düzenleyici**: teorik olarak mümkün ama **önermiyorum** — tek bir yazım hatası anında tüm siteyi beyaz ekrana düşürebilir, geri alma/yedek mekanizması yok.
- **FTP/SFTP (önerilen)**: hosting yöneticisinden **sadece `wp-content/themes/` klasörüne erişimi olan, sınırlı bir FTP/SFTP hesabı** isteyin — bu, tam cPanel/sunucu şifresi vermek anlamına gelmez, hosting yöneticisi hesap yönetimini/faturalandırmayı kendi üstünde tutarken size sadece dosya yükleme izni tanımlayabilir.

**Hosting yöneticisine sormanız gereken net soru şu:** *"Hosting yönetimini siz yapacaksınız, ama tema dosyalarını güncellemek için bize ayrı bir FTP/SFTP erişimi tanımlayabilir misiniz?"* Bu cevaplanmadan ilerlemeyin — aksi halde her küçük tasarım/metin-dışı değişiklikte hosting yöneticisine bağımlı kalırsınız.

---

## 2) Hosting Aracısının Sorabileceği Sorular ve Cevapları

### Genel / Platform
**S: Hangi CMS kullanılıyor, hazır bir şablon mu?**
C: WordPress — ama hazır bir tema değil, sıfırdan yazılmış özel (custom) PHP tema. Elementor/Divi gibi bir sayfa oluşturucu kullanılmıyor.

**S: E-ticaret/ödeme altyapısı var mı?**
C: Hayır. Kurumsal ürün katalog sitesi — satış online yapılmıyor, ziyaretçi iletişim formu veya WhatsApp üzerinden teklif istiyor.

### Sunucu / PHP
**S: Hangi PHP sürümü gerekiyor?**
C: **PHP 8.2** önerilir (biz yerel geliştirmede bunu kullanıyoruz), minimum 8.1 kabul edilebilir. 7.4 teknik olarak çalışır ama önerilmez — WordPress çekirdeği ve eklentiler yakında 7.4 desteğini tamamen kesecek.

**S: Hangi PHP eklentileri (extension) gerekli?**
C: Standart bir "WordPress uyumlu" hosting paketinde zaten bulunanlar: `mysqli`, `gd` (görsel yeniden boyutlandırma), `mbstring`, `curl`, `xml`, `zip`. Ekstra bir şey kurulması gerekmiyor.

**S: PHP `.ini` limitleri ne olmalı?**
C: `memory_limit` **256M**, `upload_max_filesize`/`post_max_size` en az **64M** (ürün PDF katalogları ve yüksek çözünürlüklü fotoğraflar var), `max_execution_time` **300 sn**, `max_input_vars` **3000** (ürün düzenleme ekranında çok sayıda alan aynı anda kaydediliyor, düşük kalırsa bazı alanlar sessizce kaydedilmez).

### Veritabanı
**S: Hangi veritabanı motoru/sürümü?**
C: MySQL 5.7+ veya MariaDB 10.3+ — biz yerelde MariaDB 10.11 kullanıyoruz. Karakter seti **`utf8mb4`** olmalı (Türkçe/Rusça/Arapça karakterlerin doğru saklanması için).

### Çoklu Dil
**S: Kaç dil var, ayrı kurulumlar mı, subdomain mi?**
C: Tek WordPress kurulumu, tek veritabanı. **Polylang** eklentisiyle 5 dil (TR/EN/RU/ES/AR) aynı kurulum içinde yönetiliyor, URL yapısı `/en/` `/ru/` `/es/` `/ar/` ön ekleriyle (subdomain veya ayrı kurulum DEĞİL). Arapça sağdan-sola (RTL) düzende render ediliyor — bu da tema kodunda halledilmiş, sunucu tarafında ek bir ayar gerekmiyor.

### E-posta
**S: İletişim formu e-postaları nasıl gidiyor, ne gerekiyor?**
C: Contact Form 7 + WP Mail SMTP eklentisi kurulu. Güvenilir teslimat için gerçek bir **SMTP hesabı** gerekiyor (PHP'nin yerleşik `mail()` fonksiyonu çoğu sağlayıcıda spam'e düşer). Hosting yöneticisinden bir e-posta hesabı (örn. `bilgi@domain.com`) + SMTP bağlantı bilgileri (sunucu adresi, port, kullanıcı adı, şifre) istenmeli; bunlar WP Mail SMTP'nin ayar ekranından bağlanır. Not: form gönderimleri ayrıca **Flamingo** eklentisiyle wp-admin içinde de yedekli tutuluyor — e-posta gitmese bile mesaj kaybolmaz, admin panelden görülebilir.

### Cron (Zamanlanmış Görevler)
**S: Cron job kurmak gerekiyor mu?**
C: Zorunlu değil. WordPress'in varsayılan "ziyaretçi tetiklemeli" sahte cron'u, düşük-orta trafikli bu site için yeterli. İsteğe bağlı iyileştirme: gerçek bir sunucu `crontab` girişiyle `wp-cron.php`'yi 15 dakikada bir tetiklemek (özellikle e-posta kuyruğunun -WP Mail SMTP'nin kullandığı Action Scheduler- zamanında işlenmesi için faydalı olur).

### SSL / Domain
**S: SSL gerekli mi, kim sağlıyor?**
C: Evet, zorunlu kabul edin. Neredeyse tüm cPanel tabanlı hostingler Let's Encrypt'i ücretsiz ve otomatik yenilemeli sağlıyor — kurulumu hosting tarafında yapılmalı.

### Dosya Erişimi
**S: Tema/kod güncellemelerini kim yapacak, nasıl erişim gerekiyor?**
C: Kod tarafı bizim tarafımızda hazırlanıp yerel ortamda test ediliyor. Canlıya almak için ya (a) hazırladığımız dosyaları size iletip sizin yüklemenizi ya da (b) bize **sadece `wp-content/themes/` klasörüne erişimi olan sınırlı bir FTP/SFTP hesabı** tanımlamanızı istiyoruz (bkz. §1-C).

### Taşıma (Migration)
**S: Site nasıl taşınacak, mevcut/eski site ne olacak?**
C: Bu tamamen yeni ve bağımsız bir WordPress kurulumu — eski sitenin veritabanı/dosyalarıyla hiçbir bağlantısı yok. Taşıma paketi (tema dosyaları + veritabanı export'u + medya dosyaları) tarafımızdan hazırlanıp verilecek; ardından domain, bu yeni kuruluma yönlendirilecek (DNS ayarı hosting tarafında yapılmalı). Taşıma sonrası **"Ayarlar → Kalıcı Bağlantılar → Kaydet"** adımı bir kez çalıştırılmalı (bkz. §3).

### Yedekleme
**S: Yedekleme kim tarafından yapılıyor?**
C: Hosting'in otomatik günlük yedeklemesi yeterli — içerik sık güncellendiği için günlük yedek öneriyoruz. Büyük bir güncelleme öncesi bizim tarafımızdan ayrıca "önce manuel yedek alın" talebi gelebilir.

### Trafik / Performans
**S: Beklenen trafik nedir, önbellekleme (cache) gerekiyor mu?**
C: Kurumsal/B2B ürün sitesi — yüksek trafik beklenmiyor. Özel bir CDN/önbellekleme sistemi şart değil; hosting paketinde hazır geliyorsa (örn. LiteSpeed Cache) kullanılması faydalı olur ama zorunlu değil.

### Güvenlik
**S: Dikkat edilmesi gereken bir ayar var mı?**
C: `WP_DEBUG` production'da **kapalı (false)** olmalı — bu genelde WordPress'in standart kurulumunda zaten varsayılan durumdur, sadece bizim yerel geliştirme ortamımızda hata ayıklamak için açık. Kurulumdan sonra bir kez kontrol edilmesi yeterli.

---

## 3) Mimari / Teknoloji / Bağımlılıklar — Hosting Yöneticisine Anlatım Metni

Aşağıdaki metni hosting yöneticisiyle ilk konuşmada olduğu gibi okuyabilir/iletebilirsiniz:

> **Ne bu site:** Standart bir WordPress kurulumu. Hazır bir tema/şablon (Divi, Astra vb.) veya bir sayfa oluşturucu (Elementor vb.) kullanılmıyor — tamamen özel yazılmış bir tema var (`wordpress-tema/cikolata-makine` klasörü). Bu, "WordPress'i biliyorsanız bu siteyi de yönetebilirsiniz" anlamına gelir — sunucu tarafında sıradışı bir şey yok, sadece kod tarafı elle yazılmış.
>
> **Kullanılan eklentiler ve neden gerekli:**
> - **Advanced Custom Fields (ücretsiz sürüm)** — ürün/sayfa alanlarının admin panelinden düzenlenebilmesini sağlıyor (teknik özellikler, PDF, görsel galerisi vb.).
> - **Polylang** — 5 dilli (TR/EN/RU/ES/AR) içerik yönetimi. Tek kurulum, tek veritabanı; her dil `/en/` gibi bir URL öneki ile ayrışıyor.
> - **Contact Form 7** + **Flamingo** — iletişim formu ve gelen mesajların panelde yedekli tutulması.
> - **WP Mail SMTP** — form e-postalarının güvenilir gönderimi için (native `mail()` yerine gerçek bir SMTP hesabı kullanır).
>
> Bunların dışında bir eklenti YOK — SEO (meta description, Open Graph, sitemap) ve çerez/KVKK onay bandı gibi özellikler de hazır bir eklenti yerine tema kodunun içinde, özel olarak yazıldı (Yoast/RankMath gibi bir SEO eklentisi KURULMAMALI — kendi hreflang/canonical mantığıyla çakışabilir).
>
> **Çalışma ortamı:** PHP 8.2, MySQL/MariaDB (`utf8mb4` karakter seti), standart Apache veya Nginx — özel bir sunucu, Node.js, Docker veya benzeri bir altyapı GEREKMİYOR. Herhangi bir "WordPress uyumlu" paylaşımlı/VPS Linux hosting paketi teknik olarak yeterli.
>
> **Dikkat edilmesi gereken tek özel nokta — kalıcı bağlantılar (permalinks):** Bu site, standart `?p=123` yerine `/urunler/kategori/cikolata-hatlari/` gibi özel (custom) URL yapıları kullanıyor. Bunun çalışması için sunucuda **mod_rewrite (Apache) veya eşdeğer bir rewrite desteği (Nginx)** aktif olmalı — WordPress kurulumu bunu genelde otomatik ayarlar, ama bir taşıma (migration) sonrası **"Ayarlar → Kalıcı Bağlantılar → Kaydet"** ekranına bir kez girip kaydetmek gerekir, aksi halde ürün/kategori sayfaları "Sayfa bulunamadı" hatası verir.
>
> **Ölçek:** Şu an ~285 ürün kaydı, 5 dilde tam çevrili içerik, birkaç yüz MB'lık medya (fotoğraf + PDF katalog) — küçük/orta ölçekli bir kurumsal site, özel bir kapasite planlaması gerektirmiyor.

---

## 4) Özet Kontrol Listesi

Hosting yöneticisiyle ilk görüşmede sırayla sorulacaklar:

- [ ] PHP sürümünü 8.1/8.2'ye ayarlayabilir misiniz, `.ini` limitlerini (§2) yükseltebilir misiniz?
- [ ] SSL sertifikası (Let's Encrypt) kurulu/otomatik yenilenir durumda mı?
- [ ] Form bildirimleri için bir e-posta hesabı + SMTP bilgileri sağlayabilir misiniz?
- [ ] Bize (sadece tema klasörüne erişimi olan) sınırlı bir FTP/SFTP hesabı tanımlayabilir misiniz?
- [ ] Otomatik günlük yedekleme var mı?
- [ ] Taşıma (migration) sırasında DNS'i yeni kuruluma ne zaman yönlendirebiliriz?

---

*Bu belge 2026-09-02 itibarıyla güncel ortam bilgilerine göre hazırlanmıştır (PHP 8.2, MariaDB 10.11, WordPress 7.0.2, aktif eklentiler: ACF 6.8.6, Polylang 3.8.6, Contact Form 7 6.1.7, WP Mail SMTP 4.9.0, Flamingo 2.6.4). Eklenti sürümleri zamanla değişebilir, bu belgeyi tekrar kullanmadan önce `wp plugin list` ile güncel sürümleri kontrol edin.*
