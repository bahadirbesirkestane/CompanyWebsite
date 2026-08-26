<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
the_post();
cm_page_banner( get_field( 'sayfa_banner_gorseli' ) );
?>

<div class="wrap page-content">
	<?php
	// Alt sayfalarda (örn. Kurumsal > Hakkımızda) üst sayfa zincirini de kırıntı yoluna ekler.
	$cm_crumbs = array( array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ) );
	foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $cm_ancestor_id ) {
		$cm_crumbs[] = array( 'label' => get_the_title( $cm_ancestor_id ), 'url' => get_permalink( $cm_ancestor_id ) );
	}
	$cm_crumbs[] = array( 'label' => get_the_title() );
	cm_breadcrumb( $cm_crumbs );
	?>

	<h1 class="h-lg" style="margin-top:16px;"><?php the_title(); ?></h1>

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
	// "Kurumsal" ailesi (TR + EN/RU/ES çevirileri) sabit ID'lerle tanınır — bkz.
	// inc/acf-fields.php group_cm_kurumsal konum kuralları, aynı ID listesi orada da kullanılır.
	$cm_kurumsal_ids = array( 7, 277, 278, 279 );
	$cm_is_kurumsal  = in_array( get_the_ID(), $cm_kurumsal_ids, true );

	$cm_children = get_pages( array( 'child_of' => get_the_ID(), 'sort_column' => 'menu_order', 'parent' => get_the_ID() ) );
	if ( $cm_children && $cm_is_kurumsal ) : ?>
		<div class="corp-grid" style="margin-top:40px;">
			<?php foreach ( $cm_children as $cm_child ) :
				$cm_child_id  = $cm_child->ID;
				$cm_tr_child  = function_exists( 'pll_get_post' ) ? pll_get_post( $cm_child_id, 'tr' ) : $cm_child_id;
				$cm_teaser    = has_excerpt( $cm_child_id ) ? get_the_excerpt( $cm_child_id ) : wp_trim_words( wp_strip_all_tags( $cm_child->post_content ), 18, '…' );
			?>
				<a class="corp-card reveal" href="<?php echo esc_url( get_permalink( $cm_child_id ) ); ?>">
					<div class="corp-card-icon"><?php cm_kurumsal_child_icon( (int) $cm_tr_child ); ?></div>
					<h3><?php echo esc_html( get_the_title( $cm_child_id ) ); ?></h3>
					<p><?php echo esc_html( $cm_teaser ); ?></p>
					<div class="go"><?php echo esc_html( cm__( 'detaylari_gor' ) ); ?></div>
				</a>
			<?php endforeach; ?>
		</div>
	<?php elseif ( $cm_children ) : ?>
		<div class="prod-grid" style="margin-top:40px;">
			<?php foreach ( $cm_children as $cm_child ) : ?>
				<a class="prod-card reveal" href="<?php echo esc_url( get_permalink( $cm_child ) ); ?>">
					<?php if ( has_post_thumbnail( $cm_child ) ) : ?>
						<div class="ph"><?php echo get_the_post_thumbnail( $cm_child, 'cm-card' ); ?></div>
					<?php endif; ?>
					<h3><?php echo esc_html( get_the_title( $cm_child ) ); ?></h3>
					<div class="go"><?php echo esc_html( cm__( 'detaylari_gor' ) ); ?></div>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( $cm_is_kurumsal ) cm_render_intl_contact_section( get_the_ID() ); ?>

	<?php
	// İletişim sayfasına özel: form + harita (Görünüm → Özelleştir → İletişim & WhatsApp
	// üzerinden yönetilir — bkz. functions.php cm_contact_form_id() / cm_harita_embed_url()).
	$cm_iletisim_page = cm_translated_page( 'iletisim' );
	$cm_is_iletisim = $cm_iletisim_page && (int) $cm_iletisim_page->ID === get_the_ID();
	if ( $cm_is_iletisim ) :
		$cm_form_id = cm_contact_form_id();
		$cm_map_url = cm_harita_embed_url();

		// Telefon/e-posta/adres/çalışma saatleri — footer'la AYNI kaynaktan (Customizer)
		// okunur, tek doğruluk kaynağı; sayfa içeriğine elle yazılıp zamanla footer'dan
		// farklılaşan/eskiyen bilgi tekrar yaşanmasın diye (bkz. proje notları). Her biri
		// ayrı ayrı "boşsa gizle" — doldurulmamış hiçbir alan sitede görünmez. Görsel dili
		// aşağıdaki Uluslararası İletişim kartlarıyla (intl-contact-*) BİLEREK aynı —
		// "bizim iletişimimiz" ile "uluslararası iletişim" tek, tutarlı bir blok gibi okunsun.
		$cm_tel     = cm_option( 'sirket_telefon' );
		$cm_eposta  = cm_option( 'sirket_eposta' );
		$cm_adres   = cm_option( 'sirket_adres' );
		$cm_saatler = cm_option( 'calisma_saatleri' );
		$cm_wa      = cm_whatsapp_url();
		if ( $cm_tel || $cm_eposta || $cm_adres || $cm_saatler || $cm_wa ) : ?>
			<div class="intl-contact-grid" style="margin-top:24px;">
				<?php if ( $cm_tel ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-country"><?php echo esc_html( cm__( 'iletisim_telefon' ) ); ?></div>
						<div class="intl-contact-details"><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $cm_tel ) ); ?>"><?php echo esc_html( $cm_tel ); ?></a></div>
					</div>
				<?php endif; ?>
				<?php if ( $cm_wa ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-country"><?php echo esc_html( cm__( 'iletisim_whatsapp' ) ); ?></div>
						<div class="intl-contact-details"><a href="<?php echo esc_url( $cm_wa ); ?>" target="_blank" rel="noopener"><?php echo esc_html( cm_format_phone_display( cm_option( 'whatsapp_numarasi' ) ) ); ?></a></div>
					</div>
				<?php endif; ?>
				<?php if ( $cm_eposta ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-country"><?php echo esc_html( cm__( 'iletisim_eposta' ) ); ?></div>
						<div class="intl-contact-details"><a href="mailto:<?php echo esc_attr( $cm_eposta ); ?>"><?php echo esc_html( $cm_eposta ); ?></a></div>
					</div>
				<?php endif; ?>
				<?php if ( $cm_adres ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-country"><?php echo esc_html( cm__( 'iletisim_adres' ) ); ?></div>
						<div class="intl-contact-details"><span style="font-size:13px;color:var(--ink-soft);"><?php echo nl2br( esc_html( $cm_adres ) ); ?></span></div>
					</div>
				<?php endif; ?>
				<?php if ( $cm_saatler ) : ?>
					<div class="intl-contact-card">
						<div class="intl-contact-country"><?php echo esc_html( cm__( 'iletisim_calisma_saatleri' ) ); ?></div>
						<div class="intl-contact-details"><span style="font-size:13px;color:var(--ink-soft);"><?php echo nl2br( esc_html( $cm_saatler ) ); ?></span></div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif;

		if ( $cm_form_id || $cm_map_url ) : ?>
			<div class="contact-grid">
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
	endif; ?>
</div>

<?php get_footer(); ?>
