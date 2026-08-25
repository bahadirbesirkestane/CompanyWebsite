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
<div class="wrap" style="margin-top:56px;">
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
	<div class="eyebrow"><?php echo esc_html( cm_urunler_label() ); ?></div>
	<h2 class="h-lg"><?php echo esc_html( cm__( 'urunler_kesfet_baslik' ) ); ?></h2>
	<div class="cat-grid">
		<?php foreach ( $cm_top_cats as $term ) cm_category_card( $term ); ?>
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
	<div class="eyebrow"><?php echo esc_html( cm__( 'vitrin_eyebrow' ) ); ?></div>
	<h2 class="h-lg"><?php echo esc_html( cm__( 'one_cikan_makineler' ) ); ?></h2>
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
	<div class="wrap"><div class="eyebrow"><?php echo esc_html( cm__( 'referanslarimiz' ) ); ?></div><h2 class="h-md"><?php echo esc_html( cm__( 'referans_baslik' ) ); ?></h2></div>
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
<?php endif; ?>

<?php
// Bunlar ACF'in "Anasayfa Ayarları" grubundan (front_page konumlu) — cm_option() ile
// KARIŞTIRMAYIN, o Özelleştir/theme_mod alanları içindir; bunlar mevcut sayfanın (Anasayfa) ACF alanlarıdır.
$cm_kat_baslik   = ( function_exists( 'get_field' ) ? get_field( 'katalog_baslik' ) : '' ) ?: cm__( 'katalog_banner_baslik_default' );
$cm_kat_aciklama = ( function_exists( 'get_field' ) ? get_field( 'katalog_aciklama' ) : '' ) ?: cm__( 'katalog_banner_aciklama_default' );
$cm_kat_pdf      = function_exists( 'get_field' ) ? get_field( 'katalog_banner_pdf' ) : false;
?>
<div class="banner">
	<div class="wrap banner-inner">
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
