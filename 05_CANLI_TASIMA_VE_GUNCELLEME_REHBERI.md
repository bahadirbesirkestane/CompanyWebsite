# Canlıya Taşıma ve Güncelleme — Sorularınızın Cevapları

Bu doküman, sormuş olduğunuz şu soruları cevaplıyor:

1. FTP olmadan direkt local'den yükleme yapılabilir mi?
2. Uploads (fotoğraf/PDF) için tek tek seçmeden, toplu ve otomatik şekilde ilgili ürünlere/sayfalara yerleşecek şekilde nasıl yüklerim?
3. Ben (Claude) olmadan, yerelde yaptığınız bir değişikliği canlıya kendiniz nasıl taşırsınız — adım adım?

**Durum tespiti:** `https://demofreme.com` adresini kontrol ettim — site zaten canlıda ve çalışıyor, ama **26 Ağustos'taki eski içerikle** (Arapça dil yok, son yaptığımız hata düzeltmeleri yok, kategori/sidebar güncellemeleri yok). Yani bu bir "ilk kurulum" değil, **"eski canlı içeriği yeni yerel içerikle değiştirme"** işlemi olacak. Bu, aşağıdaki cevapları biraz basitleştiriyor — açıklıyorum.

Bu dosya, daha önce hazırlanmış olan [`04_HOSTING_COM_TR_PERFORMANS_CANLI_YAYIN_REHBERI.md`](04_HOSTING_COM_TR_PERFORMANS_CANLI_YAYIN_REHBERI.md) dosyasının **YERİNE GEÇMİYOR** — cPanel'in genel ekranları (phpMyAdmin, Dosya Yöneticisi, Select PHP Version vb.) için o dosyadaki adımlar hâlâ geçerli. Bu dosya, sizin şu an sorduğunuz **spesifik sorulara** odaklanıyor ve o rehberi güncel duruma göre tazeliyor.

---

## Önce kısa cevaplar (TL;DR)

| Soru | Kısa cevap |
|---|---|
| FTP olmadan yükleme olur mu? | **Kısmen evet.** cPanel'in "Dosya Yöneticisi" tarayıcıdan dosya yükleyip zip açabiliyor — ayrı bir FTP programına (FileZilla vb.) gerek yok. Ama çok büyük dosyalar (200+ MB) için FTP daha güvenilir, aşağıda açıklıyorum. |
| Fotoğrafları tek tek seçmeden toplu yükleyip ürünlere otomatik bağlayabilir miyim? | **Evet, kesinlikle.** Bu, WordPress'in doğal çalışma şekli — açıklaması aşağıda. |
| Sen olmadan ben nasıl güncelleme yaparım? | Aşağıda adım adım, iki senaryo (sadece kod / içerik dahil) için ayrı ayrı anlattım. |

---

## 1) FTP olmadan yükleme yapılabilir mi?

**Kısa cevap: Evet, çoğu şey için.** cPanel'in içinde **"Dosya Yöneticisi" (File Manager)** adında, tarayıcıdan kullanılan bir araç var. Bunu kullanırken:

- Bilgisayarınızdan bir dosya (örn. bir `.zip`) seçip **"Upload"** butonuyla sunucuya yüklersiniz — bu, teknik olarak arka planda yine bir dosya transferi ama siz FileZilla gibi ayrı bir program kurmuyorsunuz, her şey tarayıcı içinde oluyor.
- Yüklenen `.zip` dosyasına sağ tıklayıp **"Extract"** (Aç) dediğinizde, sunucu dosyayı kendi içinde açıyor — siz tek tek dosya sürüklemiyorsunuz.

**Yani "FTP'siz" derken kastettiğiniz şey muhtemelen şu:** ayrı bir FTP programı kurup kullanıcı adı/şifre girip bağlanmak zorunda kalmamak. Bunu **Dosya Yöneticisi ile başarabilirsiniz.**

**Ama bir istisna var — çok büyük dosyalar:** Sitenizin tüm fotoğraf/PDF arşivi (uploads klasörü) sıkıştırılmış halde **~210 MB**. Bu boyutta bir dosyayı tarayıcıdan yüklerken:
- İnternet bağlantınız yavaşsa veya kesintiye uğrarsa yükleme baştan başlar.
- cPanel'in bazı ayarlarında tarayıcı-üzeri yükleme için bir üst sınır olabilir (genelde 100-250 MB civarı, hosting'e göre değişir).

Bu yüzden **sadece bu tek büyük dosya için** FTP (veya cPanel'in "FTP Hesapları" bölümünden oluşturacağınız bir hesapla FileZilla kullanmak) daha güvenilirdir — kesintide kaldığı yerden devam edebilir. Diğer her şey (tema dosyası birkaç yüz KB, veritabanı dosyası birkaç MB) tarayıcıdan sorunsuz yüklenir.

**Alternatif — hiç dosya yüklemeden, otomatik senkronizasyon (sadece KOD için, ileride kurulabilir):** cPanel'de "Git™ Version Control" adında bir özellik varsa, sitenizin GitHub deposunu cPanel'e bağlayıp "güncellemeleri çek" (pull) butonuna basarak **tema dosyalarını** hiç manuel yüklemeden güncelleyebilirsiniz. Bu **sadece kod** için işe yarar (veritabanı/fotoğraflar için değil) ve kurulumu birkaç adım gerektirir — isterseniz ayrı bir seferde birlikte kurabiliriz. Şimdilik Dosya Yöneticisi/FTP yeterli.

---

## 2) Fotoğrafları/PDF'leri tek tek seçmeden toplu yükleme — nasıl çalışıyor?

Bu, WordPress'i biraz "içeriden" anlamayı gerektiriyor ama mantığı basit:

**WordPress'te bir ürünün fotoğrafı, o ürüne "elle o an tıklanarak" değil, VERİTABANINDA saklanan bir numarayla bağlanır.** Örneğin "Bilyalı İnceltme Değirmeni" ürününün veritabanı kaydında şöyle bir bilgi var: *"Bu ürünün öne çıkan görseli, 575 numaralı dosya"*. 575 numaralı dosya da kendi kaydında şunu tutuyor: *"Bu dosya `wp-content/uploads/2026/07/gorsel.jpg` yolunda duruyor"*.

**Bu yüzden, eğer:**
1. Veritabanını (tüm bu "hangi numara nerede" bilgisiyle) canlıya aktarırsanız, **VE**
2. Aynı dosyaları AYNI klasör yapısıyla (`wp-content/uploads/2026/07/gorsel.jpg` gibi) canlıya kopyalarsanız,

...WordPress otomatik olarak ikisini eşleştirir. **Hiçbir ürüne girip "resim seç" demenize gerek kalmaz** — veritabanını açtığınız an, o numaralar zaten oradaki dosyalarla eşleşir ve her şey (ürün fotoğrafları, referans logoları, haber görselleri, kategori ikonları, katalog PDF'leri, sertifika görselleri) otomatik yerli yerine oturur.

**Pratikte yapmanız gereken tek şey:**
1. Yerel bilgisayarınızdaki `wp-content/uploads` klasörünün TAMAMINI tek bir `.zip` dosyası yapmak (ben bunu sizin için hazırlayabilirim).
2. Bu tek `.zip` dosyasını cPanel Dosya Yöneticisi'nde `wp-content/` klasörünün içine yükleyip **bir kere** "Extract" demek.
3. Aynı anda, o dosyalarla eşleşen veritabanını da import etmek (aşağıdaki adım adım rehberde anlatıyorum).

Bu ikisi (dosyalar + veritabanı) **aynı "anlık görüntüden" (snapshot)** geldiği sürece, hiçbir şey elle bağlanmaz, hepsi otomatik çalışır. **Tek dikkat edilecek nokta:** eski bir veritabanıyla yeni bir uploads klasörünü (veya tam tersini) karıştırmamak — ikisi de aynı "an"a ait olmalı, ben size ne zaman hangi ikisini birlikte kullanacağınızı söylerim.

---

## 3) Şimdi yapılacak: canlıdaki eski içeriği yeni içerikle değiştirme

`https://demofreme.com` şu an eski (Arapçasız, hata düzeltmeleri öncesi) içerikle çalışıyor. Yeni durumu canlıya almak için yapılacaklar:

### Adım 0 — Taşıma paketi HAZIR (2026-08-28)

Paket yeniden üretildi ve `deployment-package/` klasöründe hazır bekliyor. Ayrıca paketi hazırlamadan ÖNCE, sormuş olduğunuz gibi **5 dilin (TR/EN/RU/ES/AR) tüm içeriğini** (metin, görsel, katalog, başlık) tek tek karşılaştırıp eksikleri giderdim — bulunan ve düzeltilen gerçek eksikler:

- **57 ürünün İngilizce/Rusça/İspanyolca versiyonlarında ürün fotoğrafları hiç bağlı değildi** (459 eksik görsel bağlantısı — öne çıkan görsel + galeri görselleri) — TR'deki fotoğraflarla eşitlendi, artık 5 dilde de AYNI fotoğraflar kullanılıyor.
- Anasayfanın "hero" görselleri ve ISO 9001 sertifika rozeti İngilizce/Rusça/İspanyolca'da eksikti — TR ile eşitlendi.
- İletişim ve Kurumsal sayfalarındaki "Uluslararası İletişim" kartları (ülke/kişi bilgisi) bazı dillerde eksikti — eklendi, ülke isimleri o dile çevrildi (İtalya→Italy/Италия/Italia gibi).
- Tek haber yazısı ("ProSweets Fuarı") sadece Türkçe idi, hiç çevirisi yoktu — 4 dile de çevrilip eklendi.
- Arayüz metinlerinde ("Buradayız", "Telefon", "2 sütun görünüm" gibi) 15 anahtar İngilizce'de, 13'er anahtar Rusça/İspanyolca'da çevrilmemiş kalmıştı — tamamlandı.
- İspanyolca/Rusça sayfalarda tarih gösterimi (haber tarihleri gibi) İngilizce ay ismiyle çıkıyordu — WordPress'in bu dillere ait çekirdek dil paketleri hiç kurulu değilmiş, kuruldu.

Paketin içeriği:

- **`deployment-package/cikolata-makine-tema.zip`** (~93 KB) — tema dosyaları.
- **`deployment-package/site-export-demofreme.sql`** (~7,1 MB) — `https://demofreme.com` için hazır veritabanı. *(Önemli teknik detay: veritabanının içinde bazı ayarlar "serialize" denen özel bir formatta saklanıyor ve içinde karakter sayısı bilgisi var. Adresi düz metin "bul-değiştir" ile değiştirmek bu sayıyı bozar, o ayarlar bozulur/kaybolur. Ben bunu `wp search-replace` adlı özel bir araçla, sayıları da otomatik düzelterek yaptım — 819 değişiklik, tek bir `localhost:8090` referansı bile kalmadığı doğrulandı. Siz bunu phpMyAdmin'in basit "bul-değiştir" özelliğiyle KENDİNİZ yapmaya çalışmayın, veri kaybına yol açar.)*
- **`deployment-package/uploads.zip`** (~232 MB) — tüm ürün/sayfa/haber fotoğrafları + PDF kataloglar.
- **`deployment-package/languages.zip`** (~5 MB, YENİ) — İspanyolca/Rusça tarih gösterimi düzeltmesi için gereken WordPress çekirdek dil dosyaları.

Alan adı değiştiğinde (§4/C) veya yeni bir toplu içerik güncellemesi gerektiğinde (§4/B2) bana söylediğinizde bu paketi aynı şekilde tazelerim.

### Adım 1 — Canlıdaki mevcut veritabanını yedekleyin (güvenlik için)

Üzerine yazmadan önce bir kopya alın:
1. cPanel → **phpMyAdmin**'i açın.
2. Sol menüden sitenizin veritabanını seçin.
3. Üstten **"Dışa Aktar" (Export)** → **"Hızlı" (Quick)** → **Git (Go)**.
4. İnen `.sql` dosyasını bilgisayarınızda bir yere kaydedin (isim: örn. `demofreme-eski-yedek-28agustos.sql`).

Bu adım 2 dakika sürer ve bir şeyler ters giderse geri dönüş imkanı sağlar.

### Adım 2 — Yeni veritabanını import edin

1. Aynı phpMyAdmin ekranında, üstten **"İçe Aktar" (Import)** sekmesine geçin.
2. Benim hazırladığım yeni `.sql` dosyasını seçin, **Git (Go)**'ya basın.
3. Bu işlem, mevcut (eski) tabloları **silip yerine yenisini** koyar — Adım 1'de yedek aldığınız için sorun değil.

⚠️ **Not:** Bu işlem şu an demo aşamasında olduğunuz için güvenli — canlıda henüz gerçek ziyaretçi verisi (form mesajı, sipariş vb.) yok. Site gerçek müşteri trafiği almaya başladıktan SONRA bu yöntemi kullanmadan önce mutlaka §5'i (aşağıda) okuyun.

### Adım 3 — Tema dosyasını güncelleyin

wp-admin → **Görünüm → Temalar → Yeni Ekle → Tema Yükle** → yeni tema `.zip`'ini seçip yükleyin, **Etkinleştir**'e basın. (Detaylı alternatif yöntem: `04_HOSTING_COM_TR...` dosyasının §5.2'si.)

### Adım 4 — Uploads ve dil dosyalarını güncelleyin

1. cPanel → **Dosya Yöneticisi** → `public_html/wp-content/` klasörüne girin.
2. `uploads.zip` dosyasını buraya yükleyin (büyük dosya — yavaş/kesintili bağlantınız varsa FTP kullanın, bkz. §1).
3. Yüklenen dosyaya sağ tık → **Extract**.
4. Sorulursa **"üzerine yaz" (overwrite)** seçeneğini onaylayın — eski `uploads` klasörünün üzerine yeni dosyalar gelecek.
5. **Aynı şekilde `languages.zip`'i de yükleyip Extract edin** — İspanyolca/Rusça sayfalarda tarih gösteriminin doğru dilde çıkması için gerekli.

### Adım 5 — Kalıcı Bağlantıları tazeleyin (ÖNEMLİ, atlamayın)

wp-admin → **Ayarlar → Kalıcı Bağlantılar** → hiçbir şey değiştirmeden tekrar **Kaydet**'e basın.

Bu adım özellikle önemli çünkü **Arapça (`/ar/...`) adresleri yeni eklendi** — bu adım atlanırsa Arapça sayfalar (ve hatta bazı ürün/kategori sayfaları) "Sayfa Bulunamadı" hatası verebilir.

### Adım 6 — Kontrol listesi

- [ ] `https://demofreme.com` TR'de sorunsuz açılıyor
- [ ] Dil değiştiriciyle `/en/`, `/ru/`, `/es/`, `/ar/` adreslerine geçilebiliyor
- [ ] Arapça sayfa sağdan sola (RTL) düzgün görünüyor
- [ ] Bir kategori sayfasına girip **2. sayfaya** geçtiğinizde dil/menü bozulmuyor (yakın zamanda düzelttiğimiz bir hataydı)
- [ ] Ürün fotoğrafları görünüyor (uploads doğru geldiyse otomatik gelir)
- [ ] Katalog PDF'leri indirilebiliyor
- [ ] İletişim formu test edilip e-posta geldi
- [ ] wp-admin şifresi güçlü bir şifreyle değiştirildi

---

## 4) Bundan sonraki güncellemeler — sen (Claude) olmadan, adım adım

Buradan sonrası, **gelecekte** yerelde bir değişiklik yapıp bunu canlıya taşımak istediğinizde izleyeceğiniz yol. İki farklı değişiklik türü var, ikisi de FARKLI şekilde taşınıyor:

### A) Değişiklik SADECE görünüm/davranışla ilgiliyse (CSS, buton, animasyon, bir metin etiketinin İngilizcesi vb.)

Bu tür değişiklikler **tema dosyalarında** olur, veritabanını etkilemez. Adımlar:

1. Değişikliği yerelde (`localhost:8090`) yapıp gözle kontrol edin.
2. Değişen dosyaları bilmiyorsanız bana sorun — "hangi dosyalar değişti" derseniz `git status` ile söylerim.
3. **Küçük bir değişiklikse** (1-2 dosya): cPanel → Dosya Yöneticisi → `public_html/wp-content/themes/cikolata-makine/` içinde ilgili dosyayı bulup sağ tık → **Edit**, içeriği yerel dosyanızdakiyle değiştirip kaydedin.
4. **Çok sayıda dosya değiştiyse**: benden yeni bir tema `.zip`'i isteyin, Görünüm → Temalar → Tema Yükle ile üzerine yükleyin (WordPress otomatik değiştirilenleri günceller, silineni bozmaz).
5. Sitenizde bir önbellek (cache) eklentisi varsa (LiteSpeed Cache gibi) **"Purge All / Tümünü Temizle"** yapın — yoksa eski görünüm bir süre daha gösterilmeye devam edebilir.

**Veritabanına HİÇ dokunmuyorsunuz** — bu yüzden bu tür güncellemeler tamamen güvenli, canlıdaki içeriği (ürünler, sayfalar) etkilemez.

### B) Değişiklik İÇERİKLE ilgiliyse (yeni ürün, ürün metni, yeni dil, sayfa metni, yeni fotoğraf)

Burada iki seçeneğiniz var:

**Seçenek B1 — Küçük/tekil bir değişiklik (örn. bir ürünün açıklamasını düzeltmek, bir fotoğraf eklemek):**
En pratik yol, bunu **doğrudan canlı sitenin** (`https://demofreme.com/wp-admin`) kendi panelinden yapmaktır — yerelde hiç yapmayın. Böylece "hangi versiyon güncel" karmaşası hiç oluşmaz. Ürün düzenleme ekranları, fotoğraf yükleme, sayfa metni düzenleme — hepsi normal wp-admin kullanımı, benim yardımım gerekmez.

**Seçenek B2 — Büyük/toplu bir değişiklik (örn. "10 yeni ürün ekle", "bir dili baştan çevir", benimle yaptığımız gibi):**
Bu tür işler pratikte yerelde (benimle) yapılıyor çünkü toplu/otomatik işlemler gerektiriyor. Bu durumda canlıya taşımak için:

1. Yerelde değişikliği tamamlayıp test edin (benimle).
2. Benden **güncel bir veritabanı + uploads paketi** isteyin (yukarıdaki §3, Adım 0-2 ve 4 ile aynı).
3. **ÖNEMLİ FARK:** Eğer canlı site artık GERÇEK ziyaretçi/müşteri etkileşimi almaya başladıysa (form mesajları, yorum vb.), veritabanının TAMAMINI değiştirmek bunları SİLER. Bu durumda bana söyleyin — o zaman sadece DEĞİŞEN kısmı (örn. sadece yeni ürünleri) canlıya taşıyacak daha dikkatli bir yöntem uygularız (örn. `WP All Import` gibi bir eklenti ile sadece belirli ürünleri aktarmak, ya da canlıda formlardan gelen mesajları önce başka bir yere yedekleyip sonra geri yüklemek). Şu an demo aşamasında olduğunuz için bu bir sorun değil, ama **ne zaman gerçek trafiğe geçtiğinizi bana söylemeniz önemli** — o andan itibaren "her şeyi değiştir" yöntemini bırakırız.
4. Veritabanı + uploads + (varsa) tema güncellemesini yukarıdaki §3'teki gibi uygulayın.
5. Kalıcı Bağlantıları tazeleyin, kontrol listesini gözden geçirin.

### C) Alan adı değiştiğinde (siz "bu sonra değişecek" dediniz)

Alan adı `demofreme.com`'dan gerçek alan adınıza geçtiğinde:
1. Bana yeni alan adını söyleyin.
2. Veritabanını yeni alan adına göre **yeniden** hazırlarım (§3, Adım 0'daki `wp search-replace` işlemi, bu sefer yeni domainle).
3. cPanel'de alan adını bağlama adımları için `04_HOSTING_COM_TR...` dosyasının §4.3'üne bakın.
4. SSL sertifikasının yeni alan adı için de aktif olduğunu kontrol edin (cPanel → SSL/TLS Status → Run AutoSSL).

---

## 5) Site gerçek trafiğe geçtikten SONRA dikkat edilecek fark

Şu an (`demofreme.com`, demo aşaması) yukarıdaki "her şeyi değiştir" yöntemi tamamen güvenli. Ama site **gerçek müşterilere açıldıktan sonra**, artık canlıda "sadece sizin bildiğiniz" veri birikir: gelen iletişim formu mesajları, belki wp-admin'den elle yapılmış küçük düzeltmeler. O noktadan sonra:

- **Kod (tema) güncellemeleri** hâlâ yukarıdaki A yöntemiyle güvenle yapılabilir — hep öyle kalacak.
- **İçerik güncellemeleri** için artık "yerelde yap, veritabanını tamamen değiştir" yöntemini KULLANMAYIN — bu, o ana kadar canlıda birikmiş formu mesajlarını/düzenlemeleri siler. Bunun yerine:
  - Günlük/küçük içerik işlerini doğrudan canlı wp-admin'den yapın (Seçenek B1 kalıcı hale gelir), **veya**
  - Toplu bir işlem gerekiyorsa (yine benimle), bana söyleyin — o zaman "sadece değişen parçayı taşıyan" daha temkinli bir yöntem uygularız, veritabanının tamamını değiştirmeyiz.

Bu ayrımı unutmayın diye buraya kalıcı bir not olarak bıraktım — ilgili teknik detay zaten `CLAUDE.md` ve `04_HOSTING_COM_TR...` dosyasının §8.2'sinde de var.

---

## Özet — şimdi ne yapmalısınız?

1. Bu dosyayı okudunuz. ✅
2. Taşıma paketi hazır: `deployment-package/` klasöründe tema + veritabanı (demofreme.com'a göre hazır, 5 dilin tüm içeriği eşitlenmiş) + uploads + languages. ✅
3. Yukarıdaki **§3 (Adım 1-6)**'yı sırayla uygulayın.
4. Bir yerde takılırsanız (hata mesajı, "nasıl yapılır" sorusu) ekran görüntüsüyle bana dönün, birlikte ilerleriz.
