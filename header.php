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
		// (Home lives on the centered logo, so no separate Home item.)
		// Trust & story — dropdown keeps proof pages reachable.
		array(
			'label'    => __( 'About Us', 'artisraw' ),
			'url'      => artisraw_localized_url( '/about/' ),
			'children' => array(
				array( 'label' => __( 'About ArtisRaw', 'artisraw' ), 'url' => artisraw_localized_url( '/about/' ) ),
				array( 'label' => __( 'Quality Control', 'artisraw' ), 'url' => artisraw_localized_url( '/quality-control/' ) ),
				array( 'label' => __( 'Sustainability', 'artisraw' ), 'url' => artisraw_localized_url( '/sustainability/' ) ),
			),
		),
		// What we do for buyers — services hub absorbs private label as a child.
		array(
			'label'    => __( 'Services', 'artisraw' ),
			'url'      => artisraw_localized_url( '/services/' ),
			'children' => array(
				array( 'label' => __( 'Services Overview', 'artisraw' ), 'url' => artisraw_localized_url( '/services/' ) ),
				array( 'label' => __( 'Private Label', 'artisraw' ), 'url' => artisraw_localized_url( '/private-label-olive-wood/' ) ),
				array( 'label' => __( 'Wholesale Production', 'artisraw' ), 'url' => artisraw_localized_url( '/olive-wood-wholesale-supplier/' ) ),
			),
		),
		// Browse what we make — categories grouped under one clear label.
		array(
			'label'    => __( 'Product', 'artisraw' ),
			'url'      => artisraw_localized_url( '/catalogue/' ),
			'children' => array(
				array( 'label' => __( 'Cutting Boards', 'artisraw' ), 'url' => artisraw_localized_url( '/wholesale/olive-wood-cutting-boards/' ) ),
				array( 'label' => __( 'Utensils', 'artisraw' ), 'url' => artisraw_localized_url( '/wholesale/olive-wood-utensils/' ) ),
				array( 'label' => __( 'Bowls & Serveware', 'artisraw' ), 'url' => artisraw_localized_url( '/wholesale/olive-wood-bowls-serveware/' ) ),
				array( 'label' => __( 'Chess Sets', 'artisraw' ), 'url' => artisraw_localized_url( '/wholesale/olive-wood-chess-sets/' ) ),
				array( 'label' => __( 'Décor & Bath', 'artisraw' ), 'url' => artisraw_localized_url( '/wholesale/olive-wood-decor-bath/' ) ),
				array( 'label' => __( 'View all categories', 'artisraw' ), 'url' => artisraw_localized_url( '/wholesale/' ) ),
				array( 'label' => __( 'Full Catalogue (PDF)', 'artisraw' ), 'url' => artisraw_localized_url( '/catalogue/' ) ),
			),
		),
		// Editorial + knowledge hub.
		array(
			'label'    => __( 'Magazine', 'artisraw' ),
			'url'      => artisraw_localized_url( '/magazine/' ),
			'children' => array(
				array( 'label' => __( 'Magazine', 'artisraw' ), 'url' => artisraw_localized_url( '/magazine/' ) ),
				array( 'label' => __( 'Olive Wood Guide', 'artisraw' ), 'url' => artisraw_localized_url( '/olive-wood/' ) ),
				array( 'label' => __( 'Compliance (Lacey / EUDR)', 'artisraw' ), 'url' => artisraw_localized_url( '/compliance/' ) ),
			),
		),
		// --- centered logo splits here ---
		// Flat proof links.
		array( 'label' => __( 'Process', 'artisraw' ), 'url' => artisraw_localized_url( '/production-process/' ) ),
		array( 'label' => __( 'Certifications', 'artisraw' ), 'url' => artisraw_localized_url( '/certifications/' ) ),
		// How to buy at scale — the quote CTA plus the buying-process pages.
		array(
			'label'    => __( 'Let’s Work Together', 'artisraw' ),
			'url'      => artisraw_localized_url( '/request-quote/' ),
			'children' => array(
				array( 'label' => __( 'Request a Quote', 'artisraw' ), 'url' => artisraw_localized_url( '/request-quote/' ) ),
				array( 'label' => __( 'How to Order', 'artisraw' ), 'url' => artisraw_localized_url( '/how-to-order/' ) ),
				array( 'label' => __( 'Shipping & Logistics', 'artisraw' ), 'url' => artisraw_localized_url( '/shipping-logistics/' ) ),
				array( 'label' => __( 'Worldwide / Export', 'artisraw' ), 'url' => artisraw_localized_url( '/worldwide/' ) ),
				array( 'label' => __( 'References & Downloads', 'artisraw' ), 'url' => artisraw_localized_url( '/references/' ) ),
				array( 'label' => __( 'Client Login', 'artisraw' ), 'url' => artisraw_localized_url( '/wholesale-account/' ) ),
			),
		),
		array( 'label' => __( 'FAQs', 'artisraw' ), 'url' => artisraw_localized_url( '/faq/' ) ),
		array( 'label' => __( 'Contact', 'artisraw' ), 'url' => artisraw_localized_url( '/contact/' ) ),
	);
}

/**
 * Render the primary nav <ul> from the canonical items (plain <a href> only).
 */
function artisraw_render_primary_nav( $slice = 'all' ) {
	$current = trailingslashit( strtok( home_url( add_query_arg( null, null ) ), '?' ) );
	$items   = artisraw_primary_items();
	// Split the menu in two halves so the centered logo sits between them.
	$mid = (int) ceil( count( $items ) / 2 );
	if ( 'left' === $slice ) {
		$items     = array_slice( $items, 0, $mid, true );
		$ul_class  = 'nav__list nav__list--left';
	} elseif ( 'right' === $slice ) {
		$items     = array_slice( $items, $mid, null, true );
		$ul_class  = 'nav__list nav__list--right';
	} else {
		$ul_class  = 'nav__list';
	}
	echo '<ul class="' . esc_attr( $ul_class ) . '" role="list">';
	foreach ( $items as $i => $item ) {
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
	// Language switcher rides at the end of the nav flow (not floated), so it
	// never overlaps the last link. Lives in the right half (or the full list).
	if ( ( 'right' === $slice || 'all' === $slice ) && function_exists( 'artisraw_lang_switch' ) ) {
		echo '<li class="nav__item nav__item--lang">';
		artisraw_lang_switch();
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

		<a class="site-header__brand" href="<?php echo esc_url( artisraw_localized_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'ArtisRaw — home', 'artisraw' ); ?>">
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
			// Two halves flank the centered logo; actions float to the far right.
			artisraw_render_primary_nav( 'left' );
			artisraw_render_primary_nav( 'right' );
			?>
		</nav>

	</div>
</header>

<div class="nav-overlay" id="nav-overlay" hidden></div>

<main id="main" class="site-main" tabindex="-1">
