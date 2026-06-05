<?php
/**
 * Responsive image helper + hero preload (SPEC §6.6, §7).
 *
 * Emits WebP with srcset 600/1200/1800, explicit width/height (no CLS), lazy by
 * default, and eager + fetchpriority=high for the LCP hero. Theme-asset images
 * live in /assets and follow the `{base}-{width}.webp` convention.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL for a downloadable document (stable filenames). Served from /downloads/
 * to avoid colliding with the /references/ page.
 */
function artisraw_doc_url( $filename ) {
	return home_url( '/downloads/' . ltrim( $filename, '/' ) );
}

/**
 * Render a responsive <img> from a theme-asset base path.
 *
 * @param array $a {
 *   @type string $base   Path under the theme, no width/ext, e.g. '/assets/hero-wholesale'.
 *   @type string $alt    Alt text (product + "olive wood").
 *   @type int    $width  Intrinsic width of the 1800 variant (for ratio).
 *   @type int    $height Intrinsic height of the 1800 variant.
 *   @type string $sizes  sizes attr (default responsive).
 *   @type string $class  CSS class.
 *   @type bool   $eager  LCP image: eager + high priority + no lazy.
 *   @type array  $widths srcset widths (default 600/1200/1800).
 * }
 */
function artisraw_responsive_image( array $a ) {
	$base   = $a['base'];
	$widths = $a['widths'] ?? array( 600, 1200, 1800 );
	$sizes  = $a['sizes'] ?? '(min-width: 1024px) 50vw, 100vw';
	$eager  = ! empty( $a['eager'] );
	$ratio_w = $a['width'] ?? 1800;
	$ratio_h = $a['height'] ?? 1200;
	// Display width/height attrs scaled to the 1200 variant to keep aspect ratio.
	$disp_w = 1200;
	$disp_h = (int) round( 1200 * $ratio_h / $ratio_w );

	$srcset = array();
	foreach ( $widths as $w ) {
		$srcset[] = esc_url( ARTISRAW_URI . $base . '-' . $w . '.webp' ) . ' ' . $w . 'w';
	}
	// Default src must point to a generated width: prefer 1200, else the largest available.
	$default_w = in_array( 1200, $widths, true ) ? 1200 : max( $widths );
	$default   = esc_url( ARTISRAW_URI . $base . '-' . $default_w . '.webp' );

	printf(
		'<img class="%s" src="%s" srcset="%s" sizes="%s" width="%d" height="%d" alt="%s" decoding="async" %s>',
		esc_attr( $a['class'] ?? '' ),
		$default,
		esc_attr( implode( ', ', $srcset ) ),
		esc_attr( $sizes ),
		$disp_w,
		$disp_h,
		esc_attr( $a['alt'] ?? '' ),
		$eager ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'
	);
}

/**
 * Register a hero image to be preloaded. Call BEFORE get_header() in a template.
 */
function artisraw_set_hero_preload( $base, $sizes = '(min-width: 1024px) 50vw, 100vw', $widths = array( 600, 1200, 1800 ) ) {
	$set = array();
	foreach ( $widths as $w ) {
		$set[] = ARTISRAW_URI . $base . '-' . $w . '.webp ' . $w . 'w';
	}
	$GLOBALS['artisraw_hero_preload'] = array( 'srcset' => implode( ', ', $set ), 'sizes' => $sizes );
}

/**
 * Output the hero image preload link (SPEC §7) when a template registered one.
 */
function artisraw_print_hero_preload() {
	if ( empty( $GLOBALS['artisraw_hero_preload'] ) ) {
		return;
	}
	$h = $GLOBALS['artisraw_hero_preload'];
	printf(
		'<link rel="preload" as="image" imagesrcset="%s" imagesizes="%s" fetchpriority="high">' . "\n",
		esc_attr( $h['srcset'] ),
		esc_attr( $h['sizes'] )
	);
}
add_action( 'wp_head', 'artisraw_print_hero_preload', 1 );
