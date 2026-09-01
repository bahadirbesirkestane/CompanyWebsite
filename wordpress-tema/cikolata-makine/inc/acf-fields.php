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

/**
 * "Ek Görsel" (ek_gorsel_1..8) VE ürüne özel "PDF Katalog" (pdf_katalog) seçim
 * penceresinin kapsamını, tek bir post'un kendi post_parent'ından, AYNI ürünün
 * TR/EN/RU/ES çeviri grubunun TAMAMINA genişletir. Neden: kullanıcı dil başına ayrı
 * fotoğraf/PDF YÜKLEMİYOR, bir kez yüklediğini tüm dillerde tekrar seçmek istiyor —
 * ama diğer ÜRÜNLERİN dosyaları hâlâ görünmemeli.
 *
 * "Kataloglar" (katalog CPT) içerik tipinin KENDİ pdf_dosya alanına KASITLI OLARAK
 * uygulanmıyor — genel kataloglar dil başına gerçekten ayrı, çevrilmiş dosyalar
 * (kullanıcı bunu ayrıca belirtti: "katalog konusunda ayrı dillerde seçim yapmam
 * benim için önemli"). Sadece ÜRÜNE ÖZEL teknik PDF (pdf_katalog) genellikle aynı
 * dosyanın tüm dillerde tekrar kullanıldığı bir alan.
 *
 * NOT (düzeltme): "acf/fields/image/query" diye bir filtre YOK — ACF'in medya modalı
 * asıl WordPress çekirdeğinin "query-attachments" AJAX uç noktasını kullanıyor ve
 * çekirdeğin "ajax_query_attachments_args" filtresi devreye giriyor (bkz.
 * wp-admin/includes/ajax-actions.php). ACF, hangi alanın sorgulandığını "_acfuploader"
 * (alan anahtarı) parametresiyle gönderiyor (bkz. advanced-custom-fields/includes/media.php
 * get_source_field()) — doğru kanca budur, ACF'e özel bir filtre değil.
 */
function cm_acf_scope_gallery_query_to_translation_group( $query ) {
	if ( empty( $_REQUEST['query']['_acfuploader'] ) ) return $query;

	$cm_field_key = sanitize_text_field( wp_unslash( $_REQUEST['query']['_acfuploader'] ) );
	$cm_field     = function_exists( 'acf_get_field' ) ? acf_get_field( $cm_field_key ) : false;
	$cm_shared    = $cm_field && ( strpos( $cm_field['name'], 'ek_gorsel_' ) === 0 || $cm_field['name'] === 'pdf_katalog' );
	if ( ! $cm_shared ) return $query;
	if ( empty( $query['post_parent'] ) ) return $query;

	$post_id = (int) $query['post_parent'];
	$ids     = array( $post_id );
	if ( function_exists( 'pll_languages_list' ) && function_exists( 'pll_get_post' ) ) {
		foreach ( pll_languages_list() as $cm_lang ) {
			$cm_translated_id = pll_get_post( $post_id, $cm_lang );
			if ( $cm_translated_id ) $ids[] = (int) $cm_translated_id;
		}
	}

	unset( $query['post_parent'] );
	$query['post_parent__in'] = array_values( array_unique( $ids ) );
	return $query;
}
add_filter( 'ajax_query_attachments_args', 'cm_acf_scope_gallery_query_to_translation_group' );

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
			// 'library'=>'all': medya seçim penceresi HEM "Bu yazıya yüklenenler" (varsayılan
			// açılış, kapsamı aşağıdaki cm_acf_scope_gallery_query_to_translation_group()
			// filtresiyle bu ürünün TR/EN/RU/ES çevirilerinin TAMAMINA genişletiliyor — kullanıcı
			// dil başına ayrı fotoğraf YÜKLEMİYOR, aynı fotoğrafı tüm dillerde tekrar kullanıyor)
			// HEM DE dropdown'dan "Medya Kütüphanesi"ne geçilerek sitedeki TÜM görseller arasından
			// seçim sunar (kullanıcı isteği: "hem ilgili ürünün hem de tüm içerikten seçme").
			// 'uploadedTo' verilseydi bu ikinci seçenek (tüm kütüphane) hiç görünmezdi.
			'library' => 'all',
			'instructions'  => $i === 1 ? 'Vitrin kapak görseli için Öne Çıkan Görsel (Featured Image) alanını kullanın. Buradaki alanlar, detay sayfası galerisindeki ek fotoğraflardır — hepsini doldurmak zorunlu değildir. 2 veya daha fazla fotoğraf (vitrin + ek görseller) olduğunda galeri üzerinde otomatik ileri/geri okları çıkar. Seçim penceresinde "Bu yazıya yüklenenler" bu ürünün (tüm dillerdeki) kendi görselerini, "Medya Kütüphanesi" ise sitedeki tüm görselleri gösterir.' : '',
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
		// 'library'=>'all' + cm_acf_scope_gallery_query_to_translation_group() (yukarıda):
		// "Bu yazıya yüklenenler" bu ürünün TÜM dil kardeşlerine yüklenmiş PDF'leri gösterir
		// (kullanıcı genelde tek bir PDF'i tüm dillerde tekrar kullanıyor); dropdown'dan
		// "Medya Kütüphanesi"ne geçilerek sitedeki TÜM PDF'ler arasından da seçim yapılabilir
		// (kullanıcı isteği — "önceki gibi tüm içeriklerin arasından seçme" de kalsın).
		'library' => 'all',
		'instructions'  => 'Bu makineye özel teknik broşür/katalog dosyası. Yüklendiğinde ürün sayfasındaki "Dokümanlar" sekmesinde otomatik görünür. Seçim penceresinde "Bu yazıya yüklenenler" bu ürünün (tüm dillerdeki) kendi dosyalarını, "Medya Kütüphanesi" ise sitedeki tüm dosyaları gösterir.',
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
				// 'library'=>'all' (2026-08-27'de kullanıcı isteğiyle 'uploadedTo'dan değiştirildi):
				// seçim penceresi hem "Bu yazıya yüklenenler" (varsayılan, sadece bu kataloğa
				// yüklenenler — diller hâlâ ayrı ayrı dosya seçer/yükler, bu DEĞİŞMEDİ, kapsam
				// cm_acf_scope_gallery_query_to_translation_group()'a hiç dahil değil) HEM
				// dropdown'dan "Medya Kütüphanesi"ne geçilerek sitede önceden başka bir yere
				// yüklenmiş PDF'ler arasından da seçim sunar.
				'library' => 'all',
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

	// ---- Sayfa Banner Görseli (tüm Sayfa'larda opsiyonel) --------------------
	// "Boşsa gizle" ilkesi: alan boşken sayfa şu anki (banner'sız) haliyle
	// görünmeye devam eder — bkz. cm_page_banner() (inc/template-tags.php).
	acf_add_local_field_group( array(
		'key'      => 'group_cm_sayfa_banner',
		'title'    => 'Sayfa Üstü Banner',
		// 'side': sağdaki panelde, "Öne Çıkan Görsel" ile aynı yerde görünsün diye —
		// varsayılan 'normal' konumu içerik editörünün ALTINA (kaydırmadan görünmeyen
		// bir yere) koyuyordu, kullanıcı bu yüzden alanı hiç bulamamıştı.
		'position' => 'side',
		'fields' => array(
			array(
				'key'   => 'field_cm_sayfa_banner_gorseli',
				'label' => 'Banner Görseli',
				'name'  => 'sayfa_banner_gorseli',
				'type'  => 'image',
				'return_format' => 'array',
				'instructions' => 'Sayfanın en üstünde, geniş bir şerit halinde gösterilecek fotoğraf. Boş bırakılırsa sayfa şu anki (banner\'sız) haliyle görünmeye devam eder.',
			),
		),
		'location' => array(
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ) ),
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
			array(
				'key'   => 'field_cm_kategori_gorsel',
				'label' => 'Kategori Fotoğrafı',
				'name'  => 'kategori_gorsel',
				'type'  => 'image',
				'return_format' => 'array',
				'instructions'  => 'Anasayfadaki "Kategoriye göre keşfedin" kartında solda büyük gösterilecek fotoğraf (gerçek makine/üretim fotoğrafı önerilir). Boş bırakılırsa yer tutucu bir görsel kullanılır.',
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

	// "Kalite Belgelerimiz" (anasayfa bölümü): sabit sayıda numaralı slot — diğer
	// anasayfa bloklarıyla (hero_slayt_1..3, istatistik_1..4) AYNI desen. Belirleyici
	// alan "Belge"dir (dosya): boşsa slot sitede hiç görünmez. "Başlık" İSTEĞE BAĞLIDIR
	// — boş bırakılırsa kartta sadece başlık satırı basılmaz, kart (görsel + link) yine
	// görünür (eskiden başlık da zorunluydu; admin dosyayı doldurup başlığı unuttuğunda
	// kartın SESSİZCE tamamen kaybolması kafa karıştırıcı bulunduğu için gevşetildi —
	// bkz. front-page.php). Tek bir "dosya" alanı hem PDF hem görsel (JPG/PNG/WEBP)
	// kabul eder — hangisi yüklendiyse ön yüzde ona göre davranılır (bkz. front-page.php):
	// görsel yüklendiyse kartta o görsel gösterilir, PDF yüklendiyse ilk sayfa önizlemesi
	// (veya aşağıdaki "Önizleme Görseli" doluysa o) gösterilir; tıklandığında HER
	// DURUMDA "Belge"nin kendisi yeni sekmede açılır.
	for ( $cm_n = 1; $cm_n <= 8; $cm_n++ ) {
		$cm_anasayfa_fields[] = array(
			'key'          => "field_cm_kalite_belge_$cm_n",
			'label'        => "Kalite Belgesi $cm_n",
			'name'         => "kalite_belge_$cm_n",
			'type'         => 'group',
			'layout'       => 'block',
			'instructions' => $cm_n === 1 ? '"Belge" boş bırakılan slotlar sitede hiç görünmez — hepsini doldurmak zorunda değilsiniz. "Başlık" isteğe bağlıdır (boşsa kartta sadece başlık yazısı görünmez).' : '',
			'sub_fields'   => array(
				array( 'key' => "field_cm_kb{$cm_n}_baslik", 'label' => 'Başlık', 'name' => 'baslik', 'type' => 'text', 'placeholder' => 'örn. ISO 9001:2015' ),
				array(
					'key'           => "field_cm_kb{$cm_n}_dosya",
					'label'         => 'Belge (PDF veya Görsel)',
					'name'          => 'dosya',
					'type'          => 'file',
					'return_format' => 'array',
					'mime_types'    => 'pdf,jpg,jpeg,png,webp',
					'instructions'  => 'PDF olarak taranmış bir belge ya da doğrudan bir fotoğraf/logo (JPG, PNG, WEBP) yükleyebilirsiniz. Tıklandığında bu dosyanın kendisi yeni sekmede açılır.',
				),
				array(
					'key'           => "field_cm_kb{$cm_n}_onizleme",
					'label'         => 'Önizleme Görseli (opsiyonel)',
					'name'          => 'onizleme_gorseli',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => 'Belge PDF ise kartta varsayılan olarak PDF\'in ilk sayfasının otomatik önizlemesi gösterilir. Bunun yerine kendi seçtiğiniz bir görsel (örn. daha temiz bir logo/rozet) göstermek isterseniz buraya yükleyin — kart tıklanınca yine yukarıdaki "Belge" açılır, sadece kartta görünen resim değişir.',
				),
			),
		);
	}

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
		'title'    => 'Uluslararası İletişim',
		'fields'   => $cm_kurumsal_fields,
		// Hem Kurumsal HEM İletişim sayfalarında düzenlenebilir (kullanıcı isteği: "istersem
		// oraya da yazabileyim"). Ön yüzde hangisi gösterilecek: cm_render_intl_contact_section()
		// çağrıldığı sayfanın KENDİ alanlarını kullanır — page.php'deki İletişim dalı, İletişim
		// sayfasının kendi intl_kisi_N alanlarında en az bir girdi varsa ONU, yoksa Kurumsal
		// sayfasınınkini gösterir (bkz. page.php). Yani veri iki yerde AYRI tutulur (ACF alanları
		// post'a bağlıdır, paylaşılmaz) — admin hangisini dolduracağını kendisi seçer.
		'location' => array(
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 7 ) ),   // Kurumsal (TR)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 277 ) ), // Corporate (EN)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 278 ) ), // О компании (RU)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 279 ) ), // Corporativo (ES)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 786 ) ), // الشركة (AR)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 6 ) ),   // İletişim (TR)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 102 ) ), // Contact (EN)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 134 ) ), // Контакты (RU)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 141 ) ), // Contacto (ES)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 785 ) ), // اتصل بنا (AR)
		),
	) );

	// ---- İletişim Sayfası: "Bize Ulaşın" Kartları -----------------------------
	// Eskiden bu 5 kart (Telefon/WhatsApp/E-posta/Adres/Çalışma Saatleri) SADECE
	// Görünüm → Özelleştir → İletişim & WhatsApp'tan (bkz. inc/customizer.php)
	// okunuyordu — kullanıcı isteği: İletişim sayfası kendi başlık+içeriğini
	// Sayfalar ekranından, sayfanın kendi alanlarından yönetebilsin. Her kartın
	// başlığı VE değeri ayrı ayrı alan — kart YAPISI (ikon, grid, link davranışı)
	// koddan gelmeye devam eder, sadece METİN admin tarafından değiştirilebilir.
	// Alan boş bırakılırsa Customizer'daki (footer'la paylaşılan) değere düşülür —
	// bkz. page.php İletişim dalı — böylece mevcut siteler hiçbir şey kaybetmez.
	acf_add_local_field_group( array(
		'key'    => 'group_cm_iletisim_kartlar',
		'title'  => 'İletişim Sayfası: "Bize Ulaşın" Kartları',
		'fields' => array(
			array( 'key' => 'field_cm_ilk_tel_baslik', 'label' => 'Telefon Kartı Başlığı', 'name' => 'iletisim_tel_baslik', 'type' => 'text', 'default_value' => 'Telefon' ),
			array( 'key' => 'field_cm_ilk_tel_deger', 'label' => 'Telefon Numarası', 'name' => 'iletisim_tel_deger', 'type' => 'text', 'instructions' => 'Boş bırakılırsa Özelleştir → İletişim & WhatsApp\'taki telefon kullanılır.' ),
			array( 'key' => 'field_cm_ilk_wa_baslik', 'label' => 'WhatsApp Kartı Başlığı', 'name' => 'iletisim_wa_baslik', 'type' => 'text', 'default_value' => 'WhatsApp' ),
			array( 'key' => 'field_cm_ilk_wa_deger', 'label' => 'WhatsApp Numarası', 'name' => 'iletisim_wa_deger', 'type' => 'text', 'instructions' => 'Başında ülke kodu ile (örn. 905551234567). Boş bırakılırsa Özelleştir\'deki WhatsApp numarası kullanılır.' ),
			array( 'key' => 'field_cm_ilk_eposta_baslik', 'label' => 'E-posta Kartı Başlığı', 'name' => 'iletisim_eposta_baslik', 'type' => 'text', 'default_value' => 'E-posta' ),
			array( 'key' => 'field_cm_ilk_eposta_deger', 'label' => 'E-posta Adresi', 'name' => 'iletisim_eposta_deger', 'type' => 'email', 'instructions' => 'Boş bırakılırsa Özelleştir\'deki şirket e-postası kullanılır.' ),
			array( 'key' => 'field_cm_ilk_adres_baslik', 'label' => 'Adres Kartı Başlığı', 'name' => 'iletisim_adres_baslik', 'type' => 'text', 'default_value' => 'Adres' ),
			array( 'key' => 'field_cm_ilk_adres_deger', 'label' => 'Adres', 'name' => 'iletisim_adres_deger', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Boş bırakılırsa Özelleştir\'deki adres kullanılır.' ),
			array( 'key' => 'field_cm_ilk_saatler_baslik', 'label' => 'Çalışma Saatleri Kartı Başlığı', 'name' => 'iletisim_saatler_baslik', 'type' => 'text', 'default_value' => 'Çalışma Saatleri' ),
			array( 'key' => 'field_cm_ilk_saatler_deger', 'label' => 'Çalışma Saatleri', 'name' => 'iletisim_saatler_deger', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Boş bırakılırsa Özelleştir\'deki çalışma saatleri kullanılır; o da boşsa bu kart hiç görünmez.' ),
		),
		'location' => array(
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 6 ) ),   // İletişim (TR)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 102 ) ), // Contact (EN)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 134 ) ), // Контакты (RU)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 141 ) ), // Contacto (ES)
			array( array( 'param' => 'page', 'operator' => '==', 'value' => 785 ) ), // اتصل بنا (AR)
		),
	) );

} );

/**
 * "Kalite Belgesi" slotlarına bir PDF yüklendiğinde, ilk sayfa önizlemesini (bkz.
 * cm_pdf_preview_url(), inc/template-tags.php) SAYFA KAYDEDİLİRKEN (admin ekranında,
 * bir defaya mahsus) üretip önbelleğe alır. Bunu YAPMAZSAK ilk önizleme, o PDF'in
 * konduğu Sayfayı (Anasayfa) ilk ziyaret eden GERÇEK ziyaretçinin isteğinde Ghostscript
 * çalıştırılarak üretilir — bu da anasayfanın (ve üzerindeki referans logosu şeridi gibi
 * `window.load` sonrası çalışan script'lerin) o istekte gözle görülür şekilde
 * yavaşlamasına yol açar (bir kez tam olarak bu şekilde fark edilmiş bir sorun).
 * cm_pdf_preview_url() zaten kendi içinde dosya bazlı önbelleğe alıyor, burada sadece
 * "kaydet" anında bir kez ÖNCEDEN tetikliyoruz ki halka açık sayfa hep hazır bulsun.
 */
function cm_prewarm_kalite_belge_previews( $post_id ) {
	if ( get_post_type( $post_id ) !== 'page' || ! function_exists( 'get_field' ) ) return;
	for ( $cm_i = 1; $cm_i <= 8; $cm_i++ ) {
		$cm_cert = get_field( "kalite_belge_$cm_i", $post_id );
		$cm_dosya = $cm_cert['dosya'] ?? null;
		// "Önizleme Görseli" elle girilmişse zaten ONUN gösterileceği için PDF önizlemesi
		// hiç kullanılmayacak — boşuna üretip önbellek doldurmayalım.
		if ( ! empty( $cm_cert['onizleme_gorseli'] ) ) continue;
		if ( $cm_dosya && strpos( $cm_dosya['mime_type'] ?? '', 'image/' ) !== 0 ) {
			cm_pdf_preview_url( $cm_dosya );
		}
	}
}
add_action( 'acf/save_post', 'cm_prewarm_kalite_belge_previews', 20 );

/**
 * Aynı önbelleğe-alma önlemi (bkz. yukarısı) "katalog" CPT'sinin pdf_dosya alanı
 * için de gerekli — bkz. page-kataloglar.php kart önizlemesi (cm_pdf_preview_url()).
 * Katalog kaydedilirken bir defaya mahsus üretilir, Kataloglar sayfasını ilk açan
 * ziyaretçi Ghostscript'in çalışmasını beklemez.
 */
function cm_prewarm_katalog_preview( $post_id ) {
	if ( get_post_type( $post_id ) !== 'katalog' || ! function_exists( 'get_field' ) ) return;
	$cm_pdf = get_field( 'pdf_dosya', $post_id );
	if ( $cm_pdf ) cm_pdf_preview_url( $cm_pdf );
}
add_action( 'acf/save_post', 'cm_prewarm_katalog_preview', 20 );
