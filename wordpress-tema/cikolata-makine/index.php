<?php
/**
 * Genel yedek şablon — aynı zamanda Blog akışı (Ayarlar → Okuma → Yazılar Sayfası) için kullanılır.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="wrap section-tight">
	<?php cm_breadcrumb( array(
		array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ),
		array( 'label' => is_search() ? cm__( 'arama_sonuclari' ) : cm__( 'blog_etiket' ) ),
	) ); ?>

	<h1 class="h-lg" style="margin-top:16px;">
		<?php echo is_search() ? esc_html( cm__( 'arama_sonuclari_prefix' ) ) . esc_html( get_search_query() ) : esc_html( cm__( 'blog_etiket' ) ); ?>
	</h1>

	<?php if ( have_posts() ) : ?>
		<div class="prod-grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<a class="prod-card reveal" href="<?php the_permalink(); ?>">
					<?php cm_render_thumb( get_the_ID(), '', 'cm-card' ); ?>
					<h3><?php the_title(); ?></h3>
					<div class="spec"><?php echo esc_html( get_the_date() ); ?></div>
					<div class="go"><?php echo esc_html( cm__( 'devamini_oku' ) ); ?></div>
				</a>
			<?php endwhile; ?>
		</div>
		<div class="section-tight" style="padding-bottom:0;"><?php the_posts_pagination(); ?></div>
	<?php else : ?>
		<p class="body-p" style="margin-top:32px;"><?php echo esc_html( cm__( 'blog_bos' ) ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
