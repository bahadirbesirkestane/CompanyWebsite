<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * "Yönetici Kılavuzu" admin sayfası: sitenin günlük içerik yönetimini (ürün,
 * kategori, sayfa, çeviri vb.) A'dan Z'ye anlatan, admin panelinin İÇİNDE
 * yaşayan bir kılavuz. Kullanıcı isteği: ileride başka biri yönetici olursa,
 * dışarıda ayrı bir belge aramadan doğrudan panelden takip edebilsin.
 *
 * ACF'in ücretsiz sürümünde Options Page olmadığı için (bkz. inc/acf-fields.php
 * üstündeki not) içerik doğrudan wp_options'ta ('cm_admin_guide_content') WP'nin
 * kendi zengin metin düzenleyicisiyle (wp_editor) tutuluyor — admin bu sayfayı
 * doğrudan panelden düzenleyip güncelleyebilir, ayrı bir dosya yönetimi
 * gerekmez. Buna EK olarak (kullanıcı isteği: "yükleme alanı da yapabiliriz")
 * ayrı, opsiyonel bir dosya yükleme alanı var (ör. PDF/Word olarak dışarıda
 * hazırlanmış bir kopya) — WP'nin standart medya yükleyicisiyle seçilir,
 * seçilen medya kütüphanesi ID'si 'cm_admin_guide_file_id' seçeneğinde tutulur.
 */

function cm_admin_guide_default_content() {
	return <<<'HTML'
<p>Bu sayfa, bu sitenin (Innovas) wp-admin panelini günlük olarak yönetmek için A'dan Z'ye bir kılavuzdur. Sırasıyla okuyarak veya ihtiyacınız olan başlığa atlayarak takip edebilirsiniz. Bu sayfanın kendisi de panelden düzenlenebilir — aşağıdaki "Kılavuzu Düzenle" bölümünden güncelleyebilirsiniz.</p>

<h2>1) Panele Giriş</h2>
<p>Tarayıcıdan sitenizin adresinin sonuna <code>/wp-admin/</code> ekleyerek giriş ekranına ulaşırsınız. Kullanıcı adı/şifrenizle giriş yaptıktan sonra solda "Panel" menüsünü görürsünüz — bu kılavuzdaki tüm bölümler bu sol menüden erişilir.</p>

<h2>2) Ürünler</h2>
<p>Sol menüde <strong>Ürünler</strong> — sitedeki tüm makineler burada listelenir.</p>
<h3>Yeni ürün ekleme / var olanı düzenleme</h3>
<ul>
<li><strong>Öne Çıkan Görsel</strong> (sağ sütun): ürünün vitrin/kapak fotoğrafı, kart görünümünde ve galerinin ilk fotoğrafı olarak kullanılır.</li>
<li><strong>Kısa Özet</strong>: ürün kartlarında başlığın altında görünen tek satırlık özet (örn. "450 kg/saat · 7.5 kW").</li>
<li><strong>Kısa Açıklama</strong>: detay sayfasında galerinin yanında görünen tanıtım paragrafı.</li>
<li><strong>Ek Görsel 1-8</strong>: detay sayfasındaki galeriye eklenen ek fotoğraflar — hepsini doldurmanız gerekmez, boş bırakılanlar görünmez. Seçim penceresinde iki sekme vardır: "Bu yazıya yüklenenler" (bu ürünün tüm dillerdeki kendi görselleri) ve "Medya Kütüphanesi" (sitedeki tüm görseller).</li>
<li><strong>Teknik Özellikler</strong>: her satıra bir özellik, <em>Özellik Adı: Değer</em> formatında (örn. "Kapasite: 450 kg/saat"). Her satır, ürün sayfasında ayrı bir tablo satırına dönüşür.</li>
<li><strong>PDF Katalog</strong>: bu makineye özel teknik broşür. Yüklenince ürün sayfasında "Dokümanlar" sekmesi otomatik belirir.</li>
<li><strong>YouTube Video Linki</strong> (opsiyonel): girilirse ürün sayfasında ayrı bir "Video" sekmesi otomatik oluşur.</li>
<li><strong>CTA Butonu Metni / Linki</strong> (opsiyonel): "Bu Makine İçin Teklif İste" gibi özel bir buton eklemek isterseniz kullanın. Metin boşsa buton hiç görünmez; link boşsa buton otomatik olarak sitenin WhatsApp numarasına (o da yoksa İletişim sayfasına) yönlendirir.</li>
<li><strong>Öne Çıkan Ürün</strong>: işaretlenirse bu ürün anasayfadaki "Öne Çıkan Makineler" bölümünde gösterilir.</li>
<li>Sağ sütundaki <strong>Kategoriler</strong> kutusundan ürünün ait olduğu kategoriyi/alt kategoriyi işaretleyin.</li>
</ul>

<h3>Kategoriler</h3>
<p>Ürünler → <strong>Kategoriler</strong>'den yeni kategori/alt kategori ekleyebilir, mevcutları düzenleyebilirsiniz. Alt kategori eklerken "Üst Kategori" alanından bağlı olduğu kategoriyi seçmeniz yeterli — istediğiniz kadar derinlikte (kategori içinde alt kategori içinde alt kategori...) açabilirsiniz.</p>

<h3>Sıralama</h3>
<p>Ürünler → <strong>Sıralama</strong>: ürünlerin sitedeki (kategori sayfaları ve "Tüm Ürünler" sayfası dahil) gösterim sırasını sürükle-bırak ile değiştirirsiniz.</p>
<p>Ürünler → <strong>Kategori Sıralaması</strong>: kategorilerin (ve alt kategorilerin) menüde/sayfalarda görünme sırasını aynı şekilde sürükle-bırak ile değiştirirsiniz. Bir üst kategoriye tıklayarak onun alt kategorilerinin sırasını da ayrıca düzenleyebilirsiniz.</p>
<p><em>Not:</em> Bu sıralamaları TR dilinde bir kez yaparsınız — sistem aynı sırayı otomatik olarak diğer dillere (EN/RU/ES/AR) de uygular, her dil için ayrı ayrı yapmanıza gerek yoktur.</p>

<h2>3) Kataloglar</h2>
<p>Sol menüde <strong>Kataloglar</strong> — genel (ürüne özel olmayan) PDF katalog dosyalarınızı buradan yönetirsiniz. Her katalog kaydının kendi PDF dosyası dil başına ayrı ayrı yüklenir (yani EN/RU/ES/AR için de o dildeki PDF'i ayrı seçmeniz gerekir — ürün fotoğraflarının aksine burada dosyalar dil başına paylaşılmaz).</p>

<h2>4) Referans Firmalar</h2>
<p>Sol menüde <strong>Referanslar</strong> — anasayfadaki "bize güvenen firmalar" logo şeridini besler. Sadece başlık (firma adı) ve Öne Çıkan Görsel (logo) yeterlidir, ayrı bir içerik yazmanıza gerek yoktur.</p>

<h2>5) Haberler</h2>
<p>Sol menüde <strong>Haberler</strong> — duyuru/etkinlik/fuar gibi haberleriniz için.</p>
<ul>
<li><strong>Öne Çıkan Görsel</strong>: hem haber kartında hem haberin kendi sayfasının üst banner'ında kullanılır.</li>
<li><strong>İçerik</strong>: normal WordPress editörü — yazı yazabilir, fotoğraf ekleyebilirsiniz.</li>
<li><strong>Video eklemek için</strong>: bir YouTube linkini içerik editöründe kendi satırına yapıştırmanız yeterli, WordPress otomatik olarak videoyu gömer (embed).</li>
</ul>

<h2>6) Sayfalar</h2>
<p>Sol menüde <strong>Sayfalar</strong> — Anasayfa, Kurumsal (ve alt sayfaları: Hakkımızda, Misyon &amp; Vizyon, Üretim Tesisi, Kalite &amp; Sertifikalar, Kariyer), İletişim gibi sabit sayfalar buradadır. Her sayfanın kendi metnini düzenlemek için normal WordPress editörünü, sayfaya özel görselleri (Banner Görseli gibi) ise Öne Çıkan Görsel'in hemen altındaki kutuları kullanın.</p>

<h3>İletişim sayfası</h3>
<p>İletişim sayfasını düzenlerken aşağı kaydırdığınızda şu özel alanları görürsünüz:</p>
<ul>
<li><strong>Telefon / WhatsApp / E-posta / Adres / Çalışma Saatleri kartları</strong>: her biri için başlık ve değer girebilirsiniz. Boş bırakırsanız site genelindeki (Özelleştir'deki) değer kullanılır; o da boşsa kart hiç görünmez.</li>
<li><strong>İletişim Formunu Gizle</strong>: işaretlerseniz "Bize Ulaşın" formu bu sayfada hiç görünmez, harita (varsa) tek başına tam genişlikte kalır. İhtiyaç olursa işareti kaldırarak formu tekrar açabilirsiniz.</li>
</ul>

<h3>Uluslararası İletişim</h3>
<p>Kurumsal ve İletişim sayfalarının ikisinde de düzenlenebilen "Uluslararası İletişim" bölümü — farklı ülkelerdeki temsilcilerinizi (Ülke, Ad Soyad/Firma, Telefon, E-posta) 10 kişiye kadar girebilirsiniz. "Ülke" alanı boş bırakılan satırlar sitede hiç görünmez. "Bu Bölümü Gizle" işaretlenirse, kişi girilmiş olsa bile bölüm tamamen kapanır.</p>

<h2>7) Görünüm → Özelleştir (site geneli ayarlar)</h2>
<p>Sol menüde Görünüm → <strong>Özelleştir</strong>'de, TÜM dillerde aynı kalması gereken (dile göre değişmeyen) site geneli bilgiler yönetilir:</p>
<h3>"İletişim &amp; WhatsApp" bölümü</h3>
<ul>
<li>Adres, Telefon, E-posta, Çalışma Saatleri — footer'da ve İletişim sayfasında (sayfanın kendi kartı boşsa) gösterilir.</li>
<li><strong>WhatsApp Numarası</strong>: başında ülke koduyla girin (örn. 905551234567). Header'daki WhatsApp butonu bu numaraya yönlendirir; boş bırakılırsa yerine "Teklif İste" butonu görünür.</li>
<li><strong>Google Haritalar Yerleştirme (Embed) URL'si</strong>: Google Haritalar'da adresinizi arayın → Paylaş → "Harita yerleştir" sekmesi → verilen kodun içindeki <code>src="..."</code> adresini buraya yapıştırın. Boş bırakılırsa İletişim sayfasında harita hiç görünmez.</li>
<li><strong>Sosyal medya linkleri</strong> (Facebook, Instagram, LinkedIn, YouTube, X/Twitter): header'da ve footer'daki ikonları besler. Boş bırakılan platformun ikonu hiç gösterilmez.</li>
</ul>
<h3>"Analitik &amp; Arama Motoru Doğrulama" bölümü</h3>
<ul>
<li><strong>Google Analytics (GA4) Ölçüm Kimliği</strong>: "G-" ile başlayan kod.</li>
<li><strong>Google Tag Manager Kapsayıcı Kimliği</strong>: "GTM-" ile başlayan kod. Genelde GA4 veya GTM'den SADECE BİRİNİ kullanın, ikisini birden girmeyin.</li>
<li><strong>Google Search Console Doğrulama Kodu</strong>: search.google.com/search-console → Mülk Ekle → "HTML etiketi" yöntemiyle aldığınız kod.</li>
<li>Bu alanların hepsi boşsa sitede hiçbir izleme/etiket kodu eklenmez.</li>
</ul>

<h2>8) Çerez / KVKK Sayfaları</h2>
<p>Sayfalar listesinde "Gizlilik Politikası" ve "Çerez Politikası" adında sayfalar bulunur. Bu sayfaların aktif/pasif olması ayrı bir anahtardan DEĞİL, doğrudan o sayfanın <strong>Yayınla / Taslak</strong> durumundan yönetilir: Çerez Politikası sayfası "Yayınlandı" durumundaysa, sitede alt tarafta çerez onay bandı otomatik görünür; sayfayı "Taslak"a alırsanız banner da kaybolur. Banner'daki yazıları (buton metinleri, açıklama metni vb.) değiştirmek için Diller → Dize Çevirisi ekranında "çerez" kelimesiyle arama yapabilirsiniz (aşağıdaki 9. bölüme bakın).</p>

<h2>9) Diller (Çoklu Dil Yönetimi)</h2>
<p>Site 5 dilde çalışır: Türkçe (varsayılan), İngilizce, Rusça, İspanyolca, Arapça. Dil yönetimi Polylang eklentisiyle yapılır.</p>
<h3>Var olan bir ürün/sayfanın çevirisini düzenleme</h3>
<p>Herhangi bir ürünü/sayfayı düzenlerken sağ sütunda küçük bayraklardan oluşan bir "Diller" kutusu görürsünüz. Zaten çevrilmiş olan dillerin bayrağına tıklayarak o dildeki karşılığını direkt düzenleyebilirsiniz. Henüz çevrilmemiş bir dilin yanındaki "+" işaretine tıklarsanız, o dil için yeni bir kopya oluşturup çeviriyi girebilirsiniz.</p>
<h3>Arayüz metinlerini çevirme (buton yazıları, başlıklar vb.)</h3>
<p>Sayfa/ürün içeriği DIŞINDA kalan sabit arayüz metinleri (örn. "Sepete Ekle", çerez banner'ı yazıları, "Bize Ulaşın" gibi başlıklar) Diller → <strong>Dize Çevirisi</strong> ekranından yönetilir. Arama kutusuna metnin bir kısmını yazıp bulun, sağdaki kutulara her dil için çeviriyi girin, en alttan "Değişiklikleri Kaydet"e basın.</p>
<p><strong>Önemli:</strong> Yeni bir ürün/sayfa eklerken sadece Türkçe ile yetinmeyin — aynı içeriği diğer 4 dilde de (yukarıdaki "+" ile) oluşturup çevirisini girin, aksi halde o dildeki ziyaretçi ilgili içeriği göremez.</p>

<h2>10) İletişim Formu</h2>
<p>İletişim sayfasındaki form, Contact Form 7 eklentisiyle çalışır. Sol menüde <strong>İletişim Formları</strong>'ndan form alanlarını (hangi bilgiler isteniyor), gönderilen mesajın hangi e-posta adresine gideceğini ve otomatik onay/red mesajlarını düzenleyebilirsiniz. Her dilin kendi ayrı formu vardır (form listesinde dil etiketiyle görünür) — bir dildeki formu değiştirmek diğer dilleri etkilemez.</p>

<h2>11) Genel İpuçları</h2>
<ul>
<li><strong>Görsel boyutu</strong>: yüklemeden önce fotoğrafları çok büyük dosya boyutlarından (birkaç MB'tan fazla) kaçının — site daha hızlı açılır. 1600px genişlik genellikle yeterlidir.</li>
<li><strong>Medya Kütüphanesi</strong> (sol menü → Medya): sitede kullanılan tüm görsel/PDF dosyalarını buradan görebilir, arayabilir, silebilirsiniz. Bir dosyayı silmeden önce başka bir yerde kullanılmadığından emin olun.</li>
<li><strong>"Boşsa gizle" ilkesi</strong>: bu sitede pek çok bölüm (harita, video sekmesi, PDF sekmesi, Uluslararası İletişim, iletişim kartları vb.) ilgili alan boş bırakıldığında sitede otomatik olarak hiç görünmez — yarım/boş bir görünüm bırakmaktan çekinmeyin, doldurmadığınız alan kendiliğinden gizlenir.</li>
</ul>

<h2>12) Sık Karşılaşılan Durumlar</h2>
<ul>
<li><strong>"Yeni eklediğim ürün/sayfa diğer dillerde görünmüyor"</strong>: o dilde henüz bir çeviri oluşturmadınız demektir — ilgili ürünü/sayfayı açıp sağdaki Diller kutusundan "+" ile o dilde de oluşturun.</li>
<li><strong>"Çerez banner'ı sitede hiç çıkmıyor"</strong>: Sayfalar → Çerez Politikası sayfasının Taslak durumunda olup olmadığını kontrol edin, Yayınla'ya basın.</li>
<li><strong>"Harita görünmüyor"</strong>: Görünüm → Özelleştir → İletişim &amp; WhatsApp → Google Haritalar Embed URL'si alanının dolu olduğundan emin olun.</li>
<li><strong>"Sosyal medya ikonu görünmüyor"</strong>: o platformun linki Özelleştir'de boş bırakılmış olabilir.</li>
</ul>
HTML;
}

function cm_admin_guide_page() {
	$hook = add_menu_page(
		'Yönetici Kılavuzu',
		'Yönetici Kılavuzu',
		'manage_options',
		'cm-yonetici-kilavuzu',
		'cm_render_admin_guide_page',
		'dashicons-book-alt',
		3
	);
	add_action( 'admin_enqueue_scripts', function ( $current_hook ) use ( $hook ) {
		if ( $current_hook !== $hook ) return;
		wp_enqueue_media();
	} );
}
add_action( 'admin_menu', 'cm_admin_guide_page' );

function cm_admin_guide_handle_save() {
	if ( empty( $_POST['cm_admin_guide_save'] ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;
	check_admin_referer( 'cm_admin_guide_save', 'cm_admin_guide_nonce' );

	$content = isset( $_POST['cm_admin_guide_content'] ) ? wp_unslash( $_POST['cm_admin_guide_content'] ) : '';
	update_option( 'cm_admin_guide_content', wp_kses_post( $content ) );

	$file_id = isset( $_POST['cm_admin_guide_file_id'] ) ? absint( $_POST['cm_admin_guide_file_id'] ) : 0;
	update_option( 'cm_admin_guide_file_id', $file_id );

	wp_safe_redirect( add_query_arg( array( 'page' => 'cm-yonetici-kilavuzu', 'kaydedildi' => 1 ), admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_init', 'cm_admin_guide_handle_save' );

function cm_render_admin_guide_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$content = get_option( 'cm_admin_guide_content' );
	if ( $content === false || $content === '' ) {
		$content = cm_admin_guide_default_content();
	}
	$file_id  = (int) get_option( 'cm_admin_guide_file_id' );
	$file_url = $file_id ? wp_get_attachment_url( $file_id ) : '';
	$file_name = $file_id ? basename( get_attached_file( $file_id ) ) : '';
	?>
	<style>
		.cm-admin-guide-content h2 { margin-top: 28px; border-bottom: 1px solid #dcdcde; padding-bottom: 6px; }
		.cm-admin-guide-content h3 { margin-top: 18px; }
		.cm-admin-guide-content ul, .cm-admin-guide-content ol { margin-left: 20px; }
		.cm-admin-guide-content li { margin-bottom: 6px; }
		.cm-admin-guide-content code { background: #f0f0f1; padding: 1px 5px; border-radius: 3px; }
	</style>
	<div class="wrap">
		<h1>Yönetici Kılavuzu</h1>

		<?php if ( ! empty( $_GET['kaydedildi'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p>Kaydedildi.</p></div>
		<?php endif; ?>

		<div class="postbox" style="padding:20px; margin-top:16px; max-width:900px;">
			<?php if ( $file_id && $file_url ) : ?>
				<p>
					<strong>İndirilebilir dosya:</strong>
					<a href="<?php echo esc_url( $file_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $file_name ); ?></a>
				</p>
			<?php else : ?>
				<p><em>Henüz indirilebilir bir dosya (PDF/Word vb.) yüklenmedi — aşağıdaki içerik doğrudan bu sayfada okunabilir. İsterseniz "Kılavuzu Düzenle" bölümünden ayrıca bir dosya da ekleyebilirsiniz.</em></p>
			<?php endif; ?>
			<div class="cm-admin-guide-content">
				<?php echo wp_kses_post( $content ); ?>
			</div>
		</div>

		<h2 style="margin-top:32px;">Kılavuzu Düzenle</h2>
		<form method="post" action="">
			<?php wp_nonce_field( 'cm_admin_guide_save', 'cm_admin_guide_nonce' ); ?>

			<?php
			wp_editor(
				$content,
				'cm_admin_guide_editor',
				array(
					'textarea_name' => 'cm_admin_guide_content',
					'media_buttons' => true,
					'textarea_rows' => 28,
					'tinymce'       => true,
				)
			);
			?>

			<h3 style="margin-top:24px;">İndirilebilir Dosya (opsiyonel)</h3>
			<p class="description">Dışarıda hazırladığınız bir PDF/Word kopyasını buradan seçip yükleyebilirsiniz. Boş bırakırsanız kılavuz sadece yukarıdaki metin olarak kalır.</p>
			<p>
				<input type="hidden" id="cm_admin_guide_file_id" name="cm_admin_guide_file_id" value="<?php echo esc_attr( $file_id ); ?>">
				<span id="cm_admin_guide_file_label"><?php echo $file_name ? esc_html( $file_name ) : 'Dosya seçilmedi.'; ?></span>
				<br><br>
				<button type="button" class="button" id="cm_admin_guide_file_pick">Dosya Seç / Değiştir</button>
				<button type="button" class="button" id="cm_admin_guide_file_clear" <?php echo $file_id ? '' : 'style="display:none;"'; ?>>Dosyayı Kaldır</button>
			</p>

			<p style="margin-top:24px;">
				<button type="submit" name="cm_admin_guide_save" value="1" class="button button-primary button-large">Kaydet</button>
			</p>
		</form>
	</div>

	<script>
	jQuery(function ($) {
		var frame;
		$('#cm_admin_guide_file_pick').on('click', function (e) {
			e.preventDefault();
			if (frame) { frame.open(); return; }
			frame = wp.media({
				title: 'Kılavuz Dosyası Seç',
				button: { text: 'Bu dosyayı kullan' },
				multiple: false
			});
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				$('#cm_admin_guide_file_id').val(att.id);
				$('#cm_admin_guide_file_label').text(att.filename || att.title);
				$('#cm_admin_guide_file_clear').show();
			});
			frame.open();
		});
		$('#cm_admin_guide_file_clear').on('click', function (e) {
			e.preventDefault();
			$('#cm_admin_guide_file_id').val('');
			$('#cm_admin_guide_file_label').text('Dosya seçilmedi.');
			$(this).hide();
		});
	});
	</script>
	<?php
}
