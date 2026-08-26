# Tasarım Yenileme Önerisi

> Bu doküman bir **araştırma + öneri** dokümanıdır. Bu aşamada kod değişikliği yapılmamıştır — amaç, gerçek büyük firma sitelerinden canlı olarak ölçülen somut verileri, mevcut siteye (WordPress + ACF ücretsiz + Polylang, sayfa oluşturucu YOK) uygulanabilir, önceliklendirilmiş bir yol haritasına dönüştürmektir.
>
> **v2 notu**: İlk versiyonda önerilen bakır/altın paleti kullanıcı tarafından reddedildi ("iç kapatan ve soluk" bulundu). Bu versiyon onun yerine **açık zemin + tek canlı vurgu rengi** yaklaşımına dayanıyor; öneri, iki gerçek referans sitenin canlı DOM'undan ölçülen renk/font/hareket verisiyle destekleniyor (bkz. Bölüm 1).

---

## 1. Neden bu palet? — İki gerçek referansın canlı ölçümü

Tahmine dayanmamak için iki siteyi tarayıcıda açıp `getComputedStyle` ile en sık kullanılan arka plan/yazı rengi, font ve `transition` sayısını ölçtüm:

### Bühler Group (buhlergroup.com) — sektörün gerçek dünya lideri
Aynı sektörde (gıda/çikolata işleme makineleri), İsviçre merkezli, kurumsal, ürün tanıtımı odaklı (e-ticaret değil):

| Ölçüm | Değer |
|---|---|
| Zemin | `rgb(255,255,255)` — saf beyaz |
| Baskın vurgu rengi | `rgb(0,155,145)` → `#009B91` — **canlı petrol/teal**, 358 elementte kullanılmış (linkler, ikonlar, vurgular) |
| Metin rengi | `rgb(33,37,41)` / `rgb(28,28,28)` — neredeyse siyah, **mavi tonu yok** |
| İkincil koyu ton | `rgb(51,91,111)` → `#335B6F` — soğuk değil, "denizci mavisi/petrol" ailesinde |
| Font | `Roboto/Noto Sans` (gövde) + `Noto Serif` (bazı başlıklarda, 15 element) |
| Hareket | 43 elementte aktif `transition` |

### Framework Computer (frame.work) — "yapay zekâ/şablon sitesi" hissi vermeyen, ödüllü ürün sitesi
Donanım üreticisi, ürün anlatımı ağırlıklı, checkout değil hikaye odaklı:

| Ölçüm | Değer |
|---|---|
| Zemin | `rgb(250,250,249)` → `#FAFAF9` — **sıcak kırık-beyaz**, mavi-gri değil |
| Baskın vurgu rengi | `rgb(247,114,69)` → `#F77245` — **canlı, doymuş turuncu**, ama sadece rozet/CTA/vurgu arka planında kullanılmış, gövde metninde DEĞİL |
| Metin rengi | `rgb(31,31,31)` → `#1F1F1F` — **sıcak, neredeyse siyah** (mavimsi #1E2732 değil) |
| Font | `Graphik` — markaya özel, lisanslı, geometrik ama karakterli bir grotesk (Inter/Poppins DEĞİL) |
| Hareket | 82 elementte aktif `transition` — sektöre göre yüksek, ama zarif (renk sıçraması yok, sadece transform/opacity) |

### Buradan çıkan 3 somut ilke
1. **Zemin açık ama asla mavi-gri değil.** İkisi de saf beyaz ya da sıcak kırık-beyaz kullanıyor. Bizim mevcut `#F7F9FB`/`#EDF1F5` mavimsi-gri zeminleri "hastane" hissinin asıl kaynağı — bunlar sıcak nötr tonlara çevrilecek.
2. **Vurgu rengi TEK ve doymuş, ama küçük dozlarda kullanılıyor.** Hiçbiri "her yeri renkli yapmıyor" — vurgu rengi buton/rozet/ikon/link gibi noktalarda, büyük yüzeylerde değil. Bu hem "sade" hem "göz yormayan" isteğini karşılıyor.
3. **Font, marka font kütüphanesinden (Adobe Fonts/özel lisans) geliyor, jenerik "startup fontu" değil.** Bizim için ücretsiz ama aynı ilkeyi karşılayan bir aile Bölüm 2.3'te öneriliyor.

---

## 2. Yeni renk paleti (önerilen)

Bakır/altın yerine, kullanıcının net isteğine göre (**açık zemin, canlı ama sade, göz yormayan, bakır/altın/soluk YOK**) türetilen palet:

```css
:root {
  --ink:          #1F1B17;   /* sıcak, neredeyse siyah — mavi ton yok */
  --ink-soft:     #5C554C;   /* sıcak gri, ikincil metin */
  --paper:        #FFFFFF;   /* ana zemin — saf beyaz */
  --paper-raised: #FAF8F4;   /* kart/bölüm zemini — sıcak kırık-beyaz (Framework'ten) */
  --paper-sunken: #F1ECE4;   /* en koyu nötr — yine sıcak, gri-mavi değil */

  --accent:       #E8622C;   /* ANA vurgu — canlı, doymuş turuncu-kızıl */
  --accent-deep:  #C24A1B;   /* hover/basılı durum, metin-üstü kullanım için yeterli kontrast */
  --accent-tint:  #E8622C14; /* rozet/hafif zemin dolgusu için %8 opaklık */

  --steel:        #8C8175;   /* nötr gri (ikon/etiket) */
  --steel-soft:   #B7AEA1;
  --line:         #1F1B171a;
  --line-strong:  #1F1B1740;

  --good:         #3E8E63;   /* değişmedi — doğal yeşil, çakışmıyor */
  --footer-bg:    #17130F;   /* sıcak, neredeyse siyah footer — soğuk lacivert değil */
  --footer-text:  #F1ECE4;
}
```

**Neden turuncu-kızıl (`#E8622C`) ve neden yeterli:**
- Çikolata üretiminde **temperleme/ısı** kelimenin tam kalbinde — turuncu-kızıl, ısı/sıcaklık/enerji çağrıştıran tek renk ailesi, ve bu çağrışım bakır gibi "donuk metal" değil, **canlı ve iştah açıcı** (gıda sektöründe kırmızı-turuncu tonların iştah/enerji çağrıştırdığı bilinen bir olgudur).
- Framework örneğinde kanıtlandığı gibi doymuş turuncu, beyaz zemin üzerinde "kurumsal ciddiyeti" bozmadan canlılık katıyor — soluk/pastel değil.
- Bakır/altından (`#A65E2E`/`#C68A57`) farkı: bu ikisi **desatüre edilmiş, kahverengiye yakın metalik** tonlar (kullanıcının "soluk" dediği tam da bu) — `#E8622C` ise **tam doygunlukta, canlı** bir ton. Aynı "sıcak" aileden ama zıt "canlılık" seviyesinde.
- Mavi/mor gradyanlardan (jenerik "AI sitesi" imzası) tamamen uzak — tek düz renk, gradyan yok.

**Alternatif (isteğe bağlı, B seçeneği):** Turuncu yerine Bühler'in kanıtladığı **canlı petrol/teal `#0E8F86`** de aynı ilkeleri karşılar — daha "teknolojik/soğukkanlı", turuncu kadar "sıcak/iştah açıcı" değil ama daha "kurumsal/mesafeli ciddi" bir izlenim ister. Öneri: turuncu ana yön, teal sadece kullanıcı "daha az sıcak, daha teknik" bir his isterse yedek.

---

## 3. Tipografi

Mevcut sorun: `Segoe UI Semibold` — Windows sistem fontu, hiçbir markaya ait değil, "özenilmemiş" sinyali veriyor.

**"Yapay zekâ sitesi" tuzağı**: Inter/Poppins/Manrope/Space Grotesk, bugün AI ile üretilen veya şablon tabanlı hemen her sitede varsayılan olduğu için artık kendileri bir klişe haline geldi. Bunlardan kaçınmak, "elle tasarlanmış" hissi vermenin en ucuz yollarından biri.

**Önerilen aile — IBM Plex (Sans + Serif + Mono), ücretsiz, Google Fonts üzerinden self-host edilebilir:**

```css
--font-display:  "IBM Plex Sans", "Segoe UI", Arial, sans-serif;   /* nav, buton, etiket, küçük UI metni */
--font-headline: "IBM Plex Serif", Georgia, serif;                 /* YENİ — sadece büyük hero/bölüm başlıkları */
--font-body:     "IBM Plex Sans", "Segoe UI", Arial, sans-serif;   /* paragraf metni */
--font-mono:     "IBM Plex Mono", "SF Mono", Consolas, monospace;  /* teknik özellik tabloları — zaten mevcut kullanım */
```

Neden bu seçim:
- **Ücretsiz ve gerçek bir kimliği var** (IBM tarafından tasarlandı, teknik/mühendislik dünyasında tanıdık ama "startup şablonu" klişesi değil).
- **Serif+Sans+Mono üçlüsü zaten tek ailede geliyor** — Bühler'in kendi sitesinde bulduğumuz "gövde sans + vurgulu serif" deseniyle birebir örtüşüyor (Bölüm 1), ve bizim mevcut `--font-mono` (teknik özellik tablosu) kullanımıyla doğrudan uyumlu, sıfır mimari değişiklik gerektiriyor.
- **`--font-headline` yeni bir değişken** — sadece büyük H1/H2 başlıklarda serif kullanmak, küçük UI metnini (nav, buton, 11px etiketler) sans'ta bırakmak, hem okunabilirliği korur hem de "kurumsal + sıcak" bir kontrast yaratır (çok küçük boyutta serif okunabilirliği düşürür, bu yüzden sadece başlıklarda).

---

## 4. "Yapay zekâ / şablon sitesi" gibi görünmemek için kaçınılacaklar

Kullanıcının özellikle belirttiği bir risk — somut kaçınma listesi:

| Klişe | Neden kaçınılır | Bizim yerine koyduğumuz |
|---|---|---|
| Inter/Poppins/Manrope + mavi→mor gradyan | AI sayfa oluşturucuların ve şablonların ezici çoğunluğunun varsayılanı | IBM Plex ailesi + tek düz turuncu vurgu (Bölüm 2-3) |
| Cam-efekti (glassmorphism) kartlar, aşırı blur | Jenerik "SaaS iniş sayfası" imzası | Düz beyaz/kırık-beyaz kart + ince `--line` kenarlık + gerçek gölge (Bölüm 5) |
| Soyut, rastgele "blob" illüstrasyonlar/gradyan daireler | İçerikle ilgisiz, her yerde aynı | Gerçek fabrika/makine fotoğrafı ve videosu (zaten Faz 0'da planlı) |
| Emoji'yi ikon olarak kullanmak | Amatör/otomatik üretim izlenimi verir | Mevcut özel SVG ikon seti korunur |
| Her köşeyi aşırı yuvarlatmak (`border-radius: 9999px` her yerde) | "Generic app UI" hissi | Mevcut ince `border-radius: 3px` korunur — zaten bizim sitede var ve doğru |
| Aşırı kalın, hepsi-bold tipografi hiyerarşisi | Otomatik üretilen sitelerde sık görülen "her şey önemli" görünümü | Net boyut/ağırlık kademeleri: `--font-headline` (serif, büyük) > `--font-display` (sans, orta, UI) > `--font-body` (sans, gövde) |

---

## 5. Referans sitelerden çıkarılan diğer somut bulgular (v1'den korunmuştur)

*(Bu bölüm ilk versiyonda toplanan, hâlâ geçerli olan araştırmadır — sadece renk/font önerisi değişti, aşağıdaki bulgular aynı kalıyor.)*

### 5.1 Hareket / animasyon yoğunluğu
- **aasted.eu**: 161 elementte aktif transition, otomatik oynayan arka plan videosu.
- **selmi-group.com**: 194 elementte transition, ayrı kurumsal video bölümü.
- **sollich.com**: tam ekran video hero + beyaz başlık/CTA overlay.
- **Bühler / Framework (bu versiyonda ölçülen)**: 43 / 82 elementte transition.
- Bizim site: **0** — sadece renk hover'ı var.

→ Bu sektörde (ve genel olarak kurumsal ürün sitelerinde) "hareket" bir lüks değil, standart. Sayfa açılışında/scroll'da yumuşak fade+slide-up, buton/kart hover'larında transform (scale/translateY), sayaç animasyonları.

### 5.2 Fotoğraf ve video kullanımı
- Sollich/Aasted: gerçek video (fabrika/üretim, sessiz, loop).
- Bosch Rexroth: büyük profesyonel fotoğraf (insan + makine birlikte).
- Selmi: her makine için ayrı ürün videosu.
- Bizim site: 57 üründen sadece 7'sinde gerçek fotoğraf, geri kalanı placeholder ikon, hiç video yok.

→ **En büyük içerik açığı** — hiçbir CSS/renk/font değişikliği, gerçek fotoğraf/video eksikliğini tek başına telafi edemez (bkz. Faz 0).

### 5.3 Bilgi mimarisi / gezinme
- **Sollich**: ürünleri hem "Teknoloji" (makine tipi) hem **"Son Ürün"** (bar, praline, drajeler…) eksenine göre gezmeye izin veriyor — teknik terim bilmeyen ziyaretçi bile doğru makineye ulaşabiliyor.
- **Selmi**: fiyat aralığı, PDF teknik föy, çapraz satış bloğu.
- Bizim site: sadece makine tipi kategori/alt kategori ağacı var.

→ Orta vadeli öneri: mevcut `makine_kategori` yanına ikinci, hiyerarşik olmayan bir **"Ürün Ailesi"** taksonomisi (Bar, Praline, Drajee, Damla/Pul…) eklenip anasayfada "Ne üretmek istiyorsunuz?" bloğu oluşturulabilir. ACF/taksonomi altyapısı buna hazır.

### 5.4 Yerel pazar kıyaslaması
Türkiye pazarındaki doğrudan rakipler (Memak, Prosestek), uluslararası oyunculara göre görsel olarak geride — yerel pazarda ortalamanın üzerine çıkmak, uluslararası oyuncularla aynı ligde görünmekten çok daha ulaşılabilir bir hedef.

---

## 6. Somut örnek: "olması gereken" anasayfa hero'su

Mevcut sisteme (WordPress klasik tema, ACF ücretsiz, Polylang, `assets/css/main.css` + `assets/js/main.js`, framework yok) **doğrudan uygulanabilir**, çerçeve gerektirmeyen örnek:

```
┌──────────────────────────────────────────────────────────┐
│  [sabit üst menü — scroll'da küçülüp yarı saydam beyaza    │
│   dönüşüyor, geçiş 0.25s]                                  │
│                                                              │
│   ░░░░░░░░ arka planda loop video / Ken-Burns  ░░░░░░░░    │
│   ░░ efektli fotoğraf slaytı (yavaş zoom+pan) ░░░░░░░░     │
│   ░░ üstte koyu mürekkep → şeffaf gradient  ░░░░░░░░░░     │
│                                                              │
│        REÇETENİZDEN ÜRÜNÜNÜZE                              │
│        (turuncu renkli, ince aralıklı eyebrow etiketi)     │
│                                                              │
│        Güvenilir Üretim Teknolojisi           ← sayfa       │
│        (--font-headline: IBM Plex Serif,        açılışında  │
│        satır satır alttan yukarı fade-in)       0.6s'de     │
│                                                  sırayla     │
│        [Ürünleri İncele]  [Bir Uzmanla Görüşün]  beliriyor  │
│         ↑ turuncu dolgu     ↑ şeffaf, beyaz kenarlık        │
│         hover: hafif büyüme hover: dolgu beyaza döner       │
│                                                              │
│   [27+] [500+] [40+]  ← sayfa görünür olunca 0'dan          │
│    yıl    hat   ülke     hedef sayıya SAYARAK dolan          │
│                            istatistik şeridi                 │
└──────────────────────────────────────────────────────────┘
```

**Teknik olarak nasıl yapılır (framework gerekmez):**
- Ken-Burns/parallax: saf CSS `@keyframes` ile arka plan görselinde yavaş `scale(1)→scale(1.08)` — GPU dostu, JS gerektirmez.
- Scroll'da beliren bölümler: `IntersectionObserver` (tarayıcı yerleşik API'si) — eleman görünür olunca `.is-visible` class'ı eklenir, CSS transition devreye girer. ~40 satırlık vanilla JS, `main.js`'e eklenir.
- Sayaç animasyonu: aynı `IntersectionObserver` tetiklemesiyle `requestAnimationFrame` döngüsü, ~20 satır vanilla JS.
- Sticky nav küçülme: scroll pozisyonuna göre `<header>`'a class ekleyip CSS transition — mevcut `nav-toggle`/`megamenu` desenine benzer yaklaşım.

**Admin tarafında yönetilebilirlik**: Hero zaten ACF ile 3 slayta kadar yönetiliyor (`hero_slayt_1..3`, bkz. `inc/acf-fields.php`). Bu öneri o alanı bozmaz, sadece CSS/JS katmanında zenginleştirir.

**Ürün kartı için de aynı mantık**: hover'da fotoğraf hafif `scale(1.04)`, kart gölgesi derinleşir, "Detayları Gör" oku sağa doğru kayar — saf CSS transition, JS gerektirmez.

---

## 7. Önceliklendirilmiş yol haritası

### Faz 0 — İçerik (tasarımdan ÖNCE gelmeli)
- [ ] Üretim tesisinden gerçek fotoğraf çekimi (makineler, üretim hattı, ekip) — en azından öne çıkan 5-10 ürün + hero için
- [ ] Mümkünse 1 adet kısa (30-60 sn) genel tanıtım videosu (hero arka planı için) — yoksa fotoğraf Ken-Burns ile de büyük fark yaratır

### Faz 1 — Hızlı, düşük riskli, yüksek etkili (1 günlük iş mertebesinde) — ✅ UYGULANDI
- [x] Renk paletini mavi/gri → sıcak nötr zemin + canlı turuncu vurguya çevir (`assets/css/main.css` `:root` değişkenleri — Bölüm 2)
- [x] `--font-display`/`--font-body`'yi IBM Plex Sans'a, yeni `--font-headline`'ı IBM Plex Serif'e çevir (Bölüm 3) — Google Fonts `functions.php` → `cm_enqueue_assets()` içinde `cikolata-makine-fonts` handle'ıyla yükleniyor
- [x] Buton/kart hover'larına transform+gölge geçişleri ekle (`.btn-primary`, `.btn-outline`, `.cat-card`, `.prod-card` görsel zoom, `.corp-card`)
- [x] Sticky header scroll-küçülme efekti (`.site-header.is-scrolled`, JS: `assets/js/main.js`)

### Faz 2 — Orta vadeli (birkaç günlük iş mertebesinde) — ✅ UYGULANDI
- [x] Scroll-reveal animasyon sistemi (`IntersectionObserver` tabanlı tek yardımcı, `assets/js/main.js`; `.reveal`/`.reveal.is-visible`, `assets/css/main.css`) — `cm_category_card()`, `cm_product_card()`, kurumsal `corp-card`, blog/arama `prod-card` üzerinden TÜM sayfalarda otomatik devrede; kart ızgaralarında 6 öğeye kadar kademeli (staggered) gecikme var
- [x] İstatistik sayaç animasyonu (`.stat .n`, sadece rakamla başlayan etiketlerde — "ISO 9001" gibi metinler olduğu gibi kalıyor)
- [x] Hero'ya Ken-Burns desteği (mevcut hero fotoğrafları üzerinde `scale(1)→scale(1.08)`, slayt geçişiyle otomatik yeniden başlıyor) — tam video desteği hâlâ Faz 0'daki gerçek video içeriği gelince eklenecek
- [x] Ürün kartlarında fotoğraf hover-zoom — Faz 1'de zaten uygulanmıştı (`scale(1.045)`)

### Faz 3 — Daha büyük, isteğe bağlı
- [ ] "Ne üretmek istiyorsunuz?" ikinci navigasyon ekseni (yeni taksonomi)
- [ ] Ürün sayfalarına video sekmesi + PDF teknik föy indirimi zenginleştirme (altyapı zaten var, içerik doldurma meselesi)
- [ ] Referans/vaka çalışması sayfaları ("başarı hikayesi" formatı)

---

## 8. Kısıtlar ve ilkeler (mevcut mimariyle uyum için)

- **Framework/sayfa oluşturucu eklenmeyecek** — her şey mevcut klasik PHP tema + vanilla CSS/JS içinde kalacak (bkz. `CLAUDE.md`).
- **"Boşsa gizle" ilkesi korunacak** — yeni eklenecek her görsel/video alanı, doldurulmadığında zarifçe gizlenmeli veya mantıklı bir yedek göstermeli.
- **4 dil (TR/EN/RU/ES) etkilenmeyecek** — değişiklikler CSS/JS katmanında, çeviri sistemine dokunmaz.
- **ACF ücretsiz sürüm kısıtları** korunacak (Repeater/Gallery yok — mevcut numaralı-alan deseni kullanılmaya devam edilecek).
- **Performans**: video/animasyon eklerken `prefers-reduced-motion` desteği korunacak.
- **Erişilebilirlik/kontrast**: `--accent` (#E8622C) beyaz zemin üzerinde büyük/kalın metin ve UI elemanlarında yeterli kontrasta sahip; küçük gövde metninde vurgu rengi yerine `--ink`/`--ink-soft` kullanılmaya devam edilmeli (Bühler/Framework'te de vurgu rengi gövde metninde kullanılmıyor, bkz. Bölüm 1).

---

## Sonraki adım

Bu doküman onaylandığında, Faz 1'den başlayarak somut kod değişikliklerine geçilecek — önce **renk paleti + font** (`main.css` `:root`, tek dosya, geri alması kolay, en yüksek etki/efor oranı), sonra sırayla diğer fazlar.
