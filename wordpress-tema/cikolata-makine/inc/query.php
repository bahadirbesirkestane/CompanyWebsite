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

	if ( is_tax( 'makine_kategori' ) || is_tax( 'urun_ailesi' ) ) {
		// urun_ailesi zaten sadece "makine" post type'ına kayıtlı (bkz. inc/cpt-taxonomies.php),
		// ama post_type açıkça belirtilmezse WP_Query'nin varsayılanı "post"tur — taksonomi
		// arşivi sessizce 0 sonuç döner. Aynı düzeltme ikisi için de geçerli.
		$query->set( 'post_type', 'makine' );
		$query->set( 'posts_per_page', 12 );
		// Admin, ürün düzenleme ekranındaki "Sıra" (menu_order, page-attributes desteğiyle
		// geldi) alanından gösterim sırasını belirler; aynı sıradaki ürünler başlığa göre
		// sıralanır. page-urunler.php ("Tüm Ürünler") da AYNI mantığı kullanır — tutarlı olsun.
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'cm_pre_get_posts' );
