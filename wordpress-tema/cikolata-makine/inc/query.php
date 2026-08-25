<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Ana sorguya sayfalama ve doğru post_type kısıtlamasını uygular.
 *
 * makine_kategori taksonomisi hem "makine" hem "katalog" post type'larına kayıtlıdır
 * (bkz. inc/cpt-taxonomies.php) — post_type belirtilmeden taksonomi sorgusu ikisini de
 * çeker. Bugün hiçbir katalog kaydına kategori atanmadığı için gizli kalan bir hata:
 * biri atanırsa cm_product_card() o post type'da olmayan ACF alanlarını okumaya
 * çalışıp bozuk kart üretir. Burada post_type açıkça "makine"ye sabitlenir.
 */
function cm_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) return;

	if ( is_tax( 'makine_kategori' ) ) {
		$query->set( 'post_type', 'makine' );
		$query->set( 'posts_per_page', 12 );
	} elseif ( is_post_type_archive( 'katalog' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'cm_pre_get_posts' );
