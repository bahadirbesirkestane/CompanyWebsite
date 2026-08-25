<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="wrap section" style="text-align:center;">
	<div class="eyebrow" style="justify-content:center;">404</div>
	<h1 class="h-lg"><?php echo esc_html( cm__( 'baslik_404' ) ); ?></h1>
	<p class="body-p" style="margin:16px auto 0;"><?php echo esc_html( cm__( 'aciklama_404' ) ); ?></p>
	<div class="hero-cta" style="justify-content:center; margin-top:28px;">
		<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( cm__( 'anasayfaya_don' ) ); ?></a>
		<a class="btn btn-outline" href="<?php echo esc_url( cm_translated_page_url( 'urunler', '/urunler/' ) ); ?>"><?php echo esc_html( cm_urunler_label() ); ?></a>
	</div>
</div>

<?php get_footer(); ?>
