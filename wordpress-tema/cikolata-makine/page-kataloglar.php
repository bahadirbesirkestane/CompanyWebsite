<?php
/**
 * Bu dosya, slug'ı "kataloglar" olan sayfada WordPress tarafından otomatik kullanılır
 * (page-{slug}.php kuralı) — page-urunler.php ile AYNI desen. "katalog" CPT'sinin artık
 * kendi arşivi YOK (has_archive=false, bkz. inc/cpt-taxonomies.php) — bilerek, bu Sayfayla
 * slug çakışması olmasın diye. Katalog kayıtlarının kendisi (yükleme/düzenleme) DEĞİŞMEDİ,
 * hâlâ tamamen aynı şekilde wp-admin -> Kataloglar'dan yönetiliyor; değişen tek şey bu
 * listeleme sayfasının artık gerçek, düzenlenebilir bir Sayfa olması (başlık + banner +
 * opsiyonel açıklama metni wp-admin'den değiştirilebilir).
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
the_post();

$cm_paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$cm_katalog_q = new WP_Query( array(
	'post_type'      => 'katalog',
	'posts_per_page' => 12,
	'paged'          => $cm_paged,
) );

$cm_crumbs = array(
	array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ),
	array( 'label' => get_the_title() ?: cm__( 'kataloglar_baslik' ) ),
);
$cm_has_banner = cm_page_banner( get_field( 'sayfa_banner_gorseli' ), get_the_title(), $cm_crumbs );
?>

<div class="wrap section-tight">
	<?php if ( ! $cm_has_banner ) : ?>
		<?php cm_breadcrumb( $cm_crumbs ); ?>
		<h1 class="h-lg" style="margin-top:16px;"><?php the_title(); ?></h1>
	<?php endif; ?>
	<?php if ( trim( get_the_content() ) !== '' ) : ?>
		<div class="body-p" style="margin-top:10px;"><?php the_content(); ?></div>
	<?php else : ?>
		<p class="body-p" style="margin-top:10px;"><?php echo esc_html( cm__( 'kataloglar_aciklama' ) ); ?></p>
	<?php endif; ?>

	<?php if ( $cm_katalog_q->have_posts() ) : ?>
		<div class="catalog-grid">
			<?php while ( $cm_katalog_q->have_posts() ) : $cm_katalog_q->the_post();
				$cm_pdf = get_field( 'pdf_dosya' );
				$cm_dil = get_field( 'dil' );
			?>
				<div class="catalog-card">
					<?php cm_render_thumb( get_the_ID(), '', 'cm-card' ); ?>
					<div class="body">
						<h3><?php the_title(); ?></h3>
						<div class="meta">
							PDF<?php if ( $cm_dil ) echo ' · ' . esc_html( $cm_dil ); ?>
							<?php if ( ! empty( $cm_pdf['filesize'] ) ) echo ' · ' . esc_html( size_format( $cm_pdf['filesize'] ) ); ?>
						</div>
						<?php if ( $cm_pdf ) : ?>
							<div class="actions">
								<a class="btn btn-outline" href="<?php echo esc_url( $cm_pdf['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( cm__( 'goruntule' ) ); ?></a>
								<a class="btn btn-primary" href="<?php echo esc_url( $cm_pdf['url'] ); ?>" download><?php echo esc_html( cm__( 'indir' ) ); ?></a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php
		// the_posts_pagination() burada KULLANILAMAZ — aynı gerekçe page-urunler.php'de
		// açıklandığı gibi: global $wp_query hep bu Sayfanın kendi tekil sorgusuna bakar,
		// ikincil $cm_katalog_q sorgumuzu görmez. paginate_links() doğrudan çağrılıyor.
		$cm_links = paginate_links( array(
			'total'     => $cm_katalog_q->max_num_pages,
			'current'   => $cm_paged,
			'prev_text' => cm__( 'sayfalama_onceki' ),
			'next_text' => cm__( 'sayfalama_sonraki' ),
		) );
		if ( $cm_links ) : ?>
			<nav class="pagination navigation" aria-label="<?php echo esc_attr( cm__( 'sayfalama_aria' ) ); ?>">
				<div class="nav-links"><?php echo $cm_links; ?></div>
			</nav>
		<?php endif; ?>
	<?php else : ?>
		<p class="body-p" style="margin-top:32px;"><?php echo esc_html( cm__( 'kataloglar_bos' ) ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
