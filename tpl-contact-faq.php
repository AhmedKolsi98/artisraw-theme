<?php
/**
 * Template Name: Contact / FAQ
 *
 * Mode via page meta `cf_mode`:
 *  - faq     → /faq/ : full FAQ bank + FAQPage JSON-LD
 *  - contact → /contact/ : quick answer + NAP + form + LocalBusiness JSON-LD
 *  - quote   → /request-quote/ : trimmed header/footer, two-step form only
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pid  = get_queried_object_id();
$mode = get_post_meta( $pid, 'cf_mode', true ) ?: 'contact';
$qa   = get_post_meta( $pid, 'quick_answer', true );

// Trimmed chrome on the quote page (SPEC §5.6).
if ( 'quote' === $mode ) {
	add_filter( 'body_class', function ( $c ) { $c[] = 'is-trimmed'; return $c; } );
}

get_header();
artisraw_breadcrumbs();
?>
<div class="container section hub-section">
	<header class="page-head"><h1><?php the_title(); ?></h1></header>
	<?php if ( $qa ) { artisraw_quick_answer( $qa ); } ?>
</div>

<?php if ( 'faq' === $mode ) : /* ---------------- FAQ ---------------- */ ?>

	<section class="container section hub-section">
	<?php
	$faq = array(
		array( __( 'Do you have a minimum order quantity?', 'artisraw' ), __( 'Yes. MOQ starts at 50 units per SKU for most lines and is confirmed on your quote. Mixed assortments across SKU families are welcome.', 'artisraw' ) ),
		array( __( 'Can I request samples?', 'artisraw' ), __( 'Yes. Samples are available and their cost is deducted from your first production order. Shipping depends on product type and destination.', 'artisraw' ) ),
		array( __( 'How fast can you ship?', 'artisraw' ), __( 'In-stock SKUs are dispatched within 72 hours. Custom and private-label production takes about 6–8 weeks before shipping.', 'artisraw' ) ),
		array( __( 'Do you offer private label?', 'artisraw' ), __( 'Yes — in-house. We engrave logos and produce custom packaging, retail labels and barcode-ready references for approved projects.', 'artisraw' ) ),
		array( __( 'Are the products food-safe?', 'artisraw' ), __( 'Food-contact items are finished with a food-safe mineral oil and beeswax blend; finish MSDS is available to professional buyers.', 'artisraw' ) ),
		array( __( 'Do you provide quality control?', 'artisraw' ), __( 'Yes — unit-by-unit inspection, batch photo documentation and packing control, with ≥96% first-pass yield.', 'artisraw' ) ),
		array( __( 'What certifications do you have?', 'artisraw' ), __( 'ISO 9001:2015 quality management, plus forestry licence #4684. Certificates and reports are downloadable on the certifications page.', 'artisraw' ) ),
		array( __( 'Can you handle US and EU import paperwork?', 'artisraw' ), __( 'Yes — Lacey Act declaration data, EUDR traceability, ISPM-15 pallets, commercial invoice and packing list per shipment.', 'artisraw' ) ),
		array( __( 'Which Incoterms do you offer?', 'artisraw' ), __( 'FOB Tunisia, CIF, DAP or DDP, depending on your preference and destination.', 'artisraw' ) ),
		array( __( 'What are your shipping transit times?', 'artisraw' ), __( 'Air freight runs 5–12 days; ocean 25–40 days, on ISPM-15 pallets.', 'artisraw' ) ),
		array( __( 'Which product categories do you offer?', 'artisraw' ), __( 'Cutting boards, utensils, bowls and serveware, mortars, chess sets, jars, trays and décor — 16 product families in total.', 'artisraw' ) ),
		array( __( 'Are all products identical?', 'artisraw' ), __( 'No — olive wood is natural, so grain, colour and small variations are part of the handmade value, not defects.', 'artisraw' ) ),
		array( __( 'Can I reorder previous SKUs?', 'artisraw' ), __( 'Yes. We keep SKU references and order history to make replenishment fast.', 'artisraw' ) ),
		array( __( 'Do you create corporate gifts?', 'artisraw' ), __( 'Yes — personalised gift packs, chess sets, kitchen bundles and seasonal collections with branded packaging.', 'artisraw' ) ),
		array( __( 'How is olive wood cared for?', 'artisraw' ), __( 'Hand-wash, dry immediately, avoid prolonged soaking, and refresh with food-safe oil when needed.', 'artisraw' ) ),
		array( __( 'How do I start an order?', 'artisraw' ), __( 'Send your company, country, client type, categories, estimated quantities, packaging needs and target date via the quote form.', 'artisraw' ) ),
	);
	artisraw_faq_accordion( $faq, true, 'faq' ); // emits FAQPage JSON-LD
	?>
	</section>

	<div class="container section hub-section">
		<p class="hub-hero__cta"><a class="btn btn--primary" href="<?php echo esc_url( artisraw_localized_url( '/request-quote/' ) ); ?>"><?php esc_html_e( 'Request Line-Sheet & Compliance Pack', 'artisraw' ); ?></a></p>
	</div>

<?php else : /* ---------------- CONTACT or QUOTE ---------------- */ ?>

	<section class="container section hub-section">
		<?php
		artisraw_quote_block( array(
			'id'       => 'contact-quote',
			'location' => $mode,
			'eyebrow'  => __( 'Wholesale inquiries', 'artisraw' ),
			'heading'  => __( 'Request a Quote', 'artisraw' ),
			'intro'    => __( 'Send your market, categories and quantities — we reply within one business day with MOQ, pricing and import documentation.', 'artisraw' ),
		) );
		?>
	</section>

	<?php if ( 'contact' === $mode ) : ?>
	<section class="section--sand">
		<div class="container section hub-section">
			<aside class="contact-grid__nap">
				<h2><?php esc_html_e( 'Factory &amp; contact', 'artisraw' ); ?></h2>
				<address class="contact-nap">
					<strong>ArtisRaw</strong><br>
					Route Saltania, km 4.5<br>
					Sfax, Tunisia<br><br>
					<a href="mailto:contact@artisraw.com">contact@artisraw.com</a><br>
					<a href="tel:+19292381075">+1 929-238-1075</a><br>
					<a href="https://wa.me/19292381075" aria-label="<?php esc_attr_e( 'Chat with ArtisRaw on WhatsApp', 'artisraw' ); ?>" data-ga="whatsapp_click"><?php esc_html_e( 'WhatsApp', 'artisraw' ); ?></a>
				</address>
				<p class="eyebrow"><?php esc_html_e( 'Quotes within 24 hours', 'artisraw' ); ?></p>
			</aside>
		</div>
	</section>
	<?php endif; ?>

	<?php
	if ( 'contact' === $mode && function_exists( 'artisraw_jsonld' ) ) {
		artisraw_jsonld( array(
			'@context'    => 'https://schema.org',
			'@type'       => 'LocalBusiness',
			'name'        => 'ArtisRaw',
			'image'       => ARTISRAW_URI . '/assets/hero-wholesale-1200.webp',
			'email'       => 'contact@artisraw.com',
			'telephone'   => '+1-929-238-1075',
			'url'         => artisraw_localized_url( '/contact/' ),
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => 'Route Saltania, km 4.5',
				'addressLocality' => 'Sfax',
				'addressCountry'  => 'TN',
			),
			'priceRange'  => '$$',
		) );
	}
	?>

<?php endif; ?>

<?php
get_footer();
