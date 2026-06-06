<?php
/**
 * Header: <head> + sticky site header with primary nav and mobile drawer.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical primary-nav items (SPEC §5.3). Used as the wp_nav_menu fallback so
 * the real header renders before any WP menu is assigned in admin.
 * Each item: label, url, and optional children (the "Why ArtisRaw" dropdown).
 */
function artisraw_primary_items() {
	return array(
		// Browse what we make — categories grouped under one clear label.
		array(
			'label'    => __( 'Catalogue', 'artisraw' ),
			'url'      => home_url( '/wholesale/' ),
			'children' => array(
				array( 'label' => __( 'Cutting Boards', 'artisraw' ), 'url' => home_url( '/wholesale/olive-wood-cutting-boards/' ) ),
				array( 'label' => __( 'Utensils', 'artisraw' ), 'url' => home_url( '/wholesale/olive-wood-utensils/' ) ),
				array( 'label' => __( 'Bowls & Serveware', 'artisraw' ), 'url' => home_url( '/wholesale/olive-wood-bowls-serveware/' ) ),
				array( 'label' => __( 'Chess Sets', 'artisraw' ), 'url' => home_url( '/wholesale/olive-wood-chess-sets/' ) ),
				array( 'label' => __( 'Décor & Bath', 'artisraw' ), 'url' => home_url( '/wholesale/olive-wood-decor-bath/' ) ),
				array( 'label' => __( 'View all categories', 'artisraw' ), 'url' => home_url( '/wholesale/' ) ),
				array( 'label' => __( 'Full Catalogue (PDF)', 'artisraw' ), 'url' => home_url( '/catalogue/' ) ),
			),
		),
		// How to buy at scale — the hub plus the buying-process pages.
		array(
			'label'    => __( 'Wholesale', 'artisraw' ),
			'url'      => home_url( '/olive-wood-wholesale-supplier/' ),
			'children' => array(
				array( 'label' => __( 'Wholesale Hub', 'artisraw' ), 'url' => home_url( '/olive-wood-wholesale-supplier/' ) ),
				array( 'label' => __( 'How to Order', 'artisraw' ), 'url' => home_url( '/how-to-order/' ) ),
				array( 'label' => __( 'Shipping & Logistics', 'artisraw' ), 'url' => home_url( '/shipping-logistics/' ) ),
				array( 'label' => __( 'Worldwide / Export', 'artisraw' ), 'url' => home_url( '/worldwide/' ) ),
				array( 'label' => __( 'References & Downloads', 'artisraw' ), 'url' => home_url( '/references/' ) ),
			),
		),
		// What we do for buyers — services hub absorbs private label as a child.
		array(
			'label'    => __( 'Services', 'artisraw' ),
			'url'      => home_url( '/services/' ),
			'children' => array(
				array( 'label' => __( 'Services Overview', 'artisraw' ), 'url' => home_url( '/services/' ) ),
				array( 'label' => __( 'Private Label', 'artisraw' ), 'url' => home_url( '/private-label-olive-wood/' ) ),
				array( 'label' => __( 'Wholesale Production', 'artisraw' ), 'url' => home_url( '/olive-wood-wholesale-supplier/' ) ),
			),
		),
		// Trust & proof.
		array(
			'label'    => __( 'Why ArtisRaw', 'artisraw' ),
			'url'      => home_url( '/about/' ),
			'children' => array(
				array( 'label' => __( 'About ArtisRaw', 'artisraw' ), 'url' => home_url( '/about/' ) ),
				array( 'label' => __( 'Our Process', 'artisraw' ), 'url' => home_url( '/production-process/' ) ),
				array( 'label' => __( 'Certifications', 'artisraw' ), 'url' => home_url( '/certifications/' ) ),
				array( 'label' => __( 'Quality Control', 'artisraw' ), 'url' => home_url( '/quality-control/' ) ),
				array( 'label' => __( 'Sustainability', 'artisraw' ), 'url' => home_url( '/sustainability/' ) ),
			),
		),
		array(
			'label'    => __( 'Guide', 'artisraw' ),
			'url'      => home_url( '/olive-wood/' ),
			'children' => array(
				array( 'label' => __( 'Olive Wood Guide', 'artisraw' ), 'url' => home_url( '/olive-wood/' ) ),
				array( 'label' => __( 'Magazine', 'artisraw' ), 'url' => home_url( '/magazine/' ) ),
				array( 'label' => __( 'Compliance (Lacey / EUDR)', 'artisraw' ), 'url' => home_url( '/compliance/' ) ),
			),
		),
		array( 'label' => __( 'Contact', 'artisraw' ), 'url' => home_url( '/contact/' ) ),
	);
}

/**
 * Render the primary nav <ul> from the canonical items (plain <a href> only).
 */
function artisraw_render_primary_nav() {
	$current = trailingslashit( strtok( home_url( add_query_arg( null, null ) ), '?' ) );
	echo '<ul class="nav__list" role="list">';
	foreach ( artisraw_primary_items() as $i => $item ) {
		$has_children = ! empty( $item['children'] );
		$is_current   = trailingslashit( $item['url'] ) === $current;
		$li_class     = 'nav__item' . ( $has_children ? ' nav__item--has-children' : '' );
		echo '<li class="' . esc_attr( $li_class ) . '">';

		if ( $has_children ) {
			$panel_id = 'nav-sub-' . $i;
			$chevron  = '<svg class="nav__chevron" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>';
			// Desktop: chevron lives inside the link, hugging the label (decorative).
			printf(
				'<a class="nav__link nav__link--parent" href="%s"%s>%s<span class="nav__chevron-inline" aria-hidden="true">%s</span></a>',
				esc_url( $item['url'] ),
				$is_current ? ' aria-current="page"' : '',
				esc_html( $item['label'] ),
				$chevron
			);
			// Mobile drawer: a real toggle button reveals the submenu.
			printf(
				'<button type="button" class="nav__disclosure" aria-expanded="false" aria-controls="%s"><span class="visually-hidden">%s</span>%s</button>',
				esc_attr( $panel_id ),
				/* translators: %s: nav section name */
				esc_html( sprintf( __( 'Toggle %s submenu', 'artisraw' ), $item['label'] ) ),
				$chevron
			);
			echo '<ul class="nav__sub" id="' . esc_attr( $panel_id ) . '" role="list">';
			foreach ( $item['children'] as $child ) {
				printf(
					'<li class="nav__sub-item"><a class="nav__sub-link" href="%s">%s</a></li>',
					esc_url( $child['url'] ),
					esc_html( $child['label'] )
				);
			}
			echo '</ul>';
		} else {
			printf(
				'<a class="nav__link" href="%s"%s>%s</a>',
				esc_url( $item['url'] ),
				$is_current ? ' aria-current="page"' : '',
				esc_html( $item['label'] )
			);
		}
		echo '</li>';
	}
	echo '</ul>';
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php // Progressive enhancement: mark JS so the off-canvas drawer only activates when JS can open it. ?>
	<script>document.documentElement.className+=" js";</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'artisraw' ); ?></a>

<header class="site-header" id="site-header">
	<div class="site-header__inner container">

		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'ArtisRaw — home', 'artisraw' ); ?>">
			<?php
			if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo '<span class="site-header__wordmark">ArtisRaw<span class="site-header__reg">&reg;</span></span>';
			}
			?>
		</a>

		<button type="button" class="nav-toggle" id="nav-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="<?php esc_attr_e( 'Open menu', 'artisraw' ); ?>" data-label-open="<?php esc_attr_e( 'Open menu', 'artisraw' ); ?>" data-label-close="<?php esc_attr_e( 'Close menu', 'artisraw' ); ?>">
			<span class="nav-toggle__bars" aria-hidden="true"><span></span><span></span><span></span></span>
		</button>

		<nav class="nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'artisraw' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav__list',
					'fallback_cb'    => 'artisraw_render_primary_nav',
				) );
			} else {
				artisraw_render_primary_nav();
			}
			?>

			<div class="nav__actions">
				<a class="btn btn--primary nav__cta" href="<?php echo esc_url( home_url( '/request-quote/' ) ); ?>"><?php esc_html_e( 'Request Quote', 'artisraw' ); ?></a>
				<a class="nav__login" href="<?php echo esc_url( home_url( '/wholesale-account/' ) ); ?>"><?php esc_html_e( 'Wholesale Login', 'artisraw' ); ?></a>
				<?php // Language toggle — links to the EN/FR counterpart (Phase 10). ?>
				<?php $artisraw_lang = function_exists( 'artisraw_lang_links' ) ? artisraw_lang_links() : array( 'current' => 'en', 'en' => home_url( '/' ), 'fr' => home_url( '/fr/' ) ); ?>
				<div class="lang-toggle" role="group" aria-label="<?php esc_attr_e( 'Language', 'artisraw' ); ?>">
					<a href="<?php echo esc_url( $artisraw_lang['en'] ); ?>" hreflang="en"<?php echo 'en' === $artisraw_lang['current'] ? ' aria-current="true"' : ''; ?>>EN</a>
					<span class="lang-toggle__sep" aria-hidden="true">/</span>
					<a href="<?php echo esc_url( $artisraw_lang['fr'] ); ?>" hreflang="fr"<?php echo 'fr' === $artisraw_lang['current'] ? ' aria-current="true"' : ''; ?>>FR</a>
				</div>
			</div>
		</nav>

	</div>
</header>

<div class="nav-overlay" id="nav-overlay" hidden></div>

<main id="main" class="site-main" tabindex="-1">
