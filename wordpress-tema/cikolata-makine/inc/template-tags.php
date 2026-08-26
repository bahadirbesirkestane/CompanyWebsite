<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * "Özellik Adı: Değer" satırlarından oluşan düz metni [['ozellik_adi'=>..,'ozellik_degeri'=>..], ...] dizisine çevirir.
 * Teknik özellikler ACF'in ücretsiz sürümünde olmayan Repeater alanı yerine düz metin olarak girildiği için kullanılır.
 */
function cm_parse_specs( $text ) {
	$rows = array();
	if ( ! $text ) return $rows;
	foreach ( preg_split( '/\r\n|\r|\n/', $text ) as $line ) {
		$line = trim( $line );
		if ( $line === '' ) continue;
		$parts = explode( ':', $line, 2 );
		$rows[] = count( $parts ) === 2
			? array( 'ozellik_adi' => trim( $parts[0] ), 'ozellik_degeri' => trim( $parts[1] ) )
			: array( 'ozellik_adi' => $line, 'ozellik_degeri' => '' );
	}
	return $rows;
}

/**
 * Kırıntı (breadcrumb) yolu yazdırır.
 * $items = array( array('label' => 'Anasayfa', 'url' => home_url('/')), ..., array('label' => 'Geçerli Sayfa') )
 * Son elemanın 'url' anahtarı yoksa "current" olarak işaretlenir.
 */
function cm_breadcrumb( $items ) {
	echo '<nav class="breadcrumb" aria-label="' . esc_attr( cm__( 'breadcrumb_kirinti_yolu' ) ) . '">';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $item ) {
		if ( $i > 0 ) echo '<span>/</span>';
		if ( $i === $last || empty( $item['url'] ) ) {
			echo '<span class="current">' . esc_html( $item['label'] ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
		}
	}
	echo '</nav>';
}

/**
 * Genel makine silüeti — gerçek görsel yüklenmediğinde yer tutucu olarak kullanılır.
 */
function cm_generic_icon( $size = 34 ) {
	printf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 34 34" fill="none" stroke="currentColor" stroke-width="1.3"><rect x="6" y="10" width="22" height="16" rx="1"/><circle cx="12" cy="18" r="2.6"/><circle cx="22" cy="18" r="2.6"/><line x1="10" y1="10" x2="10" y2="6"/><line x1="24" y1="10" x2="24" y2="6"/></svg>',
		(int) $size
	);
}

/**
 * Öne çıkan görseli (varsa) .ph çerçevesi içinde, yoksa yer tutucu ikonla basar.
 */
function cm_render_thumb( $post_id, $extra_class = '', $img_size = 'large', $featured_badge = false ) {
	$has_thumb = has_post_thumbnail( $post_id );
	echo '<div class="ph ' . esc_attr( $extra_class ) . '">';
	if ( $featured_badge ) echo '<span class="featured-tag">' . esc_html( cm__( 'one_cikan_etiket' ) ) . '</span>';
	if ( $has_thumb ) {
		echo get_the_post_thumbnail( $post_id, $img_size );
	} else {
		cm_generic_icon( 40 );
	}
	echo '<span class="corner c-tl"></span><span class="corner c-tr"></span><span class="corner c-bl"></span><span class="corner c-br"></span>';
	if ( ! $has_thumb ) echo '<span class="tag">' . esc_html( cm__( 'gorsel_etiket' ) ) . '</span>';
	echo '</div>';
}

/**
 * Bir kategorinin toplam ürün sayısı: kendi doğrudan sayısı + tüm alt kategorilerinin
 * sayıları (bir makine sadece alt kategoriye atanmış olsa bile üst kategoride sayılsın diye).
 * $children önceden çekilmişse (get_term_children() sonucu) tekrar sorgu atmamak için verilebilir.
 */
function cm_category_total_count( $term, $children = null ) {
	if ( $children === null ) $children = get_term_children( $term->term_id, 'makine_kategori' );
	$count = (int) $term->count;
	if ( ! is_wp_error( $children ) && $children ) {
		foreach ( $children as $child_id ) {
			$child = get_term( $child_id, 'makine_kategori' );
			if ( $child && ! is_wp_error( $child ) ) $count += (int) $child->count;
		}
	}
	return $count;
}

/**
 * Kategori kartı — get_terms() sonucundaki bir WP_Term nesnesini kart olarak basar.
 */
function cm_category_card( $term ) {
	$icon     = function_exists( 'get_field' ) ? get_field( 'kategori_ikon', $term ) : false;
	$children = get_term_children( $term->term_id, 'makine_kategori' );
	$count    = cm_category_total_count( $term, $children );
	$sub_names = array();
	if ( ! is_wp_error( $children ) && $children ) {
		foreach ( array_slice( $children, 0, 3 ) as $child_id ) {
			$child = get_term( $child_id, 'makine_kategori' );
			if ( $child && ! is_wp_error( $child ) ) $sub_names[] = $child->name;
		}
	}
	?>
	<a class="cat-card reveal" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
		<div class="cat-icon">
			<?php if ( $icon && ! empty( $icon['url'] ) ) : ?>
				<img src="<?php echo esc_url( $icon['url'] ); ?>" alt="" width="34" height="34" style="filter:none;">
			<?php else : ?>
				<?php cm_generic_icon( 34 ); ?>
			<?php endif; ?>
		</div>
		<h3><?php echo esc_html( $term->name ); ?></h3>
		<div class="sub"><?php echo $sub_names ? esc_html( implode( ' · ', $sub_names ) ) : esc_html( wp_trim_words( $term->description, 10, '…' ) ); ?></div>
		<?php if ( $count ) : ?><div class="count"><?php echo esc_html( $count . ' ' . cm__( 'urun_etiketi' ) ); ?></div><?php endif; ?>
	</a>
	<?php
}

/**
 * Kategori/alt kategori sayfalarında (taxonomy-makine_kategori.php) solda gösterilen
 * sabit kategori ağacı. Sadece geçerli sayfanın ait olduğu ÜST kategorinin alt
 * kategorileri açık gösterilir, diğer üst kategoriler hiç alt liste basmaz — JS/accordion
 * yok, tamamen $current_term'e göre sunucu tarafında hesaplanır.
 */
function cm_category_sidebar( $current_term = null ) {
	$active_top_id = 0;
	$active_sub_id = 0;
	if ( $current_term ) {
		$active_top_id = $current_term->parent ? $current_term->parent : $current_term->term_id;
		$active_sub_id = $current_term->parent ? $current_term->term_id : 0;
	}
	$top_cats = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => 0, 'hide_empty' => false ) );
	if ( is_wp_error( $top_cats ) ) return;
	?>
	<nav class="cat-sidebar" aria-label="<?php echo esc_attr( cm__( 'sidebar_aria' ) ); ?>">
		<a class="cat-sidebar-all<?php echo ! $current_term ? ' active' : ''; ?>" href="<?php echo esc_url( cm_translated_page_url( 'urunler', '/urunler/' ) ); ?>"><?php echo esc_html( cm__( 'sidebar_tum_urunler' ) ); ?></a>
		<ul>
			<?php foreach ( $top_cats as $top ) :
				$is_active_top = $top->term_id === $active_top_id;
				$icon = function_exists( 'get_field' ) ? get_field( 'kategori_ikon', $top ) : false;
				// Sayım için alt kategoriler HER ZAMAN çekilir (aktif olmasa bile) —
				// aksi halde bir üst kategorinin tüm ürünleri sadece alt kategorilerinde
				// olduğunda, o kategori aktif olmadan (0) gösterip tıklanınca doğru sayıya
				// "zıplardı". Alt liste sadece aktifken basılır, sayım her zaman doğru olur.
				$children = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => $top->term_id, 'hide_empty' => false ) );
				if ( is_wp_error( $children ) ) $children = array();
				$count = cm_category_total_count( $top, $children );
			?>
				<li>
					<a class="cat-sidebar-icon-row<?php echo $is_active_top ? ' active' : ''; ?>" href="<?php echo esc_url( get_term_link( $top ) ); ?>">
						<span class="cat-sidebar-icon"><?php if ( $icon && ! empty( $icon['url'] ) ) : ?><img src="<?php echo esc_url( $icon['url'] ); ?>" alt="" width="18" height="18"><?php else : ?><?php cm_generic_icon( 18 ); ?><?php endif; ?></span>
						<span class="cat-sidebar-name"><?php echo esc_html( $top->name ); ?></span>
						<span class="cat-sidebar-count">(<?php echo (int) $count; ?>)</span>
					</a>
					<?php if ( $is_active_top && ! is_wp_error( $children ) && $children ) : ?>
						<ul class="cat-sidebar-children">
							<?php foreach ( $children as $child ) : ?>
								<li><a class="<?php echo $child->term_id === $active_sub_id ? 'active' : ''; ?>" href="<?php echo esc_url( get_term_link( $child ) ); ?>"><?php echo esc_html( $child->name ); ?> (<?php echo (int) $child->count; ?>)</a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Makine ürün kartı (grid içinde kullanılır).
 */
function cm_product_card( $post_id ) {
	$featured = function_exists( 'get_field' ) ? (bool) get_field( 'one_cikan', $post_id ) : false;
	$ozet     = function_exists( 'get_field' ) ? get_field( 'kisa_ozet', $post_id ) : '';
	?>
	<a class="prod-card reveal" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
		<?php cm_render_thumb( $post_id, '', 'medium_large', $featured ); ?>
		<h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
		<?php if ( $ozet ) : ?><div class="spec mono"><?php echo esc_html( $ozet ); ?></div><?php endif; ?>
		<div class="go"><?php echo esc_html( cm__( 'detaylari_gor' ) ); ?></div>
	</a>
	<?php
}

/**
 * PDF görüntüle/indir satırı. $file, ACF file alanının (return_format=array) değeridir.
 */
function cm_pdf_row( $file, $label = null ) {
	if ( empty( $file['url'] ) ) return;
	$label = $label ?: $file['filename'];
	?>
	<div class="pdf-row">
		<div class="pdf-icon"></div>
		<div class="pdf-meta">
			<div class="fn"><?php echo esc_html( $label ); ?></div>
			<div class="fs"><?php echo esc_html( size_format( $file['filesize'] ?? 0 ) ); ?></div>
		</div>
		<div class="pdf-actions">
			<a class="btn btn-outline" href="<?php echo esc_url( $file['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( cm__( 'goruntule' ) ); ?></a>
			<a class="btn btn-primary" href="<?php echo esc_url( $file['url'] ); ?>" download><?php echo esc_html( cm__( 'indir' ) ); ?></a>
		</div>
	</div>
	<?php
}

/**
 * Header'da "Ürünler" menü öğesinin üzerine gelince (masaüstü) / dokununca (mobil)
 * açılan kategori/alt kategori panosu. cm_category_sidebar() ile aynı taksonomi
 * ağacını kullanır ama liste yerine çok sütunlu bir ızgara olarak basar —
 * bkz. inc/nav-walker.php (bu fonksiyonu "Ürünler" menü öğesinin içine ekler).
 */
function cm_products_megamenu() {
	$top_cats = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => 0, 'hide_empty' => false ) );
	if ( is_wp_error( $top_cats ) || ! $top_cats ) return;
	?>
	<div class="megamenu">
		<ul class="megamenu-list">
			<?php foreach ( $top_cats as $top ) :
				$icon = function_exists( 'get_field' ) ? get_field( 'kategori_ikon', $top ) : false;
				$children = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => $top->term_id, 'hide_empty' => false ) );
				if ( is_wp_error( $children ) ) $children = array();
			?>
				<li class="megamenu-item<?php echo $children ? ' has-children' : ''; ?>">
					<a href="<?php echo esc_url( get_term_link( $top ) ); ?>">
						<span class="megamenu-item-icon"><?php if ( $icon && ! empty( $icon['url'] ) ) : ?><img src="<?php echo esc_url( $icon['url'] ); ?>" alt="" width="16" height="16"><?php else : ?><?php cm_generic_icon( 16 ); ?><?php endif; ?></span>
						<span class="megamenu-item-name"><?php echo esc_html( $top->name ); ?></span>
						<?php if ( $children ) : ?><span class="megamenu-item-arrow" aria-hidden="true">›</span><?php endif; ?>
					</a>
					<?php if ( $children ) : ?>
						<button type="button" class="megamenu-item-toggle" aria-expanded="false" aria-label="<?php echo esc_attr( sprintf( cm__( 'megamenu_alt_kategori_aria' ), $top->name ) ); ?>">
							<svg viewBox="0 0 12 8" width="10" height="7" aria-hidden="true"><path d="M1 1l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
						</button>
						<ul class="megamenu-flyout">
							<?php foreach ( $children as $child ) : ?>
								<li><a href="<?php echo esc_url( get_term_link( $child ) ); ?>"><?php echo esc_html( $child->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="megamenu-footer">
			<a href="<?php echo esc_url( cm_translated_page_url( 'urunler', '/urunler/' ) ); ?>"><?php echo esc_html( cm__( 'sidebar_tum_urunler' ) ); ?> →</a>
		</div>
	</div>
	<?php
}

/**
 * Kurumsal sayfasının 4 alt sayfası (Hakkımızda/Misyon/Üretim Tesisi/Kalite) için
 * konuya uygun küçük çizgi ikonlar — çeviri hangi dilde olursa olsun aynı ikon
 * gösterilsin diye ID eşleştirmesi HER ZAMAN Türkçe (varsayılan dil) karşılığı
 * üzerinden yapılır (bkz. cm_kurumsal_child_icon() çağrısı, page.php).
 */
function cm_kurumsal_child_icon( $tr_id ) {
	$icons = array(
		154 => '<rect x="8" y="5" width="18" height="24" rx="1"/><line x1="12" y1="12" x2="22" y2="12"/><line x1="12" y1="17" x2="22" y2="17"/><line x1="12" y1="22" x2="18" y2="22"/>',
		155 => '<circle cx="17" cy="17" r="11"/><circle cx="17" cy="17" r="6"/><circle cx="17" cy="17" r="1.5" fill="currentColor" stroke="none"/>',
		156 => '<rect x="6" y="18" width="22" height="11" rx="1"/><path d="M6 18l6-6 6 6 6-6 4 4"/><line x1="10" y1="12" x2="10" y2="7"/>',
		157 => '<circle cx="17" cy="13" r="8"/><path d="M12 19l-3 8 5-2.5 3 2.5v-6M22 19l3 8-5-2.5-3 2.5v-6"/>',
	);
	$path = $icons[ $tr_id ] ?? '';
	if ( ! $path ) { cm_generic_icon( 34 ); return; }
	echo '<svg width="34" height="34" viewBox="0 0 34 34" fill="none" stroke="currentColor" stroke-width="1.3">' . $path . '</svg>'; // phpcs:ignore -- sabit, kullanıcı girdisi değil
}

/**
 * YouTube linkini (watch/kısa/embed formatları) gömülebilir embed url'sine çevirir.
 * Tanınmayan/boş linkte '' döner.
 */
function cm_youtube_embed_url( $url ) {
	if ( ! $url ) return '';
	if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $url, $m ) ) {
		return 'https://www.youtube.com/embed/' . $m[1];
	}
	return '';
}
