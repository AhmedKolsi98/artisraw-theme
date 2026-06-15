<?php
/**
 * Shared homepage composition (Figma). Rendered by front-page.php (EN front
 * page) and tpl-home.php (the /fr/ French homepage), so both languages get the
 * same design; gettext + artisraw_localized_url() localise strings and links.
 *
 * @package ArtisRaw
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- 1 · Hero carousel (Olyfo-style): rotating backgrounds + headlines + ISO badge -->
<?php
artisraw_hero_carousel(
	array(
		array(
			'base'      => '/assets/ar-hero-logs',
			'alt'       => __( 'Stacked raw olive wood logs ready for artisan production', 'artisraw' ),
			'widths'    => array( 600, 1200, 1800 ),
			'w'         => 1800, 'h' => 1012,
			'eyebrow'   => __( 'ISO 9001 olive wood manufacturer · Sfax, Tunisia', 'artisraw' ),
			'title'     => __( 'Premium Olive Wood for Wholesale Buyers', 'artisraw' ),
			'support'   => __( 'Creators of handmade Tunisian olive wood collections for retailers, distributors, hospitality groups, corporate gifts and private-label brands.', 'artisraw' ),
			'support_tag' => 'h2',
			'cta_label' => __( 'Start Your Project', 'artisraw' ),
			'cta_url'   => artisraw_localized_url( '/request-quote/' ),
		),
		array(
			'base'      => '/assets/ar-hero-showroom',
			'alt'       => __( 'ArtisRaw olive wood collections on showroom shelves', 'artisraw' ),
			'widths'    => array( 600, 1200, 1800 ),
			'w'         => 1800, 'h' => 1012,
			'eyebrow'   => __( 'Export-ready collections · Shipped to 30+ countries', 'artisraw' ),
			'title'     => __( 'Handmade Tunisian Olive Wood Collections', 'artisraw' ),
			'support'   => __( 'From cutting boards to serveware, crafted by artisans in Sfax and delivered with full export documentation.', 'artisraw' ),
			'support_tag' => 'h2',
			'cta_label' => __( 'Browse Catalogue', 'artisraw' ),
			'cta_url'   => artisraw_localized_url( '/catalogue/' ),
		),
	),
	array(
		'badge'    => true,
		'loc'      => 'home-hero',
		'interval' => 6000,
	)
);
?>

<!-- 1b · Floating differentiator cards — straddle the hero's bottom edge -->
<section class="hero-cards">
	<div class="container">
		<div class="hero-cards__grid">
			<?php
			// Icons (stroke = currentColor, inherits the cream slab text colour).
			$svg_shield = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>';
			$svg_globe  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 2.5 15 0 18M12 3c-2.5 2.5-2.5 15 0 18"/></svg>';
			$svg_tag    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12V4h8l9 9-8 8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>';
			$svg_truck  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h11v9H3zM14 9h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>';
			$diffs = array(
				array( __( 'ISO 9001 quality', 'artisraw' ), __( 'Certified quality management with ≥96% first-pass yield and unit-by-unit inspection.', 'artisraw' ), $svg_shield ),
				array( __( 'Import-ready', 'artisraw' ), __( 'Lacey Act data, EUDR traceability and ISPM-15 pallets prepared per shipment.', 'artisraw' ), $svg_globe ),
				array( __( 'In-house private label', 'artisraw' ), __( 'Engraving, custom packaging and barcode-ready references under your brand.', 'artisraw' ), $svg_tag ),
				array( __( 'Fast fulfilment', 'artisraw' ), __( 'In-stock SKUs dispatched within 72 hours; custom runs in 6–8 weeks.', 'artisraw' ), $svg_truck ),
			);
			foreach ( $diffs as $n => $d ) {
				echo '<div class="hero-card">';
				echo '<div class="hero-card__top"><span class="hero-card__num">' . esc_html( sprintf( '%02d', $n + 1 ) ) . '</span><span class="hero-card__icon">' . $d[2] . '</span></div>';
				echo '<h3 class="hero-card__title">' . esc_html( $d[0] ) . '</h3>';
				echo '<p class="hero-card__text">' . esc_html( $d[1] ) . '</p>';
				echo '</div>';
			}
			?>
		</div>
	</div>
</section>

<!-- 2 · Intro (plain editorial, two-column, per design) -->
<section class="container section home-intro">
	<div class="home-intro__head">
		<h2><?php esc_html_e( 'From Olive Tree to Premium Collections', 'artisraw' ); ?></h2>
	</div>
	<div class="home-intro__body">
		<p><?php esc_html_e( 'Born in Sfax, Tunisia — the heart of Mediterranean olive country — ArtisRaw gives century-old Chemlali olive trees a second life handcrafted pieces that retailers, distributors and brands actually want on their shelves.', 'artisraw' ); ?></p>
		<p><?php esc_html_e( 'Cutting boards, tableware, utensils, chess sets: every item is shaped by hand, finished with food-safe mineral oil and beeswax, and built to sell. We’re ISO 9001:2015 certified, we ship to over 30 countries, and we keep things simple —stock items out the door in 72 hours, custom production in 6 to 8 weeks.', 'artisraw' ); ?></p>
		<p><?php esc_html_e( 'Whether you need sale-ready , private label or full export documentation, ArtisRaw gives you factory-direct quality without the guesswork.', 'artisraw' ); ?></p>
		<p class="home-intro__note"><?php artisraw_arrow_link( __( 'More info', 'artisraw' ), artisraw_localized_url( '/about/' ) ); ?></p>
	</div>
</section>

<!-- 3 · Product mosaic (PDF): tree → workshop → wholesale → shelves -->
<section class="container section mosaic-section">
	<div class="mosaic-section__head">
		<h2><?php esc_html_e( 'From tree, to workshop, to wholesale, to shelves', 'artisraw' ); ?></h2>
		<?php artisraw_arrow_link( __( 'Discover our product', 'artisraw' ), artisraw_localized_url( '/catalogue/' ) ); ?>
	</div>
	<?php
	artisraw_photo_mosaic(
		array(
			array( 'base' => '/assets/ar-grove', 'alt' => __( 'Century-old Chemlali olive trees near Sfax', 'artisraw' ), 'variant' => 'big', 'href' => artisraw_localized_url( '/about/' ), 'w' => 1273, 'h' => 900, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-lathe', 'alt' => __( 'Artisan shaping olive wood in the Sfax workshop', 'artisraw' ), 'href' => artisraw_localized_url( '/production-process/' ), 'w' => 1400, 'h' => 933, 'widths' => array( 600 ) ),
			array( 'base' => '/assets/ar-collection', 'alt' => __( 'Wholesale olive wood collection ready for export', 'artisraw' ), 'href' => artisraw_localized_url( '/catalogue/' ), 'w' => 1200, 'h' => 900, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-workshop', 'alt' => __( 'Finished olive wood pieces on the showroom shelves', 'artisraw' ), 'variant' => 'wide', 'href' => artisraw_localized_url( '/wholesale/' ), 'w' => 1400, 'h' => 900, 'widths' => array( 600, 1200 ) ),
		)
	);
	?>
</section>

<!-- 4 · Color-block mosaic (§4): who we are · how it's made · sustainability -->
<?php
artisraw_color_block( array(
	'field' => 'clay', 'field_left' => true,
	'eyebrow' => __( 'Who we are', 'artisraw' ),
	'heading' => __( 'A Tunisian manufacturer with artisan roots', 'artisraw' ),
	'body'    => __( 'Founded in Sfax in 2019, ArtisRaw blends ancestral woodworking with modern, ISO 9001 production — handmade heritage built for export-ready wholesale.', 'artisraw' ),
	'link_label' => __( 'Our story & founders', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/about/' ),
	'img_base' => '/assets/ar-story-founders', 'img_alt' => __( 'ArtisRaw artisans in the Sfax workshop', 'artisraw' ),
	'img_widths' => array( 600, 800 ), 'w' => 800, 'h' => 498,
) );
artisraw_color_block( array(
	'field' => 'espresso',
	'eyebrow' => __( 'Our Mission', 'artisraw' ),
	'heading' => __( 'Beautiful products, responsibly made', 'artisraw' ),
	'body'    => __( 'Our mission is to craft responsible, food-safe and beautiful olive wood products for global wholesale markets while supporting artisans, preserving raw material value and reducing waste.', 'artisraw' ),
	'link_label' => __( 'Learn more', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/about/' ),
	'img_base' => '/assets/ar-mission-workshop', 'img_alt' => __( 'ArtisRaw artisans at work in the olive wood workshop', 'artisraw' ),
	'img_widths' => array( 600, 1200 ), 'w' => 1600, 'h' => 900,
) );
artisraw_color_block( array(
	'field' => 'clay', 'field_left' => true,
	'heading' => __( 'Our Vision', 'artisraw' ),
	'body'    => __( 'We aim to make Tunisian olive wood a global standard of sustainable luxury by connecting Mediterranean heritage with international B2B export systems.', 'artisraw' ),
	'link_label' => __( 'Read more', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/about/' ),
	'img_base' => '/assets/ar-vision-export', 'img_alt' => __( 'Export-ready olive wood orders packed on pallets for global shipping', 'artisraw' ),
	'img_widths' => array( 600, 1200 ), 'w' => 1600, 'h' => 900,
) );
?>

<!-- 4a · Feature testimonial (Figma) — directly under Our Vision -->
<?php
artisraw_testimonial_feature( array(
	'heading'    => __( 'What they say…', 'artisraw' ),
	'quote'      => __( 'ArtisRaw is reliable, consistent and easy to work with. Their handmade olive wood products helped us build a premium retail collection with a strong Mediterranean story.', 'artisraw' ),
	'author'     => __( 'Retail buyer', 'artisraw' ),
	'role'       => __( 'Specialty retailer, United Kingdom', 'artisraw' ),
	'link_label' => __( 'Request a quote', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/request-quote/' ),
	'img_base'   => '/assets/ar-say-showroom',
	'img_alt'    => __( 'ArtisRaw olive wood collection on display in a retail showroom', 'artisraw' ),
	'img_w'      => 1600, 'img_h' => 900, 'img_widths' => array( 600, 1200 ),
) );
?>

<!-- 4b · Wayfinding trio band: catalogue · process · FAQs -->
<?php
artisraw_trio_band( array(
	array( 'label' => __( 'Discover Our Catalogue', 'artisraw' ), 'cue' => __( 'Discover more', 'artisraw' ), 'href' => artisraw_localized_url( '/catalogue/' ) ),
	array( 'label' => __( 'Our process', 'artisraw' ), 'cue' => __( 'Learn more', 'artisraw' ), 'href' => artisraw_localized_url( '/production-process/' ), 'surface' => 'clay' ),
	array( 'label' => __( 'FAQs', 'artisraw' ), 'cue' => __( 'Read more', 'artisraw' ), 'href' => artisraw_localized_url( '/faq/' ) ),
), 'trio-band--wide-first' );
?>

<!-- 5 · Olive wood products strip (§6, Olyfo-style carousel) -->
<?php
$ar_products = array(
	array( 'name' => __( 'Olive Wood Bowls', 'artisraw' ),      'base' => '/assets/ar-prod-bowls',     'href' => artisraw_localized_url( '/olive-wood-bowls-serveware/' ) ),
	array( 'name' => __( 'Olive Wood Soap Dishes', 'artisraw' ), 'base' => '/assets/ar-prod-soap-dish', 'href' => artisraw_localized_url( '/olive-wood-decor-bath/' ) ),
	array( 'name' => __( 'Olive Wood Chess Sets', 'artisraw' ),  'base' => '/assets/ar-prod-chess',     'href' => artisraw_localized_url( '/olive-wood-chess-sets/' ) ),
	array( 'name' => __( 'Olive Wood Cups', 'artisraw' ),        'base' => '/assets/ar-prod-cups',      'href' => artisraw_localized_url( '/wholesale/' ) ),
	array( 'name' => __( 'Olive Wood Salt Box', 'artisraw' ),    'base' => '/assets/ar-prod-salt-box',  'href' => artisraw_localized_url( '/wholesale/' ) ),
	array( 'name' => __( 'Olive Wood Utensil Set', 'artisraw' ), 'base' => '/assets/ar-prod-utensils',  'href' => artisraw_localized_url( '/olive-wood-utensils/' ) ),
);
?>
<section class="section--white">
	<div class="container section hub-section sku-strip">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Elevate the selection With Memorable Olive Wood Products', 'artisraw' ); ?></h2></header>
		<?php artisraw_product_carousel( $ar_products ); ?>
		<p class="hub-section__note"><?php artisraw_arrow_link( __( 'Browse all categories', 'artisraw' ), artisraw_localized_url( '/wholesale/' ) ); ?></p>
	</div>
</section>

<!-- 7 · Built for your channel (self-selection, block formula) -->
<section class="section--white">
	<div class="container section hub-section">
		<div class="section-opener"><h2><?php esc_html_e( 'Built for your channel', 'artisraw' ); ?></h2></div>
		<div class="grid">
			<?php
			// Minimalist inline line icons (stroke = currentColor), matching the
			// hero differentiator cards above so the block stays visually unified.
			$svg_open  = '<svg class="hub-service__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';
			$ic_store  = $svg_open . '<path d="M3 9l1.5-4.5h15L21 9"/><path d="M4 9v10h16V9"/><path d="M3 9h18"/><path d="M9.5 19v-5h5v5"/></svg>'; // storefront → retailers
			$ic_crate  = $svg_open . '<path d="M3 7.5l9-4 9 4-9 4-9-4z"/><path d="M3 7.5v6l9 4 9-4v-6"/><path d="M12 11.5v6"/></svg>'; // crate/containers → distributors
			$ic_cloche = $svg_open . '<path d="M3.5 18h17"/><path d="M5 18a7 7 0 0 1 14 0"/><path d="M12 8V6"/><path d="M10.5 6h3"/></svg>'; // serving cloche → hospitality
			$ic_tag    = $svg_open . '<path d="M3 12V4h8l9 9-8 8-9-9z"/><circle cx="7.5" cy="7.5" r="1.4"/></svg>'; // tag → private label
			$segs = array(
				array( __( 'Retailers', 'artisraw' ), __( 'Curated, sale-ready assortments for kitchenware, gift and décor shops.', 'artisraw' ), artisraw_localized_url( '/wholesale/' ), $ic_store ),
				array( __( 'Distributors & importers', 'artisraw' ), __( 'Volume pricing, container loads and full export documentation.', 'artisraw' ), artisraw_localized_url( '/olive-wood-wholesale-supplier/' ), $ic_crate ),
				array( __( 'Hospitality', 'artisraw' ), __( 'Durable boards and serveware with custom branding for venues.', 'artisraw' ), artisraw_localized_url( '/olive-wood-wholesale-supplier/' ), $ic_cloche ),
				array( __( 'Private-label brands', 'artisraw' ), __( 'Your logo, packaging and barcode-ready references, made in-house.', 'artisraw' ), artisraw_localized_url( '/private-label-olive-wood/' ), $ic_tag ),
			);
			foreach ( $segs as $s ) {
				echo '<div class="col-3"><div class="hub-service">' . $s[3] . '<h3>' . esc_html( $s[0] ) . '</h3><p>' . esc_html( $s[1] ) . '</p><p>';
				artisraw_arrow_link( __( 'Learn more', 'artisraw' ), $s[2] );
				echo '</p></div></div>';
			}
			?>
		</div>
	</div>
</section>

<!-- 8 · Quantified proof band + reference buyers -->
<?php
artisraw_stat_band( array(
	array( '10,790+', __( 'Trees sponsored', 'artisraw' ), 10790 ),
	array( '≥96%', __( 'First-pass yield', 'artisraw' ) ),
	array( '≤0.5%', __( 'Return rate', 'artisraw' ) ),
	array( '30+', __( 'Countries served', 'artisraw' ), 30 ),
), true );
?>

<!-- 11b · Global distribution: world map only, full-width -->
<section class="global-dist">
	<div class="global-dist__map">
		<img class="global-dist__img" src="<?php echo esc_url( ARTISRAW_URI . '/assets/' . ( function_exists( 'artisraw_is_fr' ) && artisraw_is_fr() ? 'ar-world-map-fr.svg' : 'ar-world-map.svg' ) ); ?>" width="1206" height="582" alt="<?php esc_attr_e( 'Map of ArtisRaw olive wood export markets, from Sfax, Tunisia to 30+ countries', 'artisraw' ); ?>" loading="lazy" decoding="async">
	</div>
</section>

<!-- 12 · Quote block (§9.1 two-column pattern) -->
<section class="container section hub-section" id="quote">
	<?php
	artisraw_quote_block( array(
		'id'       => 'home-quote',
		'location' => 'home',
		'eyebrow'  => __( 'Wholesale inquiries', 'artisraw' ),
		'heading'  => __( 'Request a Quote', 'artisraw' ),
		'intro'    => __( 'Tell us your market and quantities — get a quote with MOQ, pricing and import documentation within 24 hours.', 'artisraw' ),
	) );
	?>
</section>

