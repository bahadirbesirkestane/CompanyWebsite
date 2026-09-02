<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Çerez onay bandı — AYRI bir Özelleştir anahtarı YOK, kasıtlı olarak: aktiflik
 * doğrudan "Çerez Politikası" sayfasının kendi Taslak/Yayınla durumuna bağlı
 * (bkz. cm_cerez_banner_aktif()). Sayfa taslakken (varsayılan durum) bu
 * dosyadaki HİÇBİR ŞEY basılmaz — ne banner, ne footer'daki "Çerez Ayarları"/
 * "Gizlilik Politikası" linkleri, ne de inc/seo.php'deki Analitik (GA/GTM)
 * script gating'i devreye girer. Admin Sayfalar → Çerez Politikası → Yayınla
 * dediği AN her şey otomatik etkinleşir — Gizlilik Politikası (KVKK
 * Aydınlatma Metni) ise TAMAMEN AYRI, kendi Taslak/Yayınla durumuyla
 * yönetilir (sadece kendi linkinin görünüp görünmediğini belirler, banner'ı
 * etkilemez).
 *
 * Aktifken: birçok kurumsal sitede görülen 3 katmanlı desen (bkz. Bosch
 * Rexroth Türkiye referansı) — "Tümünü Kabul Et" / "Sadece Zorunlu Olanlar" /
 * "Ayarlar" (kategori bazlı aç-kapa: Zorunlu her zaman açık/kilitli, Analitik
 * ziyaretçi seçer). Metinler (başlık/açıklama/buton/kategori) cm__() üzerinden
 * geliyor — Diller → Dize Çevirisi'nden 5 dilde de düzenlenebilir (bkz.
 * inc/strings.php, "cerez_..." anahtarları).
 */

function cm_cerez_banner_aktif() {
	return (bool) cm_cerez_legal_page_url( 'cerez-politikasi' );
}

/**
 * Gizlilik Politikası / Çerez Politikası sayfa URL'si — sayfa YAYINLANMAMIŞSA
 * (taslaksa) boş döner, çağıran taraf o durumda linki hiç basmaz. Slug'lar
 * sabit: gizlilik-politikasi / cerez-politikasi (Kurumsal sayfasının altında
 * alt sayfa olarak oluşturulmuşlardır, bkz. bu dosyanın altındaki kurulum
 * notu — TR sayfaların post_name'i bu olmalı, üst-alt ilişkisi cm_translated_
 * page()'in "name" ile aramasını ETKİLEMEZ).
 */
function cm_cerez_legal_page_url( $slug ) {
	$page = cm_translated_page( $slug );
	return $page ? get_permalink( $page ) : '';
}

add_action( 'wp_footer', function () {
	if ( ! cm_cerez_banner_aktif() ) return;
	?>
	<div id="cm-cerez-banner" class="cerez-banner" hidden>
		<div class="cerez-banner-inner">
			<div class="cerez-banner-text">
				<strong><?php echo esc_html( cm__( 'cerez_baslik' ) ); ?></strong>
				<p><?php echo esc_html( cm__( 'cerez_aciklama' ) ); ?></p>
				<?php cm_cerez_legal_links(); ?>
			</div>
			<div class="cerez-banner-actions">
				<button type="button" class="btn btn-outline" data-cerez-action="ayarlar"><?php echo esc_html( cm__( 'cerez_ayarlar' ) ); ?></button>
				<button type="button" class="btn btn-outline" data-cerez-action="zorunlu"><?php echo esc_html( cm__( 'cerez_sadece_zorunlu' ) ); ?></button>
				<button type="button" class="btn btn-primary" data-cerez-action="tumu"><?php echo esc_html( cm__( 'cerez_tumunu_kabul' ) ); ?></button>
			</div>
		</div>

		<div class="cerez-settings" hidden>
			<h3><?php echo esc_html( cm__( 'cerez_ayarlar_baslik' ) ); ?></h3>
			<p><?php echo esc_html( cm__( 'cerez_ayarlar_aciklama' ) ); ?></p>

			<div class="cerez-category">
				<div class="cerez-category-head">
					<span class="cerez-category-title"><?php echo esc_html( cm__( 'cerez_zorunlu_baslik' ) ); ?></span>
					<span class="cerez-toggle-locked"><?php echo esc_html( cm__( 'cerez_her_zaman_acik' ) ); ?></span>
				</div>
				<p><?php echo esc_html( cm__( 'cerez_zorunlu_aciklama' ) ); ?></p>
			</div>

			<div class="cerez-category">
				<div class="cerez-category-head">
					<span class="cerez-category-title"><?php echo esc_html( cm__( 'cerez_analitik_baslik' ) ); ?></span>
					<label class="cerez-switch">
						<input type="checkbox" data-cerez-category="analytics">
						<span></span>
					</label>
				</div>
				<p><?php echo esc_html( cm__( 'cerez_analitik_aciklama' ) ); ?></p>
			</div>

			<div class="cerez-settings-actions">
				<button type="button" class="btn btn-outline" data-cerez-action="geri"><?php echo esc_html( cm__( 'cerez_geri' ) ); ?></button>
				<button type="button" class="btn btn-primary" data-cerez-action="kaydet"><?php echo esc_html( cm__( 'cerez_kaydet' ) ); ?></button>
			</div>
		</div>
	</div>
	<?php
}, 20 );

/**
 * Banner içindeki "Gizlilik Politikası · Çerez Politikası" linkleri — sadece
 * gerçekten yayınlanmış olanlar basılır (bkz. cm_cerez_legal_page_url()).
 */
function cm_cerez_legal_links() {
	$links = array();
	$gizlilik_url = cm_cerez_legal_page_url( 'gizlilik-politikasi' );
	$cerez_url    = cm_cerez_legal_page_url( 'cerez-politikasi' );
	if ( $gizlilik_url ) $links[] = '<a href="' . esc_url( $gizlilik_url ) . '">' . esc_html( cm__( 'gizlilik_politikasi_baglanti' ) ) . '</a>';
	if ( $cerez_url ) $links[] = '<a href="' . esc_url( $cerez_url ) . '">' . esc_html( cm__( 'cerez_politikasi_baglanti' ) ) . '</a>';
	if ( $links ) echo '<p class="cerez-banner-links">' . implode( ' · ', $links ) . '</p>'; // phpcs:ignore -- içerik yukarıda esc edildi
}

add_action( 'wp_enqueue_scripts', function () {
	if ( ! cm_cerez_banner_aktif() ) return;
	wp_enqueue_script( 'cikolata-makine-cookie-consent', CM_THEME_URI . '/assets/js/cookie-consent.js', array(), CM_THEME_VERSION, true );
} );
