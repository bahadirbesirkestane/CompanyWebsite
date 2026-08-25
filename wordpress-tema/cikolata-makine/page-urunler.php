<?php
/**
 * Bu dosya, slug'ı "urunler" olan sayfada WordPress tarafından otomatik kullanılır
 * (page-{slug}.php kuralı). Sayfanın görünen BAŞLIĞI wp-admin'den istediğiniz gibi
 * değiştirilebilir (örn. "Ürünler", "Ürün Kataloğu" vb.) — sadece slug "urunler" kalmalı,
 * aksi halde bu şablon devreye girmez ve site genelindeki linkler kırılır.
 *
 * "Ürünler" menüsüne doğrudan tıklandığında gelinen sayfa: TÜM makineleri, kategori
 * sayfalarıyla aynı sol kenar çubuğu (cm_category_sidebar()) ve aynı sayfalamayla listeler.
 * Kategori/alt kategoriye özel listeleme için bkz. taxonomy-makine_kategori.php.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$cm_paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$cm_all_products = new WP_Query( array(
	'post_type'      => 'makine',
	'posts_per_page' => 12,
	'paged'          => $cm_paged,
	'orderby'        => 'title',
	'order'          => 'ASC',
) );
?>

<div class="wrap section-tight">
	<?php cm_breadcrumb( array(
		array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ),
		array( 'label' => get_the_title() ?: cm_urunler_label() ),
	) ); ?>

	<div class="category-layout">
		<div class="category-sidebar-col">
			<?php cm_category_sidebar(); ?>
		</div>
		<div class="category-main-col">
			<h1 class="h-lg"><?php the_title(); ?></h1>

			<?php
			$cm_has_content = false;
			if ( have_posts() ) : while ( have_posts() ) : the_post();
				if ( trim( get_the_content() ) !== '' ) : $cm_has_content = true; ?>
					<div class="body-p" style="margin-top:10px;"><?php the_content(); ?></div>
				<?php endif;
			endwhile; endif;
			if ( ! $cm_has_content ) : ?>
				<p class="body-p" style="margin-top:10px;"><?php echo esc_html( cm__( 'tum_urunler_aciklama' ) ); ?></p>
			<?php endif; ?>

			<?php if ( $cm_all_products->have_posts() ) : ?>
				<p class="breadcrumb" style="margin-top:20px;"><?php echo esc_html( sprintf( cm__( 'kategori_urun_sayisi' ), $cm_all_products->found_posts ) ); ?></p>
				<div class="prod-grid" style="margin-top:8px;">
					<?php while ( $cm_all_products->have_posts() ) : $cm_all_products->the_post(); cm_product_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
				</div>
				<?php
				/*
				 * the_posts_pagination() burada KULLANILAMAZ: o, "sayfalanacak" olup olmadığına
				 * hep GLOBAL $wp_query'ye (bu Sayfanın kendi tekil sorgusu, her zaman 1 sonuç/1
				 * sayfa) bakarak karar verir — bizim $cm_all_products ikincil sorgumuzu hiç
				 * görmez, 'total' argümanı geçilse bile sessizce boş döner. Bunun yerine
				 * paginate_links() doğrudan çağrılıyor; taxonomy-makine_kategori.php'deki
				 * sayfalamayla aynı .pagination/.page-numbers CSS sınıflarını üretir.
				 */
				$cm_links = paginate_links( array(
					'total'     => $cm_all_products->max_num_pages,
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
				<p class="body-p" style="margin-top:32px;"><?php echo esc_html( cm__( 'urunler_bos_kategori' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
