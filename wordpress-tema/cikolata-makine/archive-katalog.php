<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="wrap section-tight">
	<?php cm_breadcrumb( array(
		array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ),
		array( 'label' => cm__( 'kataloglar_baslik' ) ),
	) ); ?>

	<h1 class="h-lg" style="margin-top:16px;"><?php echo esc_html( cm__( 'kataloglar_baslik' ) ); ?></h1>
	<p class="body-p" style="margin-top:10px;"><?php echo esc_html( cm__( 'kataloglar_aciklama' ) ); ?></p>

	<?php if ( have_posts() ) : ?>
		<div class="catalog-grid">
			<?php while ( have_posts() ) : the_post();
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
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( array(
			'prev_text' => cm__( 'sayfalama_onceki' ),
			'next_text' => cm__( 'sayfalama_sonraki' ),
		) ); ?>
	<?php else : ?>
		<p class="body-p" style="margin-top:32px;"><?php echo esc_html( cm__( 'kataloglar_bos' ) ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
