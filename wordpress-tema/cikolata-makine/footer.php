<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<footer class="site-footer">
	<div class="footer-inner">
		<div class="footer-grid">
			<div>
				<div class="logo-mark" style="color:var(--footer-text);margin-bottom:14px;"><?php bloginfo( 'name' ); ?></div>
				<p style="max-width:32ch;"><?php echo esc_html( cm__( 'footer_slogan' ) ); ?></p>
				<?php cm_social_links( array( 'facebook', 'instagram', 'linkedin', 'youtube', 'twitter' ), 'footer-social' ); ?>
			</div>

			<div>
				<h4><?php echo esc_html( cm__( 'footer_hizli_linkler' ) ); ?></h4>
				<?php if ( has_nav_menu( 'footer_1' ) ) : ?>
					<?php wp_nav_menu( array( 'theme_location' => 'footer_1', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1 ) ); ?>
				<?php else : ?>
					<a href="<?php echo esc_url( cm_translated_page_url( 'urunler', '/urunler/' ) ); ?>"><?php echo esc_html( cm_urunler_label() ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'katalog' ) ?: home_url( '/kataloglar/' ) ); ?>"><?php echo esc_html( cm__( 'kataloglar_baslik' ) ); ?></a>
					<a href="<?php echo esc_url( cm_translated_page_url( 'iletisim', '/iletisim/' ) ); ?>"><?php echo esc_html( cm__( 'footer_iletisim' ) ); ?></a>
				<?php endif; ?>
			</div>

			<div>
				<h4><?php echo esc_html( cm__( 'footer_kategoriler' ) ); ?></h4>
				<?php if ( has_nav_menu( 'footer_2' ) ) : ?>
					<?php wp_nav_menu( array( 'theme_location' => 'footer_2', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1 ) ); ?>
				<?php else :
					$cm_footer_cats = get_terms( array( 'taxonomy' => 'makine_kategori', 'parent' => 0, 'hide_empty' => false, 'number' => 5 ) );
					if ( ! is_wp_error( $cm_footer_cats ) ) :
						foreach ( $cm_footer_cats as $cm_cat ) : ?>
							<a href="<?php echo esc_url( get_term_link( $cm_cat ) ); ?>"><?php echo esc_html( $cm_cat->name ); ?></a>
						<?php endforeach;
					endif;
				endif; ?>
			</div>

			<div>
				<h4><?php echo esc_html( cm__( 'footer_iletisim' ) ); ?></h4>
				<?php
				$cm_adres   = cm_option( 'sirket_adres' );
				$cm_tel     = cm_option( 'sirket_telefon' );
				$cm_eposta  = cm_option( 'sirket_eposta' );
				?>
				<?php if ( $cm_adres ) : ?><p><?php echo nl2br( esc_html( $cm_adres ) ); ?></p><?php endif; ?>
				<?php if ( $cm_tel || $cm_eposta ) : ?>
					<p>
						<?php if ( $cm_tel ) : ?><?php echo esc_html( $cm_tel ); ?><br><?php endif; ?>
						<?php if ( $cm_eposta ) : ?><a href="mailto:<?php echo esc_attr( $cm_eposta ); ?>"><?php echo esc_html( $cm_eposta ); ?></a><?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="footer-bottom">
			<span>© <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — <?php echo esc_html( cm__( 'footer_haklar' ) ); ?></span>
			<?php if ( function_exists( 'cm_cerez_banner_aktif' ) && cm_cerez_banner_aktif() ) : ?>
				<span class="footer-legal-links">
					<?php
					$cm_gizlilik_url = cm_cerez_legal_page_url( 'gizlilik-politikasi' );
					$cm_cerez_url    = cm_cerez_legal_page_url( 'cerez-politikasi' );
					if ( $cm_gizlilik_url ) : ?>
						<a href="<?php echo esc_url( $cm_gizlilik_url ); ?>"><?php echo esc_html( cm__( 'gizlilik_politikasi_baglanti' ) ); ?></a>
					<?php endif;
					if ( $cm_cerez_url ) : ?>
						<a href="<?php echo esc_url( $cm_cerez_url ); ?>"><?php echo esc_html( cm__( 'cerez_politikasi_baglanti' ) ); ?></a>
					<?php endif; ?>
					<a href="#" data-cerez-reopen><?php echo esc_html( cm__( 'cerez_ayarlarini_degistir' ) ); ?></a>
				</span>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
