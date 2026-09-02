<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'CM_THEME_VERSION', '1.0.0' );
define( 'CM_THEME_DIR', get_template_directory() );
define( 'CM_THEME_URI', get_template_directory_uri() );

require_once CM_THEME_DIR . '/inc/cpt-taxonomies.php';
require_once CM_THEME_DIR . '/inc/acf-fields.php';
require_once CM_THEME_DIR . '/inc/seo.php';
require_once CM_THEME_DIR . '/inc/cookie-consent.php';
require_once CM_THEME_DIR . '/inc/template-tags.php';
require_once CM_THEME_DIR . '/inc/nav-walker.php';
require_once CM_THEME_DIR . '/inc/customizer.php';
require_once CM_THEME_DIR . '/inc/strings.php';
require_once CM_THEME_DIR . '/inc/query.php';
require_once CM_THEME_DIR . '/inc/admin-makine-list.php';
require_once CM_THEME_DIR . '/inc/admin-product-order.php';

/**
 * Sayfalar (post_type=page) Klasik Düzenleyici ile açılır. Sebep: bu temadaki tüm
 * "Sayfa" içeriği zaten ACF meta kutuları ve düz HTML/shortcode ile yönetiliyor,
 * Gutenberg blokları hiç kullanılmıyor — ama Gutenberg'in klasik meta box uyumluluk
 * katmanı 'side' konumlu kutuları (örn. Banner Görseli) GÜVENİLMEZ şekilde gösteriyor
 * (bazı ekranlarda sıfır boyutlu, kaydırmadan erişilemeyen bir alanda kalıyor — admin
 * bu yüzden Banner Görseli alanını bulamamıştı). Klasik Düzenleyici'de Öne Çıkan
 * Görsel + Banner Görseli + diğer tüm 'side' ACF alanları AYNI, öngörülebilir sağ
 * sütunda, üst üste görünür.
 */
function cm_disable_block_editor_for_pages( $use_block_editor, $post_type ) {
	if ( $post_type === 'page' ) return false;
	return $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'cm_disable_block_editor_for_pages', 10, 2 );

/**
 * Sağ sütunda Banner Görseli'ni Öne Çıkan Görsel'in HEMEN ALTINA sabitler — WP
 * çekirdeği "postimagediv"i 'low' öncelikte kaydettiği için ACF'in (daha yüksek
 * öncelikli) kutusu varsayılan olarak ÖNCE/ÜSTTE çıkıyordu (kullanıcı bunu istemedi:
 * "banner'lar görseldeki sayfanın altındaki kısımdan yönetilsin").
 */
function cm_reorder_page_side_metaboxes() {
	global $wp_meta_boxes;
	if ( empty( $wp_meta_boxes['page']['side'] ) ) return;

	$featured = $banner = null;
	foreach ( $wp_meta_boxes['page']['side'] as $priority => &$boxes ) {
		if ( isset( $boxes['postimagediv'] ) )              { $featured = $boxes['postimagediv']; unset( $boxes['postimagediv'] ); }
		if ( isset( $boxes['acf-group_cm_sayfa_banner'] ) ) { $banner   = $boxes['acf-group_cm_sayfa_banner']; unset( $boxes['acf-group_cm_sayfa_banner'] ); }
	}
	unset( $boxes );

	if ( $featured ) $wp_meta_boxes['page']['side']['low']['postimagediv'] = $featured;
	if ( $banner )   $wp_meta_boxes['page']['side']['low']['acf-group_cm_sayfa_banner'] = $banner;
}
add_action( 'do_meta_boxes', 'cm_reorder_page_side_metaboxes', 999 );

function cm_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 220,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	// Haber (ve sayfa) içeriğine bloklar üzerinden fotoğraf/galeri/video eklenebilsin diye
	// (haber CPT zaten 'editor' destekliyor + show_in_rest=true, yani blok düzenleyici
	// zaten aktif — Klasik Editör eklentisi YOK). Bu satır SADECE YouTube/Vimeo gibi
	// yerleştirilen (embed) videoların en-boy oranını koruyarak mobilde/dar ekranda
	// düzgün küçülmesini sağlar (aksi halde iframe sağlayıcının sabit piksel
	// genişliğinde kalıp ya taşar ya da mobilde orantısız görünürdü).
	add_theme_support( 'responsive-embeds' );

	register_nav_menus( array(
		'primary' => 'Üst Menü (Header)',
		'footer_1'=> 'Footer — Hızlı Linkler',
		'footer_2'=> 'Footer — Kategoriler',
	) );

	set_post_thumbnail_size( 900, 675, true );
	add_image_size( 'cm-card', 640, 480, true );
	add_image_size( 'cm-thumb', 200, 200, true );
	// 'cm-thumb' KARE olarak sert kırpıyor (ürün galerisi küçük resimleri için doğru) —
	// ama marka logoları (referanslar şeridi, bkz. front-page.php) genelde kare değil,
	// geniş/dikdörtgen; kare kırpma logonun kenarlarını keserdi. crop=false ile SADECE
	// bu kutuya sığacak şekilde ORANI KORUYARAK küçültülür, hiçbir zaman kırpılmaz.
	add_image_size( 'cm-logo', 400, 120, false );
}
add_action( 'after_setup_theme', 'cm_theme_setup' );

function cm_enqueue_assets() {
	// filemtime tabanlı sürüm: dosya her kaydedildiğinde tarayıcı önbelleğini otomatik tazeler.
	$cm_css = CM_THEME_DIR . '/assets/css/main.css';
	$cm_js  = CM_THEME_DIR . '/assets/js/main.js';
	// IBM Plex Sans/Serif/Mono — bkz. 03_TASARIM_YENILEME_ONERISI.md Bölüm 3
	// (Inter/Poppins gibi "her yapay zekâ sitesinde aynı" fontlardan kaçınmak için seçildi).
	wp_enqueue_style( 'cikolata-makine-fonts', 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Serif:wght@600;700&family=IBM+Plex+Mono:wght@400;500&display=swap', array(), null );
	wp_enqueue_style( 'cikolata-makine-main', CM_THEME_URI . '/assets/css/main.css', array( 'cikolata-makine-fonts' ), file_exists( $cm_css ) ? filemtime( $cm_css ) : CM_THEME_VERSION );
	wp_enqueue_script( 'cikolata-makine-main', CM_THEME_URI . '/assets/js/main.js', array(), file_exists( $cm_js ) ? filemtime( $cm_js ) : CM_THEME_VERSION, true );

	// RTL dil (şu an sadece Arapça) aktifken: main.css'teki yön-sabit (left/right,
	// border-left vb.) kuralları geçersiz kılan katman + marka tutarlılığı için Latin
	// IBM Plex ailesiyle aynı tasarım dilindeki Arapça yazı tipi (main.css'ten SONRA
	// yüklenmeli, ona bağımlı — bkz. rtl.css).
	if ( is_rtl() ) {
		$cm_rtl_css = CM_THEME_DIR . '/assets/css/rtl.css';
		wp_enqueue_style( 'cikolata-makine-fonts-rtl', 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap', array(), null );
		wp_enqueue_style( 'cikolata-makine-rtl', CM_THEME_URI . '/assets/css/rtl.css', array( 'cikolata-makine-main', 'cikolata-makine-fonts-rtl' ), file_exists( $cm_rtl_css ) ? filemtime( $cm_rtl_css ) : CM_THEME_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'cm_enqueue_assets' );

/**
 * Kurulumda rewrite kurallarını (kategori/katalog url yapıları) günceller.
 */
function cm_flush_rewrites_on_activation() {
	cm_register_taxonomies();
	cm_register_post_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'cm_flush_rewrites_on_activation' );

/**
 * wp_nav_menu() fallback: menü tanımlanmadıysa header çökmesin diye statik bir liste basar.
 */
function cm_primary_menu_fallback() {
	$cm_urunler = cm_translated_page( 'urunler' );
	echo '<div class="nav-menu nav" id="cm-primary-menu">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( cm__( 'breadcrumb_anasayfa' ) ) . '</a>';
	echo '<li class="menu-item has-megamenu" style="list-style:none;">';
	echo '<a href="' . esc_url( $cm_urunler ? get_permalink( $cm_urunler ) : home_url( '/urunler/' ) ) . '">' . esc_html( $cm_urunler ? get_the_title( $cm_urunler ) : cm__( 'urunler_varsayilan_baslik' ) ) . '</a>';
	echo '<button type="button" class="megamenu-toggle" aria-expanded="false" aria-label="' . esc_attr( cm__( 'urunler_alt_menu_aria' ) ) . '"><svg viewBox="0 0 12 8" width="10" height="7" aria-hidden="true"><path d="M1 1l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg></button>';
	cm_products_megamenu();
	echo '</li>';
	echo '<a href="' . esc_url( get_post_type_archive_link( 'katalog' ) ?: home_url( '/kataloglar/' ) ) . '">' . esc_html( cm__( 'kataloglar_baslik' ) ) . '</a>';
	echo '</div>';
}

/**
 * Diller arası dahili link çözücü: varsayılan (Türkçe) dildeki, slug'ı $slug olan
 * SAYFAYI (post_type=page) bulur, Polylang kuruluysa geçerli ziyaretçi diline
 * çevrilmiş halini döndürür (pll_get_post — Polylang'in belgelenen deseni).
 * Polylang yokken veya çeviri henüz girilmemişken varsayılan dildeki sayfayı döndürür.
 * "urunler"/"iletisim" gibi sabit slug'lı SAYFALAR için kullanılır — CPT arşivleri
 * (örn. kataloglar) için değil, onlar get_post_type_archive_link() ile zaten
 * otomatik dile duyarlıdır (bkz. cm_primary_menu_fallback()).
 *
 * @return WP_Post|null
 */
function cm_translated_page( $slug ) {
	static $cache = array();
	if ( array_key_exists( $slug, $cache ) ) return $cache[ $slug ];

	$args = array( 'name' => $slug, 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1 );
	if ( function_exists( 'pll_default_language' ) ) $args['lang'] = pll_default_language();
	$posts = get_posts( $args );
	$canonical = $posts ? $posts[0] : null;

	if ( $canonical && function_exists( 'pll_get_post' ) ) {
		$translated_id = pll_get_post( $canonical->ID ); // boş argüman = geçerli dile çözer
		if ( $translated_id ) $canonical = get_post( $translated_id );
	}

	return $cache[ $slug ] = $canonical;
}

/**
 * Ürün kataloğu (slug: urunler) sayfasının, geçerli dile çevrilmiş BAŞLIĞINI döndürür —
 * sabit metin yerine bunu kullanmak, admin sayfa başlığını değiştirdiğinde
 * breadcrumb/footer/menü metinlerinin ayrıca kod değişikliği gerekmeden otomatik
 * güncellenmesini sağlar. Sayfa bulunamazsa $default'a düşer.
 */
function cm_urunler_label( $default = null ) {
	$default = $default ?? cm__( 'urunler_varsayilan_baslik' );
	$page = cm_translated_page( 'urunler' );
	return $page ? $page->post_title : $default;
}

/**
 * "urunler"/"iletisim" gibi sabit slug'lı sayfaların geçerli dildeki linkini döndürür.
 * Sayfa bulunamazsa (henüz o dilde çeviri girilmemişse) $fallback_path'e (varsayılan
 * dildeki adres) düşer.
 */
function cm_translated_page_url( $slug, $fallback_path ) {
	$page = cm_translated_page( $slug );
	return $page ? get_permalink( $page ) : home_url( $fallback_path );
}

/**
 * Menü öğesine, geçerli sayfadaysa "current" sınıfı ekler (temanın nav a.current stiliyle uyumlu).
 */
function cm_nav_menu_css_class( $classes, $item ) {
	if ( in_array( 'current-menu-item', $classes, true ) ) {
		$classes[] = 'current';
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'cm_nav_menu_css_class', 10, 2 );

/**
 * Polylang'de her dil için AYRI bir menü atanır (bkz. README → Çoklu Dil Kurulumu §3).
 * Yanlışlıkla başka dildeki bir sayfa/kategori aynı menüye eklenirse (örn. İngilizce menüye
 * Türkçe bir sayfa sürüklenirse), Polylang bunu SESSİZCE görmezden gelir/engellemez — sonuç,
 * o dilde gezinirken diğer dildeki bir öğenin de menüde görünmesidir. Bu, tam olarak bir kez
 * yaşanan bir hataydı (İngilizce sayfalar yanlışlıkla Türkçe menüye eklenmişti). Burada,
 * her menü-dil eşleşmesindeki öğeleri kontrol edip dili UYUŞMAYANLARI wp-admin'de görünür bir
 * uyarıyla bildiriyoruz — hata sessizce tekrar etmesin diye.
 */
function cm_check_menu_language_mismatches() {
	if ( ! function_exists( 'PLL' ) || ! is_admin() ) return;
	global $pagenow;
	if ( ! in_array( $pagenow, array( 'nav-menus.php', 'index.php' ), true ) ) return;

	$nav_menus = PLL()->model->options->get( 'nav_menus' );
	$theme = get_stylesheet();
	if ( empty( $nav_menus[ $theme ] ) ) return;

	$problems = array();

	/*
	 * WordPress'in kendi "Bu menüye yeni üst-seviye sayfaları otomatik ekle" özelliği
	 * (Görünüm → Menüler → Yönet Konumları'ndaki checkbox), Polylang'in dil başına ayrı
	 * menü mantığıyla TAMAMEN çelişir: HANGİ dilde olursa olsun yeni bir sayfa oluşturulduğunda
	 * o menüye sessizce eklenir — tam olarak bu projede iki kez yaşanan "menüler karıştı"
	 * hatasının gerçek kök nedeni budur (Polylang bunu engellemez, WordPress çekirdeği yapar).
	 * Bu yüzden, dile atanmış herhangi bir menüde bu ayar açık bulunursa hem uyarıyoruz HEM
	 * DE otomatik olarak kapatıyoruz — tekrar sessizce açılıp aynı hatayı üretmesin diye.
	 */
	$assigned_menu_ids = array();
	foreach ( $nav_menus[ $theme ] as $by_lang ) {
		foreach ( $by_lang as $menu_id ) $assigned_menu_ids[] = (int) $menu_id;
	}
	$auto_add = get_option( 'nav_menu_options' );
	$auto_add_ids = ! empty( $auto_add['auto_add'] ) ? array_map( 'intval', (array) $auto_add['auto_add'] ) : array();
	$culprit_ids = array_intersect( $auto_add_ids, $assigned_menu_ids );
	if ( $culprit_ids ) {
		$names = array();
		foreach ( $culprit_ids as $mid ) {
			$m = wp_get_nav_menu_object( $mid );
			$names[] = $m ? $m->name : $mid;
		}
		$auto_add['auto_add'] = array_values( array_diff( $auto_add_ids, $culprit_ids ) );
		update_option( 'nav_menu_options', $auto_add );
		$problems[] = sprintf(
			'"%s" menüsünde "Yeni sayfaları otomatik ekle" özelliği açıktı — bu, HANGİ dilde olursa olsun yeni oluşturulan her sayfayı bu menüye sessizce ekler. Bu özellik sizin için kapatıldı.',
			implode( '", "', $names )
		);
	}

	foreach ( $nav_menus[ $theme ] as $location => $by_lang ) {
		foreach ( $by_lang as $lang => $menu_id ) {
			$items = wp_get_nav_menu_items( (int) $menu_id );
			if ( ! $items ) continue;
			foreach ( $items as $item ) {
				$item_lang = false;
				if ( 'post_type' === $item->type ) {
					$item_lang = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( (int) $item->object_id ) : false;
				} elseif ( 'taxonomy' === $item->type ) {
					$item_lang = function_exists( 'pll_get_term_language' ) ? pll_get_term_language( (int) $item->object_id ) : false;
				}
				if ( $item_lang && $item_lang !== $lang ) {
					$menu_obj = wp_get_nav_menu_object( (int) $menu_id );
					$problems[] = sprintf(
						'"%s" menüsü ("%s" diline atanmış) içinde "%s" başlıklı öğe aslında "%s" dilinde.',
						$menu_obj ? $menu_obj->name : $menu_id, $lang, $item->title, $item_lang
					);
				}
			}
		}
	}

	if ( $problems ) {
		add_action( 'admin_notices', function() use ( $problems ) {
			echo '<div class="notice notice-warning"><p><strong>Menü dil uyuşmazlığı bulundu:</strong></p><ul style="list-style:disc; margin-left:20px;">';
			foreach ( $problems as $p ) echo '<li>' . esc_html( $p ) . '</li>';
			echo '</ul><p>Bu öğeleri ilgili menüden kaldırıp doğru dildeki menüye ekleyin (Görünüm → Menüler, sağ üstten dil seçip düzenleyin).</p></div>';
		} );
	}
}
add_action( 'admin_init', 'cm_check_menu_language_mismatches' );

/**
 * "Aynı slug + dil öneki" (/urunler/, /en/urunler/) yapısında WordPress'in kendi
 * "pagename" çözümlemesi dili HİÇ dikkate almaz — Polylang bunu sadece anasayfa/
 * yazılar-sayfası için düzeltir (bkz. PLL_Static_Pages), sıradan sayfalar için değil.
 * Sonuç: /en/urunler/ isteği, dil etiketi aynı olsa bile YANLIŞLIKLA Türkçe sayfaya
 * (ilk eklenen/ID'si küçük olan) eşleşip /urunler/'e yönlendirir. Burada, rewrite
 * eşleşmesinden gelen "pagename" + "lang" query var'larını "page_id"ye çevirerek
 * WP_Query'nin doğrudan doğru dildeki sayfaya gitmesini sağlıyoruz.
 *
 * ÖNEMLİ: "page_id" SADECE post_type=page için kullanılabilir — WP_Query bu query
 * var'ı görünce post_type'ı otomatik "page"e sabitler VE is_page=true bayrağını
 * doğru kurar (bu, page-{slug}.php şablon hiyerarşisinin çalışması için ZORUNLU).
 * "p" + "post_type" kombinasyonuyla değiştirmeyin — o, is_page yerine yanlışlıkla
 * is_single=true kurar ve WordPress page-urunler.php yerine single.php'yi seçer
 * (bir kez yaşanmış, düzeltilmiş bir hata). "makine" gibi sayfa OLMAYAN CPT'ler
 * zaten aynı slug şemasını kullanmadığı için bu filtreye hiç ihtiyaç duymaz.
 */
function cm_pll_disambiguate_pagename_request( $query_vars ) {
	if ( empty( $query_vars['pagename'] ) || empty( $query_vars['lang'] ) || ! function_exists( 'pll_get_post_language' ) ) {
		return $query_vars;
	}
	$candidates = get_posts( array(
		'name'        => $query_vars['pagename'],
		'post_type'   => 'page',
		'post_status' => 'publish',
		'numberposts' => -1,
	) );
	foreach ( $candidates as $candidate ) {
		if ( pll_get_post_language( $candidate->ID ) === $query_vars['lang'] ) {
			unset( $query_vars['pagename'] );
			$query_vars['page_id'] = $candidate->ID;
			break;
		}
	}
	return $query_vars;
}
add_filter( 'request', 'cm_pll_disambiguate_pagename_request', 20 );

/**
 * KRİTİK — sayfalanmış (paged) taksonomi arşivlerinde Polylang'in geçerli dili
 * yanlış dile (genelde İngilizce) "sızdırması" hatasını düzeltir.
 *
 * Kaynak: Polylang'in PLL_Canonical::redirect_canonical() metodu (src/frontend/canonical.php,
 * 'template_redirect' önceliği 4), $wp_query'yi "yedeklerken" sadece referansı kopyalıyor
 * ($backup = $wp_query; — nesne KLONLANMIYOR), sonra $wp_query->tax_query->queried_terms['language']
 * öğesini SİLİP çekirdeğin redirect_canonical() fonksiyonunu çağırıyor ve "curlang"ı bu işlem
 * süresince geçici olarak hedef dile ayarlıyor ("Hack to filter the page_for_posts option").
 * Sayfalanmış arşivlerde çekirdek fonksiyon kanonik URL'yi tam eşleşmeyen bulup kendini
 * yinelemeli çağırdığında, bu ikinci çağrı artık SİLİNMİŞ 'language' bilgisiyle çalışıyor;
 * get_queried_term_id() dil eşleşmesi bulamayınca "ilk bulunan terim"e (aynı slug'ı paylaşan
 * TR/EN/RU/ES/AR terimleri arasından alfabetik ilk isim — genelde İngilizce "Chocolate Lines"
 * gibi) düşüyor ve bunu curlang olarak bırakıyor; GERİ YÜKLEME hiç yapılmıyor. Sonuç: ana
 * WP_Query (ve dolayısıyla ürün listesi) doğru dilde kalırken menü/kategori ağacı/dil seçici
 * gibi curlang'a bağlı HER ŞEY yanlış dilde render oluyor — sadece 2. ve sonraki sayfalarda
 * (bu tetikleyici sadece sayfalama canonical kontrolünde devreye giriyor).
 *
 * Polylang'in kendi dosyasını yamalamak (bir plugin güncellemesinde kaybolur) yerine, doğru
 * dili bu hack'ten HEMEN ÖNCE (öncelik 0) yakalayıp HEMEN SONRA (öncelik 5) zorla geri
 * yüklüyoruz — check_canonical_url'ün (öncelik 4) yaptığı her şeyi (olası bir GERÇEK
 * yönlendirme dahil) etkilemeden, sadece kalıcı yan etkisini temizliyor.
 */
function cm_capture_correct_pll_language() {
	if ( function_exists( 'PLL' ) && PLL() && ! empty( PLL()->curlang ) ) {
		$GLOBALS['cm_correct_pll_language'] = PLL()->curlang;
	}
}
add_action( 'template_redirect', 'cm_capture_correct_pll_language', 0 );

function cm_restore_pll_language_after_canonical_check() {
	if ( isset( $GLOBALS['cm_correct_pll_language'] ) && function_exists( 'PLL' ) && PLL() ) {
		PLL()->curlang = $GLOBALS['cm_correct_pll_language'];
	}
}
add_action( 'template_redirect', 'cm_restore_pll_language_after_canonical_check', 5 );

/**
 * page-{slug}.php şablon kuralı (bkz. page-kataloglar.php, page-urunler.php,
 * page-haberler.php) WordPress çekirdeğinde SADECE görüntülenen Sayfanın KENDİ
 * post_name'ine bakar. Ama bu Sayfaların EN/RU/ES çevirileri farklı, yerelleştirilmiş
 * bir slug'a sahip OLABİLİR (örn. "kataloglar" yerine İngilizce'de "catalogues") — bu
 * durumda çekirdek doğru özel şablonu hiç bulamaz ve sessizce genel page.php'ye düşer
 * (SONUÇ: örn. İngilizce "Catalogues" sayfası PDF listesini hiç göstermez, sadece boş
 * bir içerik sayfası gibi görünür — bu tam olarak böyle bir hata olarak bulundu).
 * ÇÖZÜM: hedef Sayfanın DEĞİL, o Sayfanın Polylang çeviri grubundaki VARSAYILAN DİL
 * sürümünün slug'ına göre de page-{slug}.php ara; bulunursa çekirdeğin (varsayılan
 * page.php'ye düşmüş) seçimi yerine onu kullan.
 */
function cm_page_template_by_canonical_slug( $template ) {
	if ( ! is_page() || ! function_exists( 'pll_get_post' ) || ! function_exists( 'pll_default_language' ) ) return $template;

	$post_id      = get_queried_object_id();
	$default_lang = pll_default_language();
	if ( ! $default_lang ) return $template;

	$canonical_id = pll_get_post( $post_id, $default_lang );
	if ( ! $canonical_id || (int) $canonical_id === (int) $post_id ) return $template;

	$canonical_slug = get_post_field( 'post_name', $canonical_id );
	if ( ! $canonical_slug ) return $template;

	$candidate = locate_template( "page-{$canonical_slug}.php" );
	return $candidate ?: $template;
}
add_filter( 'template_include', 'cm_page_template_by_canonical_slug' );

/**
 * Görünüm → Özelleştir → "İletişim & WhatsApp" alanını (bkz. inc/customizer.php)
 * kolayca okumak için kısa yardımcı.
 */
function cm_option( $key, $default = '' ) {
	$value = get_theme_mod( $key );
	return $value !== null && $value !== '' ? $value : $default;
}

/**
 * Bir WhatsApp numarasından wa.me linki üretir. $number verilmezse Özelleştir →
 * İletişim & WhatsApp'taki genel numara kullanılır (bkz. inc/customizer.php) — İletişim
 * sayfasının kendi iletisim_wa_deger alanı gibi başka bir kaynaktan da çağrılabilir.
 * Numara yoksa boş döner (çağıran taraf bunu fallback için kullanır).
 */
function cm_whatsapp_url( $number = null ) {
	if ( $number === null ) $number = cm_option( 'whatsapp_numarasi' );
	if ( ! $number ) return '';
	$digits = preg_replace( '/\D+/', '', $number );
	return $digits ? 'https://wa.me/' . $digits : '';
}

/**
 * "whatsapp_numarasi" alanına düz rakam dizisi olarak girilen numarayı (örn.
 * "905377258129") ekranda okunaklı biçime çevirir ("+90 537 725 81 29").
 * Türkiye cep telefonu biçimine (ülke kodu 2 + operatör 3 + 3+2+2) uymayan
 * numaralarda (farklı ülke kodu, eksik/fazla hane) olduğu gibi (rakamlar halinde)
 * döner — yanlış gruplamayla yanıltıcı bir görünüm oluşturmaktansa ham hali gösterilir.
 */
function cm_format_phone_display( $raw ) {
	$digits = preg_replace( '/\D+/', '', (string) $raw );
	if ( strlen( $digits ) === 12 ) {
		return '+' . substr( $digits, 0, 2 ) . ' ' . substr( $digits, 2, 3 ) . ' ' . substr( $digits, 5, 3 ) . ' ' . substr( $digits, 8, 2 ) . ' ' . substr( $digits, 10, 2 );
	}
	return $raw;
}

/**
 * Görünüm → Özelleştir → İletişim & WhatsApp'ta girilen Google Haritalar embed URL'sini
 * döndürür. Boşsa boş string döner — çağıran taraf (page.php) bunu görüp harita
 * bölümünü hiç basmaz (yanlış/örnek bir konum asla otomatik gösterilmez).
 */
function cm_harita_embed_url() {
	$url = cm_option( 'harita_gomme_url' );
	return $url && filter_var( $url, FILTER_VALIDATE_URL ) ? $url : '';
}

/**
 * İletişim Formu 7'nin, geçerli ziyaretçi diline karşılık gelen form ID'sini döndürür.
 * "iletisim-formu" slug'lı (varsayılan dildeki) form Polylang ile çevrilebilir işaretlenmiştir
 * (bkz. README → Çoklu Dil Kurulumu) — diğer sayfalar/kategoriler gibi "+ Çeviri Ekle" ile
 * EN/RU/ES formları oluşturulup buna bağlanır. Çeviri yoksa varsayılan dildeki form döner.
 */
function cm_contact_form_id() {
	static $id = null;
	if ( $id !== null ) return $id;

	$args = array( 'name' => 'iletisim-formu', 'post_type' => 'wpcf7_contact_form', 'post_status' => 'publish', 'numberposts' => 1 );
	if ( function_exists( 'pll_default_language' ) ) $args['lang'] = pll_default_language();
	$posts = get_posts( $args );
	$form_id = $posts ? $posts[0]->ID : 0;

	if ( $form_id && function_exists( 'pll_get_post' ) ) {
		$translated_id = pll_get_post( $form_id );
		if ( $translated_id ) $form_id = $translated_id;
	}
	return $id = $form_id;
}

/**
 * İletişim Formu 7'nin bildirim e-postasını, Görünüm → Özelleştir'de girilen şirket
 * e-postasına yönlendirir — form eklentisinin kendi admin ekranından ayrıca ayarlamaya
 * gerek kalmaz, tek kaynak (Customizer) üzerinden yönetilir. Alan boşsa formun kendi
 * varsayılan alıcısı (site yönetici e-postası) kullanılmaya devam eder.
 */
function cm_wpcf7_mail_recipient( $components ) {
	$email = cm_option( 'sirket_eposta' );
	if ( $email && is_email( $email ) ) $components['recipient'] = $email;
	return $components;
}
add_filter( 'wpcf7_mail_components', 'cm_wpcf7_mail_recipient' );

/**
 * WordPress çekirdeği post/terim slug benzersizliğini dilden habersiz kontrol eder
 * (Polylang ücretsiz sürüm bunu düzeltmez) — bu yüzden "aynı slug + dil öneki" (örn.
 * /urunler/ ve /en/urunler/) yapısında ikinci dilde kaydedilen sayfa/terim otomatik
 * olarak "urunler-2" gibi bir slug'a düşer. Burada: çakışan tüm kayıtlar FARKLI bir
 * dildeyse orijinal slug'ın aynen kullanılmasına izin veriyoruz; aynı dilde gerçek bir
 * çakışma varsa WP'nin ürettiği benzersiz slug'ı olduğu gibi bırakıyoruz.
 */
function cm_pll_unique_post_slug( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ) {
	if ( empty( $original_slug ) || $slug === $original_slug ) return $slug;
	if ( ! function_exists( 'pll_get_post_language' ) ) return $slug;
	$lang = pll_get_post_language( $post_ID );
	if ( ! $lang ) return $slug;

	global $wpdb;
	if ( is_post_type_hierarchical( $post_type ) ) {
		$conflicts = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type IN ( %s, 'attachment' ) AND ID != %d AND post_parent = %d",
			$original_slug, $post_type, $post_ID, $post_parent
		) );
	} else {
		$conflicts = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = %s AND ID != %d",
			$original_slug, $post_type, $post_ID
		) );
	}
	foreach ( $conflicts as $cid ) {
		if ( pll_get_post_language( (int) $cid ) === $lang ) return $slug;
	}
	return $original_slug;
}
add_filter( 'wp_unique_post_slug', 'cm_pll_unique_post_slug', 10, 6 );

/**
 * Aynı mantığın terim (kategori) karşılığı — bkz. cm_pll_unique_post_slug() açıklaması.
 * Not: "Yeni Terim Ekle" formunda dil, terim DB'ye yazılırken henüz atanmamış olur
 * (Polylang dili `created_term` kancasında, slug üretiminden SONRA atar) — bu yüzden
 * `$_POST['term_lang_choice']` (dil terim ID'si) değerine de fallback yapıyoruz,
 * tıpkı Polylang'in kendi admin-filters-term.php save_language() metodunun yaptığı gibi.
 */
function cm_pll_unique_term_slug( $slug, $term, $original_slug = null ) {
	if ( ! function_exists( 'pll_get_term_language' ) || ! function_exists( 'PLL' ) ) return $slug;

	$original_slug = $original_slug ?: ( is_object( $term ) && ! empty( $term->name ) ? sanitize_title( $term->name ) : $slug );
	if ( $slug === $original_slug ) return $slug;

	$term_id = is_object( $term ) && ! empty( $term->term_id ) ? (int) $term->term_id : 0;
	$lang = $term_id ? pll_get_term_language( $term_id ) : false;

	if ( ! $lang && isset( $_POST['term_lang_choice'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$lang_obj = PLL()->model->get_language( (int) $_POST['term_lang_choice'] ); // phpcs:ignore WordPress.Security.NonceVerification
		$lang = $lang_obj ? $lang_obj->slug : false;
	}
	if ( ! $lang ) return $slug;

	global $wpdb;
	$conflicts = $wpdb->get_col( $wpdb->prepare(
		"SELECT term_id FROM {$wpdb->terms} WHERE slug = %s AND term_id != %d",
		$original_slug, $term_id
	) );
	foreach ( $conflicts as $cid ) {
		$conflict_lang = pll_get_term_language( (int) $cid );
		if ( $conflict_lang && $conflict_lang === $lang ) return $slug;
	}
	return $original_slug;
}
add_filter( 'wp_unique_term_slug', 'cm_pll_unique_term_slug', 10, 3 );
