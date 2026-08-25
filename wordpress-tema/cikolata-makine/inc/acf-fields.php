<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ACF (Advanced Custom Fields) kurulu değilse site yine de çalışır,
 * sadece bu özel alanlar admin panelinde görünmez.
 *
 * NOT: Bu tema bilerek sadece ACF'in ÜCRETSİZ sürümündeki alan tiplerini kullanır
 * (Tekrarlayan Alan/Repeater ve Galeri PRO'ya özeldir ve ücretsiz sürümde admin
 * ekranında hiç görünmez). Değişken sayıda satır gereken yerlerde (teknik özellikler)
 * satır satır yazılan bir metin alanı kullanılıp PHP tarafında ayrıştırılıyor;
 * sabit sayıda tekrar eden yerlerde (hero slaytları, istatistikler, ek görseller)
 * numaralı ayrı alan grupları kullanılıyor.
 */
if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

add_action( 'acf/init', function () {

	// ---- Makine Detayları -------------------------------------------------
	$cm_makine_fields = array(
		array(
			'key'   => 'field_cm_one_cikan',
			'label' => 'Öne Çıkan Makine',
			'name'  => 'one_cikan',
			'type'  => 'true_false',
			'ui'    => 1,
			'instructions' => 'Anasayfadaki "Öne Çıkan Makineler" bölümünde gösterilsin mi?',
		),
		array(
			'key'   => 'field_cm_kisa_ozet',
			'label' => 'Kısa Özet',
			'name'  => 'kisa_ozet',
			'type'  => 'text',
			'instructions' => 'Ürün kartlarında başlığın altında görünen tek satırlık özet (örn. "450 kg/saat · 7.5 kW").',
		),
		array(
			'key'   => 'field_cm_kisa_aciklama',
			'label' => 'Kısa Açıklama',
			'name'  => 'kisa_aciklama',
			'type'  => 'textarea',
			'rows'  => 4,
			'instructions' => 'Detay sayfasında galerinin yanında görünen tanıtım paragrafı. Boş bırakılırsa (varsa) yazının "Alıntı" (excerpt) kutusu kullanılır.',
		),
	);

	for ( $i = 1; $i <= 8; $i++ ) {
		$cm_makine_fields[] = array(
			'key'   => "field_cm_ek_gorsel_$i",
			'label' => "Ek Görsel $i",
			'name'  => "ek_gorsel_$i",
			'type'  => 'image',
			'return_format' => 'array',
			'instructions'  => $i === 1 ? 'Vitrin kapak görseli için Öne Çıkan Görsel (Featured Image) alanını kullanın. Buradaki alanlar, detay sayfası galerisindeki ek fotoğraflardır — hepsini doldurmak zorunlu değildir. 2 veya daha fazla fotoğraf (vitrin + ek görseller) olduğunda galeri üzerinde otomatik ileri/geri okları çıkar.' : '',
		);
	}

	$cm_makine_fields[] = array(
		'key'   => 'field_cm_teknik_ozellikler_metin',
		'label' => 'Teknik Özellikler',
		'name'  => 'teknik_ozellikler_metin',
		'type'  => 'textarea',
		'rows'  => 8,
		'instructions' => 'Her satıra bir özellik yazın, format: <strong>Özellik Adı: Değer</strong> (örn. "Kapasite: 450 kg/saat"). Her satır ürün sayfasında ayrı bir tablo satırı olur.',
		'placeholder' => "Kapasite: 450 kg/saat\nKurulu Güç: 7.5 kW\nEbat (U×D×Y): 2100 × 950 × 1800 mm\nAğırlık: 680 kg",
	);
	$cm_makine_fields[] = array(
		'key'   => 'field_cm_pdf_katalog',
		'label' => 'PDF Katalog',
		'name'  => 'pdf_katalog',
		'type'  => 'file',
		'return_format' => 'array',
		'mime_types'    => 'pdf',
		'instructions'  => 'Bu makineye özel teknik broşür/katalog dosyası. Yüklendiğinde ürün sayfasındaki "Dokümanlar" sekmesinde otomatik görünür.',
	);
	$cm_makine_fields[] = array(
		'key'   => 'field_cm_video_url',
		'label' => 'YouTube Video Linki (opsiyonel)',
		'name'  => 'video_url',
		'type'  => 'url',
		'placeholder' => 'https://www.youtube.com/watch?v=...',
		'instructions' => 'Girildiğinde ürün sayfasında ayrı bir "Video" sekmesi otomatik oluşur.',
	);
	$cm_makine_fields[] = array(
		'key'   => 'field_cm_cta_metin',
		'label' => 'CTA Butonu Metni (opsiyonel)',
		'name'  => 'cta_metin',
		'type'  => 'text',
		'placeholder' => 'örn. Bu Makine İçin Teklif İste',
		'instructions' => 'Boş bırakılırsa bu makinede teklif butonu hiç görünmez. Bir metin girerseniz buton görünür.',
	);
	$cm_makine_fields[] = array(
		'key'   => 'field_cm_cta_link',
		'label' => 'CTA Linki (opsiyonel)',
		'name'  => 'cta_link',
		'type'  => 'url',
		'instructions' => 'Boş bırakılırsa sitedeki WhatsApp numarasına (o da yoksa İletişim sayfasına) yönlendirir.',
	);

	acf_add_local_field_group( array(
		'key'      => 'group_cm_makine',
		'title'    => 'Makine Detayları',
		'fields'   => $cm_makine_fields,
		'location' => array(
			array(
				array( 'param' => 'post_type', 'operator' => '==', 'value' => 'makine' ),
			),
		),
	) );

	// ---- Katalog Detayları --------------------------------------------------
	acf_add_local_field_group( array(
		'key'    => 'group_cm_katalog',
		'title'  => 'Katalog Detayları',
		'fields' => array(
			array(
				'key'   => 'field_cm_katalog_pdf',
				'label' => 'PDF Dosyası',
				'name'  => 'pdf_dosya',
				'type'  => 'file',
				'return_format' => 'array',
				'mime_types'    => 'pdf',
				'required'      => 1,
			),
			array(
				'key'   => 'field_cm_katalog_dil',
				'label' => 'Dil',
				'name'  => 'dil',
				'type'  => 'text',
				'default_value' => 'TR/EN',
			),
		),
		'location' => array(
			array(
				array( 'param' => 'post_type', 'operator' => '==', 'value' => 'katalog' ),
			),
		),
	) );

	// ---- Kategori İkonu (taksonomi terimi) -----------------------------------
	acf_add_local_field_group( array(
		'key'    => 'group_cm_kategori_ikon',
		'title'  => 'Kategori Görünümü',
		'fields' => array(
			array(
				'key'   => 'field_cm_kategori_ikon',
				'label' => 'Kategori İkonu',
				'name'  => 'kategori_ikon',
				'type'  => 'image',
				'return_format' => 'array',
				'instructions'  => 'Kategori kartlarında gösterilecek küçük ikon (SVG/PNG, tercihen kare ve tek renk). Boş bırakılırsa genel bir ikon kullanılır.',
			),
		),
		'location' => array(
			array(
				array( 'param' => 'taxonomy', 'operator' => '==', 'value' => 'makine_kategori' ),
			),
		),
	) );

	// ---- Anasayfa Ayarları ---------------------------------------------------
	$cm_hero_sub_fields = function ( $n ) {
		return array(
			array( 'key' => "field_cm_hs{$n}_eyebrow", 'label' => 'Üst Etiket', 'name' => 'eyebrow', 'type' => 'text', 'placeholder' => 'örn. Endüstriyel Çikolata Üretim Sistemleri' ),
			array( 'key' => "field_cm_hs{$n}_baslik", 'label' => 'Başlık', 'name' => 'baslik', 'type' => 'textarea', 'rows' => 2, 'instructions' => 'Bu alan boşsa slayt sitede hiç görünmez.' ),
			array( 'key' => "field_cm_hs{$n}_aciklama", 'label' => 'Açıklama', 'name' => 'aciklama', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => "field_cm_hs{$n}_gorsel", 'label' => 'Görsel', 'name' => 'gorsel', 'type' => 'image', 'return_format' => 'array', 'instructions' => 'Geniş/yatay fotoğraf önerilir (örn. 1920×1080). Boş bırakılırsa yer tutucu gösterilir.' ),
			array( 'key' => "field_cm_hs{$n}_btn1_metin", 'label' => 'Buton 1 Metni', 'name' => 'buton1_metin', 'type' => 'text', 'default_value' => 'Makineleri İncele' ),
			array( 'key' => "field_cm_hs{$n}_btn1_link", 'label' => 'Buton 1 Linki', 'name' => 'buton1_link', 'type' => 'url' ),
			array( 'key' => "field_cm_hs{$n}_btn2_metin", 'label' => 'Buton 2 Metni', 'name' => 'buton2_metin', 'type' => 'text', 'default_value' => 'Bize Ulaşın' ),
			array( 'key' => "field_cm_hs{$n}_btn2_link", 'label' => 'Buton 2 Linki', 'name' => 'buton2_link', 'type' => 'url' ),
		);
	};

	$cm_anasayfa_fields = array();
	foreach ( array( 1, 2, 3 ) as $n ) {
		$cm_anasayfa_fields[] = array(
			'key'        => "field_cm_hero_slayt_$n",
			'label'      => "Slayt $n" . ( $n === 1 ? ' (en az bu doldurulmalı)' : ' (opsiyonel)' ),
			'name'       => "hero_slayt_$n",
			'type'       => 'group',
			'layout'     => 'block',
			'sub_fields' => $cm_hero_sub_fields( $n ),
		);
	}

	foreach ( array( 1, 2, 3, 4 ) as $n ) {
		$cm_anasayfa_fields[] = array(
			'key'        => "field_cm_istatistik_$n",
			'label'      => "İstatistik $n",
			'name'       => "istatistik_$n",
			'type'       => 'group',
			'layout'     => 'table',
			'sub_fields' => array(
				array( 'key' => "field_cm_ist{$n}_sayi", 'label' => 'Sayı', 'name' => 'sayi', 'type' => 'text', 'placeholder' => 'örn. 27+' ),
				array( 'key' => "field_cm_ist{$n}_etiket", 'label' => 'Etiket', 'name' => 'etiket', 'type' => 'text', 'placeholder' => 'örn. Yıl Sektör Tecrübesi' ),
			),
		);
	}

	$cm_anasayfa_fields[] = array( 'key' => 'field_cm_katalog_baslik', 'label' => 'Katalog Banner Başlığı', 'name' => 'katalog_baslik', 'type' => 'text', 'default_value' => "Tüm ürün gamımızı tek PDF'te inceleyin." );
	$cm_anasayfa_fields[] = array( 'key' => 'field_cm_katalog_aciklama', 'label' => 'Katalog Banner Açıklaması', 'name' => 'katalog_aciklama', 'type' => 'text', 'default_value' => 'Genel katalog · Türkçe / İngilizce' );
	$cm_anasayfa_fields[] = array( 'key' => 'field_cm_katalog_banner_pdf', 'label' => 'Katalog Banner PDF Dosyası', 'name' => 'katalog_banner_pdf', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'pdf' );

	acf_add_local_field_group( array(
		'key'      => 'group_cm_anasayfa',
		'title'    => 'Anasayfa Ayarları',
		'fields'   => $cm_anasayfa_fields,
		'location' => array(
			array(
				array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
			),
		),
	) );

	// NOT: İletişim/WhatsApp/sosyal medya alanları burada DEĞİL — ACF'in "Options Page"
	// özelliği PRO'ya özeldir (ücretsiz sürümde acf_add_options_page() hiç yoktur, bu yüzden
	// önceki "Genel Ayarlar" menüsü hiç oluşmuyordu). Onun yerine WordPress'in kendi,
	// her zaman ücretsiz olan Özelleştir ekranı kullanılıyor — bkz. inc/customizer.php.

	// ---- Kurumsal Sayfası: Uluslararası İletişim ------------------------------
	// Konum, "page_type" ile değil DOĞRUDAN sayfa ID'leriyle (Kurumsal'ın TR + EN/RU/ES
	// çevirileri) eşleştiriliyor — her dil ayrı bir Sayfa (post) olduğu için "page_type"ın
	// hazır seçenekleri (front_page/top_level/parent/child) burada işe yaramaz. ID'ler
	// sabit ve kalıcıdır (bu sayfalar silinip yeniden oluşturulmadıkça değişmez).
	$cm_kurumsal_fields = array(
		array(
			'key'   => 'field_cm_intl_gizle',
			'label' => 'Bu Bölümü Gizle',
			'name'  => 'intl_gizle',
			'type'  => 'true_false',
			'ui'    => 1,
			'instructions' => 'İşaretlerseniz, aşağıya kişi girilmiş olsa bile "Uluslararası İletişim" bölümü sitede hiç görünmez. Boş bırakıp hiç kişi girmezseniz zaten aynı şekilde görünmez — bu sadece hızlıca kapatmak içindir.',
		),
	);
	for ( $i = 1; $i <= 10; $i++ ) {
		$cm_kurumsal_fields[] = array(
			'key'        => "field_cm_intl_kisi_$i",
			'label'      => "Kişi $i",
			'name'       => "intl_kisi_$i",
			'type'       => 'group',
			'layout'     => 'block',
			'instructions' => $i === 1 ? '"Ülke" boş bırakılan satırlar sitede hiç görünmez — hepsini doldurmak zorunda değilsiniz.' : '',
			'sub_fields' => array(
				array( 'key' => "field_cm_ik{$i}_ulke", 'label' => 'Ülke', 'name' => 'ulke', 'type' => 'text', 'placeholder' => 'örn. Almanya' ),
				array( 'key' => "field_cm_ik{$i}_kisi", 'label' => 'Ad Soyad / Firma Adı', 'name' => 'kisi_firma', 'type' => 'text' ),
				array( 'key' => "field_cm_ik{$i}_tel", 'label' => 'Telefon', 'name' => 'telefon', 'type' => 'text', 'placeholder' => '+49 XXX XXX XXXX' ),
				array( 'key' => "field_cm_ik{$i}_eposta", 'label' => 'E-posta', 'name' => 'eposta', 'type' => 'email' ),
			),
		);
	}

	acf_add_local_field_group( array(
		'key'      => 'group_cm_kurumsal',
		'title'    => 'Kurumsal Sayfası — Uluslararası İletişim',
		'fields'   => $cm_kurumsal_fields,
		'location' => array(
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 7 ) ),   // Kurumsal (TR)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 277 ) ), // Corporate (EN)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 278 ) ), // О компании (RU)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 279 ) ), // Corporativo (ES)
		),
	) );

} );
