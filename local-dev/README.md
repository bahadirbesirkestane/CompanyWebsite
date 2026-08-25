# Yerel Test Ortamı (Docker)

`cikolata-makine` temasının, örnek içeriklerle doldurulmuş, tarayıcıda gezilebilir yerel bir kopyası.

## Erişim

- **Site**: http://localhost:8090/ (`127.0.0.1` değil `localhost` kullanın — WordPress site adresi bu şekilde ayarlı)
- **Admin panel**: http://localhost:8090/wp-admin/
  - Kullanıcı: `admin`
  - Şifre: `admin123`

## Başlatma / durdurma

```bash
cd local-dev
docker compose up -d      # başlatır (arka planda)
docker compose down       # durdurur, içerik/veritabanı korunur
docker compose down -v    # durdurur ve TÜM içeriği siler (sıfırdan başlamak için)
```

Tema dosyaları (`../wordpress-tema/cikolata-makine`) container'a canlı bağlıdır — bir PHP/CSS dosyasını düzenleyip kaydettiğinizde, sadece tarayıcıda sayfayı yenilemeniz yeterlidir.

## İçerdiği örnek veri

- 6 örnek makine (temperleme, kaplama, kalıplama, soğutma, tank), gerçek teknik özellik tablolarıyla
- 3 kategori + 3 alt kategori
- 3 örnek katalog kaydı (PDF dosyası yok — admin panelinden yüklenebilir)
- 4 örnek referans firma (logosuz — metin olarak görünür)
- Anasayfa hero slaytları, istatistik şeridi, menü, sayfalar (Anasayfa/Kurumsal/Makinelerimiz/Kataloglar/Blog/İletişim) hazır

## Notlar

- **ACF (ücretsiz sürüm)** kurulu — tema artık ACF'in Repeater/Galeri (PRO'ya özel) alan tiplerini hiç kullanmıyor, bu yüzden hem burada hem canlı sitede ücretsiz sürüm yeterli, PRO'ya gerek yok.
- Gerçek ürün fotoğrafları/PDF/logo yüklemediğiniz sürece yer tutucu ikonlar görünür — bu normaldir.
- Bu ortam sadece **yerel önizleme** içindir, canlı hostinge taşınmaz. Canlıya geçiş için `wordpress-tema/cikolata-makine/README.md` dosyasındaki kurulum adımlarını izleyin.
