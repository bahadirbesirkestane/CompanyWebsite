<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * "Ürünler → Sıralama" admin sayfası: sürükle-bırakla ürün gösterim sırası (menu_order).
 *
 * Neden ayrı bir sayfa (tek tek "Sıra" numarası girmek yerine): her ürünün 4 dilde
 * (TR/EN/RU/ES) ayrı post'u var — bu sayfa SADECE varsayılan dildeki (TR) 57 ürünü
 * gösterir, admin bir kez sürükler, kaydetme sırasında EN/RU/ES kardeşlerine de aynı
 * menu_order otomatik yazılır (bkz. cm_ajax_save_product_order()).
 *
 * Tek/birleşik sıra modeli: burada kaydedilen menu_order, hem kategori sayfalarında
 * hem "Tüm Ürünler" sayfasında kullanılan AYNI alan (bkz. inc/query.php,
 * page-urunler.php `orderby => 'menu_order title'`) — ayrı bir "kategori sırası"
 * alanı YOK, kasıtlı olarak (kullanıcıyla netleştirildi).
 */

function cm_add_product_order_page() {
	$hook = add_submenu_page(
		'edit.php?post_type=makine',
		'Ürün Sıralaması',
		'Sıralama',
		'edit_posts',
		'cm-urun-siralama',
		'cm_render_product_order_page'
	);
	add_action( "admin_enqueue_scripts", function ( $current_hook ) use ( $hook ) {
		if ( $current_hook !== $hook ) return;
		cm_enqueue_product_order_assets();
	} );
}
add_action( 'admin_menu', 'cm_add_product_order_page' );

function cm_enqueue_product_order_assets() {
	wp_enqueue_script( 'jquery-ui-sortable' );
	$js = CM_THEME_DIR . '/assets/js/admin-product-order.js';
	wp_enqueue_script(
		'cm-admin-product-order',
		CM_THEME_URI . '/assets/js/admin-product-order.js',
		array( 'jquery-ui-sortable' ),
		file_exists( $js ) ? filemtime( $js ) : CM_THEME_VERSION,
		true
	);
	wp_localize_script( 'cm-admin-product-order', 'cmProductOrder', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'cm_save_product_order' ),
	) );
}

function cm_render_product_order_page() {
	if ( ! current_user_can( 'edit_posts' ) ) return;

	$cm_default_lang = function_exists( 'pll_default_language' ) ? pll_default_language() : '';
	$cm_selected_cat = isset( $_GET['makine_kategori'] ) ? sanitize_text_field( wp_unslash( $_GET['makine_kategori'] ) ) : '';

	$cm_args = array(
		'post_type'      => 'makine',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	);
	if ( $cm_default_lang ) $cm_args['lang'] = $cm_default_lang;
	if ( $cm_selected_cat ) {
		$cm_args['tax_query'] = array( array(
			'taxonomy' => 'makine_kategori',
			'field'    => 'slug',
			'terms'    => $cm_selected_cat,
		) );
	}
	$cm_products = get_posts( $cm_args );
	$cm_cats     = get_terms( array( 'taxonomy' => 'makine_kategori', 'hide_empty' => false ) );
	?>
	<div class="wrap">
		<h1>Ürün Sıralaması</h1>
		<p>Ürünleri sürükleyip bırakarak sitedeki gösterim sırasını belirleyin. Bu sıra hem kategori sayfalarında hem "Tüm Ürünler" sayfasında kullanılır. Bir ürünü sıraladığınızda, İngilizce/Rusça/İspanyolca çevirileri de otomatik olarak aynı sıraya alınır.</p>

		<ul class="subsubsub" style="margin-bottom:16px;">
			<li>
				<a href="<?php echo esc_url( remove_query_arg( 'makine_kategori' ) ); ?>" class="<?php echo $cm_selected_cat === '' ? 'current' : ''; ?>">Tüm Kategoriler</a> |
			</li>
			<?php foreach ( $cm_cats as $cm_i => $cm_cat ) : ?>
				<li>
					<a href="<?php echo esc_url( add_query_arg( 'makine_kategori', $cm_cat->slug ) ); ?>" class="<?php echo $cm_selected_cat === $cm_cat->slug ? 'current' : ''; ?>"><?php echo esc_html( $cm_cat->name ); ?></a><?php echo $cm_i < count( $cm_cats ) - 1 ? ' |' : ''; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( ! $cm_products ) : ?>
			<p>Bu filtrede ürün bulunamadı.</p>
		<?php else : ?>
			<ul id="cm-order-list" data-category="<?php echo esc_attr( $cm_selected_cat ); ?>" style="max-width:640px; background:#fff; border:1px solid #dcdcde; margin:0; padding:0; list-style:none;">
				<?php foreach ( $cm_products as $cm_p ) :
					$cm_terms = get_the_terms( $cm_p->ID, 'makine_kategori' );
					$cm_term_names = ( $cm_terms && ! is_wp_error( $cm_terms ) ) ? wp_list_pluck( $cm_terms, 'name' ) : array();
				?>
					<li data-id="<?php echo (int) $cm_p->ID; ?>" style="display:flex; align-items:center; gap:12px; padding:8px 12px; border-bottom:1px solid #f0f0f1; background:#fff;">
						<span class="cm-order-handle dashicons dashicons-move" style="cursor:grab; color:#787c82;"></span>
						<?php echo get_the_post_thumbnail( $cm_p->ID, array( 40, 40 ), array( 'style' => 'object-fit:cover;border-radius:2px;flex:0 0 auto;' ) ); ?>
						<span style="flex:1 1 auto;">
							<strong><?php echo esc_html( $cm_p->post_title ); ?></strong>
							<?php if ( $cm_term_names ) : ?><br><span style="color:#787c82;font-size:12px;"><?php echo esc_html( implode( ' · ', $cm_term_names ) ); ?></span><?php endif; ?>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
			<p><span id="cm-order-status" style="color:#2271b1;"></span></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Sürüklenen (görünen filtreye göre görünen) alt kümenin YENİ sırasını kaydeder.
 * İstemciye güvenmeden, sunucu tarafında güncel global sırayı çeker; sadece
 * gönderilen ID'lerin mevcut konumlarını yeni sırayla değiştirir (diğer kategorilerin
 * göreli sırası bozulmaz), sonra TÜM listeyi 10'ar arayla yeniden numaralandırır —
 * kendi kendini onaran bir şema, sayı çakışması/tükenmesi imkansız.
 */
function cm_ajax_save_product_order() {
	check_ajax_referer( 'cm_save_product_order', 'nonce' );
	if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( 'yetkisiz', 403 );

	$cm_subset = isset( $_POST['order'] ) ? array_map( 'intval', (array) $_POST['order'] ) : array();
	if ( ! $cm_subset ) wp_send_json_error( 'bos_liste' );

	$cm_default_lang = function_exists( 'pll_default_language' ) ? pll_default_language() : '';
	$cm_all_args = array(
		'post_type'      => 'makine',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'fields'         => 'ids',
	);
	if ( $cm_default_lang ) $cm_all_args['lang'] = $cm_default_lang;
	$cm_all_ids = get_posts( $cm_all_args );

	// Sürüklenen alt kümenin şu anki global listedeki konum (index) dizisini bul,
	// o slotlara YENİ sırayı yerleştir.
	$cm_indices = array();
	foreach ( $cm_all_ids as $cm_i => $cm_id ) {
		if ( in_array( $cm_id, $cm_subset, true ) ) $cm_indices[] = $cm_i;
	}
	foreach ( $cm_indices as $cm_n => $cm_idx ) {
		if ( isset( $cm_subset[ $cm_n ] ) ) $cm_all_ids[ $cm_idx ] = $cm_subset[ $cm_n ];
	}

	$cm_languages = function_exists( 'pll_languages_list' ) ? pll_languages_list() : array();
	global $wpdb;
	foreach ( $cm_all_ids as $cm_i => $cm_post_id ) {
		$cm_new_order = ( $cm_i + 1 ) * 10;

		if ( (int) get_post_field( 'menu_order', $cm_post_id ) !== $cm_new_order ) {
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $cm_new_order ), array( 'ID' => $cm_post_id ) );
			clean_post_cache( $cm_post_id );
		}
		foreach ( $cm_languages as $cm_lang ) {
			$cm_translated_id = function_exists( 'pll_get_post' ) ? pll_get_post( $cm_post_id, $cm_lang ) : 0;
			if ( $cm_translated_id && (int) $cm_translated_id !== (int) $cm_post_id
				&& (int) get_post_field( 'menu_order', $cm_translated_id ) !== $cm_new_order ) {
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $cm_new_order ), array( 'ID' => $cm_translated_id ) );
				clean_post_cache( $cm_translated_id );
			}
		}
	}

	wp_send_json_success( array( 'message' => 'Sıra kaydedildi.' ) );
}
add_action( 'wp_ajax_cm_save_product_order', 'cm_ajax_save_product_order' );
