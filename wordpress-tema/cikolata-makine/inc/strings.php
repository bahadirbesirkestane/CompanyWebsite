<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Ön yüzde (ziyaretçinin gördüğü) tüm sabit metinlerin TEK kaynağı.
 * wp-admin'deki ACF/CPT/Customizer alan etiketleri BURADA YOK — onlar hep Türkçe
 * kalacak (admin paneli her zaman Türkçe yönetilecek), sadece site ziyaretçisinin
 * gördüğü metinler burada.
 *
 * Neden gettext (__()/esc_html__()) DEĞİL: .mo dosyası derlemiyoruz, çeviri tamamen
 * Polylang'in "Diller → Dize Çevirisi" ekranından (wp-admin, kod gerektirmeden)
 * yapılacak. Tek katman (cm__()) kullanmak, iki paralel çeviri sisteminin
 * birbirinden kopması riskini ortadan kaldırır.
 */
function cm_strings() {
	static $s = null;
	if ( $s === null ) {
		$s = array(
			'breadcrumb_anasayfa'       => 'Anasayfa',
			'breadcrumb_kirinti_yolu'   => 'Kırıntı yolu',
			'pill_tumu'                 => 'Tümü',
			'urun_etiketi'              => 'ÜRÜN',
			'one_cikan_etiket'          => 'Öne Çıkan',
			'gorsel_etiket'             => 'Görsel',
			'detaylari_gor'             => 'Detayları Gör →',
			'goruntule'                 => 'Görüntüle',
			'indir'                     => 'İndir',

			'menu_toggle_aria'          => 'Menüyü aç/kapat',
			'whatsapp_aria'             => "WhatsApp'tan yazın",
			'teklif_iste'               => 'Teklif İste',

			'footer_hizli_linkler'      => 'Hızlı Linkler',
			'footer_kategoriler'        => 'Kategoriler',
			'footer_iletisim'           => 'İletişim',
			'footer_haklar'             => 'Tüm hakları saklıdır.',

			'hero_eyebrow_default'      => 'Endüstriyel Çikolata Üretim Sistemleri',
			'hero_aciklama_default'     => 'Temperlemeden ambalaja, komple çikolata üretim hatları tasarlıyor ve kuruyoruz.',
			'hero_buton1_default'       => 'Makineleri İncele',
			'hero_buton2_default'       => 'Bize Ulaşın',
			'hero_onceki_slayt'         => 'Önceki slayt',
			'hero_sonraki_slayt'        => 'Sonraki slayt',
			'hero_slayt_secimi'         => 'Slayt seçimi',
			'hero_slayt_suffix'         => '. slayt',

			'urunler_kesfet_baslik'     => 'Kategoriye göre keşfedin',
			'ne_uretmek_eyebrow'        => 'Son Ürüne Göre',
			'ne_uretmek_baslik'         => 'Ne üretmek istiyorsunuz?',
			'vitrin_eyebrow'            => 'Vitrin',
			'one_cikan_makineler'       => 'Öne Çıkan Makineler',
			'referanslarimiz'           => 'Referanslarımız',
			'referans_baslik'           => 'Sahada Güvenilen Çözüm Ortağı',
			'katalog_banner_baslik_default'   => "Tüm ürün gamımızı tek PDF'te inceleyin.",
			'katalog_banner_aciklama_default' => 'Genel katalog · Türkçe / İngilizce',
			'katalog_indir'             => 'Kataloğu İndir',
			'kataloglara_git'           => 'Kataloglara Git',

			'urunler_varsayilan_baslik' => 'Ürünler',
			'urunler_bos_kategori'      => 'Henüz kategori eklenmedi. wp-admin → Ürünler → Kategoriler bölümünden ekleyebilirsiniz.',
			'kategori_bos_makine'       => 'Bu kategoride henüz makine eklenmedi.',

			'gorsel_onceki'             => 'Önceki görsel',
			'gorsel_sonraki'            => 'Sonraki görsel',
			'urun_sekmeleri_aria'       => 'Ürün detay sekmeleri',
			'tab_aciklama'              => 'Ürün Açıklaması',
			'tab_ozellikler'            => 'Teknik Özellikler',
			'tab_video'                 => 'Video',
			'tab_dokuman'               => 'Dokümanlar',
			'video_baslik_suffix'       => '— video',
			'teknik_katalog_suffix'     => '— Teknik Katalog',
			'benzer_urunler'            => 'Benzer Ürünler',
			'ayni_kategoriden'          => 'Aynı Kategoriden',

			'kataloglar_baslik'         => 'Kataloglar',
			'kataloglar_aciklama'       => 'Genel ürün kataloğumuzu veya kategoriye özel teknik kataloglarımızı görüntüleyin ya da indirin.',
			'kataloglar_bos'            => 'Henüz katalog eklenmedi.',

			'baslik_404'                => 'Aradığınız sayfa bulunamadı.',
			'aciklama_404'              => 'Sayfa kaldırılmış veya adres hatalı olabilir.',
			'anasayfaya_don'            => 'Anasayfaya Dön',

			'arama_sonuclari'           => 'Arama Sonuçları',
			'arama_sonuclari_prefix'    => 'Arama Sonuçları: ',
			'arama_sonucu_bos'          => 'Aramanızla eşleşen bir sonuç bulunamadı.',

			'iletisim_formu_baslik'     => 'Bize Ulaşın',
			'konum_baslik'              => 'Bulunduğumuz Konum',
			'iletisim_telefon'          => 'Telefon',
			'iletisim_eposta'           => 'E-posta',
			'iletisim_adres'            => 'Adres',
			'iletisim_whatsapp'         => 'WhatsApp',
			'iletisim_calisma_saatleri' => 'Çalışma Saatleri',

			'sidebar_tum_urunler'       => 'Tüm Ürünler',
			'sidebar_aria'              => 'Kategori filtresi',
			'kategori_urun_sayisi'      => '%d ürün bulundu',
			'sayfalama_onceki'          => '‹ Önceki',
			'sayfalama_sonraki'         => 'Sonraki ›',

			'urunler_alt_menu_aria'     => 'Ürün kategorilerini göster',
			'megamenu_alt_kategori_aria'=> '%s alt kategorilerini göster',
			'tum_urunler_aciklama'      => 'Soldaki kategori ve alt kategorilerden ilerleyin ya da tüm ürün gamımıza aşağıdan göz atın.',
			'sayfalama_aria'            => 'Sayfalama',

			'kurumsal_uluslararasi_eyebrow'   => 'Global Destek',
			'kurumsal_uluslararasi_baslik'    => 'Uluslararası İletişim',
			'kurumsal_uluslararasi_aciklama'  => 'Uluslararası müşterilerimize daha hızlı destek sunabilmek amacıyla, farklı ülkelerde iş birliği yaptığımız yerel iletişim noktalarımız bulunmaktadır. Bulunduğunuz ülkeye göre aşağıdaki kişilerle iletişime geçebilirsiniz.',

			'kalite_belgeleri_eyebrow'  => 'Kalite Güvencesi',
			'kalite_belgeleri_baslik'   => 'Kalite Belgelerimiz',
			'belge_yeni_sekme_aria'     => '%s belgesini yeni sekmede aç',

			'haberler_eyebrow'    => 'Bizden Haberler',
			'haberlerimiz_baslik' => 'Haberler',
			'haberler_baslik'     => 'Haberler',
			'haberler_aciklama'   => 'Şirketimizden ve sektörden güncel gelişmeler.',
			'haberler_bos'        => 'Henüz haber eklenmedi.',
			'devamini_oku'        => 'Devamını Oku →',
			'tum_haberler'        => 'Tüm Haberler',
		);
	}
	return $s;
}

/**
 * Her metni Polylang'in "Diller → Dize Çevirisi" ekranına kaydeder — admin/çevirmen
 * buradan İngilizce/Rusça/İspanyolca karşılıklarını kod gerektirmeden girebilir.
 * Polylang kurulu değilse hiçbir şey yapmaz (site yine Türkçe çalışmaya devam eder).
 */
function cm_register_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) return;
	foreach ( cm_strings() as $name => $text ) {
		pll_register_string( $name, $text, 'Çikolata Makine Tema' );
	}
}
add_action( 'init', 'cm_register_strings' );

/**
 * Ön yüz metni okuma yardımcısı. $name, cm_strings() dizisindeki anahtardır.
 * Polylang kuruluysa geçerli ziyaretçi diline çevrilmiş halini, değilse ham
 * Türkçe metni döndürür. Çıktı escaping'i çağıran tarafın sorumluluğundadır
 * (esc_html()/esc_attr() ile), tıpkı diğer WP metin fonksiyonları gibi.
 */
function cm__( $name ) {
	$strings = cm_strings();
	$text    = $strings[ $name ] ?? $name;
	return function_exists( 'pll__' ) ? pll__( $text ) : $text;
}
