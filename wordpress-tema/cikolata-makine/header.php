<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="site-header-inner">
		<?php if ( has_custom_logo() ) : ?>
			<div class="logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="logo-mark"><?php bloginfo( 'name' ); ?></span></a>
		<?php endif; ?>

		<button type="button" class="nav-toggle" aria-expanded="false" aria-controls="cm-primary-menu" aria-label="<?php echo esc_attr( cm__( 'menu_toggle_aria' ) ); ?>">
			<span></span><span></span><span></span>
		</button>

		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => 'nav',
				'container_class'=> 'nav-menu nav',
				'menu_id'        => 'cm-primary-menu',
				'walker'         => new CM_Nav_Walker(),
			) );
		} else {
			cm_primary_menu_fallback();
		}
		?>

		<div class="nav-actions">
			<?php cm_social_links( array( 'linkedin', 'instagram', 'youtube' ), 'nav-social' ); ?>

			<?php $cm_wa = cm_whatsapp_url(); ?>
			<?php if ( $cm_wa ) : ?>
				<a class="nav-whatsapp" href="<?php echo esc_url( $cm_wa ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( cm__( 'whatsapp_aria' ) ); ?>">
					<svg viewBox="0 0 32 32" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M16.04 3C9.37 3 3.98 8.39 3.98 15.06c0 2.2.58 4.26 1.6 6.04L3 29l8.1-2.53a11.98 11.98 0 0 0 4.94 1.06h.01c6.67 0 12.06-5.39 12.06-12.06C28.1 8.4 22.71 3 16.04 3zm0 21.9h-.01a9.8 9.8 0 0 1-4.99-1.36l-.36-.21-3.71 1.16 1.19-3.62-.24-.37a9.78 9.78 0 0 1-1.5-5.24c0-5.43 4.42-9.85 9.86-9.85 2.63 0 5.11 1.03 6.97 2.89a9.78 9.78 0 0 1 2.88 6.96c0 5.43-4.42 9.84-9.86 9.84zm5.4-7.38c-.3-.15-1.75-.87-2.02-.96-.27-.1-.47-.15-.66.15-.2.3-.76.96-.93 1.16-.17.2-.34.22-.63.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.34.45-.51.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.66-1.6-.91-2.19-.24-.58-.48-.5-.66-.51h-.56c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.35.2 1.86.12.57-.08 1.75-.71 2-1.4.25-.68.25-1.27.17-1.4-.07-.12-.27-.2-.57-.35z"/></svg>
				</a>
			<?php else : ?>
				<a class="nav-cta" href="<?php echo esc_url( cm_translated_page_url( 'iletisim', '/iletisim/' ) ); ?>"><?php echo esc_html( cm__( 'teklif_iste' ) ); ?></a>
			<?php endif; ?>
		</div>

		<?php
		if ( function_exists( 'pll_the_languages' ) ) :
			$cm_langs = pll_the_languages( array( 'raw' => 1 ) );
			$cm_current_lang = null;
			foreach ( (array) $cm_langs as $cm_l ) {
				if ( ! empty( $cm_l['current_lang'] ) ) { $cm_current_lang = $cm_l; break; }
			}
			if ( $cm_langs && $cm_current_lang ) :
		?>
			<div class="lang-dropdown">
				<button type="button" class="lang-dropdown-toggle" aria-haspopup="true" aria-expanded="false">
					<?php if ( ! empty( $cm_current_lang['flag'] ) ) : ?><img src="<?php echo esc_url( $cm_current_lang['flag'] ); ?>" alt="" width="20" height="14"><?php endif; ?>
					<span><?php echo esc_html( strtoupper( $cm_current_lang['slug'] ) ); ?></span>
					<svg class="lang-dropdown-caret" width="10" height="6" viewBox="0 0 10 6" aria-hidden="true"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
				</button>
				<ul class="lang-dropdown-menu">
					<?php foreach ( $cm_langs as $cm_l ) : ?>
						<li>
							<a href="<?php echo esc_url( $cm_l['url'] ); ?>" lang="<?php echo esc_attr( $cm_l['slug'] ); ?>"<?php echo ! empty( $cm_l['current_lang'] ) ? ' class="active" aria-current="true"' : ''; ?>>
								<?php if ( ! empty( $cm_l['flag'] ) ) : ?><img src="<?php echo esc_url( $cm_l['flag'] ); ?>" alt="" width="20" height="14"><?php endif; ?>
								<span><?php echo esc_html( $cm_l['name'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php
			endif;
		endif;
		?>
	</div>
</header>
