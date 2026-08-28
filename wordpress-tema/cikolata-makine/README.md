# Çikolata Makine — WordPress Teması

Onaylanan tasarım önizlemesinin ([01_tasarim_onizleme.html](../01_tasarim_onizleme.html)) birebir çalışan WordPress temaya dönüştürülmüş hâli. Hiyerarşik makine kategorileri, PDF katalog yönetimi ve tüm içerikler admin panelinden (wp-admin) yönetilir.

## Gereksinimler

- WordPress 6.x, PHP 7.4+, MySQL/MariaDB (herhangi bir Linux paylaşımlı hosting yeterli)
- **Advanced Custom Fields** eklentisi — **ücretsiz sürüm yeterlidir**, PRO gerekmez. (Bu tema bilerek sadece ücretsiz sürümdeki alan tiplerini kullanır: Tekrarlayan Alan/Repeater ve Galeri PRO'ya özel olduğu için kullanılmamıştır — hero slaytları ve teknik özellikler gibi değişken listeler, sabit sayıda grup alanı veya satır satır metin ile çözülmüştür. Bu tema tüm özel alanları PHP içinde otomatik tanımlar, ACF arayüzünden ayrıca alan oluşturmanıza gerek yoktur.)
- **Contact Form 7** — İletişim sayfasındaki form için gereklidir (tema, formu otomatik olarak İletişim sayfasına yerleştirir — bkz. aşağıdaki "İletişim Formu & Harita" bölümü).

## Kurulum

1. `cikolata-makine` klasörünü zip'leyip **Görünüm → Temalar → Yeni Ekle → Tema Yükle** ile yükleyin (ya da FTP ile `/wp-content/themes/` altına kopyalayın), sonra **Etkinleştir**.
2. **Eklentiler → Yeni Ekle**: "Advanced Custom Fields" (ücretsiz sürüm) ve **"Contact Form 7"** eklentilerini kurup etkinleştirin. Contact Form 7 etkinleştirildiğinde otomatik olarak "Contact form 1" adında bir form oluşur — bu formu **silmeyin/adını değiştirmeyin**, tema İletişim sayfasına bu formu (slug: `iletisim-formu`) otomatik yerleştirir (bkz. aşağıdaki "İletişim Formu & Harita" bölümü). Formun alanlarını (Ad Soyad, Firma, E-posta, Telefon, Mesaj) ve gönderim ayarlarını dilerseniz Contact Form 7 admin ekranından özelleştirebilirsiniz.
3. **Ayarlar → Kalıcı Bağlantılar** sayfasını açıp tekrar **Kaydet**'e basın (kategori/katalog url yapılarını tazeler).
4. **Sayfalar → Yeni Ekle** ile şu sayfaları oluşturun:
   - "Anasayfa" (front page olarak atanacak)
   - "Ürünler" (istediğiniz başka bir başlık da olur, örn. "Makinelerimiz") — **slug mutlaka `urunler` olmalı** (şablon otomatik eşleşir; sadece görünen BAŞLIK değil, slug/adres de değişirse bu sayfa şablonu ve site genelindeki linkler kırılır — başlığı istediğiniz kadar değiştirebilirsiniz, sadece slug'a dokunmayın)
   - "İletişim" — slug `iletisim`
   - "Haberler" — **slug mutlaka `haberler` olmalı** (haber listeleme şablonu bu slug'a bağlıdır — çeviri sayfalarının slug'ı farklı/yerelleştirilmiş olabilir, örn. İngilizce'de `news`, sorun olmaz, tema otomatik doğru şablonu bulur)
   - "Kurumsal", "Hakkımızda" vb. istediğiniz diğer sayfalar
5. **Ayarlar → Okuma**: Anasayfa gösterimini "Sabit bir sayfa" yapıp "Anasayfa" sayfasını seçin.
6. "Anasayfa" sayfasını düzenlerken sağdaki **Anasayfa Ayarları** kutusunu doldurun: **Slayt 1/2/3** (her biri üst etiket, başlık, açıklama, görsel, 2 buton — başlık boş bırakılan slayt sitede hiç görünmez, en az Slayt 1'i doldurun), **İstatistik 1-4**, Katalog Banner metni/PDF'i, **Kalite Belgesi 1-8** (her biri bir başlık + bir "Belge" dosyası + isteğe bağlı bir "Önizleme Görseli" — "Belge" olarak PDF VEYA görsel (JPG/PNG/WEBP) yükleyebilirsiniz; görsel yüklerseniz kartta doğrudan o görsel gösterilir, PDF yüklerseniz kartta PDF'in ilk sayfasının otomatik önizlemesi gösterilir (bkz. aşağıdaki "Kalite Belgelerimiz — PDF Önizleme Notu"), otomatik önizleme yerine kendi seçtiğiniz bir görsel göstermek isterseniz "Önizleme Görseli" alanına yükleyin — hiçbiri yoksa genel bir belge ikonu gösterilir; tıklandığında HER DURUMDA "Belge" alanına yüklediğiniz dosya yeni sekmede açılır — "Başlık" boş bırakılan slotlar sitede hiç görünmez, hiç dolu slot yoksa "Kalite Belgelerimiz" bölümü anasayfada hiç görünmez).
7. **Görünüm → Menüler**: Bir menü oluşturup **"Üst Menü (Header)"** konumuna atayın (Anasayfa, Kurumsal, Ürünler, Kataloglar, Haberler, İletişim). İsteğe bağlı olarak "Footer — Hızlı Linkler" ve "Footer — Kategoriler" konumlarını da doldurun. Bir üst menü öğesini fare ile bir üst öğenin biraz altına/sağına sürükleyerek bırakırsanız (WordPress'in standart girintileme yöntemi), o öğe alt sayfa/alt kategori olarak eklenir ve mobil hamburger menüde üst öğenin altında girintili görünür.
8. **Ürünler → Kategoriler**: Kategori/alt kategori ağacınızı oluşturun (yapı karmaşıklaşmasın diye **en fazla 2 seviye** — kategori → alt kategori — önerilir). Her kategoriye isterseniz bir **Kategori İkonu** yükleyin. Bir kategoride ürün yoksa (kendi içinde veya alt kategorilerinde) kart üzerinde sayı satırı hiç görünmez — "Proje Bazlı" gibi bir yer tutucu metin yoktur. Ürün listeleme sayfalarında (Tüm Ürünler, kategori/alt kategori, ürün ailesi) ziyaretçi ürün sayısının yanındaki **2/3 sütun** ikonlarıyla görünümü değiştirebilir — tercih tarayıcıda hatırlanır (localStorage), varsayılan her zaman 2 sütundur.
9. **Ürünler → Yeni Ekle**: Her makine için:
   - Öne Çıkan Görsel (vitrin kapak — bu, detay sayfasındaki galerinin de ilk fotoğrafıdır)
   - Kategori, Kısa Özet (ürün kartlarında başlığın altında görünen tek satır)
   - **Kısa Açıklama** (detay sayfasında galerinin yanında görünen paragraf)
   - **Ek Görsel 1-8** (galeri — istediğiniz kadarını doldurun, hepsi zorunlu değil). 2'den fazla fotoğraf eklerseniz ana görselin üzerinde otomatik olarak ileri/geri okları çıkar.
   - **Teknik Özellikler**: her satıra "Özellik Adı: Değer" (örn. "Kapasite: 450 kg/saat"). **İlk 3 satır**, detay sayfasında galerinin yanında küçük kart olarak da gösterilir (bkz. aşağıdaki not) — kart başlıklarını/değerlerini değiştirmek için sadece bu satırları düzenlemeniz yeterli, ayrı bir alan yoktur.
   - PDF Katalog, opsiyonel YouTube video linki — girildiğinde ürün sayfasında "Dokümanlar"/"Video" sekmeleri otomatik açılır. Varsayılan açılan sekme "Ürün Açıklaması"dır.
   - **CTA Butonu Metni** (opsiyonel) — boş bırakılırsa "Bu Makine İçin Teklif İste" butonu o makinede hiç görünmez. Bir metin girerseniz buton görünür; **CTA Linki** de girerseniz oraya, girmezseniz sitedeki WhatsApp numarasına (o da yoksa İletişim sayfasına) yönlendirir.

   > **"Kapasite / Kurulu Güç / Ebat" kartları nereden geliyor?** Bunlar ayrı bir alan değildir — doğrudan yukarıdaki **Teknik Özellikler** kutusunun ilk 3 satırından otomatik oluşur. "Kapasite" yerine "Çalışma Sıcaklığı" gibi farklı bir başlık göstermek isterseniz o satırı düzenlemeniz yeterli; makineden makineye tamamen farklı olabilir. 3'ten az satır girerseniz daha az kart görünür, hiç satır girmezseniz bu kart alanı da "Teknik Özellikler" sekmesi de hiç görünmez.
10. **Kataloglar → Yeni Ekle**: Genel/kategori bazlı PDF kataloglarınızı ekleyin (öne çıkan görsel = kapak, ACF'ten PDF dosyasını yükleyin).
11. **Referanslar → Yeni Ekle**: Müşteri/referans firma adı + logo (öne çıkan görsel) — anasayfadaki kayan şeritte otomatik görünür. Hiç referans eklenmezse o bölüm sayfada hiç görünmez.
12. **Genel Ayarlar** (sol menüde): telefon, e-posta, adres, sosyal linkler — footer'da otomatik görünür.
13. **Görünüm → Özelleştir → Site Kimliği**: logonuzu yükleyin.

## İletişim Formu & Harita

İletişim sayfası, kodda sabit değil — tema bunu **otomatik olarak** ekler, siz sadece iki yeri doldurursunuz:

- **Form**: Contact Form 7 ile gelen (veya siz düzenlediğiniz) `iletisim-formu` slug'lı form, İletişim sayfasının ALTINA otomatik yerleştirilir. Formu görünüme/alanlara müdahale etmek isterseniz Contact Form 7 → Formlar'dan düzenleyin — sayfa içeriğine ayrıca shortcode eklemenize gerek YOKTUR (elle eklerseniz iki form birden görünür, eklemeyin).
- **Harita**: **Görünüm → Özelleştir → İletişim & WhatsApp → "Google Haritalar Yerleştirme (Embed) URL'si"** alanına, Google Haritalar'da adresinizi bulup **Paylaş → Harita Yerleştir** sekmesinden aldığınız `src="..."` adresini yapıştırın (API anahtarı gerekmez, ücretsizdir). **Daha hızlı bir yol**: adresinizi URL-encode edip şu kalıba yapıştırın (Google Haritalar'a hiç girmeden) — `https://www.google.com/maps?q=ADRESİNİZ&output=embed` (örn. `https://www.google.com/maps?q=Hanl%C4%B1+Sakarya+Mahallesi+Satso+Caddesi+No:10+Arifiye+Sakarya+T%C3%BCrkiye&output=embed`). **Bu alan boşken harita hiç görünmez** — yanlış/örnek bir konum asla otomatik gösterilmez; form da bu durumda (harita yoksa VEYA form yoksa) otomatik tek sütuna genişler, boş bir yarı sütun kalmaz.
- **Form e-postası nereye gider?** Görünüm → Özelleştir → İletişim & WhatsApp → **E-posta** alanına girdiğiniz adrese (tüm dillerdeki formlar için ortak, tek kaynak). Bu alan boşsa formun kendi varsayılan ayarındaki (site yönetici e-postası) adrese gider.
- **Çoklu dil**: Form, "Ürünler"/"İletişim" sayfaları gibi Polylang ile çevrilebilir işaretlenmiştir. Yeni bir dilde form eklemek için Contact Form 7 → Formlar'da `iletisim-formu`nun yanındaki **"+ Çeviri Ekle"** ile yeni dilde bir form oluşturup alan etiketlerini (Ad Soyad, Firma Adı, vb.) o dile çevirin — tema otomatik olarak geçerli ziyaretçi diline uygun formu gösterir, ekstra kod/ayar gerekmez.
- **"Bize Ulaşın" kartları (Telefon/WhatsApp/E-posta/Adres/Çalışma Saatleri)**: Formun ÜSTÜNDE görünen bu kartların başlığı VE içeriği, **Sayfalar → İletişim** (her dilin kendi sayfası) düzenleme ekranındaki **"İletişim Sayfası: 'Bize Ulaşın' Kartları"** kutusundan yönetilir — Özelleştir'e gitmenize gerek yoktur. Bir kartın alanını boş bırakırsanız Özelleştir → İletişim & WhatsApp'taki (footer'la paylaşılan) genel değere düşer; o da boşsa kart hiç görünmez.

## Yeni Sayfa Ekleme & Kod/HTML ile Düzenleme

Sayfa eklemek için özel bir işlem gerekmez: **Sayfalar → Yeni Ekle**, herhangi bir sayıda ve istediğiniz zaman kendiniz oluşturabilirsiniz — hazır sayfalarla (Ürünler, İletişim, Kataloglar, Haberler) sınırlı değilsiniz.

Sayfa içeriği editörünün sağ üstünde **"Görsel" / "Kod"** iki sekmesi vardır:
- **Görsel**: normal biçimlendirilmiş metin editörü (kalın, başlık, liste, link vb.).
- **Kod**: sayfanın HAM HTML kodunu görüp doğrudan düzenleyebileceğiniz kaynak kod görünümü — kendi HTML'inizi ekleyebilir veya içeriği komple sıfırdan HTML olarak yazabilirsiniz. Yönetici (Administrator) hesabıyla girdiğiniz HTML (gerekirse `<style>`/`<script>` dahil) KAYDEDİLİRKEN budanmaz/temizlenmez.

Bu, sayfanın sağındaki **Sayfa Öznitelikleri**, **Öne Çıkan Görsel**, **Sayfa Üstü Banner** gibi meta kutulardan tamamen BAĞIMSIZDIR — onlar ayrı ayrı, içerik alanına dokunmadan doldurulur.

**Tamamen serbest / şablonsuz bir sayfa isterseniz**: aynı Sayfa Öznitelikleri kutusundan **Şablon: "Serbest Sayfa (HTML / Kod)"** seçin (bkz. `page-templates/serbest-sayfa.php`). Bu şablon, temanın diğer sayfalarda otomatik eklediği HİÇBİR ŞEYİ (kırıntı/breadcrumb, sayfa başlığı, banner, alt sayfa kartları) basmaz — sadece site header/footer'ı (menü, logo, WhatsApp butonu) korunur, içerik editöründe ne yazdıysanız (Kod sekmesinden girilen ham HTML dahil) sayfanın genişlik sınırı bile olmadan OLDUĞU GİBİ basılır; kendi düzeninizi (genişlik, arka plan, grid) tamamen kendi HTML/CSS'inizle kurabileceğiniz tam bir boş tuval — bir kampanya/açılış sayfası gibi standart site tasarımından bağımsız bir şey gerektiğinde kullanın.

## Haberler

`wp-admin → Haberler → Yeni Ekle` ile her haber için sadece standart WordPress alanları kullanılır — ayrı bir özel alan YOKTUR:
- **Başlık**, **İçerik** (tam haber metni, haber detay sayfasında gösterilir), **Öne Çıkan Görsel** (hem haber kartında hem detay sayfasının üstündeki banner'da kullanılır — banner'da sayfa başlığı bu görselin üstüne bindirilir), **Alıntı (Excerpt)** (kart üzerindeki kısa özet — boş bırakılırsa içerikten otomatik kısaltılır).
- **Anasayfa**: en yeni 3 haber otomatik olarak "Haberler" bölümünde gösterilir (hiç haber yoksa bölüm hiç görünmez). **Haberler Sayfası** (`/haberler/`): tüm haberleri sayfalı olarak listeler.
- **Çoklu dil**: `Ürünler`/`Kataloglar` gibi Polylang ile çevrilebilir işaretlenmiştir (yukarıdaki kurulum adım 3) — her haberin "+ Çeviri Ekle" ile diğer 3 dildeki karşılığını girin.

## Kalite Belgelerimiz — PDF Önizleme Notu

Bir "Kalite Belgesi" slotunun "Belge" alanına PDF yüklediğinizde, tema o PDF'in İLK SAYFASINI otomatik olarak küçük bir görsele çevirmeye çalışır (kartta boş/ikon yerine gerçek belge önizlemesi görünsün diye). Bunun çalışması sunucunuzda **Ghostscript** kurulu ve PHP'nin **Imagick** eklentisinin (veya `exec()` fonksiyonunun) açık olmasına bağlıdır — birçok paylaşımlı hosting'te bu KAPALI olabilir (güvenlik amacıyla). Önizleme üretilemezse kart otomatik olarak genel bir belge ikonuna düşer, site bozulmaz.

Eğer canlı sunucunuzda otomatik önizleme çalışmıyorsa (veya çalışıyor ama görüntüsünü beğenmediyseniz), iki seçeneğiniz var:
- **Önizleme Görseli** alanına ayrıca kendi seçtiğiniz bir görsel (temiz bir logo/rozet fotoğrafı) yükleyin — "Belge" alanındaki PDF olduğu gibi kalır (tıklanınca yine o açılır), sadece kartta görünen resim değişir. **Bu, aynı sistem Kataloglar sayfasında da geçerlidir**: bir Katalog'a **Öne Çıkan Görsel** eklerseniz kartta PDF önizlemesi yerine o görsel kullanılır.
- Veya "Belge" alanına PDF yerine doğrudan belgenin taranmış/fotoğraflanmış bir görselini (JPG/PNG) yükleyin — aynı alan ikisini de kabul eder.

## Yükleme Boyutu Sınırı (PDF/Görsel)

wp-admin → Medya yükleme ekranında gördüğünüz "Maksimum yükleme boyutu" (genelde 2MB), WordPress'in değil **hosting'in PHP ayarının** (`upload_max_filesize`, `post_max_size`) sınırıdır — bu tema kodundan değiştirilemez, hosting tarafında yükseltilmesi gerekir:

- **cPanel'li hosting (çoğu Türk paylaşımlı hosting)**: cPanel → "MultiPHP INI Editor" (veya "Select PHP Version → Options") → `upload_max_filesize` ve `post_max_size` değerlerini örn. `64M` yapıp kaydedin.
- **cPanel yoksa / yukarıdaki çalışmazsa**: WordPress kurulumunun kök dizinindeki `.htaccess` dosyasının başına şunu eklemeyi deneyin (yalnızca klasik "mod_php" host'larda çalışır, PHP-FPM'de etkisi olmaz):
  ```
  php_value upload_max_filesize 64M
  php_value post_max_size 64M
  ```
- **Hiçbiri işe yaramazsa**: hosting desteğine "PHP upload_max_filesize ve post_max_size değerlerini 64M'ye çıkarır mısınız?" diye yazın — bu standart bir istektir, her hosting destek ekibi yapabilir.

(Yerel Docker test ortamında bu zaten `local-dev/uploads.ini` ile 64MB'a çıkarılmış durumda.)

## Dosya Yapısı

```
cikolata-makine/
├── style.css                    Tema başlığı
├── functions.php                Kurulum, enqueue, menüler
├── header.php / footer.php      Ortak site iskeleti
├── front-page.php               Anasayfa (hero slider, kategori vitrini, öne çıkanlar, referans şeridi, kalite belgeleri, haberler, katalog banner)
├── page-urunler.php             Ürünler kategori indeksi (slug: urunler — sayfa BAŞLIĞI değiştirilebilir, slug değişemez)
├── taxonomy-makine_kategori.php Kategori / alt kategori + ürün listesi
├── single-makine.php            Makine detay sayfası
├── page-kataloglar.php          Kataloglar (PDF) grid sayfası (slug: kataloglar — page-urunler.php ile aynı desen)
├── page-haberler.php            Haberler grid/listeleme sayfası (slug: haberler — aynı desen)
├── single-haber.php             Haber detay sayfası
├── page.php                     Genel içerik sayfası (Kurumsal, Hakkımızda, İletişim...)
├── page-templates/
│   └── serbest-sayfa.php        Seçimlik "Serbest Sayfa (HTML/Kod)" şablonu — sarmalayıcısız, tam serbest
├── single.php / index.php       Blog yazı detayı / listesi
├── 404.php                      Sayfa bulunamadı
├── inc/
│   ├── cpt-taxonomies.php       makine / katalog / referans / haber post type'ları + makine_kategori taksonomisi
│   ├── acf-fields.php           Tüm özel alan tanımları (kod tabanlı, ACF arayüzünden bağımsız)
│   ├── template-tags.php        breadcrumb, kategori/ürün kartı, PDF satırı gibi paylaşılan render fonksiyonları
│   ├── customizer.php           Görünüm → Özelleştir → "İletişim & WhatsApp" (telefon/adres/WhatsApp/sosyal linkler)
│   └── strings.php              Ön yüzdeki tüm sabit metinlerin tek sözlüğü (cm__()) — çoklu dil çevirisi buradan akar
└── assets/
    ├── css/main.css             Onaylanan tasarımın (beyaz/kırık beyaz + açık mavi, Segoe UI) tüm stilleri
    └── js/main.js                Hero slider + galeri küçük görsel geçişi
```

## Çoklu Dil Kurulumu (Türkçe + İngilizce + Rusça + İspanyolca + Arapça)

Tema, **Polylang (ücretsiz sürüm)** ile çalışacak şekilde baştan hazırlanmıştır. URL yapısı: Türkçe (varsayılan dil) önekssiz kalır (`/urunler/`), diğer diller önek alır (`/en/urunler/`, `/ru/...`, `/es/...`, `/ar/...`). Arapça **RTL** (sağdan sola) — Polylang'i "Arabic" olarak eklediğinizde bunu otomatik algılar, ekstra bir ayar gerekmez (bkz. aşağıdaki "RTL / Arapça Notu").

### 1) Kurulum sırası (bu sıra önemlidir)

1. **Eklentiler → Yeni Ekle**: "Polylang" kurup etkinleştirin. İlk açılan kurulum sihirbazında Türkçe/İngilizce/Rusça/İspanyolca/Arapça dillerini ekleyin, **varsayılan dil Türkçe** olarak seçin.
2. Sihirbazın "mevcut içeriği varsayılan dile ata" adımını mutlaka çalıştırın — bu atlanırsa mevcut makine/kategori/sayfa içerikleriniz dil filtresine takılıp sitede görünmez olur.
3. **Diller → Ayarlar → Custom Post Types and Taxonomies**: `Ürünler` (makine), `Kataloglar` (katalog) ve `Haberler` (haber) post type'larını, `Ürün Kategorileri` (makine_kategori) taksonomisini "çevrilebilir" işaretleyip kaydedin. (`Referanslar` işaretlemeyin — logo/isim dilden bağımsızdır.)
4. **Diller → Ayarlar → URL sekmesi**: "Anasayfa URL'i sayfa adı/id yerine dil kodunu içersin" seçeneğini **işaretleyin** — bu işaretlenmezse İngilizce/Rusça/İspanyolca/Arapça anasayfa `/en/` yerine `/en/anasayfa-slug-adi/` gibi yanlış bir adrese yönlenir.
5. **Ayarlar → Kalıcı Bağlantılar → Kaydet** (rewrite kurallarını tazeler).

### RTL / Arapça Notu

Arapça, artık diğer 4 dille (TR/EN/RU/ES) tam eşdeğer: sayfa/menü iskeleti, 57 ürünün tamamı (başlık/açıklama/teknik özellikler/kategori), Kurumsal aile sayfalarının gövde metni, Anasayfa'nın hero/istatistik alanları, İletişim sayfası ve ayrı bir Arapça Contact Form 7 formu (`iletisim-formu-ar`) dahil olmak üzere içerik makine çevirisiyle dolduruldu. Katalog/sertifika PDF **dosyalarının kendisi** çevrilemedi (bu, RU/ES'te de var olan genel bir kısıt — sadece başlık/meta çevrildi, dosya TR'den kopyalandı). Makine çevirisiyle girilen tüm metinlerin bir Arapça anadili konuşan tarafından gözden geçirilmesi, özellikle teknik terimler ve ürün açıklamaları için önerilir.

RTL düzen (sağdan sola) tamamen otomatik — `assets/css/rtl.css` sadece Arapça'da (`is_rtl()`) yüklenir, yeni bir CSS kuralı eklerken `left`/`right`/`border-left`/`margin-left` gibi yön-sabit bir şey yazdıysanız orada da bir karşılığı gerekip gerekmediğini kontrol edin.

### 2) Aynı slug + dil öneki hakkında önemli not

Bu yapıda her sayfa/kategori TÜM dillerde aynı slug'ı kullanır (örn. TR `/urunler/`, EN `/en/urunler/`). **WordPress çekirdeği ve Polylang ücretsiz sürüm, sayfa/terim slug benzersizliğini varsayılan olarak dilden bağımsız kontrol eder** — yani ikinci dilde aynı slug'la bir sayfa/kategori oluşturduğunuzda WordPress bunu otomatik olarak `urunler-2` gibi bir slug'a çevirebilir ve `/en/urunler/` adresi yanlış (Türkçe) içeriğe yönlenebilir.

Bunu önlemek için `functions.php` içine iki özel filtre eklenmiştir (`cm_pll_unique_post_slug`, `cm_pll_unique_term_slug`) — bunlar sayesinde **Polylang'in "+ Çeviri Ekle" ekranından** (bkz. aşağıdaki 4. madde) oluşturduğunuz çeviriler doğru slug'ı otomatik korur. Bu ikisi kod tarafında zaten çözülmüştür, ekstra bir işlem gerekmez — sadece çevirileri mutlaka Polylang'in "+ Çeviri Ekle" ekranından oluşturun, sayfayı/kategoriyi Polylang'in dil seçimi dışında bağımsız/manuel bir yöntemle (örn. içe aktarma aracı) oluşturmayın.

### 3) Menüler

Polylang'de tek menü diller arasında otomatik filtrelenmez — **her dil için AYRI bir menü** oluşturulmalıdır. Konum başına bir dil değil, **dil başına bir menü** vardır:

1. **Görünüm → Menüler → Yeni menü oluştur**: her dil için ayrı bir menü oluşturun (örn. "Ana Menü", "Ana Menü (EN)", "Ana Menü (RU)", "Ana Menü (ES)").
2. Her menüyü düzenlerken, menü ayarları kutusunda **sadece o dile ait sayfa/kategori/link** ekleyin — örn. "Ana Menü (EN)" içine sadece İngilizce sayfalara/kategorilere giden öğeler eklenmeli, Türkçe bir sayfa asla eklenmemeli.
3. Menüyü **"Üst Menü (Header)"** konumuna atarken, Polylang menü ekranının üstünde her dil için AYRI bir konum seçici görürsünüz (örn. "Üst Menü (Header) — Türkçe", "— English" gibi) — her dilin menüsünü kendi seçiciyle atayın. "Footer — Hızlı Linkler" için de aynısını yapın (4 dil × 2 konum = en fazla 8 menü). "Footer — Kategoriler" konumu otomatik/dinamik olduğundan menü atamanıza gerek yoktur.

> **Sık yapılan hata — bunu asla yapmayın:** Bir dilin sayfasını/kategorisini, YANLIŞLIKLA başka bir dilin menüsüne eklemek (örn. İngilizce "Home" sayfasını Türkçe menüye sürüklemek). Polylang bunu engellemez, sessizce kabul eder — sonuç, o dilde gezinirken menüde diğer dildeki bir öğenin de görünmesi, ya da menünün beklenmedik şekilde karışık görünmesidir (bu proje sırasında bir kez yaşanmış ve düzeltilmiş bir hatadır).
>
> Bunu yakalamak için `functions.php` içine bir **güvenlik kontrolü** eklenmiştir (`cm_check_menu_language_mismatches`): wp-admin'de Panel veya Menüler ekranını her açtığınızda, herhangi bir menüde dili uyuşmayan bir öğe varsa üstte sarı bir uyarı kutusu ("Menü dil uyuşmazlığı bulundu") otomatik çıkar ve hangi menüde hangi öğenin sorunlu olduğunu tam olarak söyler. Böyle bir uyarı görürseniz, belirtilen öğeyi o menüden kaldırıp doğru dildeki menüye taşıyın.
>
> **İkinci bir gizli tuzak — WordPress'in "Yeni sayfaları otomatik ekle" özelliği:** Görünüm → Menüler → bir menüyü düzenlerken "Menü Ayarları" altında **"Bu menüye yeni üst-seviye sayfaları otomatik ekle"** adlı bir kutu vardır. Bu işaretliyse, HANGİ dilde olursa olsun (Türkçe, İngilizce, fark etmez) oluşturulan HER yeni sayfa o menüye sessizce eklenir — Polylang bunu engellemez, çünkü bu tamamen WordPress çekirdeğinin özelliğidir. Bu proje sırasında gerçekten yaşanmış bir hatadır: yeni dil sayfaları oluşturulduğunda Türkçe menüye otomatik eklenip menüyü karıştırmıştır. **Bu yüzden bu özellik artık koddan da korunuyor**: yukarıdaki güvenlik kontrolü, dile atanmış menülerden herhangi birinde bu kutu işaretli bulunursa hem sizi uyarır HEM DE otomatik olarak kapatır. Yine de wp-admin'de bu kutuyu elle tekrar işaretlemeyin.

### 4) İçerik çevirisi (elle girilmeli — otomatik çeviri yoktur)

Her makine, kategori, sayfa (Anasayfa, Ürünler, İletişim, Kurumsal...) ve katalog kaydı için:

1. wp-admin'de ilgili içeriğin listesinde veya düzenleme ekranında sağdaki **Diller** kutusunda, çevirmek istediğiniz dilin yanındaki **"+"** ikonuna tıklayın.
2. Açılan yeni taslakta başlık/açıklama/teknik özellikler gibi tüm alanları o dilde yeniden girin.
3. Kategori ikonu, makine fotoğrafları gibi **görseller dilden dile otomatik kopyalanmaz** — aynı görseli her dilde ayrıca seçmeniz (veya yeniden yüklemeniz) gerekir.
4. **Sabit arayüz metinleri** (breadcrumb "Anasayfa", sekme adları "Ürün Açıklaması"/"Teknik Özellikler", buton yazıları, footer başlıkları, 404 mesajı vb.) admin panelinde değil, **Diller → Dize Çevirisi** ekranından çevrilir — "Çikolata Makine Tema" grubu altında tüm bu metinler listelenir, her biri için İngilizce/Rusça/İspanyolca karşılığını girip kaydedin.
5. Site başlığı/açıklaması (Ayarlar → Genel) da aynı ekrandan ("WordPress" grubu altında) her dilde ayrıca girilmelidir.

Bir dilde henüz çeviri girilmemiş içerik varsa, o dilin ziyaretçisi otomatik olarak Türkçe (varsayılan dil) içeriğini görür — site bozulmaz, sadece o bölüm henüz çevrilmemiş demektir.

### 5) Dil değiştirici ve dillerin görünürlüğü

Header'daki dil değiştirici (TR/EN/RU/ES), bir dilde **hiç içerik yoksa o dili otomatik gizler** (Polylang'in `hide_if_empty` varsayılan davranışı) — örneğin Rusça için henüz hiçbir sayfa/makine çevrilmediyse, dil değiştiricide "ru" hiç görünmez. Bu bir hata değildir; o dilde ilk içeriği (örn. Anasayfa çevirisini) girdiğiniz an dil değiştiricide otomatik belirir.

## Bilinen sınırlamalar / sonraki adımlar

- Gerçek ürün fotoğrafları/PDF'ler yüklendikçe teknik-çizim yer tutucular otomatik kaybolur — ayrıca bir işlem gerekmez.
- İletişim formu ve harita otomatik gelir, elle shortcode eklemeye gerek yoktur (bkz. yukarıdaki "İletişim Formu & Harita" bölümü). Formdan gelen e-postaların spam'e düşmemesi için canlı hostingde bir SMTP eklentisi (örn. WP Mail SMTP) kurulması önerilir — bkz. `02_HOSTING_KURULUM_REHBERI.md` §2.6.
- Otomatik/makine çevirisi yoktur — kalite ve doğruluk için tüm çeviriler elle girilir (bkz. yukarıdaki "Çoklu Dil Kurulumu" bölümü).
