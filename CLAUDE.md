# Çikolata Makine — WordPress Sitesi

Çikolata/şekerleme üretim makineleri üreticisi için özel WordPress teması + yerel Docker geliştirme ortamı. Ürün kataloğu, kategori/alt kategori hiyerarşisi, 4 dilli (TR/EN/RU/ES) içerik ve kurumsal sayfalar içerir.

İçerik (57 ürün, kategoriler, iletişim bilgileri) gerçek İNNOVAS firmasının verilerinden alınmıştır — kardeş proje `C:\Users\O M E N\Masaüstü\CloneWebsite` (Next.js, aynı firma için ayrı bir site) ile aynı kaynak veriyi paylaşır ama bu, tamamen ayrı bir WordPress projesidir.

## Yerel geliştirme ortamı

```bash
cd local-dev
docker compose up -d      # başlatır
docker compose down       # durdurur (veritabanı korunur)
```

- Site: http://localhost:8090/ — Admin: http://localhost:8090/wp-admin/ (`admin` / `admin123`)
- WP-CLI: `docker compose exec wpcli wp <komut> --path=/var/www/html --allow-root`
- **Windows Git Bash'te**: `/var/www/...` gibi container-içi yolları komut satırında MSYS'in Windows yoluna çevirmesini önlemek için `MSYS_NO_PATHCONV=1` ön eki kullan (örn. `MSYS_NO_PATHCONV=1 docker compose exec -T wpcli php -l /var/www/html/...`).
- **OPcache açık** (`wordpress` container'ında `opcache.enable=On`) — bazı PHP değişiklikleri (özellikle `pre_get_posts` gibi erken hook'lara dokunanlar) kaydedildikten hemen sonra tarayıcıya yansımayabilir. Beklenmedik/eski davranış görürsen önce `docker compose restart wordpress` dene, sonra hata aramaya devam et — bu tam olarak bir kez yaşanan, "kod doğru ama sonuç eski" şeklinde kafa karıştıran bir durumdu (bkz. `urun_ailesi` taksonomi arşivi hatası).
- Tema dosyaları (`wordpress-tema/cikolata-makine/`) container'a canlı bağlı — kaydet, tarayıcıda yenile.

## Teknoloji ve kısıtlar

- Klasik PHP teması (block editor / FSE değil), ACF (**ücretsiz sürüm** — Repeater/Gallery/Options Page YOK, bkz. numaralı alan grupları deseni aşağıda), Polylang (4 dil: tr varsayılan, en/ru/es), Contact Form 7.
- Custom Post Type'lar: `makine` (ürünler), `katalog`, `referans` (public değil). Taksonomi: `makine_kategori` (hiyerarşik, `makine`+`katalog` ortak).
- ACF Repeater olmadığı için değişken sayıda tekrar eden içerik iki şekilde çözülüyor: (a) satır satır yazılan bir textarea + PHP'de ayrıştırma (örn. teknik özellikler — `cm_parse_specs()`), (b) sabit sayıda numaralı `group` alanı (örn. `hero_slayt_1..3`, `intl_kisi_1..10`) — her biri "belirleyici" bir alt alan boşsa (örn. `baslik`, `ulke`) o slot sitede hiç görünmez.

## Önemli mimari desenler

- **Mega menü** (header "Ürünler"): sol dar liste + oklu yana-açılan flyout (Bootstrap tarzı, klasik desen). `inc/nav-walker.php` (`CM_Nav_Walker`) menüdeki "Ürünler" öğesini URL eşleşmesiyle bulup içine `cm_products_megamenu()` (inc/template-tags.php) enjekte eder. Masaüstünde saf CSS `:hover`, mobilde `assets/js/main.js` ile JS toggle. **CSS özgüllük tuzağı**: `.nav, .nav ul { display:flex }` temel kuralı (0,1,1 özgüllük) tek-class seçicileri (0,1,0) eziyor — mega menü `<ul>`'ları `ul.megamenu-list`/`ul.megamenu-flyout` gibi TİP+CLASS seçiciyle yazılmalı, yoksa liste yatay akar ve gizli paneller sızar.
- **"Tüm Ürünler" sayfası** (`page-urunler.php`): sidebar + `WP_Query` + **elle `paginate_links()`** kullanır — `the_posts_pagination()` KULLANILAMAZ, çünkü o her zaman global `$wp_query`'ye (bu Sayfanın kendi 1 sonuçluk sorgusu) bakar, ikincil sorguyu görmez ve sessizce boş döner.
- **KRİTİK BUG / DESEN — Polylang + aynı slug + Custom Post Type**: `makine`/`katalog` post'larının 4 dilde AYNI `post_name` slug'ına sahip olması, tekil sayfa çözümlemesini bozar (`WP_Query`'nin `name` araması `lang` filtresini uygulamıyor — kategori/taksonomi sayfalarında bu sorun YOK, sadece CPT tekillerinde var). Sonuç: hangi dilden girilirse girilsin hep AYNI (rastgele/ilk bulunan) posta 301 yönlendirme. **Çözüm**: TR slug'lar temiz kalır, EN/RU/ES slug'larına dil soneki eklenir (`-en`/`-ru`/`-es`). Yeni çok-dilli CPT içeriği eklerken bu deseni koru. Sayfalar (`page` post type) için zaten `functions.php`'de `cm_pll_disambiguate_pagename_request()` filtresi bu sınıf sorunu çözüyor, CPT'ler için böyle bir filtre yok.
- **Terim (taksonomi) slug'ını kod içinden değiştirme**: `wp_update_term()` betik bağlamında (admin POST context'i olmadan) "duplicate_term_slug" hatası verip aynı-taksonomideki farklı-dil terimlerin aynı slug'ı paylaşmasına izin vermiyor — `wp_insert_post`/`wp_update_post` postlarda sorunsuz çalışırken terimlerde ÇALIŞMIYOR. Terim slug'ını değiştirmek gerekirse `$wpdb->update($wpdb->terms, ...)` + `clean_term_cache()` kullan.
- **Tasarım sistemi (renk/font) — bkz. `03_TASARIM_YENILEME_ONERISI.md`**: Site 2026-08'de "hastane gibi soğuk" bulunan eski mavi/gri paletten, o dosyanın Bölüm 1'inde Bühler Group + Framework Computer'dan canlı ölçülen veriyle temellenen yeni palete geçti — sıcak nötr zeminler (`--paper-raised`/`--paper-sunken`, mavi-gri DEĞİL) + tek doymuş vurgu rengi `--accent: #E8622C` (turuncu-kızıl). **Bakır/altın veya soluk/metalik tonlara GERİ DÖNÜLMEMELİ** — kullanıcı bunu açıkça reddetti ("iç kapatan ve soluk"). Font: `IBM Plex Sans` (gövde + küçük UI, `--font-display`/`--font-body`) + `IBM Plex Serif` (SADECE büyük başlıklar, yeni `--font-headline`, `.h-xl`/`.h-lg` class'larına bağlı) + `IBM Plex Mono` (teknik özellik tabloları) — Google Fonts, `functions.php` → `cm_enqueue_assets()` içinde `cikolata-makine-fonts` handle'ıyla yükleniyor. Inter/Poppins/Manrope gibi "her yapay zekâ sitesinde aynı" fontlardan ve mavi→mor gradyan/glassmorphism gibi jenerik şablon imzalarından kasıtlı olarak kaçınıldı (bkz. dosyanın Bölüm 4'ü). Faz 1 (renk+font+buton/kart hover transform+gölge+sticky header küçülme) ve Faz 2 (scroll-reveal `.reveal`/`IntersectionObserver`, istatistik sayaç, hero Ken-Burns) tamamlandı. `.reveal` sınıfı `cm_category_card()`/`cm_product_card()` (inc/template-tags.php) gibi paylaşılan şablon fonksiyonlarına eklendi — yeni bir kart/ızgara eklenirken bu fonksiyonlar kullanılırsa scroll-reveal otomatik gelir, elle class eklemeye gerek yok. Faz 3'ün "Ne üretmek istiyorsunuz?" ekseni de tamamlandı: `urun_ailesi` (hiyerarşik olmayan, sadece `makine` post type'ına kayıtlı) taksonomisi + anasayfa bloğu (`front-page.php`) + arşiv şablonu (`taxonomy-urun_ailesi.php`, bkz. `inc/query.php` → `cm_pre_get_posts()`'te `is_tax('urun_ailesi')` için de `post_type=makine` zorunlu — aksi halde WP_Query varsayılanı "post" olur ve arşiv sessizce 0 sonuç döner, bu tam olarak bir kez yaşanan bir hataydı). 4 hazır terim var (Bar, Praline, Drajee, Damla/Pul) ama **mevcut 57 ürün BİLİNÇLİ OLARAK etiketlenmedi** — kullanıcı bunu kendisi wp-admin → Ürünler → Ürün Aileleri'nden yapacak. Anasayfa bloğu `hide_empty=true` kullandığı için hiç etiketli ürün yokken tamamen gizli kalır (bu doğrulandı). **Not**: `urun_ailesi` Polylang'de henüz "çevrilebilir" olarak işaretli değil (rewrite kurallarında `makine_kategori`'nin aksine `en/ru/es` varyantı yok) — çok dilli terim çevirisi gerekiyorsa Diller → Ayarlar'dan etkinleştirilmeli. Hero video (Faz 0'daki gerçek video içeriği gelince) ve referans/vaka sayfaları (gerçek müşteri verisi/onayı olmadan içerik uydurulmayacağı için bilinçli olarak ertelendi) henüz uygulanmadı.
- **Nav menüleri dil başına AYRI**: Polylang'de her dil kendi menüsüne sahip (`primary`, `primary___en`, `primary___ru`, `primary___es` — `get_nav_menu_locations()`). Türkçe menüye yeni bir üst/alt öğe eklendiğinde bu OTOMATİK diğer dillere yansımaz — elle (wp-admin → Görünüm → Menüler, sağ üstten dil seçip) veya `wp_update_nav_menu_item()` ile tek tek eklenmeli.
- **Kurumsal sayfa ailesi**: sabit ID'lerle tanınıyor — TR=7, EN=277, RU=278, ES=279 (`page.php`'deki `$cm_kurumsal_ids` ve `inc/acf-fields.php`'deki `group_cm_kurumsal` konum kuralında AYNI liste — biri değişirse diğeri de güncellenmeli). Bu sayfalar silinip yeniden oluşturulursa ID'ler değişir.
- **"Boşsa gizle" ilkesi**: harita (harita URL'si yoksa hiç basılmaz), PDF indir/görüntüle butonları (dosya yoksa basılmaz), video sekmesi (link yoksa sekme hiç yok), Uluslararası İletişim bölümü (ülke girilmemişse VEYA "Bölümü Gizle" işaretliyse hiç basılmaz), Kurumsal kartları vb. — yeni eklenen her opsiyonel içerik bloğu bu deseni izlemeli, boş/yarım görünüm asla sitede görünmemeli.
- **Çeviri metinleri**: `inc/strings.php` → `cm_strings()` (TR varsayılan metinler) + `cm__($key)` okuma yardımcısı. Gerçek EN/RU/ES çevirileri Polylang'in `PLL_MO` sınıfı ile veritabanına yazılır (`Diller → Dize Çevirisi` ekranıyla aynı depo). Yeni bir `cm__()` anahtarı eklerken MUTLAKA 3 dilin çevirisini de eklemeyi unutma (`new PLL_MO(); $mo->import_from_db($lang); $mo->add_entry($mo->make_entry($orijinal, $ceviri)); $mo->export_to_db($lang);`) — aksi halde o dilde Türkçe metin sızar.

## Git iş akışı — KURAL

- Uzak depo: `https://github.com/bahadirbesirkestane/CompanyWebsite.git`
- Anlamlı, spesifik commit'ler oluştur (tek büyük dump değil).
- **Push etmeden önce MUTLAKA kullanıcıya sor** — otomatik/varsayılan olarak push yapma, her seferinde onay iste.

## Yakın gelecek (henüz başlama, sadece not)

Kullanıcı görsel/tasarımsal iyileştirmeler ve daha gelişmiş frontend özellikleri istiyor — referans/ilham siteleri bulunup, güncel tasarım ölçütlerine göre uygulanacak. Bu talimat şu an için sadece bir NOT; kullanıcı açıkça başlamadıkça bu çalışmaya girişme.
