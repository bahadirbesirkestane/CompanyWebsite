<?php
/**
 * Bu dosya, slug'ı "haberler" olan sayfada WordPress tarafından otomatik kullanılır
 * (page-{slug}.php kuralı) — page-kataloglar.php ile AYNI desen. Haber kayıtlarının
 * kendisi (ekleme/düzenleme) wp-admin -> Haberler'den yönetilir; bu Sayfa sadece
 * listeleme ekranı (başlık + banner + opsiyonel açıklama metni wp-admin'den değiştirilebilir).
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
the_post();

$cm_paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$cm_haber_q = new WP_Query( array(
	'post_type'      => 'haber',
	'posts_per_page' => 9,
	'paged'          => $cm_paged,
) );

$cm_has_banner = cm_page_banner( get_field( 'sayfa_banner_gorseli' ), get_the_title() );
?>

<div class="wrap section-tight">
	<?php cm_breadcrumb( array(
		array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ),
		array( 'label' => get_the_title() ?: cm__( 'haberler_baslik' ) ),
	) ); ?>

	<?php if ( ! $cm_has_banner ) : ?><h1 class="h-lg" style="margin-top:16px;"><?php the_title(); ?></h1><?php endif; ?>
	<?php if ( trim( get_the_content() ) !== '' ) : ?>
		<div class="body-p" style="margin-top:10px;"><?php the_content(); ?></div>
	<?php else : ?>
		<p class="body-p" style="margin-top:10px;"><?php echo esc_html( cm__( 'haberler_aciklama' ) ); ?></p>
	<?php endif; ?>

	<?php if ( $cm_haber_q->have_posts() ) : ?>
		<div class="prod-grid" style="margin-top:32px;">
			<?php while ( $cm_haber_q->have_posts() ) : $cm_haber_q->the_post(); cm_news_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
		</div>
		<?php
		// the_posts_pagination() burada KULLANILAMAZ — aynı gerekçe page-urunler.php/
		// page-kataloglar.php'de açıklandığı gibi: global $wp_query hep bu Sayfanın kendi
		// tekil sorgusuna bakar, ikincil $cm_haber_q sorgumuzu görmez.
		$cm_links = paginate_links( array(
			'total'     => $cm_haber_q->max_num_pages,
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
		<p class="body-p" style="margin-top:32px;"><?php echo esc_html( cm__( 'haberler_bos' ) ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
