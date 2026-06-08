<?php
/**
 * Template Name: Services
 *
 * B2B services hub (mockup page 3): intro → 6 core services → 12 buyer profiles
 * → 3 service packs → 8-step process → selected clients → FAQ → quote form.
 * Quick answer + SEO come from page meta; the rich body lives here.
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
	<p class="lead" style="max-width:70ch"><?php esc_html_e( 'ArtisRaw is not only a manufacturer — we are a B2B partner for sourcing, product development, private label, corporate gifts, quality control, packaging and export. Our goal is to make Tunisian olive wood easy to buy, easy to present and easy to reorder.', 'artisraw' ); ?></p>
</div>

<!-- Core B2B services -->
<section class="section--sand">
	<div class="container section hub-section">
		<header class="hub-section__head">
			<h2><?php esc_html_e( 'Core B2B services', 'artisraw' ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Each service reduces complexity for professional buyers while protecting the handmade value of the product.', 'artisraw' ); ?></p>
		</header>
		<div class="cell-grid cell-grid--3">
			<?php
			$services = array(
				array( '01', __( 'Manufacturing', 'artisraw' ), __( 'Wholesale Production', 'artisraw' ), __( 'Scalable production for recurring orders, importers, distributors, retailers and seasonal replenishment.', 'artisraw' ) ),
				array( '02', __( 'Branding', 'artisraw' ), __( 'Private Label', 'artisraw' ), __( 'Engraving, custom packaging, retail labels, barcode-ready references and brand presentation.', 'artisraw' ) ),
				array( '03', __( 'Gifting', 'artisraw' ), __( 'Corporate Gifts', 'artisraw' ), __( 'Premium gift packs, chess sets, kitchen bundles, event gifts and personalized handmade articles.', 'artisraw' ) ),
				array( '04', __( 'Tailor-made', 'artisraw' ), __( 'Custom Orders', 'artisraw' ), __( 'Custom shapes, sizes, finishes, bundles and product development for your target audience.', 'artisraw' ) ),
				array( '05', __( 'Control', 'artisraw' ), __( 'Quality Control', 'artisraw' ), __( 'Inspection, batch photo documentation, finish verification and packing control with continuous improvement.', 'artisraw' ) ),
				array( '06', __( 'Logistics', 'artisraw' ), __( 'Export Support', 'artisraw' ), __( 'Commercial documents, ISPM-15 pallets, packing lists, certificates and full export preparation.', 'artisraw' ) ),
			);
			foreach ( $services as $s ) {
				echo '<div class="cell"><p class="cell__eyebrow eyebrow">' . esc_html( $s[0] . ' · ' . $s[1] ) . '</p><h3>' . esc_html( $s[2] ) . '</h3><p>' . esc_html( $s[3] ) . '</p></div>';
			}
			?>
		</div>
		<p class="hub-section__note"><a class="btn btn--tertiary" href="<?php echo esc_url( artisraw_localized_url( '/private-label-olive-wood/' ) ); ?>"><?php esc_html_e( 'See private-label options', 'artisraw' ); ?></a></p>
	</div>
</section>

<!-- Best client profiles -->
<section class="container section hub-section">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Best client profiles for ArtisRaw', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Our products perform best where authenticity, natural materials, handmade stories and premium gifting value matter.', 'artisraw' ); ?></p>
	</header>
	<ul class="profiles" role="list">
		<?php
		$profiles = array(
			__( 'Gift shops', 'artisraw' ), __( 'Home décor shops', 'artisraw' ), __( 'Restaurants & cafés', 'artisraw' ), __( 'Wholesalers', 'artisraw' ),
			__( 'Kitchenware shops', 'artisraw' ), __( 'Home & lifestyle boutiques', 'artisraw' ), __( 'Corporate gifting', 'artisraw' ), __( 'Online retailers & marketplaces', 'artisraw' ),
			__( 'Ecological / organic stores', 'artisraw' ), __( 'Tourist shops', 'artisraw' ), __( 'Concept stores', 'artisraw' ), __( 'Hospitality buyers', 'artisraw' ),
		);
		foreach ( $profiles as $i => $p ) {
			printf( '<li class="profile"><span class="profile__num">%02d</span>%s</li>', $i + 1, esc_html( $p ) );
		}
		?>
	</ul>
</section>

<!-- Service packs -->
<section class="section--sand">
	<div class="container section hub-section">
		<header class="hub-section__head">
			<h2><?php esc_html_e( 'Service packs for different buyers', 'artisraw' ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Choose the level of support that fits your business model and market maturity.', 'artisraw' ); ?></p>
		</header>
		<div class="packs">
			<?php
			$packs = array(
				array(
					'tag' => __( 'Starter', 'artisraw' ), 'name' => __( 'Catalogue Buyer', 'artisraw' ), 'featured' => false,
					'desc' => __( 'For retailers, online sellers and concept stores looking for ready-to-sell products.', 'artisraw' ),
					'list' => array( __( 'Catalogue SKU selection', 'artisraw' ), __( 'MOQ-based quotation', 'artisraw' ), __( 'Standard packaging', 'artisraw' ), __( 'Basic export documents', 'artisraw' ) ),
					'cta'  => __( 'Request catalogue', 'artisraw' ),
				),
				array(
					'tag' => __( 'Popular', 'artisraw' ), 'name' => __( 'Private Label', 'artisraw' ), 'featured' => true,
					'desc' => __( 'For brands, distributors and boutiques wanting a customized ArtisRaw-powered collection.', 'artisraw' ),
					'list' => array( __( 'Logo engraving', 'artisraw' ), __( 'Custom labels / packaging', 'artisraw' ), __( 'Barcode-ready references', 'artisraw' ), __( 'Product development support', 'artisraw' ) ),
					'cta'  => __( 'Build my brand', 'artisraw' ),
				),
				array(
					'tag' => __( 'Premium', 'artisraw' ), 'name' => __( 'Corporate Gifts', 'artisraw' ), 'featured' => false,
					'desc' => __( 'For companies and gift agencies looking for handmade, sustainable and memorable gifts.', 'artisraw' ),
					'list' => array( __( 'Gift boxes & bundles', 'artisraw' ), __( 'Engraving & personalization', 'artisraw' ), __( 'Event / seasonal planning', 'artisraw' ), __( 'Export & logistics support', 'artisraw' ) ),
					'cta'  => __( 'Plan gift project', 'artisraw' ),
				),
			);
			foreach ( $packs as $pk ) {
				$cls = 'pack' . ( $pk['featured'] ? ' pack--featured' : '' );
				echo '<div class="' . esc_attr( $cls ) . '">';
				echo '<span class="pack__tag">' . esc_html( $pk['tag'] ) . '</span>';
				echo '<h3>' . esc_html( $pk['name'] ) . '</h3>';
				echo '<p class="pack__desc">' . esc_html( $pk['desc'] ) . '</p>';
				echo '<ul class="pack__list" role="list">';
				foreach ( $pk['list'] as $li ) {
					echo '<li>' . esc_html( $li ) . '</li>';
				}
				echo '</ul>';
				echo '<p class="pack__cta"><a class="btn btn--secondary" href="' . esc_url( artisraw_localized_url( '/request-quote/' ) ) . '" data-ga="cta_click" data-ga-label="pack" data-ga-location="services">' . esc_html( $pk['cta'] ) . '</a></p>';
				echo '</div>';
			}
			?>
		</div>
	</div>
</section>

<!-- 8-step service process -->
<section class="container section hub-section">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Our B2B service process', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'A simple, professional workflow for bulk orders, private-label projects and corporate-gift collections.', 'artisraw' ); ?></p>
	</header>
	<?php
	artisraw_steps( array(
		array( '', __( 'Buyer brief', 'artisraw' ), __( 'Tell us your market, category needs, budget, quantities and delivery expectations.', 'artisraw' ) ),
		array( '', __( 'Catalogue selection', 'artisraw' ), __( 'We recommend products by use case: kitchenware, gifts, décor, hospitality or private label.', 'artisraw' ) ),
		array( '', __( 'Quote & MOQ', 'artisraw' ), __( 'You receive pricing, MOQ options, packaging choices and export-ready information.', 'artisraw' ) ),
		array( '', __( 'Sample / branding', 'artisraw' ), __( 'Private-label, engraving or packaging samples are validated before production.', 'artisraw' ) ),
		array( '', __( 'Production', 'artisraw' ), __( 'Our team manufactures, finishes and prepares the batch with quality checkpoints.', 'artisraw' ) ),
		array( '', __( 'QC report', 'artisraw' ), __( 'Photo documentation, inspection, packing list and batch details are prepared.', 'artisraw' ) ),
		array( '', __( 'Export preparation', 'artisraw' ), __( 'ISPM-15 pallets, commercial invoice, packing list and shipping coordination.', 'artisraw' ) ),
		array( '', __( 'Reorder support', 'artisraw' ), __( 'We keep SKU logic and project history ready for easier future replenishment.', 'artisraw' ) ),
	) );
	?>
</section>

<!-- Selected clients -->
<section class="section--sand">
	<div class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Selected clients and partners who trust us', 'artisraw' ); ?></h2></header>
		<div class="cell-grid cell-grid--4">
			<?php
			$clients = array(
				array( __( 'Germany', 'artisraw' ), 'Maintea', __( 'Selected European partner for premium lifestyle and retail channels.', 'artisraw' ) ),
				array( __( 'USA', 'artisraw' ), 'Eataly', __( 'Gourmet retail and food-culture environment aligned with premium natural products.', 'artisraw' ) ),
				array( __( 'USA', 'artisraw' ), 'Olivier Napa Valley', __( 'Olive oil and lifestyle buyer focused on authentic Mediterranean products.', 'artisraw' ) ),
				array( __( 'KSA', 'artisraw' ), 'Delta Co Limited', __( 'Corporate gifting and premium B2B opportunities in the Saudi market.', 'artisraw' ) ),
			);
			foreach ( $clients as $c ) {
				echo '<div class="cell"><p class="cell__eyebrow eyebrow">' . esc_html( $c[0] ) . '</p><h3>' . esc_html( $c[1] ) . '</h3><p>' . esc_html( $c[2] ) . '</p></div>';
			}
			?>
		</div>
	</div>
</section>

<!-- Services FAQ -->
<section class="container section hub-section">
	<h2><?php esc_html_e( 'Services FAQ', 'artisraw' ); ?></h2>
	<?php
	artisraw_faq_accordion( array(
		array( __( 'Can you serve online sellers?', 'artisraw' ), __( 'Yes. We support online retailers and marketplace sellers with category selection, SKU logic and packaging suitable for e-commerce.', 'artisraw' ) ),
		array( __( 'Do you support restaurants?', 'artisraw' ), __( 'Yes. Restaurants, cafés and hospitality buyers can order boards, bowls, serving items and custom-branded accessories.', 'artisraw' ) ),
		array( __( 'Can we create corporate gifts?', 'artisraw' ), __( 'Yes. We develop corporate gift packs, personalized bundles, chess sets, kitchen kits and seasonal gift collections.', 'artisraw' ) ),
		array( __( 'Do you offer product development?', 'artisraw' ), __( 'Yes. We develop custom shapes, sizes, finishes and bundles for your market, validated with samples before production.', 'artisraw' ) ),
	), false, 'services-faq' );
	?>
</section>

<!-- Quote form -->
<section class="container section hub-section" id="quote">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Tell us your market — we prepare the B2B solution', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Share your channel and quantities and we reply within 24 hours with a tailored proposal.', 'artisraw' ); ?></p>
	</header>
	<div class="hub-form-wrap"><?php artisraw_quote_form( array( 'id' => 'services-quote', 'location' => 'services' ) ); ?></div>
</section>

<?php
get_footer();
