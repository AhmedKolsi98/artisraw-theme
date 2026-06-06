<?php
/**
 * Front page — tpl-home (CONTENT page 1 + Art Direction Addendum).
 * Editorial order (§1.4): statement → story → mosaic → products → journal →
 * voices → action.
 * 1 statement hero (+pinned trust strip) · 2 quick answer · 3 collections
 * · 4 color-block mosaic (who/how/sustainability) · 5 ready-to-ship bestsellers
 * · 6 differentiators · 7 who-we-serve · 8 stat band + buyers · 9 voices
 * · 10 Guide · 11 Instagram · 12 quote block.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ref = home_url( '/references/' );
artisraw_set_hero_preload( '/assets/ar-grove', '100vw', array( 600, 1200 ) );
get_header();

$trust = array(
	array( __( 'ISO 9001:2015', 'artisraw' ), home_url( '/certifications/' ) ),
	array( __( '30+ countries', 'artisraw' ), $ref ),
	array( __( 'MOQ 50', 'artisraw' ), home_url( '/how-to-order/' ) ),
	array( __( 'Ships in 72 h', 'artisraw' ), home_url( '/shipping-logistics/' ) ),
);
?>

<!-- 1 · Statement hero (§3): one duotone image + statement + pinned trust -->
<?php
artisraw_statement_hero( array(
	'base'      => '/assets/ar-grove',
	'alt'       => __( 'Olive grove near Sfax, Tunisia', 'artisraw' ),
	'widths'    => array( 600, 1200 ),
	'w'         => 1273, 'h' => 900,
	'eyebrow'   => __( 'ISO 9001 olive wood manufacturer · Sfax, Tunisia', 'artisraw' ),
	'statement' => __( 'wood that works.', 'artisraw' ),
	'as_h1'     => true,
	'support'   => __( 'Handmade Tunisian olive wood for retailers, distributors, hospitality groups and private-label brands in 30+ countries.', 'artisraw' ),
	'cta_label' => __( 'Request Line-Sheet & Compliance Pack', 'artisraw' ),
	'cta_url'   => home_url( '/request-quote/' ),
	'alt_label' => __( 'Explore wholesale', 'artisraw' ),
	'alt_url'   => home_url( '/olive-wood-wholesale-supplier/' ),
	'trust'     => $trust,
	'loc'       => 'home-hero',
) );
?>

<!-- 2 · Quick answer (SEO/AI extraction) -->
<div class="container section hub-section">
	<?php artisraw_quick_answer( __( 'ArtisRaw is an ISO 9001:2015-certified olive wood manufacturer in Sfax, Tunisia, supplying retailers, distributors and private-label brands in 30+ countries. Wholesale cutting boards, serveware, utensils and chess sets — MOQ from 50 units, in-stock items ship within 72 hours, custom production in 6–8 weeks.', 'artisraw' ) ); ?>
</div>

<!-- 3 · Collections (visual category nav) -->
<section class="container section hub-section">
	<div class="section-opener">
		<h2><?php esc_html_e( 'From olive tree to premium collections', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Raw olive wood, artisan production and food-safe finishing — shaped into ranges that sell on a shelf and online.', 'artisraw' ); ?></p>
	</div>
	<ul class="collections" role="list">
		<?php
		$collections = array(
			array( __( 'Kitchen & boards', 'artisraw' ), home_url( '/wholesale/olive-wood-cutting-boards/' ), '/assets/ar-boards', array( 600 ) ),
			array( __( 'Serveware & bowls', 'artisraw' ), home_url( '/wholesale/olive-wood-bowls-serveware/' ), '/assets/ar-mortar', array( 600 ) ),
			array( __( 'Gifts & chess', 'artisraw' ), home_url( '/wholesale/olive-wood-chess-sets/' ), '/assets/ar-chess', array( 600 ) ),
			array( __( 'Décor & lifestyle', 'artisraw' ), home_url( '/wholesale/olive-wood-decor-bath/' ), '/assets/ar-collection', array( 600, 1200 ) ),
		);
		foreach ( $collections as $col ) {
			echo '<li><a class="collection" href="' . esc_url( $col[1] ) . '">';
			artisraw_responsive_image( array( 'base' => $col[2], 'alt' => $col[0] . ' — wholesale olive wood', 'class' => 'collection__img', 'width' => 600, 'height' => 750, 'widths' => $col[3], 'sizes' => '(min-width: 768px) 25vw, 50vw' ) );
			echo '<span class="collection__label">' . esc_html( $col[0] ) . '</span></a></li>';
		}
		?>
	</ul>
	<p class="hub-section__note"><?php artisraw_arrow_link( __( 'View the full catalogue', 'artisraw' ), home_url( '/catalogue/' ) ); ?></p>
</section>

<!-- 4 · Color-block mosaic (§4): who we are · how it's made · sustainability -->
<?php
artisraw_color_block( array(
	'field' => 'sand', 'field_left' => true,
	'eyebrow' => __( 'Who we are', 'artisraw' ),
	'heading' => __( 'A Tunisian manufacturer with artisan roots', 'artisraw' ),
	'body'    => __( 'Founded in Sfax in 2019, ArtisRaw blends ancestral woodworking with modern, ISO 9001 production — handmade heritage built for export-ready wholesale.', 'artisraw' ),
	'link_label' => __( 'Our story & founders', 'artisraw' ),
	'link_url'   => home_url( '/about/' ),
	'img_base' => '/assets/ar-workshop', 'img_alt' => __( 'ArtisRaw artisans in the Sfax workshop', 'artisraw' ),
	'img_widths' => array( 600, 1200 ), 'w' => 1400, 'h' => 900,
) );
artisraw_color_block( array(
	'field' => 'espresso',
	'eyebrow' => __( 'How it’s made', 'artisraw' ),
	'heading' => __( 'From the tree to a finished, food-safe piece', 'artisraw' ),
	'body'    => __( 'Reclaimed Chemlali wood, controlled drying, CNC precision and hand-finishing — then unit-by-unit QC and export packing under one roof.', 'artisraw' ),
	'link_label' => __( 'See the production process', 'artisraw' ),
	'link_url'   => home_url( '/production-process/' ),
	'img_base' => '/assets/ar-boards', 'img_alt' => __( 'Olive wood board showing dense Chemlali grain', 'artisraw' ),
	'img_widths' => array( 600 ), 'w' => 548, 'h' => 365,
) );
artisraw_color_block( array(
	'field' => 'leaf', 'field_left' => true,
	'eyebrow' => __( 'Sustainability', 'artisraw' ),
	'heading' => __( 'One tree used, two planted', 'artisraw' ),
	'body'    => __( 'We work only reclaimed, end-of-life olive wood and sponsor reforestation through trees.org — with full EUDR traceability for EU buyers.', 'artisraw' ),
	'link_label' => __( 'Our sustainability approach', 'artisraw' ),
	'link_url'   => home_url( '/sustainability/' ),
	'img_base' => '/assets/ar-grove', 'img_alt' => __( 'Olive grove near Sfax', 'artisraw' ),
	'img_widths' => array( 600, 1200 ), 'w' => 1273, 'h' => 900,
) );
?>

<!-- 5 · Ready-to-Ship Bestsellers (§6 SKU strip) -->
<?php $ready = function_exists( 'artisraw_get_ready_skus' ) ? artisraw_get_ready_skus( 6 ) : array(); ?>
<?php if ( $ready ) : ?>
<section class="section--sand">
	<div class="container section hub-section sku-strip">
		<p class="sku-strip__intro"><?php esc_html_e( 'Probably the most beautiful boards your shelves will carry.', 'artisraw' ); ?></p>
		<header class="hub-section__head"><h2><?php esc_html_e( 'Ready-to-Ship Bestsellers', 'artisraw' ); ?></h2></header>
		<?php artisraw_sku_grid( array_map( 'artisraw_sku_to_array', $ready ) ); ?>
		<p class="hub-section__note"><?php artisraw_arrow_link( __( 'Browse all categories', 'artisraw' ), home_url( '/wholesale/' ) ); ?></p>
	</div>
</section>
<?php endif; ?>

<!-- 6 · Why buyers choose ArtisRaw (block formula) -->
<section class="container section hub-section">
	<div class="section-opener"><h2><?php esc_html_e( 'Why buyers choose ArtisRaw', 'artisraw' ); ?></h2></div>
	<div class="grid hub-services__grid">
		<?php
		$diffs = array(
			array( __( 'ISO 9001 quality', 'artisraw' ), __( 'Certified quality management with ≥96% first-pass yield and unit-by-unit inspection.', 'artisraw' ), home_url( '/quality-control/' ), __( 'See QC', 'artisraw' ) ),
			array( __( 'Import-ready', 'artisraw' ), __( 'Lacey Act data, EUDR traceability and ISPM-15 pallets prepared per shipment.', 'artisraw' ), home_url( '/shipping-logistics/' ), __( 'Logistics', 'artisraw' ) ),
			array( __( 'In-house private label', 'artisraw' ), __( 'Engraving, custom packaging and barcode-ready references under your brand.', 'artisraw' ), home_url( '/private-label-olive-wood/' ), __( 'Private label', 'artisraw' ) ),
			array( __( 'Fast fulfilment', 'artisraw' ), __( 'In-stock SKUs dispatched within 72 hours; custom runs in 6–8 weeks.', 'artisraw' ), home_url( '/how-to-order/' ), __( 'How to order', 'artisraw' ) ),
		);
		foreach ( $diffs as $d ) {
			echo '<div class="col-3"><div class="hub-service"><h3>' . esc_html( $d[0] ) . '</h3><p>' . esc_html( $d[1] ) . '</p><p>';
			artisraw_arrow_link( $d[3], $d[2] );
			echo '</p></div></div>';
		}
		?>
	</div>
</section>

<!-- 7 · Built for your channel (self-selection, block formula) -->
<section class="section--sand">
	<div class="container section hub-section">
		<div class="section-opener"><h2><?php esc_html_e( 'Built for your channel', 'artisraw' ); ?></h2></div>
		<div class="grid">
			<?php
			$segs = array(
				array( __( 'Retailers', 'artisraw' ), __( 'Curated, sale-ready assortments for kitchenware, gift and décor shops.', 'artisraw' ), home_url( '/wholesale/' ) ),
				array( __( 'Distributors & importers', 'artisraw' ), __( 'Volume pricing, container loads and full export documentation.', 'artisraw' ), home_url( '/olive-wood-wholesale-supplier/' ) ),
				array( __( 'Hospitality', 'artisraw' ), __( 'Durable boards and serveware with custom branding for venues.', 'artisraw' ), home_url( '/olive-wood-wholesale-supplier/' ) ),
				array( __( 'Private-label brands', 'artisraw' ), __( 'Your logo, packaging and barcode-ready references, made in-house.', 'artisraw' ), home_url( '/private-label-olive-wood/' ) ),
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

<!-- 9 · Buyer voices (§6: one quote per viewport) -->
<section class="container section hub-section">
	<?php
	artisraw_buyer_voices(
		array(
			array( __( 'ArtisRaw is reliable, consistent and easy to work with. Their handmade olive wood helped us build a premium retail collection with a strong Mediterranean story.', 'artisraw' ), __( 'Retail buyer', 'artisraw' ), __( 'Specialty retailer, United Kingdom', 'artisraw' ), 5 ),
			array( __( 'The export paperwork was ready before we asked — Lacey Act data, packing lists, the lot. Reordering is genuinely fast.', 'artisraw' ), __( 'Importer', 'artisraw' ), __( 'Distributor, United States', 'artisraw' ), 5 ),
			array( __( 'Private-label engraving and packaging came back exactly to brief. The handmade quality sells itself in our stores.', 'artisraw' ), __( 'Brand owner', 'artisraw' ), __( 'Concept stores, GCC', 'artisraw' ), 5 ),
		),
		__( 'What buyers say', 'artisraw' )
	);
	?>
</section>

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
		<p class="hub-section__note"><?php artisraw_arrow_link( __( 'Read the Magazine', 'artisraw' ), home_url( '/magazine/' ) ); ?></p>
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

<?php
get_footer();
