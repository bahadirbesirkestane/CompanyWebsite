<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
the_post();

$cm_id     = get_the_ID();
$cm_terms  = get_the_terms( $cm_id, 'makine_kategori' );
$cm_term   = ( $cm_terms && ! is_wp_error( $cm_terms ) ) ? $cm_terms[0] : null;
$cm_parent = ( $cm_term && $cm_term->parent ) ? get_term( $cm_term->parent, 'makine_kategori' ) : null;

// Galeri: vitrin (öne çıkan görsel) + Ek Görsel 1-8, tek birleşik ve sıralı bir liste.
// Her öğe: array('large' => ..., 'thumb' => ..., 'alt' => ...)
$cm_gallery = array();
if ( has_post_thumbnail( $cm_id ) ) {
	$cm_gallery[] = array(
		'large' => get_the_post_thumbnail_url( $cm_id, 'large' ),
		'thumb' => get_the_post_thumbnail_url( $cm_id, 'cm-thumb' ),
		'alt'   => get_the_title(),
	);
}
for ( $cm_n = 1; $cm_n <= 8; $cm_n++ ) {
	$cm_img = get_field( "ek_gorsel_$cm_n" );
	if ( ! $cm_img ) continue;
	$cm_gallery[] = array(
		'large' => $cm_img['sizes']['large'] ?? $cm_img['url'],
		'thumb' => $cm_img['sizes']['cm-thumb'] ?? ( $cm_img['sizes']['thumbnail'] ?? $cm_img['url'] ),
		'alt'   => $cm_img['alt'] ?: get_the_title(),
	);
}

$cm_kisa_aciklama = get_field( 'kisa_aciklama' );
if ( ! $cm_kisa_aciklama && has_excerpt() ) $cm_kisa_aciklama = get_the_excerpt();

$cm_specs       = cm_parse_specs( get_field( 'teknik_ozellikler_metin' ) );
$cm_pdf         = get_field( 'pdf_katalog' );
$cm_video_embed = cm_youtube_embed_url( get_field( 'video_url' ) );

$cm_cta_metin = get_field( 'cta_metin' );
if ( $cm_cta_metin ) {
	$cm_cta_link  = get_field( 'cta_link' );
	$cm_cta_blank = false;
	if ( ! $cm_cta_link ) {
		$cm_cta_link = cm_whatsapp_url();
		$cm_cta_blank = (bool) $cm_cta_link;
	}
	if ( ! $cm_cta_link ) $cm_cta_link = cm_translated_page_url( 'iletisim', '/iletisim/' ) . '?makine=' . get_post_field( 'post_name', $cm_id );
}

$cm_tabs = array();
if ( get_the_content() ) $cm_tabs['aciklama'] = cm__( 'tab_aciklama' );
if ( $cm_specs ) $cm_tabs['ozellikler'] = cm__( 'tab_ozellikler' );
if ( $cm_video_embed ) $cm_tabs['video'] = cm__( 'tab_video' );
if ( $cm_pdf ) $cm_tabs['dokuman'] = cm__( 'tab_dokuman' );
$cm_default_tab = isset( $cm_tabs['aciklama'] ) ? 'aciklama' : ( $cm_tabs ? array_key_first( $cm_tabs ) : '' );
?>

<div class="wrap" style="padding-top:24px;">
	<?php
	$cm_crumbs = array( array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ), array( 'label' => cm_urunler_label(), 'url' => cm_translated_page_url( 'urunler', '/urunler/' ) ) );
	if ( $cm_parent ) $cm_crumbs[] = array( 'label' => $cm_parent->name, 'url' => get_term_link( $cm_parent ) );
	if ( $cm_term ) $cm_crumbs[] = array( 'label' => $cm_term->name, 'url' => get_term_link( $cm_term ) );
	$cm_crumbs[] = array( 'label' => get_the_title() );
	cm_breadcrumb( $cm_crumbs );
	?>

	<div class="detail-top">
		<div class="gallery">
			<div class="ph main" id="cmGalleryMain">
				<?php if ( $cm_gallery ) : ?>
					<img src="<?php echo esc_url( $cm_gallery[0]['large'] ); ?>" alt="<?php echo esc_attr( $cm_gallery[0]['alt'] ); ?>">
				<?php else : ?>
					<?php cm_generic_icon( 64 ); ?>
				<?php endif; ?>
				<span class="corner c-tl"></span><span class="corner c-tr"></span><span class="corner c-bl"></span><span class="corner c-br"></span>
				<?php if ( count( $cm_gallery ) > 1 ) : ?>
					<button type="button" class="gallery-arrow prev" aria-label="<?php echo esc_attr( cm__( 'gorsel_onceki' ) ); ?>">‹</button>
					<button type="button" class="gallery-arrow next" aria-label="<?php echo esc_attr( cm__( 'gorsel_sonraki' ) ); ?>">›</button>
				<?php endif; ?>
			</div>
			<?php if ( count( $cm_gallery ) > 1 ) : ?>
				<div class="thumbs">
					<?php foreach ( $cm_gallery as $i => $img ) : ?>
						<button class="ph<?php echo $i === 0 ? ' sel' : ''; ?>" type="button" data-cm-thumb="<?php echo esc_url( $img['large'] ); ?>" aria-label="<?php echo esc_attr( cm__( 'gorsel_etiket' ) ); ?> <?php echo (int) $i + 1; ?>">
							<img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="">
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div>
			<?php if ( $cm_term ) : ?><div class="eyebrow"><a href="<?php echo esc_url( get_term_link( $cm_term ) ); ?>" style="color:inherit;text-decoration:none;"><?php echo esc_html( $cm_term->name ); ?></a></div><?php endif; ?>
			<h1 class="h-lg"><?php the_title(); ?></h1>
			<?php if ( $cm_kisa_aciklama ) : ?><p class="body-p" style="margin-top:12px;"><?php echo esc_html( $cm_kisa_aciklama ); ?></p><?php endif; ?>

			<?php if ( $cm_specs ) : ?>
				<div class="chip-row">
					<?php foreach ( array_slice( $cm_specs, 0, 3 ) as $spec ) : ?>
						<div class="chip"><div class="l"><?php echo esc_html( $spec['ozellik_adi'] ); ?></div><div class="v"><?php echo esc_html( $spec['ozellik_degeri'] ); ?></div></div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $cm_cta_metin ) : ?>
				<a href="<?php echo esc_url( $cm_cta_link ); ?>"<?php if ( $cm_cta_blank ) echo ' target="_blank" rel="noopener"'; ?> class="btn btn-primary" style="width:100%; justify-content:center;"><?php echo esc_html( $cm_cta_metin ); ?></a>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $cm_tabs ) : ?>
	<div class="tab-strip" role="tablist" aria-label="<?php echo esc_attr( cm__( 'urun_sekmeleri_aria' ) ); ?>">
		<?php foreach ( $cm_tabs as $cm_key => $cm_label ) : ?>
			<button type="button" class="<?php echo $cm_key === $cm_default_tab ? 'active' : ''; ?>" data-cm-tab="<?php echo esc_attr( $cm_key ); ?>" role="tab" aria-selected="<?php echo $cm_key === $cm_default_tab ? 'true' : 'false'; ?>"><?php echo esc_html( $cm_label ); ?></button>
		<?php endforeach; ?>
	</div>

	<?php if ( isset( $cm_tabs['aciklama'] ) ) : ?>
		<div class="tab-panel<?php echo $cm_default_tab === 'aciklama' ? ' active' : ''; ?>" data-cm-panel="aciklama">
			<div class="body-p"><?php the_content(); ?></div>
		</div>
	<?php endif; ?>

	<?php if ( isset( $cm_tabs['ozellikler'] ) ) : ?>
		<div class="tab-panel<?php echo $cm_default_tab === 'ozellikler' ? ' active' : ''; ?>" data-cm-panel="ozellikler">
			<div class="spec-wrap">
				<table class="spec">
					<?php foreach ( $cm_specs as $spec ) : ?>
						<tr><td><?php echo esc_html( $spec['ozellik_adi'] ); ?></td><td><?php echo esc_html( $spec['ozellik_degeri'] ); ?></td></tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( isset( $cm_tabs['video'] ) ) : ?>
		<div class="tab-panel<?php echo $cm_default_tab === 'video' ? ' active' : ''; ?>" data-cm-panel="video">
			<div class="video-embed"><iframe src="<?php echo esc_url( $cm_video_embed ); ?>" title="<?php the_title_attribute(); ?> <?php echo esc_attr( cm__( 'video_baslik_suffix' ) ); ?>" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
		</div>
	<?php endif; ?>

	<?php if ( isset( $cm_tabs['dokuman'] ) ) : ?>
		<div class="tab-panel<?php echo $cm_default_tab === 'dokuman' ? ' active' : ''; ?>" data-cm-panel="dokuman">
			<?php cm_pdf_row( $cm_pdf, get_the_title() . ' ' . cm__( 'teknik_katalog_suffix' ) ); ?>
		</div>
	<?php endif; ?>
	<?php endif; ?>

	<?php
	if ( $cm_term ) {
		$cm_related = new WP_Query( array(
			'post_type'      => 'makine',
			'posts_per_page' => 3,
			'post__not_in'   => array( $cm_id ),
			'tax_query'      => array( array( 'taxonomy' => 'makine_kategori', 'field' => 'term_id', 'terms' => $cm_term->term_id ) ),
		) );
		if ( $cm_related->have_posts() ) :
	?>
	<div class="section-tight">
		<div class="eyebrow"><?php echo esc_html( cm__( 'benzer_urunler' ) ); ?></div>
		<h2 class="h-md"><?php echo esc_html( cm__( 'ayni_kategoriden' ) ); ?></h2>
		<div class="prod-grid">
			<?php while ( $cm_related->have_posts() ) : $cm_related->the_post(); cm_product_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
	<?php
		endif;
	}
	?>
</div>

<?php get_footer(); ?>
