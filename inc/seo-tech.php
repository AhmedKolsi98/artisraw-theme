<?php
/**
 * Phase 6 — technical SEO & launch layer (SPEC §6.8, §7, §8, §11).
 *
 * - robots.txt with AI-bot allows (GPTBot, ClaudeBot, PerplexityBot, …)
 * - /llms.txt (company definition + key links) via a virtual route
 * - XML sitemap tuning (drop users + sku_category; exclude noindex pages)
 * - GA4-via-GTM loader, gated on constants so it's inert until configured
 * - baseline security headers
 *
 * Config (define in wp-config.php):
 *   ARTISRAW_GTM_ID   e.g. 'GTM-XXXXXXX'  (preferred — loads GTM)
 *   ARTISRAW_GA4_ID   e.g. 'G-XXXXXXXXXX' (fallback — direct gtag.js)
 *   ARTISRAW_STAGING  true on staging to force sitewide noindex (handled in seo-head.php)
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
 * robots.txt — deploy exactly per SPEC §6.8 (served virtually when no
 * physical robots.txt exists). AI crawlers explicitly allowed.
 * ====================================================================== */
function artisraw_robots_txt( $output, $public ) {
	if ( ! $public ) {
		// Staging / "discourage search engines" → block everything, no sitemap.
		return "User-agent: *\nDisallow: /\n";
	}
	$sitemap = home_url( '/wp-sitemap.xml' ); // WordPress-native sitemap index.
	$lines   = array(
		'User-agent: *',
		'Disallow: /wp-admin/',
		'Allow: /wp-admin/admin-ajax.php',
		'',
	);
	foreach ( array( 'GPTBot', 'OAI-SearchBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-Web', 'PerplexityBot', 'Google-Extended', 'Applebot-Extended', 'CCBot' ) as $bot ) {
		$lines[] = 'User-agent: ' . $bot;
		$lines[] = 'Allow: /';
	}
	$lines[] = '';
	$lines[] = 'Sitemap: ' . $sitemap;
	return implode( "\n", $lines ) . "\n";
}
add_filter( 'robots_txt', 'artisraw_robots_txt', 10, 2 );

/* =========================================================================
 * /llms.txt — virtual route (llmstxt.org format): H1 + summary + key links.
 * ====================================================================== */
function artisraw_register_llms_route() {
	add_rewrite_rule( '^llms\.txt$', 'index.php?artisraw_llms=1', 'top' );
}
add_action( 'init', 'artisraw_register_llms_route' );

function artisraw_llms_query_var( $vars ) {
	$vars[] = 'artisraw_llms';
	return $vars;
}
add_filter( 'query_vars', 'artisraw_llms_query_var' );

// Don't let WordPress append a trailing slash to /llms.txt.
add_filter( 'redirect_canonical', function ( $redirect_url ) {
	return get_query_var( 'artisraw_llms' ) ? false : $redirect_url;
} );

function artisraw_render_llms_txt() {
	if ( ! get_query_var( 'artisraw_llms' ) ) {
		return;
	}
	nocache_headers();
	header( 'Content-Type: text/plain; charset=utf-8' );

	$links = array(
		array( 'Wholesale hub', '/olive-wood-wholesale-supplier/', 'The B2B offer: MOQ, lead times, ready-to-ship SKUs and import documentation.' ),
		array( 'Catalogue', '/wholesale/', 'Olive wood product families — cutting boards, utensils, bowls, chess sets and décor.' ),
		array( 'Services', '/services/', 'Wholesale production, private label, corporate gifts, custom orders, QC and export support.' ),
		array( 'Worldwide / export', '/worldwide/', 'Export reach across North America, Europe, the GCC and Asia, with documentation per market.' ),
		array( 'Certifications & documents', '/certifications/', 'ISO 9001:2015 certificate, forestry licence, MSDS and quality reports.' ),
		array( 'Quality control', '/quality-control/', 'Unit-by-unit inspection, ≥96% first-pass yield, batch photo documentation.' ),
		array( 'Shipping & logistics', '/shipping-logistics/', 'Incoterms, transit times and ISPM-15 export packing to 30+ countries.' ),
		array( 'Olive Wood Guide', '/olive-wood/', 'Buyer knowledge hub: Chemlali wood properties, food-safe care and import compliance.' ),
		array( 'Contact', '/contact/', 'Request a wholesale quote, samples or private-label project — reply within 24 hours.' ),
	);

	echo "# ArtisRaw\n\n";
	echo "> ArtisRaw is an ISO 9001:2015-certified olive wood manufacturer and B2B exporter in Sfax, Tunisia, supplying retailers, distributors, hospitality groups and private-label brands in 30+ countries. Wholesale MOQ starts at 50 units, with in-stock lines dispatched within 72 hours and custom production in 6–8 weeks.\n\n";
	echo "## Key pages\n\n";
	foreach ( $links as $l ) {
		printf( "- [%s](%s): %s\n", $l[0], esc_url( home_url( $l[1] ) ), $l[2] );
	}
	echo "\n## Contact\n\n";
	echo "Route Saltania, km 4.5, Sfax, Tunisia · contact@artisraw.com\n";
	exit;
}
add_action( 'template_redirect', 'artisraw_render_llms_txt' );

/* =========================================================================
 * XML sitemap tuning (WordPress-native /wp-sitemap.xml).
 * Drop the users + sku_category providers; exclude noindex pages.
 * ====================================================================== */
function artisraw_sitemap_providers( $provider, $name ) {
	if ( 'users' === $name ) {
		return false; // no author archives on a B2B site.
	}
	return $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'artisraw_sitemap_providers', 10, 2 );

function artisraw_sitemap_taxonomies( $taxonomies ) {
	unset( $taxonomies['sku_category'] ); // canonical category URLs are /wholesale/ pages, not term archives.
	unset( $taxonomies['post_tag'], $taxonomies['category'] );
	return $taxonomies;
}
add_filter( 'wp_sitemaps_taxonomies', 'artisraw_sitemap_taxonomies' );

function artisraw_sitemap_exclude_noindex( $args, $post_type ) {
	if ( 'page' !== $post_type ) {
		return $args;
	}
	$exclude = array();
	// Styleguide is noindex via a template flag (no meta) — exclude by slug.
	$sg = get_page_by_path( 'styleguide' );
	if ( $sg ) {
		$exclude[] = $sg->ID;
	}
	// Any page explicitly flagged noindex via meta.
	$noindex = get_posts( array(
		'post_type'   => 'page',
		'fields'      => 'ids',
		'nopaging'    => true,
		'meta_key'    => 'seo_noindex',
		'meta_value'  => '1',
	) );
	$exclude = array_merge( $exclude, $noindex );
	if ( $exclude ) {
		$args['post__not_in'] = array_merge( $args['post__not_in'] ?? array(), $exclude );
	}
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'artisraw_sitemap_exclude_noindex', 10, 2 );

/**
 * Courtesy alias: /sitemap_index.xml → /wp-sitemap.xml (the SPEC-era URL).
 */
function artisraw_sitemap_alias() {
	$req = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
	if ( '/sitemap_index.xml' === $req ) {
		wp_safe_redirect( home_url( '/wp-sitemap.xml' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'artisraw_sitemap_alias' );

/* =========================================================================
 * GA4 via GTM (SPEC §8). Inert until a container/measurement ID is defined.
 * Events (cta_click, form_submit, doc_download, …) already push to dataLayer.
 * ====================================================================== */
function artisraw_gtm_id() {
	return defined( 'ARTISRAW_GTM_ID' ) && ARTISRAW_GTM_ID ? ARTISRAW_GTM_ID : '';
}
function artisraw_ga4_id() {
	return defined( 'ARTISRAW_GA4_ID' ) && ARTISRAW_GA4_ID ? ARTISRAW_GA4_ID : '';
}

function artisraw_analytics_head() {
	if ( is_admin() ) {
		return;
	}
	$gtm = artisraw_gtm_id();
	$ga4 = artisraw_ga4_id();
	if ( $gtm ) {
		?>
<!-- Google Tag Manager -->
<script>window.dataLayer=window.dataLayer||[];(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $gtm ); ?>');</script>
<!-- End Google Tag Manager -->
		<?php
	} elseif ( $ga4 ) {
		?>
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4 ); ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $ga4 ); ?>');</script>
<!-- End Google Analytics 4 -->
		<?php
	}
}
add_action( 'wp_head', 'artisraw_analytics_head', 1 );

function artisraw_gtm_noscript() {
	$gtm = artisraw_gtm_id();
	if ( $gtm && ! is_admin() ) {
		printf(
			'<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>',
			esc_attr( $gtm )
		);
	}
}
add_action( 'wp_body_open', 'artisraw_gtm_noscript' );

/* =========================================================================
 * Baseline security headers (front-end). CSP is documented separately
 * (it must be tuned to the live GTM/asset origins) — not auto-emitted here.
 * ====================================================================== */
function artisraw_security_headers( $headers ) {
	if ( is_admin() ) {
		return $headers;
	}
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	$headers['X-Frame-Options']        = 'SAMEORIGIN';
	$headers['Permissions-Policy']     = 'camera=(), microphone=(), geolocation=(), browsing-topics=()';
	return $headers;
}
add_filter( 'wp_headers', 'artisraw_security_headers' );
