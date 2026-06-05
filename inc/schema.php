<?php
/**
 * Site-wide JSON-LD: Organization + WebSite (SPEC §6.7).
 * Per-template graphs (BreadcrumbList, ItemList, Article, FAQPage…) are added
 * by their own templates / helpers.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print a JSON-LD <script> block from a PHP array. Single source of truth for
 * encoding flags so every graph renders identically.
 */
function artisraw_jsonld( array $data ) {
	echo "\n" . '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
	echo "\n" . '</script>' . "\n";
}

/**
 * Organization + WebSite, emitted on every page.
 */
function artisraw_site_schema() {
	$home = home_url( '/' );

	$organization = array(
		'@context'     => 'https://schema.org',
		'@type'        => 'Organization',
		'name'         => 'ArtisRaw',
		'url'          => $home,
		'logo'         => ARTISRAW_URI . '/assets/artisraw-logo.png',
		'foundingDate' => '2019',
		'founders'     => array(
			array( '@type' => 'Person', 'name' => 'Mohamed Bilel Cherif' ),
			array( '@type' => 'Person', 'name' => 'Ahmed Sakka' ),
			array( '@type' => 'Person', 'name' => 'Ihsen Triki' ),
		),
		'address'      => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Route Saltania, km 4.5',
			'addressLocality' => 'Sfax',
			'addressCountry'  => 'TN',
		),
		'award'         => 'MEA Business Awards — Best Artisan Olive Wood Products Company in North Africa',
		'hasCredential' => array(
			'@type'             => 'EducationalOccupationalCredential',
			'credentialCategory' => 'certification',
			'name'              => 'ISO 9001:2015',
		),
		'sameAs'        => array(
			'https://www.linkedin.com/company/artisraw',
			'https://trees.org/sponsor/art-is-raw/',
		),
	);

	$website = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'WebSite',
		'name'            => 'ArtisRaw',
		'url'             => $home,
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => $home . '?s={search_term_string}',
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	artisraw_jsonld( $organization );
	artisraw_jsonld( $website );
}
add_action( 'wp_head', 'artisraw_site_schema', 20 );

/**
 * ItemList of Product from SKU posts (SPEC §6.7) — hub + category pages.
 * Price omitted ("on request"); material/brand/countryOfOrigin per spec.
 *
 * @param int[]  $sku_ids  SKU post IDs.
 * @param string $list_url The page the list appears on.
 */
function artisraw_product_itemlist( array $sku_ids, $list_url ) {
	if ( empty( $sku_ids ) ) {
		return;
	}
	$elements = array();
	foreach ( $sku_ids as $pos => $id ) {
		$product = array(
			'@type'           => 'Product',
			'name'            => get_the_title( $id ),
			'sku'             => (string) ( get_post_meta( $id, 'sku_code', true ) ),
			'material'        => 'Olea europaea (Chemlali)',
			'brand'           => array( '@type' => 'Brand', 'name' => 'ArtisRaw' ),
			'countryOfOrigin' => 'TN',
			'offers'          => array(
				'@type'         => 'Offer',
				'availability'  => 'https://schema.org/InStock',
				'priceCurrency' => 'USD',
				'businessFunction' => 'http://purl.org/goodrelations/v1#Sell',
				'eligibleCustomerType' => 'http://purl.org/goodrelations/v1#Business',
				'url'           => $list_url,
			),
		);
		if ( has_post_thumbnail( $id ) ) {
			$product['image'] = get_the_post_thumbnail_url( $id, 'artisraw-1200' );
		}
		$elements[] = array(
			'@type'    => 'ListItem',
			'position' => $pos + 1,
			'item'     => $product,
		);
	}
	artisraw_jsonld( array(
		'@context'        => 'https://schema.org',
		'@type'           => 'ItemList',
		'itemListElement' => $elements,
	) );
}
