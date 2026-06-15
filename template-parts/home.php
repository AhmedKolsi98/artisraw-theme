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

<!-- 1 · Hero (Figma): photo collage + amber value-prop headline + ISO badge -->
<?php
artisraw_photo_hero( array(
	'base'      => '/assets/ar-hero-collage',
	'alt'       => __( 'Handmade Tunisian olive wood collections', 'artisraw' ),
	'widths'    => array( 600, 1200 ),
	'w'         => 1672, 'h' => 941,
	'eyebrow'   => __( 'ISO 9001 olive wood manufacturer · Sfax, Tunisia', 'artisraw' ),
	'title'     => __( 'Premium Olive Wood for Wholesale Buyers', 'artisraw' ),
	'as_h1'     => true,
	'support'   => __( 'Creators of handmade Tunisian olive wood collections for retailers, distributors, hospitality groups, corporate gifts and private-label brands.', 'artisraw' ),
	'support_tag' => 'h2',
	'cta_label' => __( 'Start Your Project', 'artisraw' ),
	'cta_url'   => artisraw_localized_url( '/request-quote/' ),
	'badge'     => true,
	'loc'       => 'home-hero',
) );
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
	'field' => 'sand', 'field_left' => true,
	'eyebrow' => __( 'Who we are', 'artisraw' ),
	'heading' => __( 'A Tunisian manufacturer with artisan roots', 'artisraw' ),
	'body'    => __( 'Founded in Sfax in 2019, ArtisRaw blends ancestral woodworking with modern, ISO 9001 production — handmade heritage built for export-ready wholesale.', 'artisraw' ),
	'link_label' => __( 'Our story & founders', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/about/' ),
	'img_base' => '/assets/ar-workshop', 'img_alt' => __( 'ArtisRaw artisans in the Sfax workshop', 'artisraw' ),
	'img_widths' => array( 600, 1200 ), 'w' => 1400, 'h' => 900,
) );
artisraw_color_block( array(
	'field' => 'espresso',
	'eyebrow' => __( 'Our Mission', 'artisraw' ),
	'heading' => __( 'Beautiful products, responsibly made', 'artisraw' ),
	'body'    => __( 'Our mission is to craft responsible, food-safe and beautiful olive wood products for global wholesale markets while supporting artisans, preserving raw material value and reducing waste.', 'artisraw' ),
	'link_label' => __( 'Learn more', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/about/' ),
	'img_base' => '/assets/ar-boards', 'img_alt' => __( 'Olive wood board showing dense Chemlali grain', 'artisraw' ),
	'img_widths' => array( 600 ), 'w' => 548, 'h' => 365,
) );
artisraw_color_block( array(
	'field' => 'sand', 'field_left' => true,
	'heading' => __( 'Our Vision', 'artisraw' ),
	'body'    => __( 'We aim to make Tunisian olive wood a global standard of sustainable luxury by connecting Mediterranean heritage with international B2B export systems.', 'artisraw' ),
	'link_label' => __( 'Read more', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/about/' ),
	'img_base' => '/assets/ar-grove', 'img_alt' => __( 'Olive grove near Sfax', 'artisraw' ),
	'img_widths' => array( 600, 1200 ), 'w' => 1273, 'h' => 900,
) );
?>

<!-- 4b · Wayfinding trio band: catalogue · process · FAQs -->
<?php
artisraw_trio_band( array(
	array( 'label' => __( 'Discover Our Catalogue', 'artisraw' ), 'cue' => __( 'Discover more', 'artisraw' ), 'href' => artisraw_localized_url( '/catalogue/' ) ),
	array( 'label' => __( 'Our process', 'artisraw' ), 'cue' => __( 'Learn more', 'artisraw' ), 'href' => artisraw_localized_url( '/production-process/' ) ),
	array( 'label' => __( 'FAQs', 'artisraw' ), 'cue' => __( 'Read more', 'artisraw' ), 'href' => artisraw_localized_url( '/faq/' ) ),
), 'trio-band--wide-first' );
?>

<!-- 5 · Ready-to-Ship Bestsellers (§6 SKU strip) -->
<?php $ready = function_exists( 'artisraw_get_ready_skus' ) ? artisraw_get_ready_skus( 6 ) : array(); ?>
<?php if ( $ready ) : ?>
<section class="section--sand">
	<div class="container section hub-section sku-strip">
		<p class="sku-strip__intro"><?php esc_html_e( 'Probably the most beautiful boards your shelves will carry.', 'artisraw' ); ?></p>
		<header class="hub-section__head"><h2><?php esc_html_e( 'Ready-to-Ship Bestsellers', 'artisraw' ); ?></h2></header>
		<?php artisraw_sku_grid( array_map( 'artisraw_sku_to_array', $ready ) ); ?>
		<p class="hub-section__note"><?php artisraw_arrow_link( __( 'Browse all categories', 'artisraw' ), artisraw_localized_url( '/wholesale/' ) ); ?></p>
	</div>
</section>
<?php endif; ?>

<!-- 7 · Built for your channel (self-selection, block formula) -->
<section class="section--sand">
	<div class="container section hub-section">
		<div class="section-opener"><h2><?php esc_html_e( 'Built for your channel', 'artisraw' ); ?></h2></div>
		<div class="grid">
			<?php
			$segs = array(
				array( __( 'Retailers', 'artisraw' ), __( 'Curated, sale-ready assortments for kitchenware, gift and décor shops.', 'artisraw' ), artisraw_localized_url( '/wholesale/' ) ),
				array( __( 'Distributors & importers', 'artisraw' ), __( 'Volume pricing, container loads and full export documentation.', 'artisraw' ), artisraw_localized_url( '/olive-wood-wholesale-supplier/' ) ),
				array( __( 'Hospitality', 'artisraw' ), __( 'Durable boards and serveware with custom branding for venues.', 'artisraw' ), artisraw_localized_url( '/olive-wood-wholesale-supplier/' ) ),
				array( __( 'Private-label brands', 'artisraw' ), __( 'Your logo, packaging and barcode-ready references, made in-house.', 'artisraw' ), artisraw_localized_url( '/private-label-olive-wood/' ) ),
			);
			foreach ( $segs as $s ) {
				echo '<div class="col-3"><div class="hub-service"><h3>' . esc_html( $s[0] ) . '</h3><p>' . esc_html( $s[1] ) . '</p><p>';
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
<div class="container section hub-section">
	<?php artisraw_logo_band( array( array( 'Eataly' ), array( 'Karthage LLC' ), array( 'Folksy' ), array( 'Delta Co.' ), array( 'TunSouk' ) ), __( 'Selected buyers &amp; partners', 'artisraw' ) ); ?>
</div>

<!-- 9 · Feature testimonial (Figma) -->
<?php
artisraw_testimonial_feature( array(
	'heading'    => __( 'What they say…', 'artisraw' ),
	'quote'      => __( 'ArtisRaw is reliable, consistent and easy to work with. Their handmade olive wood products helped us build a premium retail collection with a strong Mediterranean story.', 'artisraw' ),
	'author'     => __( 'Retail buyer', 'artisraw' ),
	'role'       => __( 'Specialty retailer, United Kingdom', 'artisraw' ),
	'link_label' => __( 'Request a quote', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/request-quote/' ),
	'img_base'   => '/assets/ar-trade-fair',
	'img_alt'    => __( 'ArtisRaw booth at an international B2B trade fair', 'artisraw' ),
	'img_w'      => 1200, 'img_h' => 900, 'img_widths' => array( 600, 1200 ),
) );
?>

<!-- 10 · From the Guide (real articles) -->
<?php
$guide = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'ignore_sticky_posts' => true ) );
if ( $guide->have_posts() ) : ?>
<section class="section--sand">
	<div class="container section hub-section">
		<div class="section-opener"><h2><?php esc_html_e( 'From the Olive Wood Guide', 'artisraw' ); ?></h2></div>
		<div class="grid">
			<?php foreach ( $guide->posts as $gp ) : ?>
				<div class="col-4"><?php artisraw_article_card( artisraw_post_to_card( $gp->ID ) ); ?></div>
			<?php endforeach; ?>
		</div>
		<p class="hub-section__note"><?php artisraw_arrow_link( __( 'Read the Magazine', 'artisraw' ), artisraw_localized_url( '/magazine/' ) ); ?></p>
	</div>
</section>
<?php endif; wp_reset_postdata(); ?>

<!-- 11 · Instagram strip -->
<section class="container section hub-section">
	<?php
	artisraw_instagram_strip(
		array(
			array( __( 'Olive wood boards', 'artisraw' ), '', '/assets/ar-boards', array( 600 ) ),
			array( __( 'In the workshop', 'artisraw' ), '', '/assets/ar-workshop', array( 600 ) ),
			array( __( 'Mortar & pestle', 'artisraw' ), '', '/assets/ar-mortar', array( 600 ) ),
			array( __( 'Chess & gifts', 'artisraw' ), '', '/assets/ar-chess', array( 600 ) ),
			array( __( 'Carved bowls', 'artisraw' ), '', '/assets/ar-bowl', array( 600 ) ),
			array( __( 'Ready to ship', 'artisraw' ), '', '/assets/ar-collection', array( 600 ) ),
		),
		'artisraw'
	);
	?>
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

