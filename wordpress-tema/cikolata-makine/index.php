<?php
/**
 * Genel yedek şablon — blog akışı kaldırıldı (bkz. proje notları); bu dosya artık
 * SADECE arama sonuçları için kullanılıyor (WordPress'in çekirdek kuralı gereği
 * bir index.php her zaman bulunmalı, bu yüzden dosya silinmedi, sadeleştirildi).
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="wrap section-tight">
	<?php cm_breadcrumb( array(
		array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ),
		array( 'label' => cm__( 'arama_sonuclari' ) ),
	) ); ?>

	<h1 class="h-lg" style="margin-top:16px;">
		<?php echo esc_html( cm__( 'arama_sonuclari_prefix' ) . get_search_query() ); ?>
	</h1>

	<?php if ( have_posts() ) : ?>
		<div class="prod-grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<a class="prod-card reveal" href="<?php the_permalink(); ?>">
					<?php cm_render_thumb( get_the_ID(), '', 'cm-card' ); ?>
					<h3><?php the_title(); ?></h3>
					<div class="go"><?php echo esc_html( cm__( 'detaylari_gor' ) ); ?></div>
				</a>
			<?php endwhile; ?>
		</div>
		<div class="section-tight" style="padding-bottom:0;"><?php the_posts_pagination(); ?></div>
	<?php else : ?>
		<p class="body-p" style="margin-top:32px;"><?php echo esc_html( cm__( 'arama_sonucu_bos' ) ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
