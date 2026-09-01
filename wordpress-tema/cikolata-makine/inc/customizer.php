<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * İletişim bilgileri + WhatsApp numarası + sosyal linkler.
 * ACF Options Page yerine WordPress'in kendi Özelleştir ekranı kullanılıyor
 * (Options Page ACF PRO'ya özeldir; bu alanlar her WordPress kurulumunda,
 * ek eklenti gerekmeden, Görünüm → Özelleştir altında çalışır).
 */

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'CM_Customize_Textarea_Control' ) ) {
	class CM_Customize_Textarea_Control extends WP_Customize_Control {
		public $type = 'textarea';
		public function render_content() {
			?>
			<label>
				<?php if ( $this->label ) : ?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php endif; ?>
				<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span><?php endif; ?>
				<textarea rows="3" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
			</label>
			<?php
		}
	}
}

function cm_customize_register( $wp_customize ) {
	// ---- Footer ---------------------------------------------------------
	// "Hızlı Linkler" ve "Kategoriler" sütunları zaten Görünüm → Menüler'deki
	// "Footer — Hızlı Linkler" / "Footer — Kategoriler" konumlarına menü atanarak
	// düzenlenebilir (bkz. functions.php → register_nav_menus()). Logonun altındaki
	// slogan İSE Customizer'da DEĞİL — cm__('footer_slogan') (inc/strings.php)
	// üzerinden geliyor: Customizer ayarları (theme_mod) WordPress'te SİTE GENELİDİR,
	// Polylang dile göre AYRI değer TUTMAZ (test edilip doğrulandı — TR'de girilen
	// metin /en/ sayfasında da aynen çıkıyordu). Dile göre değişmesi gereken bir
	// metin (slogan gibi) bu yüzden cm__() + "Diller → Dize Çevirisi" ile
	// yönetilmeli; Customizer sadece telefon/adres gibi dilden BAĞIMSIZ, site
	// geneli gerçek verilere ayrılmalı (bkz. aşağıdaki İletişim & WhatsApp bölümü).

	$wp_customize->add_section( 'cm_iletisim', array(
		'title'    => 'İletişim & WhatsApp',
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'sirket_adres', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( new CM_Customize_Textarea_Control( $wp_customize, 'sirket_adres', array(
		'label'   => 'Adres',
		'section' => 'cm_iletisim',
	) ) );

	$wp_customize->add_setting( 'sirket_telefon', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'sirket_telefon', array(
		'label'   => 'Telefon',
		'section' => 'cm_iletisim',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'sirket_eposta', array( 'sanitize_callback' => 'sanitize_email' ) );
	$wp_customize->add_control( 'sirket_eposta', array(
		'label'   => 'E-posta',
		'section' => 'cm_iletisim',
		'type'    => 'email',
	) );

	$wp_customize->add_setting( 'whatsapp_numarasi', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'whatsapp_numarasi', array(
		'label'       => 'WhatsApp Numarası',
		'description' => 'Başında ülke kodu ile girin (örn. 905551234567). Header\'daki WhatsApp butonu bu numaraya yönlendirir. Boş bırakılırsa "Teklif İste" butonu görünür.',
		'section'     => 'cm_iletisim',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'calisma_saatleri', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( new CM_Customize_Textarea_Control( $wp_customize, 'calisma_saatleri', array(
		'label'       => 'Çalışma Saatleri',
		'description' => 'İletişim sayfasında gösterilir. Boş bırakılırsa hiç görünmez.',
		'section'     => 'cm_iletisim',
	) ) );

	$wp_customize->add_setting( 'harita_gomme_url', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'harita_gomme_url', array(
		'label'       => 'Google Haritalar Yerleştirme (Embed) URL\'si',
		'description' => 'Google Haritalar\'da adresinizi arayın → Paylaş → "Harita yerleştir" sekmesi → verilen HTML kodundaki src="..." içindeki adresi buraya yapıştırın (API anahtarı gerekmez). Boş bırakılırsa İletişim sayfasında harita hiç görünmez — yanlış/örnek bir adres asla otomatik gösterilmez.',
		'section'     => 'cm_iletisim',
		'type'        => 'url',
	) );

	// Sosyal medya linkleri — footer'daki ikon şeridi (5 platform) VE header'daki
	// WhatsApp yanındaki 3 ikon (LinkedIn/Instagram/YouTube, bkz. header.php) buradan
	// beslenir. Her biri boşsa o ikon hiç basılmaz ("boşsa gizle").
	$wp_customize->add_setting( 'sosyal_facebook', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'sosyal_facebook', array(
		'label'   => 'Facebook Linki',
		'section' => 'cm_iletisim',
		'type'    => 'url',
	) );

	$wp_customize->add_setting( 'sosyal_instagram', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'sosyal_instagram', array(
		'label'       => 'Instagram Linki',
		'description' => 'Header\'da (WhatsApp yanında) ve footer\'da ikon olarak gösterilir.',
		'section'     => 'cm_iletisim',
		'type'        => 'url',
	) );

	$wp_customize->add_setting( 'sosyal_linkedin', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'sosyal_linkedin', array(
		'label'       => 'LinkedIn Linki',
		'description' => 'Header\'da (WhatsApp yanında) ve footer\'da ikon olarak gösterilir.',
		'section'     => 'cm_iletisim',
		'type'        => 'url',
	) );

	$wp_customize->add_setting( 'sosyal_youtube', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'sosyal_youtube', array(
		'label'       => 'YouTube Linki',
		'description' => 'Header\'da (WhatsApp yanında) ve footer\'da ikon olarak gösterilir.',
		'section'     => 'cm_iletisim',
		'type'        => 'url',
	) );

	$wp_customize->add_setting( 'sosyal_twitter', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'sosyal_twitter', array(
		'label'       => 'X (Twitter) Linki',
		'description' => 'Sadece footer\'da ikon olarak gösterilir.',
		'section'     => 'cm_iletisim',
		'type'        => 'url',
	) );

	// ---- Analitik & Arama Motoru Doğrulama ---------------------------------
	// cm_option() ile okunur, çıktısı inc/seo.php'de basılır — hiçbiri doldurulmazsa
	// (varsayılan durum) sitede hiçbir izleme kodu/etiket eklenmez.
	$wp_customize->add_section( 'cm_analitik', array(
		'title'    => 'Analitik & Arama Motoru Doğrulama',
		'priority' => 35,
	) );

	$wp_customize->add_setting( 'google_analytics_id', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'google_analytics_id', array(
		'label'       => 'Google Analytics (GA4) Ölçüm Kimliği',
		'description' => 'analytics.google.com üzerinden alacağınız "G-" ile başlayan ölçüm kimliği (örn. G-ABC1234XYZ). Boş bırakılırsa hiçbir izleme kodu eklenmez.',
		'section'     => 'cm_analitik',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'google_tag_manager_id', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'google_tag_manager_id', array(
		'label'       => 'Google Tag Manager Kapsayıcı Kimliği',
		'description' => '"GTM-" ile başlayan kapsayıcı kimliği. Genelde YALNIZCA bunu veya yukarıdaki GA4 kimliğini kullanın — Tag Manager kullanıyorsanız GA4\'ü de oradan ekleyin, ikisini birden buraya girmeyin (aksi halde ziyaretçi çift sayılabilir).',
		'section'     => 'cm_analitik',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'google_site_verification', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'google_site_verification', array(
		'label'       => 'Google Search Console Doğrulama Kodu',
		'description' => 'search.google.com/search-console → Mülk Ekle → "HTML etiketi" yöntemi → content="..." içindeki kodu (tırnak işaretleri olmadan) buraya yapıştırın. Boş bırakılırsa hiçbir şey eklenmez.',
		'section'     => 'cm_analitik',
		'type'        => 'text',
	) );
}
add_action( 'customize_register', 'cm_customize_register' );
