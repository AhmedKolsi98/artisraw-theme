<?php
/**
 * Template Name: About (Figma)
 *
 * The /about/ page rebuilt to the Figma composition: photo hero → intro →
 * captioned visuals → value pillars → facility stats → end-to-end process →
 * certification → founders → "Stories" band. Prose intro + quick answer come
 * from post meta/content; the designed sections are template-rendered and
 * gettext-wrapped for FR parity. Built from Phase 12.1 components.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pid = get_queried_object_id();
$qa  = get_post_meta( $pid, 'quick_answer', true );

get_header();

/* ---- Hero (photo + amber serif title + ISO badge) ---- */
artisraw_photo_hero( array(
	'base'    => '/assets/ar-olive-tree',
	'alt'     => __( 'Ancient Chemlali olive tree at sunset in Tunisia', 'artisraw' ),
	'w'       => 1200,
	'h'       => 800,
	'widths'  => array( 600, 1200 ),
	'eyebrow' => __( 'About ArtisRaw', 'artisraw' ),
	'title'   => __( 'Discover our Story', 'artisraw' ),
	'support' => __( 'Premium Olive Wood, Crafted in Sustainability', 'artisraw' ),
	'badge'   => true,
	'loc'     => 'about_hero',
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

<?php /* ---- The ArtisRaw world in four visuals ---- */ ?>
<div class="container section">
	<?php
	artisraw_caption_grid(
		array(
			array( 'base' => '/assets/ar-grove', 'alt' => __( 'Olive grove — responsible sourcing', 'artisraw' ), 'caption' => __( 'Responsible olive wood sourcing', 'artisraw' ), 'w' => 1273, 'h' => 900, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-lathe', 'alt' => __( 'ArtisRaw workshop production in Sfax', 'artisraw' ), 'caption' => __( 'Own factory in Sfax', 'artisraw' ), 'w' => 1400, 'h' => 933, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-boards-drying', 'alt' => __( 'Dense Chemlali olive wood grain', 'artisraw' ), 'caption' => __( 'Chemlali dense grain', 'artisraw' ), 'w' => 1200, 'h' => 801, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-warehouse', 'alt' => __( 'Wholesale collections ready for export', 'artisraw' ), 'caption' => __( 'Premium wholesale collections', 'artisraw' ), 'w' => 1100, 'h' => 1650, 'widths' => array( 600 ) ),
		),
		__( 'The ArtisRaw world in four visuals', 'artisraw' )
	);
	?>
</div>

<?php /* ---- Four value pillars ---- */ ?>
<div class="container section">
	<?php
	artisraw_value_cards( array(
		array( 'heading' => __( 'Premium quality &amp; craftsmanship', 'artisraw' ), 'body' => __( 'Crafted from premium Tunisian olive wood, each piece reveals the unique character, beauty and durability of nature.', 'artisraw' ) ),
		array( 'heading' => __( 'Excellence &amp; reliability', 'artisraw' ), 'body' => __( 'Every product undergoes careful selection, quality control and food-safe finishing to meet the expectations of global buyers.', 'artisraw' ) ),
		array( 'heading' => __( 'Mediterranean heritage &amp; innovation', 'artisraw' ), 'body' => __( 'Inspired by generations of craftsmanship, we combine traditional expertise with modern production standards.', 'artisraw' ) ),
		array( 'heading' => __( 'Sustainability &amp; impact', 'artisraw' ), 'body' => __( 'We transform reclaimed olive wood into lasting creations, supporting responsible sourcing, local artisans and a more sustainable future.', 'artisraw' ) ),
	) );
	?>
</div>

<?php /* ---- More than a workshop — facility stats ---- */ ?>
<div class="container section">
	<?php
	artisraw_stat_cards(
		array(
			array( 'label' => __( 'Location', 'artisraw' ), 'value' => __( 'Sfax', 'artisraw' ), 'note' => __( 'Route Saltania Km 4.5, Tunisia, in the Mediterranean olive region.', 'artisraw' ) ),
			array( 'label' => __( 'Facility type', 'artisraw' ), 'value' => __( 'Factory', 'artisraw' ), 'note' => __( 'Dedicated olive wood manufacturing plant with specialised production areas.', 'artisraw' ) ),
			array( 'label' => __( 'Team', 'artisraw' ), 'value' => '11+', 'note' => __( 'Skilled artisans, designers, QC specialists and operations staff.', 'artisraw' ) ),
			array( 'label' => __( 'Capacity', 'artisraw' ), 'value' => '1000+', 'note' => __( 'Unit orders fulfilled regularly for B2B partners.', 'artisraw' ) ),
		),
		__( 'More than a workshop — a dedicated production facility', 'artisraw' ),
		__( 'ArtisRaw combines traditional hand tools with professional woodworking equipment, producing consistent quality at scale while keeping the authentic handmade character of olive wood.', 'artisraw' )
	);
	?>
</div>

<?php /* ---- End-to-end manufacturing process ---- */ ?>
<div class="container section">
	<div class="section-opener"><h2><?php esc_html_e( 'End-to-end manufacturing process', 'artisraw' ); ?></h2></div>
	<p class="muted"><?php esc_html_e( 'From raw material sourcing to export packaging, every step is built for traceability, consistency and quality control.', 'artisraw' ); ?></p>
	<?php
	artisraw_steps(
		array(
			array( '', __( 'Raw material sourcing', 'artisraw' ), __( 'Selection of authentic Tunisian olive wood from end-of-life trees.', 'artisraw' ) ),
			array( '', __( 'Drying &amp; curing', 'artisraw' ), __( 'Wood stabilised before cutting and shaping to support durability.', 'artisraw' ) ),
			array( '', __( 'Machining &amp; shaping', 'artisraw' ), __( 'Skilled preparation using professional equipment and controlled dimensions.', 'artisraw' ) ),
			array( '', __( 'Handcrafting', 'artisraw' ), __( 'Artisans sand, polish and refine every piece by hand.', 'artisraw' ) ),
			array( '', __( 'Food-safe finishing', 'artisraw' ), __( 'Mineral oil and beeswax applied to protect and enhance the grain.', 'artisraw' ) ),
			array( '', __( 'QC, packaging &amp; export', 'artisraw' ), __( 'Inspection, retail or wholesale packing, documents and shipment preparation.', 'artisraw' ) ),
		),
		1,
		array( 'numbered' => true )
	);
	?>
</div>

<?php /* ---- Certification ---- */ ?>
<section class="section--sand">
	<div class="container section">
		<div class="cert-block">
			<?php
			artisraw_responsive_image( array(
				'base'   => '/assets/ar-certificate',
				'alt'    => __( 'ArtisRaw ISO 9001:2015 certificate', 'artisraw' ),
				'class'  => 'cert-block__img',
				'width'  => 570,
				'height' => 570,
				'widths' => array( 570 ),
				'sizes'  => '(min-width: 820px) 40vw, 100vw',
			) );
			?>
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Certification', 'artisraw' ); ?></p>
				<h2><?php esc_html_e( 'World’s first ISO 9001 certified olive wood manufacturer', 'artisraw' ); ?></h2>
				<p><?php esc_html_e( 'ArtisRaw is certified for the design, production and sale (national and international) of olive wood articles — operating a documented quality management system across the full workflow.', 'artisraw' ); ?></p>
				<ul class="cert-block__list">
					<li><?php esc_html_e( 'ISO 9001:2015 certified quality management system', 'artisraw' ); ?></li>
					<li><?php esc_html_e( 'Documented quality inspections and full batch traceability', 'artisraw' ); ?></li>
					<li><?php esc_html_e( 'Food-grade finishing with material safety documentation', 'artisraw' ); ?></li>
					<li><?php esc_html_e( 'Export documents prepared per destination market', 'artisraw' ); ?></li>
				</ul>
				<?php artisraw_arrow_link( __( 'Ask for documents', 'artisraw' ), artisraw_localized_url( '/certifications/' ) ); ?>
			</div>
		</div>
	</div>
</section>

<?php /* ---- Founders ---- */ ?>
<div class="container section">
	<?php
	artisraw_founders(
		array(
			array( __( 'Mohamed Bilel Cherif', 'artisraw' ), __( 'Co-founder &amp; CEO', 'artisraw' ), __( 'Leads ArtisRaw’s wholesale strategy and export partnerships across global markets.', 'artisraw' ) ),
			array( __( 'Ihsen Triki', 'artisraw' ), __( 'Head of Design — registered artisan', 'artisraw' ), __( 'Designs every collection and reviews each product family for craft, function and durability.', 'artisraw' ) ),
			array( __( 'Ahmed Sakka', 'artisraw' ), __( 'Co-founder — Operations', 'artisraw' ), __( 'Runs production, quality systems and the factory workflow in Sfax.', 'artisraw' ) ),
		),
		__( 'The founders behind ArtisRaw', 'artisraw' )
	);
	?>
</div>

<?php /* ---- Stories band ---- */ ?>
<?php
artisraw_testimonial_feature( array(
	'dark'         => true,
	'heading'      => __( 'Stories worth telling', 'artisraw' ),
	'quote'        => __( 'ArtisRaw isn’t just about products; it’s about stories worth telling — tradition, sustainability and longevity.', 'artisraw' ),
	'button_label' => __( 'Let’s work together', 'artisraw' ),
	'button_url'   => artisraw_localized_url( '/request-quote/' ),
	'img_base'     => '/assets/ar-collection',
	'img_alt'      => __( 'ArtisRaw olive wood collection on display', 'artisraw' ),
	'img_w'        => 1200,
	'img_h'        => 557,
	'img_widths'   => array( 600, 1200 ),
) );

get_footer();
