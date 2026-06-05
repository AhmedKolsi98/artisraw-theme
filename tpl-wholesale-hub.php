<?php
/**
 * Template Name: Wholesale Hub
 *
 * The money page — /olive-wood-wholesale-supplier/ (CONTENT page 2).
 * One scroll path: can I buy it · on what terms · can I trust you · → quote.
 * Sections ≤9, one idea each; form mid + end; every claim → /references/.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ref = home_url( '/references/' );
$sku_ids = artisraw_get_ready_skus( 6 );

// Preload the hero (LCP) before the <head> is emitted.
artisraw_set_hero_preload( '/assets/hero-wholesale', '(min-width: 1024px) 46vw, 100vw' );

get_header();
artisraw_breadcrumbs();
?>

<!-- 1 · Hero: offer + terms + primary CTA (CONTENT page 2) -->
<section class="section section--dark on-dark hub-hero">
	<div class="container hub-hero__grid">
		<div class="hub-hero__copy">
			<p class="eyebrow"><?php esc_html_e( 'Olive wood wholesale supplier · Sfax, Tunisia', 'artisraw' ); ?></p>
			<h1 class="h1-hero"><?php esc_html_e( 'Olive Wood Wholesale Supplier — Sale-Ready Stock & Private Label', 'artisraw' ); ?></h1>
			<ul class="hub-hero__terms" role="list">
				<li><strong>MOQ 50</strong></li>
				<li><strong>Ships in 72 h</strong></li>
				<li><strong>Private-label engraving</strong></li>
			</ul>
			<p class="lead hub-hero__lead"><?php esc_html_e( 'In-stock olive-wood boards, serveware, utensils and chess sets — ISO 9001 quality control, in-house engraving, and complete US/EU import documentation.', 'artisraw' ); ?></p>
			<p class="hub-hero__cta">
				<a class="btn btn--primary" href="#quote" data-ga="cta_click" data-ga-label="hero" data-ga-location="hub-hero"><?php esc_html_e( 'Request Line-Sheet & Compliance Pack', 'artisraw' ); ?></a>
				<a class="btn btn--tertiary hub-hero__alt" href="#ready-to-ship"><?php esc_html_e( 'See ready-to-ship stock', 'artisraw' ); ?></a>
			</p>
		</div>
		<div class="hub-hero__media">
			<?php
			artisraw_responsive_image( array(
				'base'   => '/assets/hero-wholesale',
				'alt'    => __( 'Olive wood cutting boards and serveware ready for wholesale export', 'artisraw' ),
				'class'  => 'hub-hero__img',
				'width'  => 1800, 'height' => 1200,
				'sizes'  => '(min-width: 1024px) 46vw, 100vw',
				'eager'  => true,
			) );
			?>
		</div>
	</div>
</section>

<!-- Trust strip directly under hero (SPEC §5.6) -->
<div class="section--sand hub-trust">
	<div class="container">
		<?php
		artisraw_trust_strip( array(
			array( __( 'ISO 9001:2015', 'artisraw' ), home_url( '/certifications/' ) ),
			array( __( 'Lacey Act data ready', 'artisraw' ), $ref ),
			array( __( 'EUDR traceability', 'artisraw' ), $ref ),
			array( __( 'ISPM-15 pallets', 'artisraw' ), home_url( '/shipping-logistics/' ) ),
			array( __( '30+ countries', 'artisraw' ), $ref ),
			array( __( 'Unit-by-unit QC', 'artisraw' ), home_url( '/quality-control/' ) ),
		) );
		?>
	</div>
</div>

<!-- 2 · Quick answer (first content block under H1) -->
<div class="container section hub-section">
	<?php
	artisraw_quick_answer( __( 'ArtisRaw supplies wholesale olive wood products from Sfax, Tunisia: cutting boards, serveware, utensils and chess sets. MOQ from 50 units, in-stock SKUs ship in 72 hours, private-label engraving in-house, with ISO 9001 quality control and complete US/EU import documentation (Lacey Act data, ISPM-15, EUDR traceability).', 'artisraw' ) );
	?>
</div>

<!-- 3 · Ready-to-Ship SKU grid (evidence) -->
<section class="container section hub-section" id="ready-to-ship">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Ready-to-ship stock', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'In-stock SKUs with full spec cards — dimensions, case-pack, MOQ and lead time. Dispatched in 72 hours.', 'artisraw' ); ?></p>
	</header>
	<?php
	if ( $sku_ids ) {
		$skus = array_map( 'artisraw_sku_to_array', $sku_ids );
		artisraw_sku_grid( $skus );
	} else {
		echo '<p>' . esc_html__( 'Stock list loading — request the line-sheet for the full catalogue.', 'artisraw' ) . '</p>';
	}
	?>
	<p class="hub-section__note"><?php esc_html_e( 'Samples available — cost deducted from your first order.', 'artisraw' ); ?>
		<a href="<?php echo esc_url( home_url( '/how-to-order/' ) ); ?>"><?php esc_html_e( 'How ordering works', 'artisraw' ); ?></a></p>
</section>

<!-- 4 · Risk-reversal: QC numbers + import confidence (claim → number → document) -->
<?php
artisraw_stat_band( array(
	array( '≥96%', __( 'First-pass yield', 'artisraw' ) ),
	array( '≤0.5%', __( 'Return rate', 'artisraw' ) ),
	array( '72 h', __( 'Stock dispatch', 'artisraw' ) ),
), true );
?>
<section class="container section hub-section">
	<div class="hub-confidence">
		<h2><?php esc_html_e( 'Import with confidence — US &amp; EU', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Every shipment ships with the paperwork your broker needs. Each claim is backed by a downloadable document.', 'artisraw' ); ?></p>
		<ul class="hub-confidence__list" role="list">
			<li><strong><?php esc_html_e( 'Lacey Act', 'artisraw' ); ?></strong> — <?php esc_html_e( 'PPQ 505 declaration data (genus, species, country of harvest) prepared per shipment.', 'artisraw' ); ?> <a href="<?php echo esc_url( $ref ); ?>"><?php esc_html_e( 'See documentation', 'artisraw' ); ?></a></li>
			<li><strong><?php esc_html_e( 'EUDR', 'artisraw' ); ?></strong> — <?php esc_html_e( 'Geolocated sourcing and due-diligence traceability for EU importers.', 'artisraw' ); ?> <a href="<?php echo esc_url( $ref ); ?>"><?php esc_html_e( 'See documentation', 'artisraw' ); ?></a></li>
			<li><strong><?php esc_html_e( 'ISO 9001:2015', 'artisraw' ); ?></strong> — <?php esc_html_e( 'Certified quality management; batch traceability on request.', 'artisraw' ); ?> <a href="<?php echo esc_url( home_url( '/certifications/' ) ); ?>"><?php esc_html_e( 'View certificate', 'artisraw' ); ?></a></li>
			<li><strong><?php esc_html_e( 'Food-safe finish', 'artisraw' ); ?></strong> — <?php esc_html_e( 'Mineral oil + beeswax; MSDS available.', 'artisraw' ); ?> <a href="<?php echo esc_url( $ref ); ?>"><?php esc_html_e( 'Download MSDS', 'artisraw' ); ?></a></li>
		</ul>
	</div>
</section>

<!-- 5 · Quote form — mid placement -->
<section class="container section hub-section" id="quote">
	<div class="hub-form-wrap">
		<?php artisraw_quote_form( array( 'id' => 'hub-quote-mid', 'location' => 'hub-mid', 'heading' => __( 'Get your line-sheet &amp; compliance pack', 'artisraw' ) ) ); ?>
	</div>
</section>

<!-- 6 · Core B2B services (one idea: what we do for buyers) -->
<section class="section--sand hub-services">
	<div class="container section hub-section">
		<header class="hub-section__head">
			<h2><?php esc_html_e( 'More than a manufacturer — your B2B partner', 'artisraw' ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Sourcing, private label, QC and export support — built to make Tunisian olive wood easy to buy and easy to reorder.', 'artisraw' ); ?></p>
		</header>
		<div class="grid hub-services__grid">
			<?php
			$services = array(
				array( __( 'Wholesale production', 'artisraw' ), __( 'Scalable runs for importers, distributors, retailers and seasonal replenishment.', 'artisraw' ) ),
				array( __( 'Private label', 'artisraw' ), __( 'Engraving, custom packaging, retail labels and barcode-ready references.', 'artisraw' ) ),
				array( __( 'Corporate gifts', 'artisraw' ), __( 'Premium gift packs, chess sets, kitchen bundles and personalised articles.', 'artisraw' ) ),
				array( __( 'Custom orders', 'artisraw' ), __( 'Custom shapes, sizes, finishes and product development for your market.', 'artisraw' ) ),
				array( __( 'Quality control', 'artisraw' ), __( 'Inspection, batch photo documentation, finish verification and packing control.', 'artisraw' ) ),
				array( __( 'Export support', 'artisraw' ), __( 'Commercial documents, ISPM-15 pallets, packing lists and certificates.', 'artisraw' ) ),
			);
			foreach ( $services as $s ) {
				echo '<div class="col-4"><div class="hub-service"><h3>' . esc_html( $s[0] ) . '</h3><p>' . esc_html( $s[1] ) . '</p></div></div>';
			}
			?>
		</div>
	</div>
</section>

<!-- 7 · Micro-FAQ (objection handling). Visible accordion only — FAQPage schema lives on /faq/. -->
<section class="container section hub-section">
	<h2><?php esc_html_e( 'Wholesale questions, answered', 'artisraw' ); ?></h2>
	<?php
	artisraw_faq_accordion( array(
		array( __( 'What is your minimum order quantity?', 'artisraw' ), __( 'MOQ starts at 50 units per SKU for most lines and is confirmed on your quote. Mixed assortments across SKU families are welcome.', 'artisraw' ) ),
		array( __( 'How fast can you ship?', 'artisraw' ), __( 'In-stock SKUs are dispatched within 72 hours. Made-to-order and private-label runs typically take 2–4 weeks before shipping.', 'artisraw' ) ),
		array( __( 'Can I get samples first?', 'artisraw' ), __( 'Yes. Samples are available and their cost is deducted from your first production order. Shipping depends on product and destination.', 'artisraw' ) ),
		array( __( 'Do you handle US and EU import paperwork?', 'artisraw' ), __( 'Yes — Lacey Act declaration data, EUDR traceability, ISPM-15 pallets, commercial invoice and packing list are prepared per shipment.', 'artisraw' ) ),
		array( __( 'Do you offer private-label engraving?', 'artisraw' ), __( 'In-house. We engrave logos and produce custom packaging, retail labels and barcode-ready references for approved private-label projects.', 'artisraw' ) ),
		array( __( 'What are your shipping options?', 'artisraw' ), __( 'Air freight runs 5–12 days; ocean 25–40 days. We quote FOB Tunisia, CIF, DAP or DDP depending on your preference.', 'artisraw' ) ),
	), false, 'hub-faq' );
	?>
</section>

<!-- 8 · Procurement reference: logistics + downloads + trusted clients -->
<section class="section--sand">
	<div class="container section hub-section">
		<h2><?php esc_html_e( 'Logistics &amp; commercial terms', 'artisraw' ); ?></h2>
		<?php
		artisraw_data_table(
			__( 'Lead times, transit & Incoterms', 'artisraw' ),
			array( __( 'Mode', 'artisraw' ), __( 'Transit', 'artisraw' ), __( 'MOQ', 'artisraw' ), __( 'Incoterms', 'artisraw' ) ),
			array(
				array( __( 'Air freight', 'artisraw' ), __( '5–12 days', 'artisraw' ), '50', 'FOB / CIF / DAP / DDP' ),
				array( __( 'Ocean (LCL)', 'artisraw' ), __( '25–40 days', 'artisraw' ), '50', 'FOB / CIF / DAP' ),
				array( __( 'Ocean (FCL)', 'artisraw' ), __( '25–40 days', 'artisraw' ), '500', 'FOB / CIF / DDP' ),
			),
			'logistics'
		);
		?>
		<h3 class="hub-downloads__title"><?php esc_html_e( 'Download centre', 'artisraw' ); ?></h3>
		<div class="grid">
			<?php
			$docs = array(
				array( 'title' => __( 'Wholesale line-sheet', 'artisraw' ), 'type' => 'PDF', 'size' => '2.1 MB', 'updated' => 'Jun 2026', 'href' => artisraw_doc_url( 'line-sheet.pdf' ), 'name' => 'line-sheet' ),
				array( 'title' => __( 'Compliance pack (Lacey / EUDR)', 'artisraw' ), 'type' => 'ZIP', 'size' => '8.4 MB', 'updated' => 'Jun 2026', 'href' => artisraw_doc_url( 'compliance-pack.zip' ), 'name' => 'compliance-pack' ),
				array( 'title' => __( 'ISO 9001:2015 certificate', 'artisraw' ), 'type' => 'PDF', 'size' => '1.2 MB', 'updated' => 'May 2026', 'href' => artisraw_doc_url( 'iso_9001_2015.pdf' ), 'name' => 'iso_9001_2015' ),
			);
			foreach ( $docs as $d ) {
				echo '<div class="col-4">';
				artisraw_doc_card( $d );
				echo '</div>';
			}
			?>
		</div>
		<?php
		// Trusted clients — type-only band (names listed with permission only; SPEC §6.9).
		artisraw_logo_band(
			array( array( 'Maintea' ), array( 'Eataly' ), array( 'Olivier Napa Valley' ), array( 'Delta Co Limited' ) ),
			__( 'Selected buyers &amp; partners', 'artisraw' )
		);
		?>
	</div>
</section>

<!-- 9 · Quote form — end placement -->
<section class="container section hub-section">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Tell us your market — get a quote in 24 h', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Share your destination, categories and quantities. We’ll prepare a wholesale proposal with MOQ, pricing and import documentation.', 'artisraw' ); ?></p>
	</header>
	<div class="hub-form-wrap">
		<?php artisraw_quote_form( array( 'id' => 'hub-quote-end', 'location' => 'hub-end' ) ); ?>
	</div>
</section>

<?php
// JSON-LD: ItemList of Product (BreadcrumbList already emitted by breadcrumbs).
artisraw_product_itemlist( $sku_ids, get_permalink() );

get_footer();
