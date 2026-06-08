<?php
/**
 * Template Name: Process (Figma)
 *
 * /production-process/ rebuilt to the Figma composition: photo hero → intro →
 * eight-step numbered overview → three alternating step-feature bands → QC
 * timeline → FAQ → quote block. Quick answer + prose intro from post
 * meta/content; designed sections template-rendered and gettext-wrapped.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pid = get_queried_object_id();
$qa  = get_post_meta( $pid, 'quick_answer', true );

get_header();

/* ---- Hero ---- */
artisraw_photo_hero( array(
	'base'      => '/assets/ar-worker-carry',
	'alt'       => __( 'ArtisRaw artisan carrying olive wood at the Sfax factory', 'artisraw' ),
	'w'         => 1400,
	'h'         => 933,
	'widths'    => array( 600, 1200 ),
	'eyebrow'   => __( 'Our process', 'artisraw' ),
	'title'     => get_the_title(),
	'support'   => __( 'Responsible sourcing, drying, cutting, hand-finishing, food-safe protection, quality control, packaging and B2B export preparation.', 'artisraw' ),
	'cta_label' => __( 'Request a quote', 'artisraw' ),
	'cta_url'   => home_url( '/request-quote/' ),
	'badge'     => true,
	'loc'       => 'process_hero',
) );

artisraw_breadcrumbs();
?>

<div class="container section hub-section">
	<?php
	if ( $qa ) {
		artisraw_quick_answer( $qa );
	}
	if ( get_the_content() ) {
		echo '<div class="about-intro">';
		the_content();
		echo '</div>';
	}
	?>
</div>

<?php /* ---- Eight-step process overview ---- */ ?>
<div class="container section">
	<div class="section-opener">
		<p class="eyebrow"><?php esc_html_e( 'Process overview', 'artisraw' ); ?></p>
		<h2><?php esc_html_e( 'Eight core phases that turn raw Chemlali olive wood into premium B2B collections', 'artisraw' ); ?></h2>
	</div>
	<?php
	artisraw_steps(
		array(
			array( '', __( 'Responsible sourcing', 'artisraw' ), __( 'Selection of authentic Tunisian olive wood from end-of-life trees.', 'artisraw' ) ),
			array( '', __( 'Drying &amp; curing', 'artisraw' ), __( 'Wood is stabilised before cutting and shaping to support durability.', 'artisraw' ) ),
			array( '', __( 'Cutting &amp; shaping', 'artisraw' ), __( 'Skilled preparation using professional equipment and controlled dimensions.', 'artisraw' ) ),
			array( '', __( 'Hand finishing', 'artisraw' ), __( 'Artisans sand, polish and refine every piece by hand.', 'artisraw' ) ),
			array( '', __( 'Food-safe finish', 'artisraw' ), __( 'Mineral oil and beeswax are applied to protect and enhance the grain.', 'artisraw' ) ),
			array( '', __( 'Quality checks', 'artisraw' ), __( 'Inspection of surface, dimensions, finish, packing and consistency.', 'artisraw' ) ),
			array( '', __( 'Export packing', 'artisraw' ), __( 'Products prepared for retail, wholesale cartons or private-label needs.', 'artisraw' ) ),
			array( '', __( 'Documents &amp; shipment', 'artisraw' ), __( 'Packing list, invoice, certificates and export preparation for partners.', 'artisraw' ) ),
		),
		1,
		array( 'numbered' => true )
	);
	?>
</div>

<?php /* ---- Three alternating step-feature bands ---- */ ?>
<?php
artisraw_color_block( array(
	'field' => 'amber', 'field_left' => true,
	'eyebrow' => __( 'Step 01 — Raw material', 'artisraw' ),
	'heading' => __( 'Responsible olive wood sourcing', 'artisraw' ),
	'body'    => __( 'The process begins with Tunisian Chemlali olive wood, selected for dense grain, natural contrast and durability. We focus on responsible use of end-of-life olive trees and avoid waste by transforming raw material into high-value products.', 'artisraw' ),
	'link_label' => __( 'Our story', 'artisraw' ), 'link_url' => home_url( '/about/' ),
	'img_base' => '/assets/ar-grove', 'img_alt' => __( 'Olive grove — raw material selection', 'artisraw' ), 'img_widths' => array( 600, 1200 ), 'w' => 1273, 'h' => 900,
) );
artisraw_color_block( array(
	'field' => 'sand',
	'eyebrow' => __( 'Step 02 — Workshop', 'artisraw' ),
	'heading' => __( 'Cutting, shaping and artisan work', 'artisraw' ),
	'body'    => __( 'Each product is cut, shaped and sanded according to its category and SKU requirements. The workshop combines professional equipment with hand finishing to preserve the unique character of olive wood.', 'artisraw' ),
	'link_label' => __( 'Quality control', 'artisraw' ), 'link_url' => home_url( '/quality-control/' ),
	'img_base' => '/assets/ar-lathe', 'img_alt' => __( 'Cutting and shaping olive wood in the ArtisRaw workshop', 'artisraw' ), 'img_widths' => array( 600, 1200 ), 'w' => 1400, 'h' => 933,
) );
artisraw_color_block( array(
	'field' => 'espresso', 'field_left' => true,
	'eyebrow' => __( 'Step 03 — Quality', 'artisraw' ),
	'heading' => __( 'Inspection, food-safe finish and packing', 'artisraw' ),
	'body'    => __( 'Each product is checked, oiled with food-grade mineral oil and beeswax, then packed for retail or wholesale. Private-label requests, packaging needs and shipment preparation are handled before export.', 'artisraw' ),
	'link_label' => __( 'Wholesale', 'artisraw' ), 'link_url' => home_url( '/olive-wood-wholesale-supplier/' ),
	'img_base' => '/assets/ar-warehouse', 'img_alt' => __( 'Inspected and packed olive wood orders in the warehouse', 'artisraw' ), 'img_widths' => array( 600 ), 'w' => 1100, 'h' => 1650,
) );
?>

<?php /* ---- Quality control timeline ---- */ ?>
<div class="container section">
	<?php
	artisraw_qc_timeline(
		array(
			array( 'heading' => __( 'Raw material check', 'artisraw' ), 'body' => __( 'Trunk integrity, density and natural defects reviewed before production.', 'artisraw' ) ),
			array( 'heading' => __( 'Cutting check', 'artisraw' ), 'body' => __( 'Dimensions per SKU verified with controlled tolerances.', 'artisraw' ) ),
			array( 'heading' => __( 'Surface check', 'artisraw' ), 'body' => __( 'Smoothness, cracks and grain inspected piece by piece.', 'artisraw' ) ),
			array( 'heading' => __( 'Finish check', 'artisraw' ), 'body' => __( 'Food-safe oiling and wax coverage verified on every unit.', 'artisraw' ) ),
			array( 'heading' => __( 'Packing check', 'artisraw' ), 'body' => __( 'Protection, carton specs and labelling confirmed before closing.', 'artisraw' ) ),
			array( 'heading' => __( 'Export check', 'artisraw' ), 'body' => __( 'Documents and batch traceability validated before shipment.', 'artisraw' ) ),
		),
		__( 'Quality control timeline', 'artisraw' ),
		__( 'A simple, buyer-facing view of the quality checkpoints every order passes through.', 'artisraw' )
	);
	?>
</div>

<?php /* ---- FAQ (no schema here — the /faq/ page owns the FAQPage entity) ---- */ ?>
<div class="container section">
	<div class="section-opener"><h2><?php esc_html_e( 'Process FAQs', 'artisraw' ); ?></h2></div>
	<?php
	artisraw_faq_accordion(
		array(
			array( __( 'What makes ArtisRaw a trusted wholesale partner?', 'artisraw' ), __( 'An ISO 9001:2015-certified manufacturer with its own factory in Sfax, documented quality control, food-safe finishing and export experience across 30+ countries — built for B2B ordering with sale-ready SKUs and MOQ-based logic.', 'artisraw' ) ),
			array( __( 'Where does your olive wood come from?', 'artisraw' ), __( 'Exclusively from end-of-life Chemlali olive trees in the Sfax region of Tunisia. Productive trees are protected; only trees that no longer bear fruit enter the workshop.', 'artisraw' ) ),
			array( __( 'Are your products food-safe and certified?', 'artisraw' ), __( 'Yes. Every piece is finished with food-grade mineral oil and beeswax, with material safety documentation available, under an ISO 9001:2015 quality management system.', 'artisraw' ) ),
			array( __( 'How do you ensure consistent quality?', 'artisraw' ), __( 'Through the six-checkpoint timeline above — raw material, cutting, surface, finish, packing and export checks — applied unit by unit with batch traceability.', 'artisraw' ) ),
			array( __( 'Can you customise products and packaging?', 'artisraw' ), __( 'Yes. Engraving, co-designed SKUs and branded packaging are handled in-house as part of the private-label service.', 'artisraw' ) ),
			array( __( 'What are your minimum order quantities?', 'artisraw' ), __( 'MOQ starts at 50 units and varies by SKU. Samples are available, with the cost deducted from your first order.', 'artisraw' ) ),
			array( __( 'How long are production and delivery times?', 'artisraw' ), __( 'In-stock items ship within 72 hours; custom production takes 6–8 weeks. Transit is typically 5–12 days by air or 25–40 days by ocean.', 'artisraw' ) ),
			array( __( 'Do you export worldwide?', 'artisraw' ), __( 'Yes — ArtisRaw ships to 30+ countries with full export documentation, supporting FOB, CIF, DAP and DDP terms depending on destination.', 'artisraw' ) ),
		),
		false,
		'process-faq'
	);
	?>
</div>

<?php /* ---- Request a quote ---- */ ?>
<section class="section--sand">
	<div class="container section">
		<?php
		artisraw_quote_block( array(
			'id'       => 'process-quote',
			'location' => 'production-process',
			'intro'    => __( 'Tell us about your project and our team will prepare a tailored proposal for your business.', 'artisraw' ),
		) );
		?>
	</div>
</section>

<?php
get_footer();
