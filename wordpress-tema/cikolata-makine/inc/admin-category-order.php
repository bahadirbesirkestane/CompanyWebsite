<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * "Ürünler → Kategori Sıralaması" admin sayfası: sürükle-bırakla kategori
 * gösterim sırası — cm_urun_product_order.php'deki ÜRÜN sıralamasının aynısı,
 * ama WordPress terimlerinde (kategori) menu_order alanı OLMADIĞI için sıra
 * bir term meta'sında ('cm_kategori_sira') tutuluyor.
 *
 * Neden ayrı bir sayfa: her kategorinin 5 dilde (TR/EN/RU/ES/AR) AYRI bir
 * terimi var — bu sayfa SADECE varsayılan dildeki (TR) terimleri gösterir,
 * admin bir kez sürükler, kaydetme sırasında diğer 4 dildeki karşılıklarına
 * da AYNI sıra değeri otomatik yazılır (bkz. cm_ajax_save_category_order()).
 *
 * Hiyerarşi BOZULMAZ: sayfa her zaman TEK bir ebeveynin (varsayılan: kök,
 * yani üst kategoriler) doğrudan çocuklarını gösterir — sürükleme sadece o
 * grubun İÇİNDEKİ sırayı değiştirir, kategoriler arası taşıma (yeniden
 * ebeveynleme) yapılmaz. Bir üst kategoriye tıklayınca ONUN alt kategorileri
 * için AYNI ekranda sıralama yapılabilir (?parent=<id>).
 *
 * Bu sıra front-end'deki TÜM makine_kategori get_terms() çağrılarında (bkz.
 * front-page.php, footer.php, inc/template-tags.php sidebar/mega menü)
 * kullanılır — ayrı bir "görünüm sırası" alanı YOK, tek kaynak burası.
 */

define( 'CM_KATEGORI_SIRA_META', 'cm_kategori_sira' );

function cm_add_category_order_page() {
	$hook = add_submenu_page(
		'edit.php?post_type=makine',
		'Kategori Sıralaması',
		'Kategori Sıralaması',
		'manage_categories',
		'cm-kategori-siralama',
		'cm_render_category_order_page'
	);
	add_action( 'admin_enqueue_scripts', function ( $current_hook ) use ( $hook ) {
		if ( $current_hook !== $hook ) return;
		cm_enqueue_category_order_assets();
	} );
}
add_action( 'admin_menu', 'cm_add_category_order_page' );

function cm_enqueue_category_order_assets() {
	wp_enqueue_script( 'jquery-ui-sortable' );
	$js = CM_THEME_DIR . '/assets/js/admin-category-order.js';
	wp_enqueue_script(
		'cm-admin-category-order',
		CM_THEME_URI . '/assets/js/admin-category-order.js',
		array( 'jquery-ui-sortable' ),
		file_exists( $js ) ? filemtime( $js ) : CM_THEME_VERSION,
		true
	);
	wp_localize_script( 'cm-admin-category-order', 'cmCategoryOrder', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'cm_save_category_order' ),
	) );
}

function cm_render_category_order_page() {
	if ( ! current_user_can( 'manage_categories' ) ) return;

	$cm_default_lang = function_exists( 'pll_default_language' ) ? pll_default_language() : '';
	$cm_parent_id    = isset( $_GET['parent'] ) ? absint( $_GET['parent'] ) : 0;

	$cm_parent_term = $cm_parent_id ? get_term( $cm_parent_id, 'makine_kategori' ) : null;
	// Güvenlik: ?parent= ile verilen ID gerçekten var mı ve TR mi, değilse köke düş.
	if ( $cm_parent_id && ( ! $cm_parent_term || is_wp_error( $cm_parent_term ) ) ) $cm_parent_id = 0;

	$cm_args = array(
		'taxonomy'   => 'makine_kategori',
		'parent'     => $cm_parent_id,
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'meta_key'   => CM_KATEGORI_SIRA_META, // phpcs:ignore -- performans endişesi yok, en fazla birkaç düzine terim
		'order'      => 'ASC',
	);
	if ( $cm_default_lang ) $cm_args['lang'] = $cm_default_lang;
	$cm_terms = get_terms( $cm_args );
	if ( is_wp_error( $cm_terms ) ) $cm_terms = array();

	// Üstte gezinme: her zaman "Üst Kategoriler" + o an İÇİNDE olduğumuz zincir.
	$cm_top_terms_args = array( 'taxonomy' => 'makine_kategori', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'meta_value_num', 'meta_key' => CM_KATEGORI_SIRA_META, 'order' => 'ASC' );
	if ( $cm_default_lang ) $cm_top_terms_args['lang'] = $cm_default_lang;
	$cm_top_terms = get_terms( $cm_top_terms_args );
	if ( is_wp_error( $cm_top_terms ) ) $cm_top_terms = array();
	?>
	<div class="wrap">
		<h1>Kategori Sıralaması</h1>
		<p>Kategorileri sürükleyip bırakarak sitedeki (anasayfa, footer, kategori menüsü, kenar çubuğu) gösterim sırasını belirleyin. Bir kategoriyi sıraladığınızda, İngilizce/Rusça/İspanyolca/Arapça karşılıkları da otomatik olarak aynı sıraya alınır.</p>

		<ul class="subsubsub" style="margin-bottom:16px;">
			<li><a href="<?php echo esc_url( remove_query_arg( 'parent' ) ); ?>" class="<?php echo $cm_parent_id === 0 ? 'current' : ''; ?>">Üst Kategoriler</a> |</li>
			<?php foreach ( $cm_top_terms as $cm_i => $cm_t ) : ?>
				<li>
					<a href="<?php echo esc_url( add_query_arg( 'parent', $cm_t->term_id ) ); ?>" class="<?php echo $cm_parent_id === $cm_t->term_id ? 'current' : ''; ?>"><?php echo esc_html( $cm_t->name ); ?> — alt kategorileri</a><?php echo $cm_i < count( $cm_top_terms ) - 1 ? ' |' : ''; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $cm_parent_id ) : ?>
			<p><em><?php echo esc_html( $cm_parent_term->name ); ?></em> kategorisinin alt kategorilerini sıralıyorsunuz.</p>
		<?php endif; ?>

		<?php if ( ! $cm_terms ) : ?>
			<p><?php echo $cm_parent_id ? 'Bu kategorinin alt kategorisi yok.' : 'Henüz kategori yok.'; ?></p>
		<?php else : ?>
			<ul id="cm-cat-order-list" data-parent="<?php echo (int) $cm_parent_id; ?>" style="max-width:640px; background:#fff; border:1px solid #dcdcde; margin:0; padding:0; list-style:none;">
				<?php foreach ( $cm_terms as $cm_t ) :
					$cm_child_count = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => $cm_t->term_id, 'hide_empty' => false, 'fields' => 'ids', 'lang' => $cm_default_lang ) );
					$cm_child_count = is_wp_error( $cm_child_count ) ? 0 : count( $cm_child_count );
				?>
					<li data-id="<?php echo (int) $cm_t->term_id; ?>" style="display:flex; align-items:center; gap:12px; padding:10px 12px; border-bottom:1px solid #f0f0f1; background:#fff;">
						<span class="cm-cat-order-handle dashicons dashicons-move" style="cursor:grab; color:#787c82;"></span>
						<span style="flex:1 1 auto;">
							<strong><?php echo esc_html( $cm_t->name ); ?></strong>
							<span style="color:#787c82;"> (<?php echo (int) $cm_t->count; ?> ürün<?php echo $cm_child_count ? ', ' . (int) $cm_child_count . ' alt kategori' : ''; ?>)</span>
						</span>
						<?php if ( $cm_child_count ) : ?>
							<a href="<?php echo esc_url( add_query_arg( 'parent', $cm_t->term_id ) ); ?>" class="button button-small">Alt kategorileri sırala</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<p><span id="cm-cat-order-status" style="color:#2271b1;"></span></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Görüntülenen tek ebeveynin (parent) doğrudan çocuklarının YENİ sırasını
 * kaydeder — ürün sıralamasının aksine, ekranda HER ZAMAN o ebeveynin
 * TAM çocuk kümesi gösterildiği için (filtrelenmiş bir alt küme değil)
 * karmaşık "pozisyon bulma" gerekmiyor, doğrudan 10'ar arayla numaralandırılır.
 */
function cm_ajax_save_category_order() {
	check_ajax_referer( 'cm_save_category_order', 'nonce' );
	if ( ! current_user_can( 'manage_categories' ) ) wp_send_json_error( 'yetkisiz', 403 );

	$cm_order = isset( $_POST['order'] ) ? array_map( 'intval', (array) $_POST['order'] ) : array();
	if ( ! $cm_order ) wp_send_json_error( 'bos_liste' );

	$cm_languages = function_exists( 'pll_languages_list' ) ? pll_languages_list() : array();

	foreach ( $cm_order as $cm_i => $cm_term_id ) {
		$cm_new_sira = ( $cm_i + 1 ) * 10;
		update_term_meta( $cm_term_id, CM_KATEGORI_SIRA_META, $cm_new_sira );

		foreach ( $cm_languages as $cm_lang ) {
			$cm_translated_id = function_exists( 'pll_get_term' ) ? pll_get_term( $cm_term_id, $cm_lang ) : 0;
			if ( $cm_translated_id && (int) $cm_translated_id !== (int) $cm_term_id ) {
				update_term_meta( $cm_translated_id, CM_KATEGORI_SIRA_META, $cm_new_sira );
			}
		}
	}

	wp_send_json_success( array( 'message' => 'Sıra kaydedildi.' ) );
}
add_action( 'wp_ajax_cm_save_category_order', 'cm_ajax_save_category_order' );

/**
 * Yeni bir makine_kategori terimi (wp-admin → Ürünler → Kategoriler → Yeni
 * Ekle'den, sıralama sayfası DIŞINDA) oluşturulduğunda sira meta'sı hiç
 * yoksa "meta_value_num" sıralamasında en başa (0 gibi) düşüp kafa
 * karıştırabilirdi — yeni terim otomatik olarak KENDİ ebeveyn grubunun
 * SONUNA eklenir (mevcut en yüksek sıra + 10).
 */
function cm_default_category_sira( $term_id, $tt_id, $taxonomy ) {
	if ( $taxonomy !== 'makine_kategori' ) return;
	if ( get_term_meta( $term_id, CM_KATEGORI_SIRA_META, true ) !== '' ) return;

	$term = get_term( $term_id, 'makine_kategori' );
	if ( ! $term || is_wp_error( $term ) ) return;

	global $wpdb;
	$max = $wpdb->get_var( $wpdb->prepare(
		"SELECT MAX(CAST(tm.meta_value AS UNSIGNED)) FROM {$wpdb->term_taxonomy} tt
		 INNER JOIN {$wpdb->termmeta} tm ON tm.term_id = tt.term_id AND tm.meta_key = %s
		 WHERE tt.taxonomy = 'makine_kategori' AND tt.parent = %d",
		CM_KATEGORI_SIRA_META,
		$term->parent
	) );
	update_term_meta( $term_id, CM_KATEGORI_SIRA_META, ( (int) $max ) + 10 );
}
add_action( 'created_term', 'cm_default_category_sira', 10, 3 );
