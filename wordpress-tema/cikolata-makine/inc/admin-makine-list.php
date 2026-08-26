<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * wp-admin → Ürünler listesi, Polylang'in 4 dile ayrı post olarak yaydığı 228 kaydı
 * (57 ürün × 4 dil) hiç ayrım yapmadan karışık gösteriyordu. Burada iki şey eklenir:
 * 1) Kategoriye göre filtre dropdown'ı.
 * 2) Sayfa dil parametresi olmadan açıldığında varsayılan dile (TR) yönlendirme —
 *    Polylang'in kendi dil filtresi/sütunu zaten var, sadece "ilk açılışta hangi dil"
 *    varsayılanını biz sağlıyoruz; admin bir dil seçtiği an bu devreye girmez.
 */

function cm_admin_makine_category_filter( $post_type, $which ) {
	if ( $post_type !== 'makine' ) return;
	wp_dropdown_categories( array(
		'show_option_all' => 'Tüm Kategoriler',
		'taxonomy'        => 'makine_kategori',
		'name'            => 'makine_kategori',
		'selected'        => isset( $_GET['makine_kategori'] ) ? sanitize_text_field( wp_unslash( $_GET['makine_kategori'] ) ) : '',
		'hierarchical'    => true,
		'hide_empty'      => false,
		'value_field'     => 'slug',
	) );
}
add_action( 'restrict_manage_posts', 'cm_admin_makine_category_filter', 10, 2 );

function cm_admin_makine_category_filter_query( $query ) {
	global $pagenow, $typenow;
	if ( ! is_admin() || $pagenow !== 'edit.php' || $typenow !== 'makine' || ! $query->is_main_query() ) return;
	if ( empty( $_GET['makine_kategori'] ) ) return;
	$query->set( 'tax_query', array( array(
		'taxonomy' => 'makine_kategori',
		'field'    => 'slug',
		'terms'    => sanitize_text_field( wp_unslash( $_GET['makine_kategori'] ) ),
	) ) );
}
add_action( 'parse_query', 'cm_admin_makine_category_filter_query' );

/**
 * Sayfa ilk kez (lang parametresi olmadan) açıldığında varsayılan dile yönlendirir.
 * isset() kontrolü kasıtlı (empty() değil): Polylang'in kendi dropdown'ı "Tüm Diller"
 * seçilse bile lang=... parametresini URL'e yazıyor, o durumda tekrar yönlendirme
 * yapılmamalı — sadece parametre HİÇ yoksa (ilk giriş) devreye girer.
 */
function cm_admin_makine_default_language_redirect() {
	global $pagenow, $typenow;
	if ( $pagenow !== 'edit.php' || $typenow !== 'makine' ) return;
	if ( isset( $_GET['lang'] ) ) return;
	if ( wp_doing_ajax() || ! function_exists( 'pll_default_language' ) ) return;

	wp_safe_redirect( add_query_arg( 'lang', pll_default_language() ) );
	exit;
}
add_action( 'load-edit.php', 'cm_admin_makine_default_language_redirect' );

/**
 * Hangi dilde bakılırsa bakılsın, bir ürünün 4 dildeki çeviri durumunu tek satırda gösterir
 * (örn. "TR ✓  EN ✓  RU —  ES ✓") — eksik çevirileri fark etmek için.
 */
function cm_admin_makine_translation_columns( $columns ) {
	$columns['cm_ceviri'] = 'Çeviriler';
	return $columns;
}
add_filter( 'manage_makine_posts_columns', 'cm_admin_makine_translation_columns' );

function cm_admin_makine_translation_column_content( $column, $post_id ) {
	if ( $column !== 'cm_ceviri' || ! function_exists( 'pll_languages_list' ) ) return;
	$parts = array();
	foreach ( pll_languages_list() as $lang ) {
		$exists  = (bool) pll_get_post( $post_id, $lang );
		$parts[] = strtoupper( $lang ) . ' ' . ( $exists ? '✓' : '—' );
	}
	echo esc_html( implode( '  ', $parts ) );
}
add_action( 'manage_makine_posts_custom_column', 'cm_admin_makine_translation_column_content', 10, 2 );
