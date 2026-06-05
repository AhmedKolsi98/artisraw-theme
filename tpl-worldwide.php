<?php
/**
 * Template Name: Worldwide
 *
 * Export-reach page (mockup page 4): intro → hub-and-spoke map + market legend →
 * per-region support blocks (USA/Canada, EU, GCC, Asia) → transit table → CTA.
 * An honest abstract map (Sfax → regions), not a faux-precise world atlas.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pid = get_queried_object_id();
$qa  = get_post_meta( $pid, 'quick_answer', true );

get_header();
artisraw_breadcrumbs();
?>

<div class="container section hub-section">
	<header class="page-head"><h1><?php the_title(); ?></h1></header>
	<?php if ( $qa ) { artisraw_quick_answer( $qa ); } ?>
	<p class="lead" style="max-width:70ch"><?php esc_html_e( 'ArtisRaw serves wholesale partners, retailers, hospitality groups and private-label brands across international markets — factory-direct from Sfax, Tunisia, with full export documentation and worldwide delivery.', 'artisraw' ); ?></p>
</div>

<!-- Hub-and-spoke map + legend -->
<section class="section--sand">
	<div class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'From Tunisia to international buyers', 'artisraw' ); ?></h2></header>
		<div class="world-map">
			<svg class="world-map__svg" viewBox="0 0 480 300" role="img" aria-label="<?php esc_attr_e( 'Map showing ArtisRaw exporting from Sfax, Tunisia to North America, Europe, the GCC/Middle East, North Africa and Asia.', 'artisraw' ); ?>">
				<!-- spokes -->
				<g class="spokes" fill="none" stroke-width="1.5" stroke-dasharray="3 4">
					<path d="M240 165 L80 70"/><path d="M240 165 L210 60"/><path d="M240 165 L360 80"/><path d="M240 165 L200 235"/><path d="M240 165 L410 175"/>
				</g>
				<!-- region nodes -->
				<g font-size="11">
					<circle class="pin" cx="80" cy="70" r="6"/><text x="80" y="55" text-anchor="middle"><?php esc_html_e( 'North America', 'artisraw' ); ?></text>
					<circle class="pin" cx="210" cy="60" r="6"/><text x="210" y="45" text-anchor="middle"><?php esc_html_e( 'Europe', 'artisraw' ); ?></text>
					<circle class="pin" cx="360" cy="80" r="6"/><text x="360" y="65" text-anchor="middle"><?php esc_html_e( 'GCC / Middle East', 'artisraw' ); ?></text>
					<circle class="pin" cx="410" cy="175" r="6"/><text x="410" y="160" text-anchor="middle"><?php esc_html_e( 'Asia', 'artisraw' ); ?></text>
					<circle class="pin" cx="200" cy="235" r="6"/><text x="200" y="255" text-anchor="middle"><?php esc_html_e( 'North Africa', 'artisraw' ); ?></text>
					<circle class="home" cx="240" cy="165" r="9"/><text x="240" y="188" text-anchor="middle" font-weight="700">Sfax, Tunisia</text>
				</g>
			</svg>
			<ul class="world-legend" role="list">
				<li><strong><?php esc_html_e( '30+ countries served', 'artisraw' ); ?></strong><?php esc_html_e( 'Wholesale partners and private-label destinations on five regions.', 'artisraw' ); ?></li>
				<li><strong><?php esc_html_e( 'Export-ready documentation', 'artisraw' ); ?></strong><?php esc_html_e( 'Commercial invoice, packing list, ISPM-15 pallets and certificates per shipment.', 'artisraw' ); ?></li>
				<li><strong><?php esc_html_e( 'Factory-direct fulfilment', 'artisraw' ); ?></strong><?php esc_html_e( 'No middlemen — quoted in your currency with Incoterms to suit your broker.', 'artisraw' ); ?></li>
			</ul>
		</div>
	</div>
</section>

<!-- Per-region support -->
<section class="container section hub-section">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Destination support by market', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Each region has its own paperwork and expectations — here is how we prepare for yours.', 'artisraw' ); ?></p>
	</header>
	<div class="cell-grid cell-grid--4">
		<?php
		$regions = array(
			array( __( 'USA & Canada', 'artisraw' ), array( __( 'Lacey Act PPQ 505 declaration data', 'artisraw' ), __( 'HTS 4419 classification guidance', 'artisraw' ), __( 'USD invoicing, DDP on request', 'artisraw' ), __( 'Air freight 5–12 days', 'artisraw' ) ) ),
			array( __( 'Germany, France, Italy', 'artisraw' ), array( __( 'EUDR traceability & due diligence', 'artisraw' ), __( 'EUR invoicing, CE-aligned labelling', 'artisraw' ), __( 'Micro-hub sample programs', 'artisraw' ), __( 'Ocean 25–40 days, air on request', 'artisraw' ) ) ),
			array( __( 'KSA & GCC', 'artisraw' ), array( __( 'Corporate-gift & retail programs', 'artisraw' ), __( 'Consolidated freight options', 'artisraw' ), __( 'Arabic / dual-language packaging', 'artisraw' ), __( 'DDP delivery available', 'artisraw' ) ) ),
			array( __( 'Japan & Hong Kong', 'artisraw' ), array( __( 'Premium retail & concept stores', 'artisraw' ), __( 'Precise QC and finish standards', 'artisraw' ), __( 'Air freight for fast launches', 'artisraw' ), __( 'Retail-ready private label', 'artisraw' ) ) ),
		);
		foreach ( $regions as $r ) {
			echo '<div class="cell"><h3>' . esc_html( $r[0] ) . '</h3><ul>';
			foreach ( $r[1] as $li ) {
				echo '<li>' . esc_html( $li ) . '</li>';
			}
			echo '</ul></div>';
		}
		?>
	</div>
</section>

<!-- Transit & Incoterms table -->
<section class="section--sand">
	<div class="container section hub-section">
		<h2><?php esc_html_e( 'Transit times & Incoterms', 'artisraw' ); ?></h2>
		<?php
		artisraw_data_table(
			__( 'Shipping modes', 'artisraw' ),
			array( __( 'Mode', 'artisraw' ), __( 'Transit', 'artisraw' ), __( 'MOQ', 'artisraw' ), __( 'Incoterms', 'artisraw' ) ),
			array(
				array( __( 'Air freight', 'artisraw' ), __( '5–12 days', 'artisraw' ), '50', __( 'FOB / CIF / DAP / DDP', 'artisraw' ) ),
				array( __( 'Ocean (LCL)', 'artisraw' ), __( '25–40 days', 'artisraw' ), '50', __( 'FOB / CIF / DAP', 'artisraw' ) ),
				array( __( 'Ocean (FCL)', 'artisraw' ), __( '25–40 days', 'artisraw' ), '500', __( 'FOB / CIF / DDP', 'artisraw' ) ),
			),
			'logistics'
		);
		?>
		<p class="hub-section__note"><a class="btn btn--tertiary" href="<?php echo esc_url( home_url( '/shipping-logistics/' ) ); ?>"><?php esc_html_e( 'Full shipping & logistics', 'artisraw' ); ?></a></p>
	</div>
</section>

<!-- CTA -->
<section class="container section hub-section" id="quote">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Shipping to your market?', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Tell us your destination and we’ll confirm documentation, Incoterms and transit within 24 hours.', 'artisraw' ); ?></p>
	</header>
	<div class="hub-form-wrap"><?php artisraw_quote_form( array( 'id' => 'worldwide-quote', 'location' => 'worldwide' ) ); ?></div>
</section>

<?php
get_footer();
