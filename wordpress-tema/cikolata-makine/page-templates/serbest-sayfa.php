<?php
/**
 * Template Name: Serbest Sayfa (HTML / Kod)
 * Template Post Type: page
 *
 * Herhangi bir Sayfaya, düzenleme ekranındaki "Sayfa Öznitelikleri" kutusundan
 * "Şablon: Serbest Sayfa (HTML / Kod)" seçilerek uygulanabilir. Diğer page-*.php
 * şablonlarının (page.php, page-urunler.php, page-kataloglar.php, page-haberler.php)
 * OTOMATİK eklediği hiçbir şey basılmaz — breadcrumb yok, sayfa başlığı yok, banner
 * yok, "alt sayfalar" grid'i yok. Sadece site iskeleti (header/footer — menü, logo,
 * WhatsApp butonu, footer linkleri) korunur; içerik editöründeki HTML (Klasik
 * Düzenleyici'nin "Kod" sekmesinden girilen ham kod dahil) OLDUĞU GİBİ, hiçbir
 * sarmalayıcı (`.wrap` genişlik sınırı bile) olmadan basılır — tam serbest bir
 * tuval. Standart sayfa genişliğinde kalmak isterseniz içeriğinizi kendi
 * `<div class="wrap">...</div>` etiketinizle sarmalayabilirsiniz.
 *
 * NOT: Yönetici (Administrator) rolü varsayılan olarak "unfiltered_html" yetkisine
 * sahiptir — bu sayede <script>/<style>/<iframe> gibi etiketler dahil GİRİLEN HTML
 * KAYDEDİLİRKEN budanmaz/süzülmez. Düşük yetkili bir kullanıcı (Editör/Yazar) bu
 * şablonu kullanırsa WordPress çekirdeği güvenlik gereği bazı etiketleri otomatik
 * temizler — tam serbestlik için Yönetici hesabıyla düzenleyin.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
the_post();
the_content();
get_footer();
