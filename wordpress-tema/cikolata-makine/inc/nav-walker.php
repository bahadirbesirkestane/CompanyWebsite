<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Üst menüdeki "Ürünler" öğesini bulup (URL'i ürün kataloğu sayfasıyla eşleşen
 * öğe — admin başlığı ne olursa olsun çalışır) içine kategori/alt kategori mega
 * menüsünü (cm_products_megamenu()) ekleyen özel menü render'ı.
 *
 * Masaüstünde panel CSS ":hover"/"focus-within" ile açılır (bkz. assets/css/main.css
 * ".megamenu"), mobilde ise <button class="megamenu-toggle"> ile JS'te aç/kapat edilir
 * (bkz. assets/js/main.js) — diğer menü öğeleri hiç etkilenmez, sadece "Ürünler".
 */
class CM_Nav_Walker extends Walker_Nav_Menu {
	private $urunler_url;

	public function __construct() {
		$this->urunler_url = untrailingslashit( cm_translated_page_url( 'urunler', '/urunler/' ) );
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$is_urunler = 0 === $depth && untrailingslashit( $item->url ) === $this->urunler_url;
		if ( ! $is_urunler ) {
			parent::start_el( $output, $item, $depth, $args, $id );
			return;
		}

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'has-megamenu';
		$classes   = apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth );
		$class_str = $classes ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' : '';

		$output .= '<li id="menu-item-' . (int) $item->ID . '"' . $class_str . '>';
		$output .= '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
		$output .= '<button type="button" class="megamenu-toggle" aria-expanded="false" aria-label="' . esc_attr( cm__( 'urunler_alt_menu_aria' ) ) . '">';
		$output .= '<svg viewBox="0 0 12 8" width="10" height="7" aria-hidden="true"><path d="M1 1l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
		$output .= '</button>';

		ob_start();
		cm_products_megamenu();
		$output .= ob_get_clean();
	}
}
