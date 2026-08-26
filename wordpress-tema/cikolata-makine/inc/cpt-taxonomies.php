<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function cm_register_taxonomies() {
	register_taxonomy( 'makine_kategori', array( 'makine', 'katalog' ), array(
		'labels' => array(
			'name'              => 'Makine Kategorileri',
			'singular_name'     => 'Makine Kategorisi',
			'search_items'      => 'Kategori Ara',
			'all_items'         => 'Tüm Kategoriler',
			'parent_item'       => 'Üst Kategori',
			'parent_item_colon' => 'Üst Kategori:',
			'edit_item'         => 'Kategoriyi Düzenle',
			'update_item'       => 'Kategoriyi Güncelle',
			'add_new_item'      => 'Yeni Kategori Ekle',
			'new_item_name'     => 'Yeni Kategori Adı',
			'menu_name'         => 'Kategoriler',
		),
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'urunler/kategori', 'with_front' => false, 'hierarchical' => true ),
	) );

	// "Ne üretmek istiyorsunuz?" ekseni — makine tipine göre DEĞİL, nihai ürüne göre
	// (Bar, Praline, Drajee, Damla/Pul…) gezinme sağlayan, hiyerarşik olmayan ikinci
	// bir taksonomi. Bkz. 03_TASARIM_YENILEME_ONERISI.md Bölüm 5.3. Terimler ve mevcut
	// ürünlerin etiketlenmesi wp-admin → Ürünler → Ürün Aileleri üzerinden yapılır;
	// hiç terim/etiketli ürün yokken anasayfadaki ilgili blok "boşsa gizle" ilkesiyle
	// otomatik gizlenir (bkz. front-page.php).
	register_taxonomy( 'urun_ailesi', array( 'makine' ), array(
		'labels' => array(
			'name'          => 'Ürün Aileleri',
			'singular_name' => 'Ürün Ailesi',
			'search_items'  => 'Ürün Ailesi Ara',
			'all_items'     => 'Tüm Ürün Aileleri',
			'edit_item'     => 'Ürün Ailesini Düzenle',
			'update_item'   => 'Ürün Ailesini Güncelle',
			'add_new_item'  => 'Yeni Ürün Ailesi Ekle',
			'new_item_name' => 'Yeni Ürün Ailesi Adı',
			'menu_name'     => 'Ürün Aileleri',
		),
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'urunler/urun-ailesi', 'with_front' => false ),
	) );
}
add_action( 'init', 'cm_register_taxonomies' );

function cm_register_post_types() {
	register_post_type( 'makine', array(
		'labels' => array(
			'name'               => 'Ürünler',
			'singular_name'      => 'Makine',
			'add_new'            => 'Yeni Makine Ekle',
			'add_new_item'       => 'Yeni Makine Ekle',
			'edit_item'          => 'Makineyi Düzenle',
			'new_item'           => 'Yeni Makine',
			'view_item'          => 'Makineyi Görüntüle',
			'search_items'       => 'Makine Ara',
			'not_found'          => 'Makine bulunamadı',
			'not_found_in_trash' => 'Çöp kutusunda makine yok',
			'menu_name'          => 'Ürünler',
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'makine', 'with_front' => false ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'menu_icon'    => 'dashicons-admin-tools',
		'menu_position'=> 5,
		'show_in_rest' => true,
		'taxonomies'   => array( 'makine_kategori', 'urun_ailesi' ),
	) );

	register_post_type( 'katalog', array(
		'labels' => array(
			'name'               => 'Kataloglar',
			'singular_name'      => 'Katalog',
			'add_new'            => 'Yeni Katalog Ekle',
			'add_new_item'       => 'Yeni Katalog Ekle',
			'edit_item'          => 'Kataloğu Düzenle',
			'new_item'           => 'Yeni Katalog',
			'view_item'          => 'Kataloğu Görüntüle',
			'search_items'       => 'Katalog Ara',
			'not_found'          => 'Katalog bulunamadı',
			'menu_name'          => 'Kataloglar',
		),
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'kataloglar', 'with_front' => false ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'menu_icon'    => 'dashicons-media-document',
		'menu_position'=> 6,
		'show_in_rest' => true,
		'taxonomies'   => array( 'makine_kategori' ),
	) );

	register_post_type( 'referans', array(
		'labels' => array(
			'name'          => 'Referans Firmalar',
			'singular_name' => 'Referans Firma',
			'add_new'       => 'Yeni Referans Ekle',
			'add_new_item'  => 'Yeni Referans Ekle',
			'edit_item'     => 'Referansı Düzenle',
			'menu_name'     => 'Referanslar',
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'has_archive'   => false,
		'supports'      => array( 'title', 'thumbnail' ),
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 7,
	) );
}
add_action( 'init', 'cm_register_post_types' );

/**
 * wp-admin liste ekranında logo/görsel sütunu gösterir (referans + makine + katalog).
 */
function cm_admin_thumbnail_column( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( $key === 'title' ) $new['cm_thumb'] = 'Görsel';
		$new[ $key ] = $label;
	}
	return $new;
}
foreach ( array( 'makine', 'katalog', 'referans' ) as $cm_pt ) {
	add_filter( "manage_{$cm_pt}_posts_columns", 'cm_admin_thumbnail_column' );
}
function cm_admin_thumbnail_column_content( $column, $post_id ) {
	if ( $column === 'cm_thumb' ) {
		echo get_the_post_thumbnail( $post_id, array( 48, 48 ), array( 'style' => 'object-fit:cover;border-radius:3px;' ) );
	}
}
foreach ( array( 'makine', 'katalog', 'referans' ) as $cm_pt ) {
	add_action( "manage_{$cm_pt}_posts_custom_column", 'cm_admin_thumbnail_column_content', 10, 2 );
}
