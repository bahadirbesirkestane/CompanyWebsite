<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
the_post();

// Alt sayfalarda (örn. Kurumsal > Hakkımızda) üst sayfa zincirini de kırıntı yoluna ekler.
$cm_crumbs = array( array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ) );
foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $cm_ancestor_id ) {
	$cm_crumbs[] = array( 'label' => get_the_title( $cm_ancestor_id ), 'url' => get_permalink( $cm_ancestor_id ) );
}
$cm_crumbs[] = array( 'label' => get_the_title() );

// Kırıntı yolu banner varsa ONUN İÇİNE, başlığın altına basılır (bkz. cm_page_banner());
// banner yoksa aşağıdaki eski konumunda (başlığın da üstünde) basılmaya devam eder.
$cm_has_banner = cm_page_banner( get_field( 'sayfa_banner_gorseli' ), get_the_title(), $cm_crumbs );
?>

<div class="wrap page-content">
	<?php if ( ! $cm_has_banner ) : ?>
		<?php cm_breadcrumb( $cm_crumbs ); ?>
		<h1 class="h-lg" style="margin-top:16px;"><?php the_title(); ?></h1>
	<?php endif; ?>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="ph" style="aspect-ratio:16/6; margin-top:28px;">
			<?php the_post_thumbnail( 'large' ); ?>
			<span class="corner c-tl"></span><span class="corner c-tr"></span><span class="corner c-bl"></span><span class="corner c-br"></span>
		</div>
	<?php endif; ?>

	<div class="body-p" style="max-width:none; margin-top:28px;">
		<?php the_content(); ?>
	</div>

	<?php
	// "Kurumsal" ailesi (TR + EN/RU/ES/AR çevirileri) sabit ID'lerle tanınır — bkz.
	// inc/acf-fields.php group_cm_kurumsal konum kuralları, aynı ID listesi orada da kullanılır.
	$cm_kurumsal_ids = array( 7, 277, 278, 279, 786 );
	$cm_is_kurumsal  = in_array( get_the_ID(), $cm_kurumsal_ids, true );

	// İletişim sayfası mı? (form + harita + Uluslararası İletişim bloğu aşağıda buna göre
	// çalışır). Kurumsal'ın altındakiyle AYNI desen, ama sabit ID yerine slug üzerinden.
	$cm_iletisim_page = cm_translated_page( 'iletisim' );
	$cm_is_iletisim   = $cm_iletisim_page && (int) $cm_iletisim_page->ID === get_the_ID();

	// "İletişim" ailesi de aynı ikonlu kart görünümünü (corp-grid) kullanır — Gizlilik
	// Politikası/Çerez Politikası gibi fotoğrafsız, salt metin sayfaları İletişim'in
	// altına da taşınabilsin diye (bkz. inc/acf-fields.php group_cm_iletisim_kartlar
	// konum kuralındaki AYNI ID listesi). Bu, SADECE alt sayfa kart stilini belirler —
	// $cm_is_kurumsal'a bağlı Uluslararası İletişim çağrısını ETKİLEMEZ.
	$cm_iletisim_ids   = array( 6, 102, 134, 141, 785 );
	$cm_use_icon_cards = $cm_is_kurumsal || in_array( get_the_ID(), $cm_iletisim_ids, true );

	$cm_children = get_pages( array( 'child_of' => get_the_ID(), 'sort_column' => 'menu_order', 'parent' => get_the_ID() ) );
	// İletişim'de alt sayfa kartları (bkz. cm_render_page_children_grid()) BURADA DEĞİL,
	// sayfanın en altında (Uluslararası İletişim'in altında) basılır — aşağıya bkz.
	// "kullanıcı isteği: Gizlilik/Çerez Politikası kartları en altta dursun".
	if ( ! $cm_is_iletisim ) cm_render_page_children_grid( $cm_children, $cm_use_icon_cards );
	?>

	<?php if ( $cm_is_kurumsal ) cm_render_intl_contact_section( get_the_ID() ); ?>

	<?php
	// İletişim sayfasına özel: form + harita (Görünüm → Özelleştir → İletişim & WhatsApp
	// üzerinden yönetilir — bkz. functions.php cm_contact_form_id() / cm_harita_embed_url()).
	if ( $cm_is_iletisim ) :
		$cm_form_id = get_field( 'iletisim_form_gizle' ) ? 0 : cm_contact_form_id();
		$cm_map_url = cm_harita_embed_url();

		// Telefon/WhatsApp/e-posta/adres/çalışma saatleri kartları artık İletişim sayfasının
		// KENDİ alanlarından yönetilir (bkz. inc/acf-fields.php group_cm_iletisim_kartlar —
		// Sayfalar → İletişim'den, Özelleştir'e gitmeden düzenlenir). Alan boşsa footer'la
		// paylaşılan Customizer değerine düşülür, o da boşsa kart hiç görünmez ("boşsa
		// gizle"). Görsel dili aşağıdaki Uluslararası İletişim kartlarıyla (intl-contact-*)
		// BİLEREK aynı — "bizim iletişimimiz" ile "uluslararası iletişim" tek, tutarlı bir
		// blok gibi okunsun.
		$cm_tel_baslik = get_field( 'iletisim_tel_baslik' ) ?: cm__( 'iletisim_telefon' );
		$cm_tel        = get_field( 'iletisim_tel_deger' ) ?: cm_option( 'sirket_telefon' );

		$cm_wa_baslik  = get_field( 'iletisim_wa_baslik' ) ?: cm__( 'iletisim_whatsapp' );
		$cm_wa_numara  = get_field( 'iletisim_wa_deger' ) ?: cm_option( 'whatsapp_numarasi' );
		$cm_wa         = cm_whatsapp_url( $cm_wa_numara );

		$cm_eposta_baslik = get_field( 'iletisim_eposta_baslik' ) ?: cm__( 'iletisim_eposta' );
		$cm_eposta        = get_field( 'iletisim_eposta_deger' ) ?: cm_option( 'sirket_eposta' );

		$cm_adres_baslik = get_field( 'iletisim_adres_baslik' ) ?: cm__( 'iletisim_adres' );
		$cm_adres        = get_field( 'iletisim_adres_deger' ) ?: cm_option( 'sirket_adres' );

		$cm_saatler_baslik = get_field( 'iletisim_saatler_baslik' ) ?: cm__( 'iletisim_calisma_saatleri' );
		$cm_saatler        = get_field( 'iletisim_saatler_deger' ) ?: cm_option( 'calisma_saatleri' );

		if ( $cm_tel || $cm_eposta || $cm_adres || $cm_saatler || $cm_wa ) : ?>
			<div class="eyebrow reveal" style="margin-top:40px;"><?php echo esc_html( cm__( 'iletisim_bilgileri_eyebrow' ) ); ?></div>
			<h2 class="h-md reveal"><?php echo esc_html( cm__( 'iletisim_bilgileri_baslik' ) ); ?></h2>
			<div class="intl-contact-grid" style="margin-top:24px;">
				<?php if ( $cm_tel ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-icon"><?php cm_contact_icon( 'tel' ); ?></div>
						<div class="intl-contact-country"><?php echo esc_html( $cm_tel_baslik ); ?></div>
						<div class="intl-contact-details"><?php
							// Birden fazla numara ("·", "/", ",", ";" veya "|" ile ayrılmış) için her numara ayrı tel: bağlantısı olur.
							$cm_tel_links = array();
							foreach ( array_filter( array_map( 'trim', preg_split( '/[·\/,;|]+/u', $cm_tel ) ) ) as $cm_tel_part ) {
								$cm_tel_links[] = '<a href="tel:' . esc_attr( preg_replace( '/[^+0-9]/', '', $cm_tel_part ) ) . '">' . esc_html( $cm_tel_part ) . '</a>';
							}
							echo implode( ' · ', $cm_tel_links ); // Her parça yukarıda kaçışlandı.
						?></div>
					</div>
				<?php endif; ?>
				<?php if ( $cm_wa ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-icon"><?php cm_contact_icon( 'wa' ); ?></div>
						<div class="intl-contact-country"><?php echo esc_html( $cm_wa_baslik ); ?></div>
						<div class="intl-contact-details"><a href="<?php echo esc_url( $cm_wa ); ?>" target="_blank" rel="noopener"><?php echo esc_html( cm_format_phone_display( $cm_wa_numara ) ); ?></a></div>
					</div>
				<?php endif; ?>
				<?php if ( $cm_eposta ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-icon"><?php cm_contact_icon( 'eposta' ); ?></div>
						<div class="intl-contact-country"><?php echo esc_html( $cm_eposta_baslik ); ?></div>
						<div class="intl-contact-details"><a href="mailto:<?php echo esc_attr( $cm_eposta ); ?>"><?php echo esc_html( $cm_eposta ); ?></a></div>
					</div>
				<?php endif; ?>
				<?php if ( $cm_adres ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-icon"><?php cm_contact_icon( 'adres' ); ?></div>
						<div class="intl-contact-country"><?php echo esc_html( $cm_adres_baslik ); ?></div>
						<div class="intl-contact-details"><span style="font-size:13px;color:var(--ink-soft);"><?php echo nl2br( esc_html( $cm_adres ) ); ?></span></div>
					</div>
				<?php endif; ?>
				<?php if ( $cm_saatler ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-icon"><?php cm_contact_icon( 'saat' ); ?></div>
						<div class="intl-contact-country"><?php echo esc_html( $cm_saatler_baslik ); ?></div>
						<div class="intl-contact-details"><span style="font-size:13px;color:var(--ink-soft);"><?php echo nl2br( esc_html( $cm_saatler ) ); ?></span></div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif;

		if ( $cm_form_id || $cm_map_url ) : ?>
			<div class="contact-grid<?php echo ( $cm_form_id && $cm_map_url ) ? '' : ' contact-grid--single'; ?>">
				<?php if ( $cm_form_id ) : ?>
					<div class="contact-form-col">
						<h2 class="h-md"><?php echo esc_html( cm__( 'iletisim_formu_baslik' ) ); ?></h2>
						<div class="cm-form">
							<?php echo do_shortcode( '[contact-form-7 id="' . (int) $cm_form_id . '"]' ); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( $cm_map_url ) : ?>
					<div class="contact-map-col">
						<h2 class="h-md"><?php echo esc_html( cm__( 'konum_baslik' ) ); ?></h2>
						<div class="cm-map">
							<iframe src="<?php echo esc_url( $cm_map_url ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php echo esc_attr( cm__( 'konum_baslik' ) ); ?>"></iframe>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif;

		// Uluslararası İletişim, İletişim sayfasında da BİZİM kendi iletişimimizin (üstteki
		// kartlar + form/harita) ALTINDA görünür. Alan grubu artık HEM Kurumsal HEM İletişim
		// sayfalarında düzenlenebilir (bkz. inc/acf-fields.php group_cm_kurumsal) — admin
		// isterse doğrudan İletişim sayfasından da girebilsin diye. Öncelik: İletişim
		// sayfasının KENDİ girdileri varsa onlar kullanılır; hiç girilmemişse Kurumsal
		// sayfasınınkine (geçerli dile pll_get_post ile çözülerek) düşülür.
		$cm_iletisim_has_own_intl = false;
		for ( $cm_i = 1; $cm_i <= 10; $cm_i++ ) {
			$cm_row = get_field( "intl_kisi_$cm_i", get_the_ID() );
			if ( $cm_row && ! empty( $cm_row['ulke'] ) ) { $cm_iletisim_has_own_intl = true; break; }
		}
		if ( $cm_iletisim_has_own_intl ) {
			cm_render_intl_contact_section( get_the_ID() );
		} else {
			$cm_kurumsal_id_for_lang = function_exists( 'pll_get_post' ) ? pll_get_post( 7, function_exists( 'pll_current_language' ) ? pll_current_language() : 'tr' ) : 7;
			cm_render_intl_contact_section( $cm_kurumsal_id_for_lang ?: 7 );
		}

		// Gizlilik Politikası/Çerez Politikası gibi İletişim'in alt sayfaları — kullanıcı
		// isteği: sayfanın en altında, Uluslararası İletişim'in altında dursun.
		cm_render_page_children_grid( $cm_children, $cm_use_icon_cards );
	endif; ?>
</div>

<?php get_footer(); ?>
