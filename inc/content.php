<?php
/**
 * Phase 8 — editorial content: the Guide/Magazine article seeder + shared
 * post→card helper. Articles are native WordPress posts with a quick-answer and
 * reviewer byline (E-E-A-T); single.php renders them with BlogPosting JSON-LD.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARTISRAW_ARTICLES_VER', 1 );

/** Map a post ID to an artisraw_article_card() array. */
function artisraw_post_to_card( $post_id ) {
	$reviewer = get_post_meta( $post_id, 'article_reviewer', true ) ?: 'Ihsen Triki';
	$card = array(
		'title'   => get_the_title( $post_id ),
		'href'    => get_permalink( $post_id ),
		'excerpt' => get_post_meta( $post_id, 'quick_answer', true ) ?: wp_strip_all_tags( get_the_excerpt( $post_id ) ),
		'author'  => 'Reviewed by ' . $reviewer,
		'date'    => 'Updated ' . get_the_modified_date( 'M Y', $post_id ),
	);
	if ( has_post_thumbnail( $post_id ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'artisraw-600' );
		if ( $src ) {
			$card['image'] = array( 'url' => $src[0], 'w' => $src[1], 'h' => $src[2], 'alt' => get_the_title( $post_id ) );
		}
	}
	return $card;
}

/** Idempotently seed the first Guide articles. Bump ARTISRAW_ARTICLES_VER to add. */
function artisraw_seed_articles() {
	if ( (int) get_option( 'artisraw_articles_ver' ) >= ARTISRAW_ARTICLES_VER ) {
		return;
	}
	$cat_id = 0;
	$cat    = term_exists( 'guide', 'category' );
	if ( ! $cat ) {
		$cat = wp_insert_term( 'Olive Wood Guide', 'category', array( 'slug' => 'guide' ) );
	}
	if ( ! is_wp_error( $cat ) ) {
		$cat_id = is_array( $cat ) ? (int) $cat['term_id'] : (int) $cat;
	}

	foreach ( artisraw_article_data() as $a ) {
		if ( get_page_by_path( $a['slug'], OBJECT, 'post' ) ) {
			continue; // already seeded.
		}
		$pid = wp_insert_post( array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => $a['title'],
			'post_name'    => $a['slug'],
			'post_excerpt' => $a['qa'],
			'post_content' => $a['content'],
			'post_category' => $cat_id ? array( $cat_id ) : array(),
			'comment_status' => 'closed',
		) );
		if ( $pid && ! is_wp_error( $pid ) ) {
			update_post_meta( $pid, 'quick_answer', $a['qa'] );
			update_post_meta( $pid, 'article_reviewer', $a['reviewer'] );
			update_post_meta( $pid, 'seo_title', $a['seo_title'] );
			update_post_meta( $pid, 'seo_meta_description', $a['qa'] );
		}
	}
	update_option( 'artisraw_articles_ver', ARTISRAW_ARTICLES_VER );
}
add_action( 'init', 'artisraw_seed_articles', 32 );

function artisraw_article_data() {
	$rev = 'Ihsen Triki, Head of Design';
	return array(
		array(
			'slug' => 'chemlali-olive-wood-knife-scarring', 'reviewer' => $rev,
			'title' => 'Why Chemlali olive wood resists knife scarring',
			'seo_title' => 'Why Chemlali Olive Wood Resists Knife Scarring | ArtisRaw®',
			'qa' => 'Chemlali olive wood resists knife scarring because of its high density (about 900–1,100 kg/m³), low porosity and interlocked grain. Those properties — closer to a hardwood than to typical softwoods — let the surface self-close around knife marks, which is why professional kitchens favour it for boards.',
			'content' => "<h2>Density, porosity and grain</h2><p>The Chemlali variety grown around Sfax produces a dense, fine-grained wood. Density in the 900–1,100 kg/m³ range and a Janka hardness near 2,700 lbf put it well above common board woods. The interlocked grain distributes knife pressure rather than splitting along a single line.</p><h2>What that means for buyers</h2><ul><li>Fewer deep cut marks, so boards stay hygienic and presentable longer.</li><li>Low porosity resists water and odour absorption.</li><li>Each piece keeps a unique grain pattern — natural variation, not a defect.</li></ul><h2>Care in commercial use</h2><p>Hand-wash, dry immediately and re-oil with a food-safe mineral oil when the surface looks dry. See our care guide for the full routine.</p>",
		),
		array(
			'slug' => 'importing-olive-wood-usa-lacey-act', 'reviewer' => $rev,
			'title' => 'Importing olive wood to the USA: Lacey Act basics',
			'seo_title' => 'Importing Olive Wood to the USA: Lacey Act Basics | ArtisRaw®',
			'qa' => 'To import olive wood into the USA you generally file a Lacey Act declaration (PPQ Form 505) with the genus/species (Olea europaea), country of harvest (Tunisia), quantity and value. ArtisRaw supplies this declaration data and the HTS 4419 classification with every shipment.',
			'content' => "<h2>What the Lacey Act requires</h2><p>The Lacey Act requires importers of many wood products to declare the scientific name, country of harvest, quantity and value. For olive wood that is <em>Olea europaea</em>, harvested in Tunisia.</p><h2>How ArtisRaw helps</h2><ul><li>Per-shipment Lacey Act declaration data (PPQ 505 fields).</li><li>HTS 4419 classification guidance for kitchenware.</li><li>Commercial invoice, packing list and ISPM-15 pallet compliance.</li></ul><p>Air freight to the US runs 5–12 days; ocean 25–40. Quotes are issued in USD with DDP available.</p>",
		),
		array(
			'slug' => 'olive-wood-care-commercial-kitchens', 'reviewer' => $rev,
			'title' => 'Caring for olive wood in commercial kitchens',
			'seo_title' => 'Caring for Olive Wood in Commercial Kitchens | ArtisRaw®',
			'qa' => 'Olive wood lasts for years in commercial kitchens with a simple routine: hand-wash with mild soap, dry immediately, avoid prolonged soaking and the dishwasher, and re-oil with a food-safe mineral oil and beeswax blend whenever the surface looks dry or pale.',
			'content' => "<h2>The routine</h2><ol><li>Hand-wash with warm water and mild soap after use.</li><li>Dry immediately with a towel; never air-dry flat or soak.</li><li>Keep out of the dishwasher and away from prolonged heat.</li><li>Re-oil with food-safe mineral oil (and beeswax) when the wood looks dry.</li></ol><h2>Why it matters for buyers</h2><p>Proper care keeps boards and serveware food-safe and presentable, protecting your customers’ investment and your brand. Care cards ship with every wholesale order.</p>",
		),
		array(
			'slug' => 'eudr-olive-wood-eu-buyers', 'reviewer' => $rev,
			'title' => 'EUDR and olive wood: what EU buyers need',
			'seo_title' => 'EUDR & Olive Wood: What EU Buyers Need | ArtisRaw®',
			'qa' => 'Under the EU Deforestation Regulation (EUDR), operators placing wood products on the EU market need due-diligence information: geolocation of harvest, legal-harvest evidence and traceability. ArtisRaw provides EUDR-readiness documentation and traceability for olive wood sourced in Tunisia.',
			'content' => "<h2>What EUDR asks for</h2><p>EUDR requires due diligence — geolocation of the harvest area, evidence of legal harvest, and a traceable supply chain — for covered commodities including wood.</p><h2>ArtisRaw’s position</h2><ul><li>Reclaimed, end-of-life Chemlali olive wood from licensed sources (forestry licence #4684).</li><li>Traceability and due-diligence statements for EU importers.</li><li>Documentation provided per shipment alongside the commercial paperwork.</li></ul>",
		),
	);
}
