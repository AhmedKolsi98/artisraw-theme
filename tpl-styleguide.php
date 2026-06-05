<?php
/**
 * Template Name: Styleguide (hidden)
 *
 * Renders the full UI kit (SPEC §4) with sample data and every interaction
 * state, for QA. Always noindex. Not linked from anywhere.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$GLOBALS['artisraw_force_noindex'] = true; // never index the styleguide.

get_header();

/* ---- Sample data (representative; real data arrives via ACF in later phases) ---- */
$sg_skus = array(
	array( 'name' => 'Classic Cutting Board 40cm', 'sku' => 'AR-CB-40', 'dimensions' => '40 × 22 × 2 cm', 'unit_weight' => '0.9 kg', 'case_pack' => '12', 'carton' => '46 × 34 × 28 cm', 'moq' => '50', 'lead_time' => '72 h (stock)', 'exw_tier' => 'on request' ),
	array( 'name' => 'Olive Wood Serving Bowl 25cm', 'sku' => 'AR-BW-25', 'dimensions' => 'Ø25 × 9 cm', 'unit_weight' => '0.7 kg', 'case_pack' => '10', 'carton' => '54 × 28 × 30 cm', 'moq' => '50', 'lead_time' => '3–4 weeks', 'exw_tier' => 'on request' ),
	array( 'name' => 'Spatula & Spoon Set', 'sku' => 'AR-UT-SET', 'dimensions' => '30 cm', 'unit_weight' => '0.18 kg', 'case_pack' => '24', 'carton' => '42 × 30 × 24 cm', 'moq' => '100', 'lead_time' => '72 h (stock)', 'exw_tier' => 'on request' ),
);

$sg_faq = array(
	array( 'Do you have a minimum order quantity?', 'Yes. MOQ depends on the product family, size, packaging and private-label needs — typically from 50 units. Your exact MOQ is confirmed during quotation.' ),
	array( 'Are the products food-safe?', 'Food-contact items are finished with a food-safe mineral oil and beeswax blend. Finish MSDS and ISO 9001 documentation are available to professional buyers on request.' ),
	array( 'Do you offer private label?', 'Yes. We support logo engraving, custom labels, gift boxes and retail-ready packaging depending on MOQ and project feasibility.' ),
	array( 'Do you export worldwide?', 'Yes. We ship to North America, Europe, the GCC/Middle East and beyond. Air freight runs 5–12 days; ocean 25–40 days, with ISPM-15 pallets where required.' ),
);

$sg_stats = array(
	array( '10,790+', 'Trees sponsored', 10790 ),
	array( '≥96%', 'First-pass yield' ),
	array( '≤0.5%', 'Return rate' ),
	array( '30+', 'Countries served', 30 ),
	array( '50', 'Minimum order', 50 ),
	array( '72 h', 'Stock dispatch' ),
);

$sg_docs = array(
	array( 'title' => 'ISO 9001:2015 certificate', 'type' => 'PDF', 'size' => '1.2 MB', 'updated' => 'May 2026', 'href' => artisraw_doc_url( 'iso_9001_2015.pdf' ), 'name' => 'iso_9001_2015' ),
	array( 'title' => 'Compliance pack (Lacey / EUDR)', 'type' => 'ZIP', 'size' => '8.4 MB', 'updated' => 'May 2026', 'href' => artisraw_doc_url( 'compliance-pack.zip' ), 'name' => 'compliance-pack' ),
	array( 'title' => 'Finish MSDS', 'type' => 'PDF', 'size' => '420 KB', 'updated' => 'Apr 2026', 'href' => artisraw_doc_url( 'finish_msds.pdf' ), 'name' => 'finish_msds' ),
);

$sg_cats = array(
	array( 'title' => 'Cutting Boards', 'href' => home_url( '/wholesale/olive-wood-cutting-boards/' ), 'count' => '14 SKUs' ),
	array( 'title' => 'Utensils', 'href' => home_url( '/wholesale/olive-wood-utensils/' ), 'count' => '22 SKUs' ),
	array( 'title' => 'Bowls & Serveware', 'href' => home_url( '/wholesale/olive-wood-bowls-serveware/' ), 'count' => '18 SKUs' ),
);

$sg_articles = array(
	array( 'title' => 'Why Chemlali olive wood resists knife scarring', 'href' => '#', 'excerpt' => 'Density, low porosity and Janka hardness — the three properties buyers ask about most.', 'author' => 'Reviewed by Ihsen Triki', 'date' => 'Updated May 2026' ),
	array( 'title' => 'Importing olive wood to the USA: Lacey Act basics', 'href' => '#', 'excerpt' => 'What declaration data you need and how ArtisRaw supplies it with every shipment.', 'author' => 'Reviewed by Ihsen Triki', 'date' => 'Updated Apr 2026' ),
);

$sg_trust = array(
	array( 'ISO 9001', home_url( '/certifications/' ) ),
	array( 'MEA Award', home_url( '/about/' ) ),
	array( '30+ Countries', home_url( '/references/' ) ),
	array( 'Chemlali olive wood', home_url( '/olive-wood/' ) ),
	array( 'Unit-by-unit QC', home_url( '/quality-control/' ) ),
);

/** Tiny section-header helper for the styleguide only. */
function sg_h( $title, $note = '' ) {
	echo '<h2 style="margin-top:var(--sp-6)">' . esc_html( $title ) . '</h2>';
	if ( $note ) {
		echo '<p class="lead">' . esc_html( $note ) . '</p>';
	}
}
?>

<div class="container section">
	<p class="eyebrow">ArtisRaw UI kit</p>
	<h1>Component styleguide</h1>
	<p class="lead">Every reusable block (SPEC §4) with its states. Hidden &amp; noindex. Tab through it, open the accordion, and submit the form to see validation and the two-step flow.</p>

	<?php sg_h( 'Buttons', 'Three levels × all states.' ); ?>
	<div style="display:flex;flex-wrap:wrap;gap:var(--sp-2);align-items:center">
		<button class="btn btn--primary">Primary</button>
		<button class="btn btn--secondary">Secondary</button>
		<a class="btn btn--tertiary" href="#">Tertiary link</a>
		<button class="btn btn--primary" disabled>Disabled</button>
		<button class="btn btn--primary" aria-busy="true"><span class="btn__label">Loading</span></button>
	</div>

	<?php sg_h( 'Quick-answer box' ); ?>
	<?php artisraw_quick_answer( 'ArtisRaw is a Tunisian olive wood manufacturer and B2B exporter in Sfax, ISO 9001 certified, serving retailers, distributors and hospitality groups in 30+ countries. Wholesale MOQ starts at 50 units, with in-stock lines dispatched within 72 hours.' ); ?>

	<?php sg_h( 'Trust strip', 'Chips link to proof pages.' ); ?>
	<?php artisraw_trust_strip( $sg_trust ); ?>

	<?php sg_h( 'SKU spec cards' ); ?>
	<?php artisraw_sku_grid( $sg_skus ); ?>

	<?php sg_h( 'Data table — logistics', 'Stacks to label/value pairs below 768px.' ); ?>
	<?php
	artisraw_data_table(
		'Lead times & shipping',
		array( 'Mode', 'Transit', 'MOQ', 'Notes' ),
		array(
			array( 'Air freight', '5–12 days', '50', 'Express samples & top-ups' ),
			array( 'Ocean (LCL)', '25–40 days', '50', 'Best per-unit cost' ),
			array( 'Ocean (FCL)', '25–40 days', '500', 'Full container, ISPM-15 pallets' ),
		),
		'logistics'
	);
	?>

	<?php sg_h( 'Data table — Chemlali wood properties' ); ?>
	<?php
	artisraw_data_table(
		'Material properties',
		array( 'Property', 'Value' ),
		array(
			array( 'Species', 'Olea europaea (Chemlali)' ),
			array( 'Density', '~900–1,100 kg/m³' ),
			array( 'Janka hardness', '~2,700 lbf' ),
			array( 'Finish', 'Food-safe mineral oil + beeswax' ),
		),
		'properties'
	);
	?>

	<?php sg_h( 'Stat band', 'Count-up animates once, respects reduced-motion. Numbers present without JS.' ); ?>
</div>

<?php artisraw_stat_band( $sg_stats ); ?>

<div class="container section">
	<?php sg_h( 'Category cards', 'Stretched-link — whole card clickable, single &lt;a&gt;.' ); ?>
	<div class="grid">
		<?php foreach ( $sg_cats as $c ) : ?>
			<div class="col-4"><?php artisraw_category_card( $c ); ?></div>
		<?php endforeach; ?>
	</div>

	<?php sg_h( 'Article cards', 'With byline + Last updated.' ); ?>
	<div class="grid">
		<?php foreach ( $sg_articles as $a ) : ?>
			<div class="col-6"><?php artisraw_article_card( $a ); ?></div>
		<?php endforeach; ?>
	</div>

	<?php sg_h( 'Document / download cards', 'Fire GA4 doc_download.' ); ?>
	<div class="grid">
		<?php foreach ( $sg_docs as $d ) : ?>
			<div class="col-4"><?php artisraw_doc_card( $d ); ?></div>
		<?php endforeach; ?>
	</div>

	<?php sg_h( 'Reference-buyer logo band', 'Type-only fallback until permission/logos exist.' ); ?>
	<?php artisraw_logo_band( array( array( 'Maison Verde' ), array( 'Nordhaus' ), array( 'Souk &amp; Co' ), array( 'Cedar Hospitality' ) ), 'Trusted by buyers including' ); ?>

	<?php sg_h( 'FAQ accordion', 'WAI-ARIA, multiple-open, deep-linkable. Mirrors to FAQPage JSON-LD.' ); ?>
	<?php artisraw_faq_accordion( $sg_faq, true, 'sg-faq' ); ?>

	<?php sg_h( 'Two-step quote form', 'Validate on blur + submit; submit to see the success panel + Step 2.' ); ?>
	<div style="max-width:680px">
		<?php artisraw_quote_form( array( 'id' => 'sg-quote', 'location' => 'styleguide' ) ); ?>
	</div>
</div>

<?php
get_footer();
