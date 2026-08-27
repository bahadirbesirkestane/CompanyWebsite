<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
the_post();

$cm_crumbs = array(
	array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ),
	array( 'label' => cm__( 'haberler_baslik' ), 'url' => cm_translated_page_url( 'haberler', '/haberler/' ) ),
	array( 'label' => get_the_title() ),
);
// Ayrı bir "banner görseli" alanı YOK — haberin kendi Öne Çıkan Görseli (bu, ayrıca
// haber kartında da kullanılır, bkz. cm_news_card()) banner olarak yeniden kullanılır.
$cm_has_banner = cm_page_banner( get_the_post_thumbnail_url( get_the_ID(), 'large' ), get_the_title(), $cm_crumbs );
?>

<div class="wrap page-content">
	<?php if ( ! $cm_has_banner ) : ?>
		<?php cm_breadcrumb( $cm_crumbs ); ?>
		<h1 class="h-lg" style="margin-top:16px;"><?php the_title(); ?></h1>
	<?php endif; ?>
	<div class="news-date" style="margin-top:12px;"><?php echo esc_html( get_the_date() ); ?></div>

	<div class="body-p" style="max-width:none; margin-top:20px;"><?php the_content(); ?></div>
</div>

<?php get_footer(); ?>
