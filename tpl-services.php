<?php
/**
 * Template Name: Services (Figma)
 *
 * /services/ rebuilt to match the PDF design comp: photo hero → intro →
 * wholesale buyers feature → private-label feature → 8 core B2B service cards
 * → quote form.
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
	'base'      => '/assets/ar-collection',
	'alt'       => __( 'ArtisRaw olive wood collection — boards, bowls, utensils and chess sets', 'artisraw' ),
	'w'         => 1200,
	'h'         => 557,
	'widths'    => array( 600, 1200 ),
	'eyebrow'   => __( 'Services', 'artisraw' ),
	'title'     => __( 'B2B services', 'artisraw' ),
	'support'   => __( 'From product selection to private label packaging and export documentation, ArtisRaw helps professional buyers build premium handmade olive wood collections with confidence.', 'artisraw' ),
	'cta_label' => __( 'Request a B2B quote', 'artisraw' ),
	'cta_url'   => artisraw_localized_url( '/request-quote/' ),
	'badge'     => true,
	'loc'       => 'services_hero',
) );

artisraw_breadcrumbs();
?>

<div class="container section hub-section services-intro">
	<?php if ( $qa ) { artisraw_quick_answer( $qa ); } ?>
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'End-to-end solutions for wholesalers, retailers, hospitality and private-label brands', 'artisraw' ); ?></h2>
	</header>
	<p class="lead"><?php esc_html_e( 'ArtisRaw is not only a manufacturer — we are a B2B partner for sourcing, product development, private label, corporate gifts, quality control, packaging and export support. Our goal is to make Tunisian olive wood easy to buy, easy to present and easy to reorder.', 'artisraw' ); ?></p>
	<p class="lead"><?php esc_html_e( 'We serve professional buyers who need consistent quality, authentic handmade value, strong product storytelling and flexible collections for their market. From small curated assortments to large wholesale replenishment, our services are designed around your commercial needs.', 'artisraw' ); ?></p>
</div>

<?php /* ---- Wholesale buyers feature ---- */ ?>
<?php
artisraw_color_block( array(
	'field' => 'amber',
	'eyebrow' => __( 'Wholesale buyers', 'artisraw' ),
	'heading' => __( 'Build a sale-ready olive wood collection', 'artisraw' ),
	'body'    => __( 'Choose from existing catalogue SKUs or create a dedicated assortment by category, target price, packaging style and destination market. We help structure orders for retail shelves, online stores and wholesale distribution.', 'artisraw' ),
	'link_label' => __( 'Explore services', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/wholesale/' ),
	'img_base' => '/assets/ar-collection', 'img_alt' => __( 'Sale-ready olive wood collection for wholesale buyers', 'artisraw' ), 'img_widths' => array( 600, 1200 ), 'w' => 1200, 'h' => 557,
) );
?>

<?php /* ---- Private label feature ---- */ ?>
<?php
artisraw_color_block( array(
	'field' => 'sand', 'field_left' => true,
	'eyebrow' => __( 'Private label', 'artisraw' ),
	'heading' => __( 'Your brand, our handmade expertise', 'artisraw' ),
	'body'    => __( 'ArtisRaw supports logo engraving, packaging adaptation, barcode-ready products, retail labels, gift boxes and collection development for buyers who want to sell under their own brand.', 'artisraw' ),
	'link_label' => __( 'Service packs', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/private-label-olive-wood/' ),
	'img_base' => '/assets/ar-lathe', 'img_alt' => __( 'Artisan shaping olive wood for a private-label collection', 'artisraw' ), 'img_widths' => array( 600, 1200 ), 'w' => 1400, 'h' => 933,
) );
?>

<?php /* ---- Core B2B services (PDF: 8 cards) ---- */ ?>
<section class="section--sand">
	<div class="container section hub-section">
		<header class="hub-section__head">
			<h2><?php esc_html_e( 'Core B2B services', 'artisraw' ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Each service is designed to reduce complexity for professional buyers while protecting the handmade value of the product.', 'artisraw' ); ?></p>
		</header>
		<div class="cell-grid cell-grid--4">
			<?php
			$services = array(
				array( __( 'Manufacturing', 'artisraw' ), __( 'Wholesale Production', 'artisraw' ), __( 'Scalable production for recurring orders, importers, distributors, retailers and seasonal replenishment.', 'artisraw' ) ),
				array( __( 'Branding', 'artisraw' ), __( 'Private Label', 'artisraw' ), __( 'Engraving, custom packaging, retail labels, barcode-ready references and brand presentation.', 'artisraw' ) ),
				array( __( 'Production', 'artisraw' ), __( 'Cutting and shaping', 'artisraw' ), __( 'Skilled preparation using professional equipment and controlled dimensions.', 'artisraw' ) ),
				array( __( 'Craft', 'artisraw' ), __( 'Hand finishing', 'artisraw' ), __( 'Artisans sand, polish and refine every piece by hand.', 'artisraw' ) ),
				array( __( 'Safety', 'artisraw' ), __( 'Food-safe finish', 'artisraw' ), __( 'Mineral oil and beeswax are applied to protect and enhance the grain.', 'artisraw' ) ),
				array( __( 'Control', 'artisraw' ), __( 'Quality checks', 'artisraw' ), __( 'Inspection of surface, dimensions, finish, packing and product consistency.', 'artisraw' ) ),
				array( __( 'Packaging', 'artisraw' ), __( 'Export packing', 'artisraw' ), __( 'Products prepared for retail, wholesale cartons or private-label needs.', 'artisraw' ) ),
				array( __( 'B2B', 'artisraw' ), __( 'Documents and shipment', 'artisraw' ), __( 'Packing list, invoice, certificates and export preparation for partners.', 'artisraw' ) ),
			);
			foreach ( $services as $s ) {
				echo '<div class="cell"><p class="cell__eyebrow eyebrow">' . esc_html( $s[0] ) . '</p><h3>' . esc_html( $s[1] ) . '</h3><p>' . esc_html( $s[2] ) . '</p></div>';
			}
			?>
		</div>
	</div>
</section>

<?php /* ---- Quote form ---- */ ?>
<section class="container section hub-section" id="quote">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Tell us your market — we prepare the B2B solution', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Share your channel and quantities and we reply within 24 hours with a tailored proposal.', 'artisraw' ); ?></p>
	</header>
	<div class="hub-form-wrap"><?php artisraw_quote_form( array( 'id' => 'services-quote', 'location' => 'services' ) ); ?></div>
</section>

<?php
get_footer();
