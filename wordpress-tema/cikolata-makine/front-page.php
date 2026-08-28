<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$cm_slides = array();
if ( function_exists( 'get_field' ) ) {
	foreach ( array( 1, 2, 3 ) as $cm_n ) {
		$cm_slide = get_field( "hero_slayt_$cm_n" );
		if ( $cm_slide && ! empty( $cm_slide['baslik'] ) ) $cm_slides[] = $cm_slide;
	}
}
if ( ! $cm_slides ) {
	$cm_slides = array( array(
		'eyebrow'      => cm__( 'hero_eyebrow_default' ),
		'baslik'       => get_bloginfo( 'name' ),
		'aciklama'     => get_bloginfo( 'description' ) ?: cm__( 'hero_aciklama_default' ),
		'buton1_metin' => cm__( 'hero_buton1_default' ),
		'buton1_link'  => cm_translated_page_url( 'urunler', '/urunler/' ),
		'buton2_metin' => cm__( 'hero_buton2_default' ),
		'buton2_link'  => cm_translated_page_url( 'iletisim', '/iletisim/' ),
	) );
}

$cm_stats = array();
if ( function_exists( 'get_field' ) ) {
	foreach ( array( 1, 2, 3, 4 ) as $cm_n ) {
		$cm_stat = get_field( "istatistik_$cm_n" );
		if ( $cm_stat && ! empty( $cm_stat['sayi'] ) ) $cm_stats[] = $cm_stat;
	}
}
?>

<div class="hero">
	<div class="hero-viewport">
		<?php if ( count( $cm_slides ) > 1 ) : ?>
			<button class="hero-arrow prev" aria-label="<?php echo esc_attr( cm__( 'hero_onceki_slayt' ) ); ?>">‹</button>
			<button class="hero-arrow next" aria-label="<?php echo esc_attr( cm__( 'hero_sonraki_slayt' ) ); ?>">›</button>
		<?php endif; ?>

		<?php foreach ( $cm_slides as $i => $slide ) : ?>
			<div class="hero-slide<?php echo $i === 0 ? ' active' : ''; ?>" data-hero-slide="<?php echo (int) $i; ?>">
				<div class="hero-media">
					<?php if ( ! empty( $slide['gorsel']['url'] ) ) : ?>
						<img src="<?php echo esc_url( $slide['gorsel']['url'] ); ?>" alt="<?php echo esc_attr( $slide['gorsel']['alt'] ?? '' ); ?>">
					<?php else : ?>
						<div class="ph-fill"><?php cm_generic_icon( 72 ); ?></div>
					<?php endif; ?>
				</div>
				<div class="hero-text"><div class="hero-text-inner">
					<?php if ( ! empty( $slide['eyebrow'] ) ) : ?><div class="eyebrow"><?php echo esc_html( $slide['eyebrow'] ); ?></div><?php endif; ?>
					<h1 class="h-xl"><?php echo nl2br( esc_html( $slide['baslik'] ) ); ?></h1>
					<?php if ( ! empty( $slide['aciklama'] ) ) : ?><p class="lede" style="margin-top:14px;"><?php echo esc_html( $slide['aciklama'] ); ?></p><?php endif; ?>
					<div class="hero-cta">
						<?php if ( ! empty( $slide['buton1_metin'] ) ) : ?><a class="btn btn-primary" href="<?php echo esc_url( $slide['buton1_link'] ?: '#' ); ?>"><?php echo esc_html( $slide['buton1_metin'] ); ?></a><?php endif; ?>
						<?php if ( ! empty( $slide['buton2_metin'] ) ) : ?><a class="btn btn-outline" href="<?php echo esc_url( $slide['buton2_link'] ?: '#' ); ?>"><?php echo esc_html( $slide['buton2_metin'] ); ?></a><?php endif; ?>
					</div>
					<?php if ( count( $cm_slides ) > 1 ) : ?>
						<div class="hero-dots" role="tablist" aria-label="<?php echo esc_attr( cm__( 'hero_slayt_secimi' ) ); ?>">
							<?php foreach ( $cm_slides as $j => $dot_slide ) : ?>
								<button class="hero-dot<?php echo $j === 0 ? ' active' : ''; ?>" data-hero-dot="<?php echo (int) $j; ?>" aria-label="<?php echo (int) $j + 1; ?><?php echo esc_attr( cm__( 'hero_slayt_suffix' ) ); ?>"></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<?php if ( $cm_stats ) : ?>
<div class="wrap reveal" style="margin-top:56px;">
	<div class="stat-row">
		<?php foreach ( $cm_stats as $stat ) : ?>
			<div class="stat"><div class="n mono"><?php echo esc_html( $stat['sayi'] ); ?></div><div class="l"><?php echo esc_html( $stat['etiket'] ); ?></div></div>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<?php
$cm_top_cats = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => 0, 'hide_empty' => false ) );
if ( ! is_wp_error( $cm_top_cats ) && $cm_top_cats ) :
?>
<div class="wrap section">
	<div class="eyebrow reveal"><?php echo esc_html( cm_urunler_label() ); ?></div>
	<h2 class="h-lg reveal"><?php echo esc_html( cm__( 'urunler_kesfet_baslik' ) ); ?></h2>
	<div class="cat-grid">
		<?php foreach ( $cm_top_cats as $term ) cm_category_card( $term ); ?>
	</div>
</div>
<?php endif; ?>

<?php
// "Ne üretmek istiyorsunuz?" — makine tipi yerine nihai ürüne göre ikinci gezinme
// ekseni (bkz. 03_TASARIM_YENILEME_ONERISI.md Bölüm 5.3). hide_empty=true olduğu için
// admin henüz hiç "Ürün Ailesi" terimi/etiketli ürün girmediyse bu blok TAMAMEN
// gizli kalır — "boşsa gizle" ilkesi, terim eklenip ürün etiketlenince otomatik belirir.
$cm_families = get_terms( array( 'taxonomy' => 'urun_ailesi', 'hide_empty' => true ) );
if ( ! is_wp_error( $cm_families ) && $cm_families ) :
?>
<div class="wrap section-tight">
	<div class="eyebrow reveal"><?php echo esc_html( cm__( 'ne_uretmek_eyebrow' ) ); ?></div>
	<h2 class="h-lg reveal"><?php echo esc_html( cm__( 'ne_uretmek_baslik' ) ); ?></h2>
	<div class="cat-grid">
		<?php foreach ( $cm_families as $cm_term ) cm_urun_ailesi_card( $cm_term ); ?>
	</div>
</div>
<?php endif; ?>

<?php
$cm_featured_q = new WP_Query( array(
	'post_type'      => 'makine',
	'posts_per_page' => 3,
	'meta_key'       => 'one_cikan',
	'meta_value'     => '1',
) );
if ( ! $cm_featured_q->have_posts() ) {
	$cm_featured_q = new WP_Query( array( 'post_type' => 'makine', 'posts_per_page' => 3 ) );
}
if ( $cm_featured_q->have_posts() ) :
?>
<div class="wrap section-tight">
	<div class="eyebrow reveal"><?php echo esc_html( cm__( 'vitrin_eyebrow' ) ); ?></div>
	<h2 class="h-lg reveal"><?php echo esc_html( cm__( 'one_cikan_makineler' ) ); ?></h2>
	<div class="prod-grid">
		<?php while ( $cm_featured_q->have_posts() ) : $cm_featured_q->the_post(); cm_product_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
	</div>
</div>
<?php endif; ?>

<?php
$cm_refs = new WP_Query( array( 'post_type' => 'referans', 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
if ( $cm_refs->have_posts() ) :
?>
<div class="section-tight" style="padding-bottom:0;">
	<div class="wrap reveal"><div class="eyebrow"><?php echo esc_html( cm__( 'referanslarimiz' ) ); ?></div><h2 class="h-md"><?php echo esc_html( cm__( 'referans_baslik' ) ); ?></h2></div>
	<div class="wrap">
		<div class="marquee-wrap">
			<div class="marquee-track">
				<?php
				$cm_ref_items = array();
				while ( $cm_refs->have_posts() ) : $cm_refs->the_post();
					ob_start();
					if ( has_post_thumbnail() ) {
						echo '<span class="ref-logo">' . get_the_post_thumbnail( get_the_ID(), 'cm-thumb' ) . '</span>';
					} else {
						echo '<span class="ref-logo"><span class="dot"></span>' . esc_html( get_the_title() ) . '</span>';
					}
					$cm_ref_items[] = ob_get_clean();
				endwhile;
				wp_reset_postdata();
				// Tek kopya basılır; ekrana göre yetmeyecek kadar kısa olursa JS gerektiği kadar çoğaltır (main.js).
				echo implode( '', $cm_ref_items );
				?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php
// "Kalite Belgelerimiz": kalite_belge_1..8 (bkz. inc/acf-fields.php group_cm_anasayfa) —
// hiç dolu slot yoksa bölüm TAMAMEN gizli kalır ("boşsa gizle"). Belirleyici alan
// "Belge"dir (dosya) — başlıksız ama dosyalı bir slot da gösterilir (başlık YOKSA
// sadece kart üzerindeki başlık satırı basılmaz); ESKİDEN ikisi de zorunluydu, admin
// dosyayı/önizlemeyi yükleyip başlığı boş bıraktığında kart tamamen gizli kalıyordu —
// beklenmedik ve kafa karıştırıcı bulunduğu için (bkz. proje notları) gevşetildi.
$cm_certs = array();
if ( function_exists( 'get_field' ) ) {
	foreach ( range( 1, 8 ) as $cm_n ) {
		$cm_cert = get_field( "kalite_belge_$cm_n" );
		if ( $cm_cert && ! empty( $cm_cert['dosya']['url'] ) ) $cm_certs[] = $cm_cert;
	}
}
if ( $cm_certs ) : ?>
<div class="wrap section-tight">
	<div class="eyebrow reveal"><?php echo esc_html( cm__( 'kalite_belgeleri_eyebrow' ) ); ?></div>
	<h2 class="h-lg reveal"><?php echo esc_html( cm__( 'kalite_belgeleri_baslik' ) ); ?></h2>
	<div class="cert-grid">
		<?php foreach ( $cm_certs as $cm_cert ) :
			// Önizleme önceliği: (1) elle yüklenmiş "Önizleme Görseli" — varsa, "Belge" PDF olsa
			// bile ONU gösterir (bkz. inc/acf-fields.php field_cm_kbN_onizleme); (2) "Belge"nin
			// kendisi zaten bir görselse doğrudan o; (3) "Belge" PDF ise ilk sayfa önizlemesi
			// (cm_pdf_preview_url()); (4) hiçbiri yoksa genel belge ikonu.
			$cm_cert_is_image = strpos( $cm_cert['dosya']['mime_type'] ?? '', 'image/' ) === 0;
			$cm_cert_preview  = ! empty( $cm_cert['onizleme_gorseli']['url'] )
				? $cm_cert['onizleme_gorseli']['url']
				: ( $cm_cert_is_image ? $cm_cert['dosya']['url'] : cm_pdf_preview_url( $cm_cert['dosya'] ) );
			// Başlık boşsa erişilebilirlik/alt-metin için dosyanın kendi başlığına düş.
			$cm_cert_label = $cm_cert['baslik'] ?: ( $cm_cert['dosya']['title'] ?? '' );
		?>
			<a class="cert-card reveal" href="<?php echo esc_url( $cm_cert['dosya']['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( sprintf( cm__( 'belge_yeni_sekme_aria' ), $cm_cert_label ) ); ?>">
				<div class="ph">
					<?php if ( $cm_cert_preview ) : ?>
						<img src="<?php echo esc_url( $cm_cert_preview ); ?>" alt="<?php echo esc_attr( $cm_cert_label ); ?>">
					<?php else : ?>
						<?php cm_document_icon( 40 ); ?>
					<?php endif; ?>
					<span class="corner c-tl"></span><span class="corner c-tr"></span><span class="corner c-bl"></span><span class="corner c-br"></span>
				</div>
				<?php if ( $cm_cert['baslik'] ) : ?><h3><?php echo esc_html( $cm_cert['baslik'] ); ?></h3><?php endif; ?>
			</a>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<?php
// "Haberler": haber CPT'sinden en yeni 3 kayıt — hiç haber yoksa bölüm tamamen gizli
// kalır ("boşsa gizle"). Listeleme/detay: page-haberler.php ("Haberler" Sayfası) +
// single-haber.php.
$cm_haberler_q = new WP_Query( array( 'post_type' => 'haber', 'posts_per_page' => 3 ) );
if ( $cm_haberler_q->have_posts() ) : ?>
<div class="wrap section-tight">
	<div class="eyebrow reveal"><?php echo esc_html( cm__( 'haberler_eyebrow' ) ); ?></div>
	<h2 class="h-lg reveal"><?php echo esc_html( cm__( 'haberlerimiz_baslik' ) ); ?></h2>
	<div class="prod-grid">
		<?php while ( $cm_haberler_q->have_posts() ) : $cm_haberler_q->the_post(); cm_news_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
	</div>
	<div style="margin-top:28px;">
		<a class="btn btn-outline" href="<?php echo esc_url( cm_translated_page_url( 'haberler', '/haberler/' ) ); ?>"><?php echo esc_html( cm__( 'tum_haberler' ) ); ?></a>
	</div>
</div>
<?php endif; ?>

<?php
// Bunlar ACF'in "Anasayfa Ayarları" grubundan (front_page konumlu) — cm_option() ile
// KARIŞTIRMAYIN, o Özelleştir/theme_mod alanları içindir; bunlar mevcut sayfanın (Anasayfa) ACF alanlarıdır.
$cm_kat_baslik   = ( function_exists( 'get_field' ) ? get_field( 'katalog_baslik' ) : '' ) ?: cm__( 'katalog_banner_baslik_default' );
$cm_kat_aciklama = ( function_exists( 'get_field' ) ? get_field( 'katalog_aciklama' ) : '' ) ?: cm__( 'katalog_banner_aciklama_default' );
$cm_kat_pdf      = function_exists( 'get_field' ) ? get_field( 'katalog_banner_pdf' ) : false;
?>
<div class="banner">
	<div class="wrap banner-inner reveal">
		<div><h3><?php echo esc_html( $cm_kat_baslik ); ?></h3><p><?php echo esc_html( $cm_kat_aciklama ); ?></p></div>
		<div class="btns">
			<?php if ( ! empty( $cm_kat_pdf['url'] ) ) : ?>
				<a class="btn btn-outline" href="<?php echo esc_url( $cm_kat_pdf['url'] ); ?>" target="_blank" rel="noopener" style="border-color:var(--paper-raised); color:var(--paper-raised);"><?php echo esc_html( cm__( 'goruntule' ) ); ?></a>
				<a class="btn btn-primary" href="<?php echo esc_url( $cm_kat_pdf['url'] ); ?>" download><?php echo esc_html( cm__( 'katalog_indir' ) ); ?></a>
			<?php else : ?>
				<a class="btn btn-primary" href="<?php echo esc_url( get_post_type_archive_link( 'katalog' ) ?: home_url( '/kataloglar/' ) ); ?>"><?php echo esc_html( cm__( 'kataloglara_git' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
