<?php
/**
 * ArtisRaw theme — functions & global plumbing.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'ARTISRAW_VERSION', '1.0.0' );
define( 'ARTISRAW_DIR', get_template_directory() );
define( 'ARTISRAW_URI', get_template_directory_uri() );

/**
 * Cache-busting version from a file's mtime (so CSS/JS update instantly in dev).
 */
function artisraw_asset_ver( $relative_path ) {
	$abs = ARTISRAW_DIR . $relative_path;
	return file_exists( $abs ) ? (string) filemtime( $abs ) : ARTISRAW_VERSION;
}

/* -------------------------------------------------------------------------
 * Theme supports & registrations
 * ---------------------------------------------------------------------- */
function artisraw_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 40, 'width' => 160, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );

	// Image sizes — pipeline widths from SPEC §6.6 (600/1200/1800).
	add_image_size( 'artisraw-600', 600, 0, false );
	add_image_size( 'artisraw-1200', 1200, 0, false );
	add_image_size( 'artisraw-1800', 1800, 0, false );

	register_nav_menus( array(
		'primary'   => __( 'Primary navigation', 'artisraw' ),
		'footer'    => __( 'Footer links', 'artisraw' ),
		'utility'   => __( 'Utility (login)', 'artisraw' ),
		'languages' => __( 'Language switcher', 'artisraw' ),
	) );
}
add_action( 'after_setup_theme', 'artisraw_setup' );

if ( ! isset( $content_width ) ) {
	$content_width = 1180;
}

/* -------------------------------------------------------------------------
 * Assets — CSS (tokens first) + minimal deferred JS (≤30KB budget)
 * ---------------------------------------------------------------------- */
function artisraw_enqueue_assets() {
	// Tokens are the foundation; everything depends on them.
	wp_enqueue_style( 'artisraw-tokens', ARTISRAW_URI . '/css/tokens.css', array(), artisraw_asset_ver( '/css/tokens.css' ) );
	wp_enqueue_style( 'artisraw-base', ARTISRAW_URI . '/css/base.css', array( 'artisraw-tokens' ), artisraw_asset_ver( '/css/base.css' ) );
	wp_enqueue_style( 'artisraw-layout', ARTISRAW_URI . '/css/layout.css', array( 'artisraw-base' ), artisraw_asset_ver( '/css/layout.css' ) );
	wp_enqueue_style( 'artisraw-components', ARTISRAW_URI . '/css/components.css', array( 'artisraw-base' ), artisraw_asset_ver( '/css/components.css' ) );
	wp_enqueue_style( 'artisraw-forms', ARTISRAW_URI . '/css/forms.css', array( 'artisraw-base' ), artisraw_asset_ver( '/css/forms.css' ) );
	wp_enqueue_style( 'artisraw-templates', ARTISRAW_URI . '/css/templates.css', array( 'artisraw-components' ), artisraw_asset_ver( '/css/templates.css' ) );
	// Phase 5 — design-parity components + page layouts.
	wp_enqueue_style( 'artisraw-phase5', ARTISRAW_URI . '/css/phase5.css', array( 'artisraw-components' ), artisraw_asset_ver( '/css/phase5.css' ) );

	// Phase 9 — Client Area portal styles (only where the portal renders).
	if ( is_page_template( 'tpl-account.php' ) ) {
		wp_enqueue_style( 'artisraw-account', ARTISRAW_URI . '/css/account.css', array( 'artisraw-components' ), artisraw_asset_ver( '/css/account.css' ) );
	}

	// style.css holds only the theme header; load it last for overrides if needed.
	wp_enqueue_style( 'artisraw-style', get_stylesheet_uri(), array( 'artisraw-layout' ), artisraw_asset_ver( '/style.css' ) );

	// Navigation drawer JS — deferred, the bulk of the JS budget.
	wp_enqueue_script( 'artisraw-nav', ARTISRAW_URI . '/js/nav.js', array(), artisraw_asset_ver( '/js/nav.js' ), true );
	// Components JS (accordion, stats, sticky CTA, GA4) — deferred, sitewide.
	wp_enqueue_script( 'artisraw-components', ARTISRAW_URI . '/js/components.js', array(), artisraw_asset_ver( '/js/components.js' ), true );
	// Forms JS — registered only; artisraw_quote_form() enqueues it where a form renders.
	wp_register_script( 'artisraw-forms', ARTISRAW_URI . '/js/forms.js', array( 'artisraw-components' ), artisraw_asset_ver( '/js/forms.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'artisraw_enqueue_assets' );

// Defer is implied by the footer flag above, but add module-free defer attr for clarity.
function artisraw_defer_scripts( $tag, $handle ) {
	if ( in_array( $handle, array( 'artisraw-nav', 'artisraw-components', 'artisraw-forms' ), true ) ) {
		return str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'artisraw_defer_scripts', 10, 2 );

/* -------------------------------------------------------------------------
 * Preload self-hosted fonts (LCP — SPEC §7). Output early in <head>.
 * ---------------------------------------------------------------------- */
function artisraw_preload_fonts() {
	$fonts = array( '/fonts/fraunces-var.woff2', '/fonts/inter-var.woff2' );
	foreach ( $fonts as $f ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( ARTISRAW_URI . $f )
		);
	}
}
add_action( 'wp_head', 'artisraw_preload_fonts', 1 );

/* -------------------------------------------------------------------------
 * Remove front-end bloat (emoji + embeds) to protect the JS/HTML budget.
 * ---------------------------------------------------------------------- */
function artisraw_trim_bloat() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// Drop wp-embed.js (oEmbed host JS) — not needed on a content site.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}
add_action( 'init', 'artisraw_trim_bloat' );

// Dequeue the global block-library + classic-theme styles (we ship our own tokens).
function artisraw_dequeue_default_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
}
add_action( 'wp_enqueue_scripts', 'artisraw_dequeue_default_styles', 100 );

// Tidy <head>: remove generator, shortlink, RSD/wlwmanifest, REST/oEmbed link tags.
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
// We emit our own canonical in inc/seo-head.php — drop WordPress core's to avoid duplicates.
remove_action( 'wp_head', 'rel_canonical' );

/* -------------------------------------------------------------------------
 * Includes
 * ---------------------------------------------------------------------- */
require_once ARTISRAW_DIR . '/inc/breadcrumbs.php';
require_once ARTISRAW_DIR . '/inc/seo-head.php';
require_once ARTISRAW_DIR . '/inc/seo-tech.php';
require_once ARTISRAW_DIR . '/inc/schema.php';
require_once ARTISRAW_DIR . '/inc/acf-fields.php';
require_once ARTISRAW_DIR . '/inc/post-types.php';
require_once ARTISRAW_DIR . '/inc/seed-pages.php';
require_once ARTISRAW_DIR . '/inc/components.php';
require_once ARTISRAW_DIR . '/inc/quote-endpoint.php';
require_once ARTISRAW_DIR . '/inc/newsletter-endpoint.php';
require_once ARTISRAW_DIR . '/inc/account.php';
require_once ARTISRAW_DIR . '/inc/images.php';
