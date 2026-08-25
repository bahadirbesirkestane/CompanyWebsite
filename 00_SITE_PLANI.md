# Çikolata Makineleri Üretici Firma Web Sitesi — Proje Planı

## 1. Teknoloji Kararı

**WordPress (self-hosted) + Advanced Custom Fields (ücretsiz sürüm) + Custom Post Types + Custom Tema**

Neden:
- Linux hosting üzerinde native çalışır (PHP + MySQL), özel sunucu gereksinimi yok.
- Admin paneli hazır (wp-admin), firma personeli teknik bilgi olmadan içerik girebilir.
- Kategori/alt kategori hiyerarşisi WordPress taksonomi sistemiyle native destekleniyor.
- PDF yükleme/indirme Medya Kütüphanesi ile hazır geliyor.
- SEO (Yoast/RankMath), form (Contact Form 7/WPForms), çoklu dil (WPML/Polylang — ileride istenirse) gibi ihtiyaçlar plugin ile hızlı eklenir.
- Tasarım tamamen özel (custom tema, hazır şablon değil) — "kurumsal, sade, profesyonel" görünüm için sıfırdan tema yazılır, hazır WP teması kullanılmaz.

## 2. Bilgi Mimarisi / Site Haritası

```
Anasayfa
├── Kurumsal
│   ├── Hakkımızda
│   ├── Misyon & Vizyon
│   ├── Üretim Tesisi / Fabrika
│   ├── Kalite & Sertifikalar
│   └── İnsan Kaynakları (opsiyonel)
├── Makinelerimiz  (ana ürün kataloğu — hiyerarşik)
│   ├── [Kategori] → [Alt Kategori] → [Makine Detay Sayfası]
├── Kataloglar (PDF)
│   └── Genel katalog + kategori bazlı kataloglar (görüntüle / indir)
├── Projeler / Referanslar (opsiyonel, kurulum yapılan tesisler)
├── Blog / Haberler (içerik paylaşımı — fuar, yeni ürün, sektör haberi)
├── İletişim
│   ├── Form + harita + şube bilgileri
└── (Admin) /wp-admin — içerik yönetim paneli
```

> Kategori hiyerarşisi netleşmediği için aşağıda **örnek** bir yapı veriyorum — çikolata makineleri sektöründe yaygın kırılım budur, firmanın gerçek ürün gamına göre birlikte netleştirilmeli:

```
Makinelerimiz
├── Temperleme Makineleri
│   ├── Sürekli Temperleme
│   └── Kesikli (Batch) Temperleme
├── Kaplama (Enrobing) Hatları
├── Kalıplama (Moulding) Sistemleri
│   ├── Tablet / Bar Kalıplama
│   └── Pralin / Şekilli Kalıplama
├── Soğutma Tünelleri
├── Depolama & Tank Sistemleri
├── Ambalajlama Makineleri
└── Komple Üretim Hatları (Turnkey)
```

Bu yapı **maks. 2 seviye** (kategori → alt kategori) ile sınırlı tutulacak; "karmaşık olmayacak" isteğine uygun olarak 3. seviyeye inilmeyecek, gerekirse makine detay sayfasında filtre/etiket (örn. kapasite, üretim hızı) kullanılacak.

## 3. WordPress Veri Modeli

**Custom Post Type: `makine`**
- Başlık, açıklama (WYSIWYG)
- Vitrin kapak görseli (Featured Image)
- Görsel galerisi (ACF Gallery field)
- Teknik özellikler (ACF Repeater: özellik adı / değer — kapasite, güç, ebat, ağırlık vs.)
- PDF katalog (ACF File field — tekil makineye özel broşür)
- Video linki (opsiyonel, ACF URL field — YouTube/Vimeo)
- Öne çıkan makine (checkbox — anasayfada gösterim için)

**Taxonomy: `makine_kategori`** (hiyerarşik, WordPress `category` mantığında — kategori/alt kategori)

**Custom Post Type: `katalog`** (genel PDF kataloglar — kategori bazlı toplu kataloglar, tekil makine PDF'inden ayrı)
- Başlık, kapak görseli, PDF dosyası, açıklama

**Custom Post Type: `haber`** veya standart `post` (blog/haber içerikleri)

**Sayfa (Page) + ACF Flexible Content**: Anasayfa, Hakkımızda gibi sayfalar blok blok (hero, öne çıkan makineler, istatistik şeridi, referanslar, CTA) admin'den düzenlenebilir olacak — geliştirici olmadan içerik/sıra değiştirilebilsin diye.

## 4. Makine Detay Sayfası — İçerik Alanları

1. Vitrin kapak görseli (büyük hero)
2. Başlık + kısa tanıtım cümlesi
3. Görsel galerisi (lightbox ile büyütülebilir, çoklu görsel)
4. Ürün açıklama metni
5. Teknik özellikler tablosu
6. PDF katalog — "Görüntüle" (tarayıcıda aç) + "İndir" butonu ikisi birden
7. İlgili/benzer makineler (otomatik, aynı kategoriden)
8. "Teklif İste" / İletişim CTA

## 5. Admin Panel Kullanım Akışı

- Firma personeli wp-admin'e girer.
- Sol menüde "Makinelerimiz" özel menüsü → yeni makine ekle → kategori seç, görselleri yükle, PDF yükle, özellikleri gir → yayınla.
- Kategori/alt kategori ekleme-düzenleme aynı ekrandan (sürükle-bırak sıralama eklenebilir).
- Anasayfa "öne çıkan makineler" alanı sayfa düzenleyiciden checkbox ile seçilir, kod bilgisi gerekmez.

## 6. Hosting & Teknik Notlar

- PHP 8.x + MySQL/MariaDB destekli Linux hosting (paylaşımlı hosting yeterli, orta trafik için).
- SSL (Let's Encrypt genelde hosting'te ücretsiz gelir) zorunlu.
- Görsel optimizasyonu (WebP + lazy load) + PDF'lerin sıkıştırılmış yüklenmesi performans için önemli.
- Düzenli yedekleme (UpdraftPlus gibi plugin) + güvenlik sertleştirme (login limit, firewall plugin).

## 7. Durum: Tema Tamamlandı ✅

Tasarım onayının ardından çalışan WordPress teması kodlandı → [wordpress-tema/cikolata-makine/](wordpress-tema/cikolata-makine/). Kurulum adımları için o klasördeki [README.md](wordpress-tema/cikolata-makine/README.md) dosyasına bakın.

Kapsanan: custom tema iskeleti (header/footer, anasayfa hero slider + referans marquee, kategori/alt kategori sayfaları, makine detay şablonu, kataloglar sayfası, blog), ACF field group'ları (kod tabanlı) ve makine/katalog/referans CPT + makine_kategori taksonomisi.

Kalan (sitenin gerçek hostinge taşınmasından önce firma tarafında netleşecek):
1. Gerçek kategori/alt kategori listesi (mockup'taki örnek yapı yerine).
2. Gerçek ürün fotoğrafları, PDF kataloglar, makine metinleri/teknik özellikleri.
3. Hosting'e kurulum + içerik girişi (bkz. README kurulum adımları — ACF'in ücretsiz sürümü yeterli, lisans/PRO gerekmiyor).
3. Test verisiyle (birkaç örnek makine) yerelde/staging'de doğrulanacak.
4. Gerçek hosting'e taşınacak.
