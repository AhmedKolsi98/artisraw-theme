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

<!-- ===================== Phase 5 — design-parity components ===================== -->
<div class="container section">
	<?php sg_h( 'Phase 5 — design-parity components', 'Blocks for the homepage middle and the Services / Worldwide pages. Images run through the WebP pipeline.' ); ?>

	<?php sg_h( 'Photo mosaic', 'Labelled image grid with one feature tile (big), one wide and two square tiles. Stacks on mobile.' ); ?>
	<?php
	artisraw_photo_mosaic(
		array(
			array( 'base' => '/assets/ar-grove', 'alt' => 'Olive grove near Sfax', 'label' => 'From the tree', 'variant' => 'big', 'w' => 1273, 'h' => 900, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-workshop', 'alt' => 'Artisans in the workshop', 'label' => 'Handmade production', 'variant' => 'wide', 'w' => 1400, 'h' => 358, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-boards', 'alt' => 'Olive wood board grain', 'label' => 'Chemlali dense grain', 'w' => 548, 'h' => 365, 'widths' => array( 600 ) ),
			array( 'base' => '/assets/ar-collection', 'alt' => 'Olive wood product range', 'label' => 'Premium B2B collections', 'w' => 1920, 'h' => 960, 'widths' => array( 600, 1200 ) ),
		),
		'From tree, to workshop, to wholesale shelves',
		'Responsible sourcing, handmade production, dense Chemlali grain and export-ready collections.'
	);
	?>

	<?php sg_h( 'Collection tiles', 'Square image tiles, whole-card link + gradient label (homepage middle).' ); ?>
	<ul class="collections" role="list">
		<?php
		$sg_cols = array(
			array( 'Kitchen & boards', '/assets/ar-boards', array( 600 ) ),
			array( 'Serveware & bowls', '/assets/ar-mortar', array( 600 ) ),
			array( 'Gifts & chess', '/assets/ar-chess', array( 600 ) ),
			array( 'Décor & lifestyle', '/assets/ar-collection', array( 600, 1200 ) ),
		);
		foreach ( $sg_cols as $c ) {
			echo '<li><a class="collection" href="#">';
			artisraw_responsive_image( array( 'base' => $c[1], 'alt' => $c[0], 'class' => 'collection__img', 'width' => 600, 'height' => 750, 'widths' => $c[2], 'sizes' => '25vw' ) );
			echo '<span class="collection__label">' . esc_html( $c[0] ) . '</span></a></li>';
		}
		?>
	</ul>

	<?php sg_h( 'Numbered steps', 'Process / workflow (services 8-step, QC timeline).' ); ?>
	<?php
	artisraw_steps( array(
		array( '', 'Buyer brief', 'Market, categories, quantities and target date.' ),
		array( '', 'Quote & MOQ', 'Pricing, packaging options and export info in 24 h.' ),
		array( '', 'Sample / branding', 'Private-label proofs validated before production.' ),
		array( '', 'Production & QC', 'Manufactured with quality checkpoints and batch photos.' ),
	) );
	?>

	<?php sg_h( 'Testimonials', 'Star rating, quote, attribution.' ); ?>
	<?php
	artisraw_testimonials( array(
		array( 'ArtisRaw is reliable, consistent and easy to work with.', 'Retail buyer', 'Wholesale partner, Europe', 5 ),
		array( 'The export paperwork was ready before we asked.', 'Importer', 'Distributor, USA', 5 ),
		array( 'Private-label engraving came back exactly to brief.', 'Brand owner', 'Concept stores, GCC', 4 ),
	), 'What buyers say' );
	?>

	<?php sg_h( 'Founders / team', 'Initials avatar until real portraits exist.' ); ?>
	<?php
	artisraw_founders( array(
		array( 'Mohamed Bilel Cherif', 'Co-founder & CEO', 'Operations & strategy.' ),
		array( 'Ihsen Triki', 'Co-founder & Head of Design', 'Product & artistry.' ),
		array( 'Ahmed Sakka', 'Co-founder', 'Heritage & vision.' ),
	), 'The founders behind ArtisRaw' );
	?>

	<?php sg_h( 'Newsletter signup', 'Posts to /artisraw/v1/newsletter; no-JS falls back to /contact/.' ); ?>
	<?php artisraw_newsletter( array( 'id' => 'sg-newsletter', 'location' => 'styleguide' ) ); ?>
</div>

<?php // Plant-a-tree program — full-width band with its own heading. ?>
<?php artisraw_plant_a_tree(); ?>

<div class="container section">
	<?php sg_h( 'Instagram strip', 'Static curated images; whole tile links out, lazy-loaded.' ); ?>
	<?php
	artisraw_instagram_strip( array(
		array( 'Olive wood boards', '', '/assets/ar-boards', array( 600 ) ),
		array( 'In the workshop', '', '/assets/ar-workshop', array( 600 ) ),
		array( 'Mortar & pestle', '', '/assets/ar-mortar', array( 600 ) ),
		array( 'Chess & gifts', '', '/assets/ar-chess', array( 600 ) ),
		array( 'Carved bowls', '', '/assets/ar-bowl', array( 600 ) ),
		array( 'Ready to ship', '', '/assets/ar-collection', array( 600 ) ),
	), 'artisraw' );
	?>
</div>

<!-- ===================== Phase 11 — Art Direction (Addendum) ===================== -->
<div class="container section">
	<?php sg_h( 'Art Direction — Addendum v1.1', 'Olyfo visual language in ArtisRaw tokens: statement hero, color-block mosaic, arrow links, buyer voices, two-column quote.' ); ?>
	<?php sg_h( 'Arrow link (§5 block formula)' ); ?>
	<p><?php artisraw_arrow_link( 'Our story & founders', '#' ); ?></p>
</div>

<div class="container section">
	<?php sg_h( 'ISO 9001 trust badge (Figma motif, re-skinned)', 'Espresso disc / amber ring / cream type — the Figma corporate blue (#1D6FE0) was rejected as foreign to the palette. Decorative; exposed to AT as one labelled image.' ); ?>
	<?php artisraw_iso_badge(); ?>

	<?php sg_h( 'New editorial surface tokens (Figma)', 'Tan and olive mosaic fields, added alongside the existing field colors.' ); ?>
	<div style="display:flex;gap:var(--sp-2);flex-wrap:wrap">
		<span style="background:var(--c-tan-300);color:var(--c-espresso-900);padding:var(--sp-3) var(--sp-4);border-radius:var(--radius-md);font-weight:var(--fw-semibold)">--c-tan-300 · espresso text</span>
		<span style="background:var(--c-olive-600);color:var(--c-cream-100);padding:var(--sp-3) var(--sp-4);border-radius:var(--radius-md);font-weight:var(--fw-semibold)">--c-olive-600 · cream text</span>
	</div>
</div>

<?php // Statement hero (§3) — full-bleed duotone. ?>
<?php
artisraw_statement_hero( array(
	'base' => '/assets/ar-workshop', 'alt' => 'Workshop', 'widths' => array( 600, 1200 ), 'w' => 1400, 'h' => 900,
	'eyebrow' => 'ISO 9001 olive wood manufacturer · Sfax, Tunisia',
	'statement' => 'grown, not made.',
	'support' => 'Handmade Tunisian olive wood for retailers, distributors and private-label brands.',
	'cta_label' => 'Request Line-Sheet & Compliance Pack', 'cta_url' => '#',
	'trust' => array( array( 'ISO 9001:2015', '#' ), array( '30+ countries', '#' ), array( 'MOQ 50', '#' ), array( 'Ships in 72 h', '#' ) ),
) );
?>

<div class="container section">
	<?php sg_h( 'Color-block mosaic (§4)', 'Field ↔ photo, alternating sides; three field colors (sand · espresso · amber); espresso body on light fields.' ); ?>
</div>
<?php
artisraw_color_block( array( 'field' => 'sand', 'field_left' => true, 'eyebrow' => 'Who we are', 'heading' => 'Artisan roots, modern production', 'body' => 'Founded in Sfax in 2019 — handmade heritage built for export-ready wholesale.', 'link_label' => 'Our story', 'link_url' => '#', 'img_base' => '/assets/ar-workshop', 'img_alt' => 'Workshop', 'img_widths' => array( 600, 1200 ), 'w' => 1400, 'h' => 900 ) );
artisraw_color_block( array( 'field' => 'espresso', 'eyebrow' => 'How it’s made', 'heading' => 'From the tree to a finished piece', 'body' => 'Reclaimed Chemlali wood, CNC precision and hand-finishing, then unit-by-unit QC.', 'link_label' => 'The process', 'link_url' => '#', 'img_base' => '/assets/ar-boards', 'img_alt' => 'Board grain', 'img_widths' => array( 600 ), 'w' => 548, 'h' => 365 ) );
artisraw_color_block( array( 'field' => 'sand', 'field_left' => true, 'heading' => 'Our Vision', 'body' => 'We aim to make Tunisian olive wood a global standard of sustainable luxury by connecting Mediterranean heritage with international B2B export systems.', 'link_label' => 'Read more', 'link_url' => '#', 'img_base' => '/assets/ar-grove', 'img_alt' => 'Olive grove', 'img_widths' => array( 600, 1200 ), 'w' => 1273, 'h' => 900 ) );
?>

<div class="container section">
	<?php sg_h( 'Buyer voices (§6)', 'One quote per viewport; scroll-snap between.' ); ?>
	<?php
	artisraw_buyer_voices( array(
		array( 'ArtisRaw is reliable, consistent and easy to work with.', 'Retail buyer', 'Specialty retailer, United Kingdom', 5 ),
		array( 'The export paperwork was ready before we asked.', 'Importer', 'Distributor, United States', 5 ),
	) );
	?>

	<?php sg_h( 'Two-column quote block (§9.1)', 'Eyebrow + benefits checklist + white form card.' ); ?>
	<?php artisraw_quote_block( array( 'id' => 'sg-quote-block', 'location' => 'styleguide' ) ); ?>
</div>

<?php // ===================== Phase 12.1 — Figma component library ===================== ?>
<div class="container section">
	<?php sg_h( 'Captioned image grid (Figma)', 'Square photos with an overlaid paper caption label; runs through our responsive pipeline.' ); ?>
	<?php
	artisraw_caption_grid( array(
		array( 'base' => '/assets/ar-grove', 'alt' => 'Olive grove', 'caption' => 'Responsible sourcing', 'w' => 1273, 'h' => 900, 'widths' => array( 600, 1200 ) ),
		array( 'base' => '/assets/ar-workshop', 'alt' => 'Workshop', 'caption' => 'Handmade production', 'w' => 1400, 'h' => 900, 'widths' => array( 600, 1200 ) ),
		array( 'base' => '/assets/ar-boards', 'alt' => 'Board grain', 'caption' => 'Chemlali dense grain', 'w' => 548, 'h' => 365, 'widths' => array( 600 ) ),
		array( 'base' => '/assets/ar-grove', 'alt' => 'Collections', 'caption' => 'Premium B2B collections', 'w' => 1273, 'h' => 900, 'widths' => array( 600, 1200 ) ),
	), 'From tree, to workshop, to wholesale, to shelves' );
	?>

	<?php sg_h( 'Numbered steps (Figma variant)', 'The existing steps component with a large faded corner numeral.' ); ?>
	<?php
	artisraw_steps( array(
		array( '', 'Responsible sourcing', 'End-of-life Chemlali olive trees only.' ),
		array( '', 'Drying & curing', 'Stabilised before cutting and shaping.' ),
		array( '', 'Cutting & shaping', 'Controlled dimensions per SKU.' ),
		array( '', 'Hand finishing', 'Sanded, polished and refined by hand.' ),
	), 1, array( 'numbered' => true ) );
	?>

	<?php sg_h( 'Value cards (Figma About)', 'Dark pillar cards; surfaces cycle espresso / olive / bark / espresso-800.' ); ?>
	<?php
	artisraw_value_cards( array(
		array( 'heading' => 'Premium craftsmanship', 'body' => 'Each piece reveals the character and durability of olive wood.' ),
		array( 'heading' => 'Excellence & reliability', 'body' => 'Selection, QC and food-safe finishing on every unit.' ),
		array( 'heading' => 'Heritage & innovation', 'body' => 'Traditional expertise with modern production standards.' ),
		array( 'heading' => 'Sustainability & impact', 'body' => 'Reclaimed wood, responsible sourcing, lasting creations.' ),
	) );
	?>

	<?php sg_h( 'Stat cards (Figma About)', 'Labelled metric cards — Location / Factory / Team / Capacity.' ); ?>
	<?php
	artisraw_stat_cards( array(
		array( 'label' => 'Location', 'value' => 'Sfax', 'note' => 'Route Saltania Km 4.5, Tunisia.' ),
		array( 'label' => 'Facility type', 'value' => 'Factory', 'note' => 'Dedicated olive-wood plant.' ),
		array( 'label' => 'Team', 'value' => '11+', 'note' => 'Artisans, designers, QC, operations.' ),
		array( 'label' => 'Capacity', 'value' => '1000+', 'note' => 'Unit orders fulfilled regularly.' ),
	) );
	?>

	<?php sg_h( 'QC timeline (Figma Process)', 'Six checkpoint cards with an amber top rule.' ); ?>
	<?php
	artisraw_qc_timeline( array(
		array( 'heading' => 'Raw material', 'body' => 'Trunk integrity and density reviewed.' ),
		array( 'heading' => 'Cutting', 'body' => 'Dimensions per SKU verified.' ),
		array( 'heading' => 'Surface', 'body' => 'Smoothness and grain inspected.' ),
		array( 'heading' => 'Finish', 'body' => 'Food-safe oil and wax coverage.' ),
		array( 'heading' => 'Packing', 'body' => 'Carton specs and labelling.' ),
		array( 'heading' => 'Export', 'body' => 'Documents and traceability validated.' ),
	) );
	?>
</div>

<?php // Trio band + feature quote + product strip are full-bleed bands. ?>
<div class="container section"><?php sg_h( 'Trio CTA band (Figma)', 'Three colored wayfinding tiles — espresso / olive / sand.' ); ?></div>
<?php
artisraw_trio_band( array(
	array( 'label' => 'Discover the real olive-wood quality', 'cue' => 'Discover more', 'href' => '#' ),
	array( 'label' => 'Our process', 'cue' => 'Learn more', 'href' => '#' ),
	array( 'label' => 'FAQs', 'cue' => 'Read more', 'href' => '#' ),
) );
?>

<div class="container section"><?php sg_h( 'Feature testimonial (Figma)', 'One photo beside a paper field with the quote — complements the voices carousel.' ); ?></div>
<?php
artisraw_testimonial_feature( array(
	'heading' => 'What they say…',
	'quote'   => 'ArtisRaw is reliable, consistent and easy to work with. Their handmade olive wood helped us build a premium retail collection.',
	'author'  => 'Retail buyer', 'role' => 'Specialty retailer, United Kingdom',
	'link_label' => 'More buyer stories', 'link_url' => '#',
	'img_base' => '/assets/ar-workshop', 'img_alt' => 'Trade fair booth', 'img_w' => 1400, 'img_h' => 900, 'img_widths' => array( 600, 1200 ),
) );
?>

<div class="container section">
	<?php sg_h( 'Hero-products strip (Figma)', 'Product cutouts on sand cards, mix-blend-multiply.' ); ?>
	<?php
	artisraw_product_strip( array(
		array( 'base' => '/assets/ar-boards', 'alt' => 'Serving boards', 'w' => 548, 'h' => 365, 'widths' => array( 600 ) ),
		array( 'base' => '/assets/ar-boards', 'alt' => 'Chess set', 'w' => 548, 'h' => 365, 'widths' => array( 600 ) ),
		array( 'base' => '/assets/ar-boards', 'alt' => 'Utensils', 'w' => 548, 'h' => 365, 'widths' => array( 600 ) ),
		array( 'base' => '/assets/ar-boards', 'alt' => 'Mortar', 'w' => 548, 'h' => 365, 'widths' => array( 600 ) ),
		array( 'base' => '/assets/ar-boards', 'alt' => 'Bowls', 'w' => 548, 'h' => 365, 'widths' => array( 600 ) ),
	), 'Hero products', array( 'label' => 'See the catalogue', 'href' => '#' ) );
	?>
</div>

<?php
get_footer();
