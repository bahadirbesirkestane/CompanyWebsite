<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

/**
 * "Ürün Ailesi" (urun_ailesi) arşiv şablonu — taxonomy-makine_kategori.php ile aynı
 * mevcut CSS sınıflarını (category-layout, cat-sidebar, prod-grid) yeniden kullanır,
 * ek CSS gerektirmez. Taksonomi hiyerarşik OLMADIĞI için (bkz. inc/cpt-taxonomies.php)
 * sidebar burada alt kategori seviyesi olmadan, düz bir aile listesi olarak basılır.
 */
$cm_term      = get_queried_object();
$cm_families  = get_terms( array( 'taxonomy' => 'urun_ailesi', 'hide_empty' => false ) );
?>

<div class="wrap section-tight">
	<?php cm_breadcrumb( array(
		array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ),
		array( 'label' => cm_urunler_label(), 'url' => cm_translated_page_url( 'urunler', '/urunler/' ) ),
		array( 'label' => $cm_term->name ),
	) ); ?>

	<div class="category-layout">
		<div class="category-sidebar-col">
			<div class="cat-sidebar">
				<a class="cat-sidebar-all" href="<?php echo esc_url( cm_translated_page_url( 'urunler', '/urunler/' ) ); ?>"><?php echo esc_html( cm__( 'sidebar_tum_urunler' ) ); ?></a>
				<?php if ( ! is_wp_error( $cm_families ) && $cm_families ) : ?>
					<ul>
						<?php foreach ( $cm_families as $cm_fam ) : ?>
							<li>
								<a class="cat-sidebar-icon-row<?php echo $cm_fam->term_id === $cm_term->term_id ? ' active' : ''; ?>" href="<?php echo esc_url( get_term_link( $cm_fam ) ); ?>">
									<span class="cat-sidebar-name"><?php echo esc_html( $cm_fam->name ); ?></span>
									<span class="cat-sidebar-count"><?php echo (int) $cm_fam->count; ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
		<div class="category-main-col">
			<h1 class="h-lg"><?php echo esc_html( $cm_term->name ); ?></h1>
			<?php if ( $cm_term->description ) : ?><p class="body-p" style="margin-top:10px;"><?php echo esc_html( $cm_term->description ); ?></p><?php endif; ?>

			<?php if ( have_posts() ) : ?>
				<p class="breadcrumb" style="margin-top:20px;"><?php echo esc_html( sprintf( cm__( 'kategori_urun_sayisi' ), $wp_query->found_posts ) ); ?></p>
				<div class="prod-grid" style="margin-top:8px;">
					<?php while ( have_posts() ) : the_post(); cm_product_card( get_the_ID() ); endwhile; ?>
				</div>
				<?php the_posts_pagination( array(
					'prev_text' => cm__( 'sayfalama_onceki' ),
					'next_text' => cm__( 'sayfalama_sonraki' ),
				) ); ?>
			<?php else : ?>
				<p class="body-p" style="margin-top:32px;"><?php echo esc_html( cm__( 'kategori_bos_makine' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
