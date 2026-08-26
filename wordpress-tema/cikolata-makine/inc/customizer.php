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

	$wp_customize->add_setting( 'sosyal_linkedin', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'sosyal_linkedin', array(
		'label'   => 'LinkedIn Linki',
		'section' => 'cm_iletisim',
		'type'    => 'url',
	) );

	$wp_customize->add_setting( 'sosyal_instagram', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'sosyal_instagram', array(
		'label'   => 'Instagram Linki',
		'section' => 'cm_iletisim',
		'type'    => 'url',
	) );

	// ---- Kataloglar arşiv sayfası banner'ı ------------------------------------
	// Kurumsal/İletişim/Kariyer gibi tekil Sayfa'ların aksine "Kataloglar" bir
	// post type arşivi (bkz. archive-katalog.php) — arkasında düzenlenebilir tek
	// bir Sayfa yazısı olmadığı için ACF alanı değil, buradaki Customizer ayarı
	// kullanılıyor (sirket_eposta/harita_gomme_url ile aynı desen).
	$wp_customize->add_section( 'cm_sayfa_gorselleri', array(
		'title'    => 'Sayfa Görselleri',
		'priority' => 35,
	) );
	$wp_customize->add_setting( 'kataloglar_banner_gorseli' );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'kataloglar_banner_gorseli', array(
		'label'       => 'Kataloglar Sayfası Banner Görseli',
		'description' => 'Boş bırakılırsa Kataloglar sayfası banner\'sız (şu anki) haliyle görünmeye devam eder.',
		'section'     => 'cm_sayfa_gorselleri',
	) ) );
}
add_action( 'customize_register', 'cm_customize_register' );
