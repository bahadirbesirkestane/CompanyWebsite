<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Temel SEO + sosyal paylaşım altyapısı: meta description, Open Graph, Twitter Card,
 * opsiyonel başlık override'ı + Google Analytics/Tag Manager/Search Console doğrulama
 * (kimlikler inc/customizer.php → "Analitik & Arama Motoru Doğrulama" bölümünden girilir).
 *
 * Yoast/RankMath gibi hazır bir eklenti KASITLI olarak kullanılmadı: bu site Polylang'in
 * canonical-redirect davranışına elle yama uygulanmış durumda (bkz. functions.php →
 * cm_capture_correct_pll_language(), CLAUDE.md'deki ilgili not) — büyük bir SEO eklentisi
 * kendi canonical/hreflang/sitemap mantığını üstüne bindirip bu yamayla çakışabilirdi.
 * hreflang + XML sitemap zaten Polylang + WP çekirdeği tarafından otomatik üretiliyor
 * (doğrulandı: /wp-sitemap.xml, wp_head'deki <link rel="alternate" hreflang="..">), bu
 * dosya sadece o ikisinin KAPSAMADIĞI meta description + Open Graph/Twitter Card'ı ekliyor.
 *
 * Her alan "boşsa gizle" ilkesini izler: admin hiçbir SEO alanı doldurmasa bile site
 * mevcut haliyle birebir aynı davranır (hiçbir yeni etiket basılmaz zorla), sadece
 * anlamlı bir içerik bulunabildiğinde (kısa açıklama, alıntı, öne çıkan görsel vb.)
 * otomatik bir açıklama/görsel üretilir.
 */

// ---- ACF: SEO alanları (sayfa/ürün/katalog/haber/kategori ortak) ----------
add_action( 'acf/init', function () {
	acf_add_local_field_group( array(
		'key'    => 'group_cm_seo',
		'title'  => 'SEO ve Paylaşım',
		'fields' => array(
			array(
				'key'   => 'field_cm_seo_baslik',
				'label' => 'SEO Başlığı (opsiyonel)',
				'name'  => 'seo_baslik',
				'type'  => 'text',
				'instructions' => 'Google arama sonuçlarında ve tarayıcı sekmesinde görünecek başlık. Boş bırakılırsa sayfanın kendi başlığı kullanılır.',
			),
			array(
				'key'   => 'field_cm_seo_aciklama',
				'label' => 'SEO Açıklaması (opsiyonel)',
				'name'  => 'seo_aciklama',
				'type'  => 'textarea',
				'rows'  => 3,
				'instructions' => 'Google sonuçlarında başlığın altında ve WhatsApp/Facebook/LinkedIn\'de paylaşılınca görünecek 1-2 cümlelik özet (ideal uzunluk ~150 karakter). Boş bırakılırsa sayfanın kendi içeriğinden (kısa açıklama/alıntı) otomatik oluşturulur.',
			),
			array(
				'key'   => 'field_cm_seo_gorsel',
				'label' => 'Paylaşım Görseli (opsiyonel)',
				'name'  => 'seo_gorsel',
				'type'  => 'image',
				'return_format' => 'array',
				'instructions' => 'Link paylaşılınca WhatsApp/Facebook/LinkedIn\'de görünecek görsel (ideal: yatay, en az 1200×630px). Boş bırakılırsa öne çıkan görsel (varsa) kullanılır.',
			),
		),
		'location' => array(
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ) ),
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'makine' ) ),
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'katalog' ) ),
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'haber' ) ),
			array( array( 'param' => 'taxonomy',  'operator' => '==', 'value' => 'makine_kategori' ) ),
		),
	) );
} );

/** Metni meta description için güvenli uzunlukta (varsayılan ~155 karakter) kırpar. */
function cm_seo_trim( $text, $len = 155 ) {
	$text = wp_strip_all_tags( (string) $text );
	$text = trim( preg_replace( '/\s+/u', ' ', $text ) );
	if ( $text === '' ) return '';
	if ( mb_strlen( $text ) <= $len ) return $text;
	return rtrim( mb_substr( $text, 0, $len ) ) . '…';
}

/** Şu anki isteğin gerçek URL'sini hesaplar (og:url için) — singular/taksonomi/anasayfa dışında $_SERVER'a düşer. */
function cm_seo_current_url() {
	if ( is_front_page() ) return home_url( '/' );
	if ( is_singular() ) {
		$url = get_permalink();
		if ( $url ) return $url;
	}
	if ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$url = get_term_link( $term );
			if ( ! is_wp_error( $url ) ) return $url;
		}
	}
	$scheme = is_ssl() ? 'https://' : 'http://';
	return $scheme . ( $_SERVER['HTTP_HOST'] ?? '' ) . ( $_SERVER['REQUEST_URI'] ?? '' );
}

/**
 * Geçerli sayfanın bağlamına göre (anasayfa/ürün/kategori/sayfa/haber) SEO başlığı,
 * açıklaması ve paylaşım görselini TEK yerde hesaplar — hem <title> override'ı hem
 * wp_head'deki meta etiketleri aynı bu fonksiyonu kullanır, tutarsızlık olmaz.
 */
function cm_seo_context() {
	$desc = '';
	$image_id = 0;
	$title_override = '';

	if ( is_front_page() ) {
		$front_id = (int) get_option( 'page_on_front' );
		if ( $front_id ) {
			$title_override = get_field( 'seo_baslik', $front_id );
			$desc = get_field( 'seo_aciklama', $front_id );
			$hero = ! $desc || empty( get_field( 'seo_gorsel', $front_id ) ) ? get_field( 'hero_slayt_1', $front_id ) : null;
			if ( ! $desc && $hero ) {
				$desc = trim( ( $hero['baslik'] ?? '' ) . '. ' . ( $hero['aciklama'] ?? '' ) );
			}
			$manual_img = get_field( 'seo_gorsel', $front_id );
			if ( $manual_img ) {
				$image_id = (int) $manual_img['ID'];
			} elseif ( $hero && ! empty( $hero['gorsel']['ID'] ) ) {
				$image_id = (int) $hero['gorsel']['ID'];
			}
		}
	} elseif ( is_singular( array( 'makine', 'katalog', 'page', 'haber' ) ) ) {
		$id = get_the_ID();
		$title_override = get_field( 'seo_baslik', $id );
		$desc = get_field( 'seo_aciklama', $id );
		if ( ! $desc && get_post_type( $id ) === 'makine' ) {
			$desc = get_field( 'kisa_aciklama', $id );
		}
		if ( ! $desc ) {
			$desc = get_the_excerpt( $id );
		}
		if ( ! $desc ) {
			$desc = get_post_field( 'post_content', $id );
		}
		$manual_img = get_field( 'seo_gorsel', $id );
		if ( $manual_img ) {
			$image_id = (int) $manual_img['ID'];
		} elseif ( has_post_thumbnail( $id ) ) {
			$image_id = (int) get_post_thumbnail_id( $id );
		}
	} elseif ( is_tax( 'makine_kategori' ) || is_tax( 'urun_ailesi' ) ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$title_override = get_field( 'seo_baslik', $term );
			$desc = get_field( 'seo_aciklama', $term );
			if ( ! $desc ) $desc = $term->description;
			$manual_img = get_field( 'seo_gorsel', $term );
			if ( $manual_img ) {
				$image_id = (int) $manual_img['ID'];
			} elseif ( $term->taxonomy === 'makine_kategori' ) {
				$photo = get_field( 'kategori_gorsel', $term );
				if ( $photo ) $image_id = (int) $photo['ID'];
			}
		}
	}

	return array(
		'description' => $desc ? cm_seo_trim( $desc ) : '',
		'image_id'    => $image_id,
		'title'       => $title_override ? trim( $title_override ) : '',
	);
}

// ---- <title> override — SADECE manuel "SEO Başlığı" girilmişse devreye girer ----
add_filter( 'pre_get_document_title', function ( $title ) {
	if ( is_admin() ) return $title;
	$ctx = cm_seo_context();
	return $ctx['title'] !== '' ? $ctx['title'] : $title;
}, 20 );

// ---- <meta name="description"> + Open Graph + Twitter Card ----------------
add_action( 'wp_head', function () {
	if ( is_admin() ) return;

	$ctx   = cm_seo_context();
	$title = $ctx['title'] !== '' ? $ctx['title'] : wp_get_document_title();
	$desc  = $ctx['description'];
	$url   = cm_seo_current_url();
	$site  = get_bloginfo( 'name' );

	$lang_map = array( 'tr' => 'tr_TR', 'en' => 'en_US', 'ru' => 'ru_RU', 'es' => 'es_ES', 'ar' => 'ar_AR' );
	$lang     = function_exists( 'pll_current_language' ) ? pll_current_language() : 'tr';
	$locale   = $lang_map[ $lang ] ?? 'tr_TR';

	$img = $ctx['image_id'] ? wp_get_attachment_image_src( $ctx['image_id'], 'large' ) : false;

	echo "\n<!-- cm_seo: meta description + Open Graph / Twitter Card -->\n";
	if ( $desc !== '' ) {
		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
	}
	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( $site ) );
	printf( '<meta property="og:type" content="website" />' . "\n" );
	printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( $locale ) );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
	if ( $desc !== '' ) {
		printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
	}
	if ( $img ) {
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $img[0] ) );
		printf( '<meta property="og:image:width" content="%d" />' . "\n", (int) $img[1] );
		printf( '<meta property="og:image:height" content="%d" />' . "\n", (int) $img[2] );
	}
	printf( '<meta name="twitter:card" content="%s" />' . "\n", $img ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
	if ( $desc !== '' ) {
		printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $desc ) );
	}
	if ( $img ) {
		printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $img[0] ) );
	}
}, 5 );

// ---- Google Search Console doğrulama etiketi (boşsa hiç basılmaz) ----------
add_action( 'wp_head', function () {
	$code = cm_option( 'google_site_verification' );
	if ( $code ) {
		printf( '<meta name="google-site-verification" content="%s" />' . "\n", esc_attr( $code ) );
	}
}, 1 );

/**
 * Analitik <script> etiketinin type'ı — çerez bandı (Sayfalar → Çerez Politikası
 * → Yayınla/Taslak, bkz. inc/cookie-consent.php → cm_cerez_banner_aktif())
 * KAPALIYKEN "text/javascript" (doğrudan çalışır, ÖNCEKİ davranışla birebir
 * aynı), AÇIKKEN "text/plain" + data-cookie-category="analytics" (tarayıcı
 * bunu ÇALIŞTIRMAZ — assets/js/cookie-consent.js ziyaretçi Analitik'e izin
 * verince gerçek <script>'e çevirip yeniden ekliyor). Banner kapalıyken eski
 * davranış korunuyor ki bu özelliği hiç kullanmak istemeyen biri için hiçbir
 * şey değişmesin.
 */
function cm_seo_analytics_script_open_tag( $extra_attrs = '' ) {
	if ( function_exists( 'cm_cerez_banner_aktif' ) && cm_cerez_banner_aktif() ) {
		return '<script type="text/plain" data-cookie-category="analytics"' . $extra_attrs . '>';
	}
	return '<script' . $extra_attrs . '>';
}

// ---- Google Tag Manager (kapsayıcı kimliği girilmişse) ---------------------
add_action( 'wp_head', function () {
	$gtm_id = cm_option( 'google_tag_manager_id' );
	if ( ! $gtm_id ) return;
	echo cm_seo_analytics_script_open_tag(); // phpcs:ignore -- sabit, escape'e gerek yok
	?>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $gtm_id ); ?>');</script>
	<?php
}, 2 );
add_action( 'wp_body_open', function () {
	$gtm_id = cm_option( 'google_tag_manager_id' );
	if ( ! $gtm_id ) return;
	// Çerez bandı AÇIKKEN noscript fallback'i basmıyoruz: JS kapalı bir ziyaretçi
	// banner'la hiç etkileşemez, onaysız bir izleme pikselini kayıtsız şartsız
	// ateşlemek doğru olmaz — JS açıksa zaten yukarıdaki gated <script> yeterli.
	if ( function_exists( 'cm_cerez_banner_aktif' ) && cm_cerez_banner_aktif() ) return;
	?>
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<?php
} );

// ---- Google Analytics (GA4) — ölçüm kimliği girilmişse doğrudan gtag.js -----
add_action( 'wp_head', function () {
	$ga_id = cm_option( 'google_analytics_id' );
	if ( ! $ga_id ) return;
	$gated = function_exists( 'cm_cerez_banner_aktif' ) && cm_cerez_banner_aktif();
	if ( $gated ) {
		printf( '<script type="text/plain" data-cookie-category="analytics" data-src="%s"></script>' . "\n", esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . $ga_id ) );
	} else {
		printf( '<script async src="%s"></script>' . "\n", esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . $ga_id ) );
	}
	echo cm_seo_analytics_script_open_tag(); // phpcs:ignore
	?>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $ga_id ); ?>');</script>
	<?php
}, 3 );
