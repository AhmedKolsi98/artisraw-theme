<?php
/**
 * Document head SEO output — title, meta, canonical, OG, Twitter (SPEC §6.2).
 *
 * Reads the ACF global SEO field group when present, with sane fallbacks so the
 * head is correct even before ACF / per-page values are set.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Small ACF-or-null helper (no fatal if ACF is not active yet).
 */
function artisraw_field( $key, $post_id = null ) {
	if ( function_exists( 'get_field' ) ) {
		$val = get_field( $key, $post_id );
		if ( null !== $val && '' !== $val ) {
			return $val;
		}
	}
	// Fallback to raw post meta (same keys ACF uses) so per-page SEO works
	// before ACF is installed.
	if ( is_singular() || $post_id ) {
		$meta = get_post_meta( $post_id ?: get_queried_object_id(), $key, true );
		if ( '' !== $meta ) {
			return $meta;
		}
	}
	return null;
}

/**
 * Resolve the SEO title for the current view (ACF override → WP default).
 */
function artisraw_seo_title() {
	$custom = artisraw_field( 'seo_title' );
	if ( $custom ) {
		return $custom;
	}
	return wp_get_document_title();
}

/**
 * Resolve the meta description (ACF override → excerpt → tagline).
 */
function artisraw_seo_description() {
	$desc = artisraw_field( 'seo_meta_description' );
	if ( $desc ) {
		return $desc;
	}
	if ( is_singular() ) {
		$post = get_post();
		if ( $post && $post->post_excerpt ) {
			return wp_strip_all_tags( $post->post_excerpt );
		}
	}
	return get_bloginfo( 'description' );
}

/**
 * Override the document <title> when an ACF SEO title is set.
 */
function artisraw_filter_title_parts( $parts ) {
	$custom = artisraw_field( 'seo_title' );
	if ( $custom ) {
		$parts['title']   = $custom;
		$parts['site']    = '';
		$parts['tagline'] = '';
	}
	return $parts;
}
add_filter( 'document_title_parts', 'artisraw_filter_title_parts' );

/**
 * Output meta description, canonical, robots, OG and Twitter tags.
 */
function artisraw_seo_head() {
	$title = artisraw_seo_title();
	$desc  = artisraw_seo_description();
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) );
	$url   = strtok( $url, '?' ); // strip query for canonical

	// OG image: ACF override → featured image → theme default.
	$og_image = '';
	$acf_img  = artisraw_field( 'seo_og_image' );
	if ( is_array( $acf_img ) && ! empty( $acf_img['url'] ) ) {
		$og_image = $acf_img['url'];
	} elseif ( is_string( $acf_img ) && $acf_img ) {
		$og_image = $acf_img;
	} elseif ( is_singular() && has_post_thumbnail() ) {
		$og_image = get_the_post_thumbnail_url( null, 'artisraw-1200' );
	}

	// Robots: ACF noindex toggle, template-forced noindex, staging constant, OR blog_public off.
	$staging = defined( 'ARTISRAW_STAGING' ) && ARTISRAW_STAGING;
	$noindex = artisraw_field( 'seo_noindex' ) || ! empty( $GLOBALS['artisraw_force_noindex'] ) || $staging || ! get_option( 'blog_public' );

	echo "\n<!-- ArtisRaw SEO head -->\n";
	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	if ( $noindex ) {
		echo '<meta name="robots" content="noindex, nofollow">' . "\n";
	} else {
		echo '<meta name="robots" content="index, follow, max-image-preview:large">' . "\n";
	}

	// Open Graph.
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular() ? 'article' : 'website' );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	if ( $og_image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $og_image ) );
	}

	// Twitter.
	printf( '<meta name="twitter:card" content="%s">' . "\n", $og_image ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $og_image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $og_image ) );
	}
	echo "<!-- /ArtisRaw SEO head -->\n";
}
add_action( 'wp_head', 'artisraw_seo_head', 2 );
