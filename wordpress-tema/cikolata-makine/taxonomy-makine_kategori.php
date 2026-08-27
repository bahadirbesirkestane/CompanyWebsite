<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$cm_term = get_queried_object();
$cm_is_sub = (int) $cm_term->parent !== 0;
$cm_parent = $cm_is_sub ? get_term( $cm_term->parent, 'makine_kategori' ) : null;
?>

<div class="wrap section-tight">
	<?php
	$cm_crumbs = array( array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ), array( 'label' => cm_urunler_label(), 'url' => cm_translated_page_url( 'urunler', '/urunler/' ) ) );
	if ( $cm_is_sub ) $cm_crumbs[] = array( 'label' => $cm_parent->name, 'url' => get_term_link( $cm_parent ) );
	$cm_crumbs[] = array( 'label' => $cm_term->name );
	cm_breadcrumb( $cm_crumbs );
	?>

	<div class="category-layout">
		<div class="category-sidebar-col">
			<?php cm_category_sidebar( $cm_term ); ?>
		</div>
		<div class="category-main-col">
			<h1 class="h-lg"><?php echo esc_html( $cm_term->name ); ?></h1>
			<?php if ( $cm_term->description ) : ?><p class="body-p" style="margin-top:10px;"><?php echo esc_html( $cm_term->description ); ?></p><?php endif; ?>

			<?php if ( have_posts() ) : ?>
				<div class="grid-toolbar">
					<p class="breadcrumb"><?php echo esc_html( sprintf( cm__( 'kategori_urun_sayisi' ), $wp_query->found_posts ) ); ?></p>
					<?php cm_grid_toggle(); ?>
				</div>
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
