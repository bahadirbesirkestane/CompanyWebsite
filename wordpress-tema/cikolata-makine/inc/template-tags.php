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
 * Kalite Belgeleri kartlarında (bkz. front-page.php) görsel yüklenmemiş — PDF olarak
 * yüklenmiş — bir belgenin yerine basılan genel doküman ikonu. cm_generic_icon() ile
 * AYNI çizgisel stil (stroke, currentColor, 34x34 viewBox).
 */
function cm_document_icon( $size = 34 ) {
	printf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 34 34" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M10 4h9l6 6v19a1 1 0 0 1-1 1H10a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1z"/><path d="M19 4v6h6"/><line x1="12.5" y1="18" x2="21.5" y2="18"/><line x1="12.5" y1="22" x2="21.5" y2="22"/><line x1="12.5" y1="26" x2="17.5" y2="26"/></svg>',
		(int) $size
	);
}

/**
 * İletişim sayfasındaki "Bize Ulaşın" kartlarında (bkz. page.php) her kanal türü için
 * küçük bir çizgisel ikon — diğer ikonlarla (cm_generic_icon() vb.) AYNI stil
 * (stroke, currentColor). Uluslararası İletişim kartlarında (cm_render_intl_contact_section())
 * KULLANILMAZ — o kartlar bir ülke/kişi temsil eder, kanal türü değil.
 */
function cm_contact_icon( $type ) {
	$icons = array(
		'tel'    => '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M4 2.5h2.5l1 3-1.5 1.2a8 8 0 0 0 4.3 4.3l1.2-1.5 3 1V13a1.5 1.5 0 0 1-1.5 1.5C7.5 14.5 3.5 10.5 2.5 5A1.5 1.5 0 0 1 4 2.5z"/></svg>',
		'wa'     => '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M3 15l1.1-3.2A6 6 0 1 1 6.9 14L3 15z"/><path d="M6.5 6.8c0 2.7 2 4.7 4.7 4.7"/></svg>',
		'eposta' => '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.3"><rect x="2" y="4" width="14" height="10" rx="1"/><path d="M2.5 4.8l6.5 5 6.5-5"/></svg>',
		'adres'  => '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M9 16s5-4.6 5-8.7A5 5 0 0 0 4 7.3C4 11.4 9 16 9 16z"/><circle cx="9" cy="7.3" r="1.8"/></svg>',
		'saat'   => '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.3"><circle cx="9" cy="9" r="6.5"/><path d="M9 5.5V9l2.8 1.6"/></svg>',
	);
	echo $icons[ $type ] ?? '';
}

/**
 * Sosyal medya ikonları — header (WhatsApp yanında) VE footer'da kullanılır.
 * Kullanıcı geri bildirimi: önceki elle çizilmiş çizgisel simgeler yeterince
 * tanınmıyordu, uygulamaların KENDİ (resmi) logo şekilleri kullanılsın, sadece
 * TEK RENK (marka renkleri değil, currentColor — footer'da beyaz, header'da
 * okunabilirlik için koyu) basılsın. Path verileri Simple Icons'tan (MIT
 * lisanslı, simpleicons.org) alınmıştır — 24x24 viewBox, tek <path>, dolu
 * (fill) şekil; WhatsApp'ın kendi (renkli, dolu yeşil daire) butonu BUNUN
 * DIŞINDA, header.php'de ayrı ve değişmedi (kullanıcı isteği: "Whatsapp
 * hariç").
 */
function cm_social_icon( $type ) {
	$icons = array(
		'facebook'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/></svg>',
		'instagram' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M7.0301.084c-1.2768.0602-2.1487.264-2.911.5634-.7888.3075-1.4575.72-2.1228 1.3877-.6652.6677-1.075 1.3368-1.3802 2.127-.2954.7638-.4956 1.6365-.552 2.914-.0564 1.2775-.0689 1.6882-.0626 4.947.0062 3.2586.0206 3.6671.0825 4.9473.061 1.2765.264 2.1482.5635 2.9107.308.7889.72 1.4573 1.388 2.1228.6679.6655 1.3365 1.0743 2.1285 1.38.7632.295 1.6361.4961 2.9134.552 1.2773.056 1.6884.069 4.9462.0627 3.2578-.0062 3.668-.0207 4.9478-.0814 1.28-.0607 2.147-.2652 2.9098-.5633.7889-.3086 1.4578-.72 2.1228-1.3881.665-.6682 1.0745-1.3378 1.3795-2.1284.2957-.7632.4966-1.636.552-2.9124.056-1.2809.0692-1.6898.063-4.948-.0063-3.2583-.021-3.6668-.0817-4.9465-.0607-1.2797-.264-2.1487-.5633-2.9117-.3084-.7889-.72-1.4568-1.3876-2.1228C21.2982 1.33 20.628.9208 19.8378.6165 19.074.321 18.2017.1197 16.9244.0645 15.6471.0093 15.236-.005 11.977.0014 8.718.0076 8.31.0215 7.0301.0839m.1402 21.6932c-1.17-.0509-1.8053-.2453-2.2287-.408-.5606-.216-.96-.4771-1.3819-.895-.422-.4178-.6811-.8186-.9-1.378-.1644-.4234-.3624-1.058-.4171-2.228-.0595-1.2645-.072-1.6442-.079-4.848-.007-3.2037.0053-3.583.0607-4.848.05-1.169.2456-1.805.408-2.2282.216-.5613.4762-.96.895-1.3816.4188-.4217.8184-.6814 1.3783-.9003.423-.1651 1.0575-.3614 2.227-.4171 1.2655-.06 1.6447-.072 4.848-.079 3.2033-.007 3.5835.005 4.8495.0608 1.169.0508 1.8053.2445 2.228.408.5608.216.96.4754 1.3816.895.4217.4194.6816.8176.9005 1.3787.1653.4217.3617 1.056.4169 2.2263.0602 1.2655.0739 1.645.0796 4.848.0058 3.203-.0055 3.5834-.061 4.848-.051 1.17-.245 1.8055-.408 2.2294-.216.5604-.4763.96-.8954 1.3814-.419.4215-.8181.6811-1.3783.9-.4224.1649-1.0577.3617-2.2262.4174-1.2656.0595-1.6448.072-4.8493.079-3.2045.007-3.5825-.006-4.848-.0608M16.953 5.5864A1.44 1.44 0 1 0 18.39 4.144a1.44 1.44 0 0 0-1.437 1.4424M5.8385 12.012c.0067 3.4032 2.7706 6.1557 6.173 6.1493 3.4026-.0065 6.157-2.7701 6.1506-6.1733-.0065-3.4032-2.771-6.1565-6.174-6.1498-3.403.0067-6.156 2.771-6.1496 6.1738M8 12.0077a4 4 0 1 1 4.008 3.9921A3.9996 3.9996 0 0 1 8 12.0077"/></svg>',
		'linkedin'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
		'youtube'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
		'twitter'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14.234 10.162 22.977 0h-2.072l-7.591 8.824L7.251 0H.258l9.168 13.343L.258 24H2.33l8.016-9.318L16.749 24h6.993zm-2.837 3.299-.929-1.329L3.076 1.56h3.182l5.965 8.532.929 1.329 7.754 11.09h-3.182z"/></svg>',
	);
	echo $icons[ $type ] ?? '';
}

/**
 * Sosyal medya ikon şeridi — $types hangi platformların (ve hangi sırada)
 * basılacağını belirler (header'da 3'ü: linkedin/instagram/youtube; footer'da
 * 5'i). Her platformun Customizer linki (inc/customizer.php → cm_iletisim
 * bölümü) boşsa o ikon hiç basılmaz; HİÇBİRİ doluysa şerit tamamen basılmaz
 * ("boşsa gizle").
 */
function cm_social_links( $types, $extra_class = '' ) {
	$urls = array(
		'facebook'  => cm_option( 'sosyal_facebook' ),
		'instagram' => cm_option( 'sosyal_instagram' ),
		'linkedin'  => cm_option( 'sosyal_linkedin' ),
		'youtube'   => cm_option( 'sosyal_youtube' ),
		'twitter'   => cm_option( 'sosyal_twitter' ),
	);
	$labels = array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'linkedin'  => 'LinkedIn',
		'youtube'   => 'YouTube',
		'twitter'   => 'X (Twitter)',
	);
	$active = array_filter( $types, function ( $t ) use ( $urls ) { return ! empty( $urls[ $t ] ); } );
	if ( ! $active ) return;
	echo '<div class="social-links' . ( $extra_class ? ' ' . esc_attr( $extra_class ) : '' ) . '">';
	foreach ( $active as $t ) {
		echo '<a href="' . esc_url( $urls[ $t ] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $labels[ $t ] ) . '">';
		cm_social_icon( $t );
		echo '</a>';
	}
	echo '</div>';
}

/**
 * Bir PDF ekinin İLK SAYFASINI JPG önizleme olarak döndürür (bkz. Kalite Belgeleri
 * kartları, front-page.php) — bulunamazsa/oluşturulamazsa boş döner, çağıran taraf
 * bu durumda cm_document_icon() gibi genel bir ikona düşer ("boşsa gizle" ilkesi).
 *
 * İki kademeli dener:
 * 1) WordPress'in KENDİ ürettiği önizleme boyutu — sunucuda Imagick'in PDF delege
 *    desteği (Ghostscript ile) çalışıyorsa yükleme sırasında OTOMATİK oluşur, ekstra
 *    kod gerekmez.
 * 2) O yoksa, sunucuda `exec()` açıksa (birçok paylaşımlı hosting'te KAPALIDIR —
 *    bu normal, bu durumda sessizce 2. adım atlanır) Ghostscript doğrudan çağrılır
 *    ve üretilen önizleme, PDF'in yanına TEK SEFERLİK önbelleğe alınır (aynı PDF
 *    için bir daha çalıştırılmaz — bkz. dosya adı ekin ID'sine bağlı, değişmez).
 *
 * NOT: Production sunucusunda (hosting.com.tr vb.) `exec()` kapalıysa VE Imagick'in
 * PDF delegesi de yoksa, kart otomatik olarak genel belge ikonuna düşer — bu durumda
 * gerçek bir önizleme istenirse tek seçenek PDF'in yanında admin tarafından ayrıca
 * bir görsel yüklenmesidir (aynı slotun "Belge" alanına PDF yerine doğrudan görsel
 * yüklenebilir).
 */
function cm_pdf_preview_url( $file ) {
	if ( empty( $file['id'] ) || empty( $file['url'] ) ) return '';
	$id = (int) $file['id'];

	$native = wp_get_attachment_image_url( $id, 'medium' );
	if ( $native && $native !== $file['url'] ) return $native;

	static $cm_exec_ok = null;
	if ( $cm_exec_ok === null ) {
		$disabled   = array_map( 'trim', explode( ',', (string) ini_get( 'disable_functions' ) ) );
		$cm_exec_ok = function_exists( 'exec' ) && ! in_array( 'exec', $disabled, true );
	}
	if ( ! $cm_exec_ok ) return '';

	$pdf_path = get_attached_file( $id );
	if ( ! $pdf_path || ! file_exists( $pdf_path ) ) return '';

	$upload_dir   = wp_upload_dir();
	$preview_rel  = 'cm-pdf-preview/' . $id . '.jpg';
	$preview_path = trailingslashit( $upload_dir['basedir'] ) . $preview_rel;
	$preview_url  = trailingslashit( $upload_dir['baseurl'] ) . $preview_rel;

	if ( file_exists( $preview_path ) && filemtime( $preview_path ) >= filemtime( $pdf_path ) ) {
		return $preview_url;
	}

	wp_mkdir_p( dirname( $preview_path ) );
	$cmd = sprintf(
		'gs -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=jpeg -r120 -dFirstPage=1 -dLastPage=1 -o %s %s 2>&1',
		escapeshellarg( $preview_path ),
		escapeshellarg( $pdf_path )
	);
	@exec( $cmd, $cm_out, $cm_code );

	return ( $cm_code === 0 && file_exists( $preview_path ) ) ? $preview_url : '';
}

/**
 * Sayfa üstü banner: görsel varsa geniş bir fotoğraf şeridi basar, yoksa HİÇBİR
 * ŞEY çıktılamaz — "boşsa gizle" ilkesi, sayfa banner'sız haliyle görünmeye devam
 * eder. $image, ACF image alanının döndürdüğü dizi (return_format=array) VEYA
 * doğrudan bir URL string'i olabilir (Customizer theme_mod'ları URL döner) —
 * ikisi de kabul edilir, çağıran taraf hangisi olduğunu düşünmek zorunda kalmaz.
 */
/**
 * Sayfa üstü banner + üzerine bindirilen sayfa başlığı (bkz. solen.com.tr/hakkimizda
 * referansı — başlık banner görselinin üstünde, ayrı bir satır olarak DEĞİL). Banner
 * görseli yoksa hiçbir şey basmaz (false döner) — çağıran taraf bu durumda başlığı
 * kendi <h1>'iyle sayfa içeriğinde basmaya devam eder, "boşsa gizle" ilkesi korunur.
 */
function cm_page_banner( $image, $title = '', $crumbs = array() ) {
	$url = '';
	$alt = '';
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		$url = $image['url'];
		$alt = $image['alt'] ?? '';
	} elseif ( is_string( $image ) && $image ) {
		$url = $image;
	}
	if ( ! $url ) return false;
	?>
	<div class="page-banner">
		<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
		<?php if ( $title ) : ?>
			<div class="page-banner-text"><div class="page-banner-text-inner">
				<h1 class="h-xl"><?php echo esc_html( $title ); ?></h1>
				<?php if ( $crumbs ) cm_breadcrumb( $crumbs ); ?>
			</div></div>
		<?php endif; ?>
	</div>
	<?php
	return true;
}

/**
 * "Uluslararası İletişim" bölümünü $source_post_id'deki (bkz. inc/acf-fields.php
 * group_cm_kurumsal, ID 7/277/278/279) intl_kisi_1..10 alanlarından render eder.
 * Hem Kurumsal sayfasının kendisinde HEM İletişim sayfasında (bizim kendi
 * iletişimimizin altında) çağrılır — aynı veri iki yerde gösteriliyor, ayrı bir
 * veri girişi YOK. "intl_gizle" işaretliyse veya hiç ülke girilmemişse hiçbir
 * şey basmaz (boşsa gizle).
 */
function cm_render_intl_contact_section( $source_post_id ) {
	if ( ! $source_post_id || ! function_exists( 'get_field' ) || get_field( 'intl_gizle', $source_post_id ) ) return;

	$entries = array();
	for ( $i = 1; $i <= 10; $i++ ) {
		$row = get_field( "intl_kisi_$i", $source_post_id );
		if ( $row && ! empty( $row['ulke'] ) ) $entries[] = $row;
	}
	if ( ! $entries ) return;
	?>
	<section class="intl-contact">
		<div class="eyebrow"><?php echo esc_html( cm__( 'kurumsal_uluslararasi_eyebrow' ) ); ?></div>
		<h2 class="h-md"><?php echo esc_html( cm__( 'kurumsal_uluslararasi_baslik' ) ); ?></h2>
		<p class="body-p" style="margin-top:10px; max-width:70ch;"><?php echo esc_html( cm__( 'kurumsal_uluslararasi_aciklama' ) ); ?></p>
		<div class="intl-contact-grid">
			<?php foreach ( $entries as $e ) : ?>
				<div class="intl-contact-card">
					<div class="intl-contact-country"><?php echo esc_html( $e['ulke'] ); ?></div>
					<?php if ( ! empty( $e['kisi_firma'] ) ) : ?><div class="intl-contact-name"><?php echo esc_html( $e['kisi_firma'] ); ?></div><?php endif; ?>
					<?php if ( ! empty( $e['telefon'] ) || ! empty( $e['eposta'] ) ) : ?>
						<div class="intl-contact-details">
							<?php if ( ! empty( $e['telefon'] ) ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $e['telefon'] ) ); ?>"><?php echo esc_html( $e['telefon'] ); ?></a><?php endif; ?>
							<?php if ( ! empty( $e['eposta'] ) ) : ?><a href="mailto:<?php echo esc_attr( $e['eposta'] ); ?>"><?php echo esc_html( $e['eposta'] ); ?></a><?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
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
 * Anasayfadaki üst kategori vitrini için: solda büyük fotoğraf (ACF `kategori_gorsel`),
 * sağda başlık+açıklama — eski sistemdeki büyük görselli kategori tanıtımının yeni
 * temadaki karşılığı. Fotoğraf YOK-VAR iki durumda da `.ph`/cm_render_thumb() deseni
 * (çapraz çizgili yer tutucu + köşe süsleri + "Görsel" etiketi) KASITLI olarak
 * KULLANILMIYOR — o desen ürün fotoğrafı SLOTU hissi veriyor (kullanıcı geri
 * bildirimi: gerçek bir fotoğraf konduğunda bile "buraya foto eklenecek" havası
 * kalıyor, üstelik object-fit:cover ile geniş 16:10 kutuya sığdırma ürün fotoğrafı
 * DIŞINDA (illüstrasyon/ikon tarzı) görseller kırpılınca kötü kesiliyor). Bunun
 * yerine: fotoğraf VARSA object-fit:contain ile TAMAMI (kırpılmadan) beyaz zemin
 * üzerinde gösterilir; YOKSA sade beyaz zemin + soluk/silik genel ikon (etiket/köşe
 * YOK) — "boşsa gizle" ilkesinin bu sabit-iki-yarı kart için "boşsa sakin bir
 * yer tutucu göster" karşılığı. `.cat-grid`/`.cat-card` (bkz. cm_urun_ailesi_card())
 * ile KASITLI olarak ayrı class'lar (`.cat-photo-grid`/`.cat-photo-card`) kullanılıyor
 * ki bu değişiklik o basit ikonlu ikinci ızgarayı bozmasın.
 */
function cm_category_card( $term ) {
	$photo    = function_exists( 'get_field' ) ? get_field( 'kategori_gorsel', $term ) : false;
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
	<a class="cat-photo-card reveal" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
		<div class="cat-photo-media<?php echo ( ! $photo || empty( $photo['url'] ) ) ? ' cat-photo-media-empty' : ''; ?>">
			<?php if ( $photo && ! empty( $photo['url'] ) ) : ?>
				<img src="<?php echo esc_url( $photo['url'] ); ?>" alt="<?php echo esc_attr( $term->name ); ?>">
			<?php else : ?>
				<?php cm_generic_icon( 44 ); ?>
			<?php endif; ?>
		</div>
		<div class="cat-photo-body">
			<h3><?php echo esc_html( $term->name ); ?></h3>
			<div class="sub"><?php echo $sub_names ? esc_html( implode( ' · ', $sub_names ) ) : esc_html( wp_trim_words( $term->description, 26, '…' ) ); ?></div>
			<?php if ( $count ) : ?><div class="count"><?php echo esc_html( $count . ' ' . cm__( 'urun_etiketi' ) ); ?></div><?php endif; ?>
		</div>
	</a>
	<?php
}

/**
 * Ürün listesi sayfalarında (page-urunler.php, taxonomy-makine_kategori.php,
 * taxonomy-urun_ailesi.php — hepsinde AYNI .category-main-col > .prod-grid yapısı)
 * "2 sütun / 3 sütun" görünüm seçici basar. Tıklama davranışı main.js'te — sadece
 * `.category-main-col`'a `grid-3` sınıfı ekleyip/kaldırıp tercihi localStorage'a
 * yazar, sayfa değişse de (başka bir kategoriye geçilse de) kalıcı kalır. Varsayılan
 * her zaman 2 sütun (kullanıcı isteği: "mevcut + 3'lü gibi").
 */
function cm_grid_toggle() {
	?>
	<div class="grid-toggle" role="group" aria-label="<?php echo esc_attr( cm__( 'grid_gorunum_aria' ) ); ?>">
		<button type="button" class="grid-toggle-btn active" data-cols="2" aria-pressed="true" aria-label="<?php echo esc_attr( cm__( 'grid_iki_sutun' ) ); ?>">
			<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="1" y="2" width="6" height="12" rx="0.5"/><rect x="9" y="2" width="6" height="12" rx="0.5"/></svg>
		</button>
		<button type="button" class="grid-toggle-btn" data-cols="3" aria-pressed="false" aria-label="<?php echo esc_attr( cm__( 'grid_uc_sutun' ) ); ?>">
			<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="1" y="2" width="4" height="12" rx="0.5"/><rect x="6" y="2" width="4" height="12" rx="0.5"/><rect x="11" y="2" width="4" height="12" rx="0.5"/></svg>
		</button>
	</div>
	<?php
}

/**
 * Kategori/alt kategori sayfalarında (taxonomy-makine_kategori.php) solda gösterilen
 * kategori ağacı. Herhangi bir derinlikte çalışır (üst kategori altında sınırsız
 * seviye olabilir, bkz. cm_category_sidebar_children()) — o an görüntülenen terimin
 * KÖKTEN kendisine kadar olan tüm ebeveynleri "open" (accordion açık) olarak
 * işaretlenir, terimin KENDİSİ "active" (vurgulu) olur; diğer dallar kapalı başlar
 * ama .cat-sidebar-toggle okuyla istenildiğinde açılabilir (bkz. main.js). 1 ve
 * 2 seviyeli (mevcut) kategorilerde ürettiği HTML, bu genelleştirmeden ÖNCEKİYLE
 * birebir aynıdır — sadece 3+ seviye eklendiğinde devreye giren bir davranış eklendi.
 */
function cm_category_sidebar( $current_term = null ) {
	$active_chain = array();
	if ( $current_term ) {
		$ancestors    = get_ancestors( $current_term->term_id, 'makine_kategori', 'taxonomy' ); // en yakın ebeveyn önce
		$active_chain = array_reverse( $ancestors ); // kökten aşağıya sıraya çevir
		$active_chain[] = $current_term->term_id; // en sona kendisini ekle
	}
	$top_cats = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'meta_value_num', 'meta_key' => CM_KATEGORI_SIRA_META, 'order' => 'ASC' ) );
	if ( is_wp_error( $top_cats ) ) return;
	?>
	<nav class="cat-sidebar" aria-label="<?php echo esc_attr( cm__( 'sidebar_aria' ) ); ?>">
		<a class="cat-sidebar-all<?php echo ! $current_term ? ' active' : ''; ?>" href="<?php echo esc_url( cm_translated_page_url( 'urunler', '/urunler/' ) ); ?>"><?php echo esc_html( cm__( 'sidebar_tum_urunler' ) ); ?></a>
		<ul>
			<?php foreach ( $top_cats as $top ) : cm_category_sidebar_row( $top, $active_chain ); endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Sidebar'da TEK bir ÜST kategori satırını (ikon + isim + toplam sayı, .cat-sidebar-icon-row)
 * ve varsa alt kategori listesini basar. $active_chain: cm_category_sidebar()'da hesaplanan,
 * o an görüntülenen terimin kökten kendisine kadar olan ebeveyn zinciri (dahil).
 */
function cm_category_sidebar_row( $top, $active_chain ) {
	$in_chain   = in_array( $top->term_id, $active_chain, true );
	$is_current = $active_chain && end( $active_chain ) === $top->term_id;
	$icon = function_exists( 'get_field' ) ? get_field( 'kategori_ikon', $top ) : false;
	// Doğrudan çocuklar HER ZAMAN çekilir (aktif olmasa bile) — aksi halde bir üst
	// kategorinin "has-children" durumu bilinmeden ok hiç basılmazdı.
	$children = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => $top->term_id, 'hide_empty' => false, 'orderby' => 'meta_value_num', 'meta_key' => CM_KATEGORI_SIRA_META, 'order' => 'ASC' ) );
	if ( is_wp_error( $children ) ) $children = array();
	$has_children = ! empty( $children );
	// get_term_children() TÜM alt ağacı (kaç seviye olursa olsun) özyinelemeli döndürür —
	// bu yüzden 3. (veya daha derin) bir seviye eklense bile toplam sayı doğru hesaplanır.
	$count = cm_category_total_count( $top, get_term_children( $top->term_id, 'makine_kategori' ) );
	?>
	<li class="cat-sidebar-item<?php echo $has_children ? ' has-children' : ''; ?><?php echo $in_chain ? ' open' : ''; ?>">
		<a class="cat-sidebar-icon-row<?php echo $is_current ? ' active' : ''; ?>" href="<?php echo esc_url( get_term_link( $top ) ); ?>">
			<span class="cat-sidebar-icon"><?php if ( $icon && ! empty( $icon['url'] ) ) : ?><img src="<?php echo esc_url( $icon['url'] ); ?>" alt="" width="18" height="18"><?php else : ?><?php cm_generic_icon( 18 ); ?><?php endif; ?></span>
			<span class="cat-sidebar-name"><?php echo esc_html( $top->name ); ?></span>
			<span class="cat-sidebar-count">(<?php echo (int) $count; ?>)</span>
		</a>
		<?php if ( $has_children ) : ?>
			<button type="button" class="cat-sidebar-toggle" aria-expanded="<?php echo $in_chain ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( cm__( 'sidebar_alt_kategori_aria' ), $top->name ) ); ?>">
				<svg viewBox="0 0 12 8" width="10" height="7" aria-hidden="true"><path d="M1 1l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
			</button>
			<ul class="cat-sidebar-children">
				<?php foreach ( $children as $child ) : cm_category_sidebar_children( $child, $active_chain ); endforeach; ?>
			</ul>
		<?php endif; ?>
	</li>
	<?php
}

/**
 * `.cat-sidebar-children` listesindeki TEK bir satırı basar — cm_category_sidebar_row()'un
 * ikonsuz/daha küçük yazılı sürümü. Kendi alt kategorileri varsa (3. seviye), kendini
 * YİNELEYEREK aynı şekilde bir ok butonu + iç içe `<ul class="cat-sidebar-children">`
 * basar — derinlik sınırı yok. Alt kategorisi OLMAYAN (yaprak) bir terim için ürettiği
 * HTML, bu özellik eklenmeden ÖNCEKİYLE birebir aynıdır (regresyon yok).
 */
function cm_category_sidebar_children( $term, $active_chain ) {
	$in_chain   = in_array( $term->term_id, $active_chain, true );
	$is_current = $active_chain && end( $active_chain ) === $term->term_id;
	$children   = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => $term->term_id, 'hide_empty' => false, 'orderby' => 'meta_value_num', 'meta_key' => CM_KATEGORI_SIRA_META, 'order' => 'ASC' ) );
	if ( is_wp_error( $children ) ) $children = array();
	$has_children = ! empty( $children );
	$count = $has_children ? cm_category_total_count( $term, get_term_children( $term->term_id, 'makine_kategori' ) ) : (int) $term->count;

	$li_classes = array();
	if ( $has_children ) $li_classes[] = 'has-children';
	if ( $in_chain ) $li_classes[] = 'open';
	?>
	<li<?php echo $li_classes ? ' class="' . esc_attr( implode( ' ', $li_classes ) ) . '"' : ''; ?>>
		<a class="<?php echo $is_current ? 'active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?> (<?php echo (int) $count; ?>)</a>
		<?php if ( $has_children ) : ?>
			<button type="button" class="cat-sidebar-toggle" aria-expanded="<?php echo $in_chain ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( cm__( 'sidebar_alt_kategori_aria' ), $term->name ) ); ?>">
				<svg viewBox="0 0 12 8" width="10" height="7" aria-hidden="true"><path d="M1 1l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
			</button>
			<ul class="cat-sidebar-children">
				<?php foreach ( $children as $child ) : cm_category_sidebar_children( $child, $active_chain ); endforeach; ?>
			</ul>
		<?php endif; ?>
	</li>
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
 * Haber kartı (bkz. page-haberler.php listeleme sayfası + front-page.php "Haberler"
 * bölümü) — cm_product_card() ile AYNI görsel dil (.prod-card), ayrı CSS gerekmez.
 * Tarih + kısa özet (varsa Alıntı, yoksa içerikten otomatik kısaltma).
 */
function cm_news_card( $post_id ) {
	$teaser = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 18, '…' );
	?>
	<a class="prod-card reveal" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
		<?php cm_render_thumb( $post_id, '', 'medium_large' ); ?>
		<div class="news-date"><?php echo esc_html( get_the_date( '', $post_id ) ); ?></div>
		<h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
		<?php if ( $teaser ) : ?><div class="spec"><?php echo esc_html( $teaser ); ?></div><?php endif; ?>
		<div class="go"><?php echo esc_html( cm__( 'devamini_oku' ) ); ?></div>
	</a>
	<?php
}

/**
 * "Ürün Ailesi" kartı — get_terms() sonucundaki bir WP_Term'i (urun_ailesi taksonomisi)
 * cm_category_card() ile AYNI görsel dilde (.cat-card) basar; hiyerarşi/ikon alanı
 * olmadığı için o kartın basitleştirilmiş hali — ayrı CSS gerektirmez.
 */
function cm_urun_ailesi_card( $term ) {
	?>
	<a class="cat-card reveal" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
		<div class="cat-icon"><?php cm_generic_icon( 34 ); ?></div>
		<h3><?php echo esc_html( $term->name ); ?></h3>
		<?php if ( $term->description ) : ?><div class="sub"><?php echo esc_html( wp_trim_words( $term->description, 10, '…' ) ); ?></div><?php endif; ?>
		<?php if ( $term->count ) : ?><div class="count"><?php echo esc_html( $term->count . ' ' . cm__( 'urun_etiketi' ) ); ?></div><?php endif; ?>
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
	$top_cats = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'meta_value_num', 'meta_key' => CM_KATEGORI_SIRA_META, 'order' => 'ASC' ) );
	if ( is_wp_error( $top_cats ) || ! $top_cats ) return;
	?>
	<div class="megamenu">
		<ul class="megamenu-list">
			<?php foreach ( $top_cats as $top ) :
				$icon = function_exists( 'get_field' ) ? get_field( 'kategori_ikon', $top ) : false;
				$children = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => $top->term_id, 'hide_empty' => false, 'orderby' => 'meta_value_num', 'meta_key' => CM_KATEGORI_SIRA_META, 'order' => 'ASC' ) );
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
 * Bir sayfanın alt sayfalarını kart olarak basar — page.php iki AYRI yerden
 * çağırır: Kurumsal (ve genelde) içeriğin HEMEN ALTINDA, İletişim'de ise
 * Uluslararası İletişim bloğunun ALTINDA (kullanıcı isteği: İletişim'e
 * eklenen Gizlilik/Çerez Politikası kartları sayfanın en altında dursun).
 * $use_icon_cards true'ysa ikonlu "corp-grid" (Kurumsal/İletişim ailesi),
 * false'sa fotoğraflı "prod-grid" (diğer sayfalar) kullanılır.
 */
function cm_render_page_children_grid( $children, $use_icon_cards ) {
	if ( ! $children ) return;
	if ( $use_icon_cards ) : ?>
		<div class="corp-grid" style="margin-top:40px;">
			<?php foreach ( $children as $cm_child ) :
				$cm_child_id  = $cm_child->ID;
				$cm_tr_child  = function_exists( 'pll_get_post' ) ? pll_get_post( $cm_child_id, 'tr' ) : $cm_child_id;
				$cm_teaser    = has_excerpt( $cm_child_id ) ? get_the_excerpt( $cm_child_id ) : wp_trim_words( wp_strip_all_tags( $cm_child->post_content ), 18, '…' );
			?>
				<a class="corp-card reveal" href="<?php echo esc_url( get_permalink( $cm_child_id ) ); ?>">
					<div class="corp-card-icon"><?php cm_kurumsal_child_icon( (int) $cm_tr_child ); ?></div>
					<h3><?php echo esc_html( get_the_title( $cm_child_id ) ); ?></h3>
					<p><?php echo esc_html( $cm_teaser ); ?></p>
					<div class="go"><?php echo esc_html( cm__( 'detaylari_gor' ) ); ?></div>
				</a>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="prod-grid" style="margin-top:40px;">
			<?php foreach ( $children as $cm_child ) : ?>
				<a class="prod-card reveal" href="<?php echo esc_url( get_permalink( $cm_child ) ); ?>">
					<?php if ( has_post_thumbnail( $cm_child ) ) : ?>
						<div class="ph"><?php echo get_the_post_thumbnail( $cm_child, 'cm-card' ); ?></div>
					<?php endif; ?>
					<h3><?php echo esc_html( get_the_title( $cm_child ) ); ?></h3>
					<div class="go"><?php echo esc_html( cm__( 'detaylari_gor' ) ); ?></div>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif;
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
		// Gizlilik Politikası (kalkan+onay) ve Çerez Politikası (kurabiye deseni) —
		// bkz. inc/cookie-consent.php kurulum notu, Kurumsal'ın alt sayfaları.
		916 => '<path d="M17 5l10 4v8c0 7-4.5 11-10 12-5.5-1-10-5-10-12V9z"/><path d="M12.5 17.5l3 3 6-6.5"/>',
		921 => '<circle cx="17" cy="17" r="11"/><circle cx="13" cy="13" r="1.4" fill="currentColor" stroke="none"/><circle cx="21.5" cy="14" r="1.4" fill="currentColor" stroke="none"/><circle cx="14" cy="21.5" r="1.4" fill="currentColor" stroke="none"/><circle cx="21" cy="21" r="1.4" fill="currentColor" stroke="none"/>',
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
