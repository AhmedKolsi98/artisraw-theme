<?php
/**
 * Shared homepage composition (Figma). Rendered by front-page.php (EN front
 * page) and tpl-home.php (the /fr/ French homepage), so both languages get the
 * same design; gettext + artisraw_localized_url() localise strings and links.
 *
 * @package ArtisRaw
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$ref = artisraw_localized_url( '/references/' );
$trust = array(
	array( __( 'ISO 9001:2015', 'artisraw' ), artisraw_localized_url( '/certifications/' ) ),
	array( __( '30+ countries', 'artisraw' ), $ref ),
	array( __( 'MOQ 50', 'artisraw' ), artisraw_localized_url( '/how-to-order/' ) ),
	array( __( 'Ships in 72 h', 'artisraw' ), artisraw_localized_url( '/shipping-logistics/' ) ),
);
?>

<!-- 1 · Hero (Figma): photo collage + amber value-prop headline + ISO badge + trust -->
<?php
artisraw_photo_hero( array(
	'base'      => '/assets/ar-hero-collage',
	'alt'       => __( 'Handmade Tunisian olive wood collections', 'artisraw' ),
	'widths'    => array( 600, 1200 ),
	'w'         => 1672, 'h' => 941,
	'eyebrow'   => __( 'ISO 9001 olive wood manufacturer · Sfax, Tunisia', 'artisraw' ),
	'title'     => __( 'Premium Olive Wood for Wholesale Buyers', 'artisraw' ),
	'as_h1'     => true,
	'support'   => __( 'Handmade Tunisian olive wood collections for retailers, distributors, hospitality groups, corporate gifts and private-label brands.', 'artisraw' ),
	'cta_label' => __( 'Request a Quote', 'artisraw' ),
	'cta_url'   => artisraw_localized_url( '/request-quote/' ),
	'alt_label' => __( 'Explore wholesale', 'artisraw' ),
	'alt_url'   => artisraw_localized_url( '/olive-wood-wholesale-supplier/' ),
	'trust'     => $trust,
	'badge'     => true,
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
			array( __( 'Kitchen & boards', 'artisraw' ), artisraw_localized_url( '/wholesale/olive-wood-cutting-boards/' ), '/assets/ar-boards', array( 600 ) ),
			array( __( 'Serveware & bowls', 'artisraw' ), artisraw_localized_url( '/wholesale/olive-wood-bowls-serveware/' ), '/assets/ar-mortar', array( 600 ) ),
			array( __( 'Gifts & chess', 'artisraw' ), artisraw_localized_url( '/wholesale/olive-wood-chess-sets/' ), '/assets/ar-chess', array( 600 ) ),
			array( __( 'Décor & lifestyle', 'artisraw' ), artisraw_localized_url( '/wholesale/olive-wood-decor-bath/' ), '/assets/ar-collection', array( 600, 1200 ) ),
		);
		foreach ( $collections as $col ) {
			echo '<li><a class="collection" href="' . esc_url( $col[1] ) . '">';
			artisraw_responsive_image( array( 'base' => $col[2], 'alt' => $col[0] . ' — wholesale olive wood', 'class' => 'collection__img', 'width' => 600, 'height' => 750, 'widths' => $col[3], 'sizes' => '(min-width: 768px) 25vw, 50vw' ) );
			echo '<span class="collection__label">' . esc_html( $col[0] ) . '</span></a></li>';
		}
		?>
	</ul>
	<p class="hub-section__note"><?php artisraw_arrow_link( __( 'View the full catalogue', 'artisraw' ), artisraw_localized_url( '/catalogue/' ) ); ?></p>
</section>

<!-- 3b · Captioned image grid (Figma): tree → workshop → wholesale → shelves -->
<div class="container section">
	<?php
	artisraw_caption_grid(
		array(
			array( 'base' => '/assets/ar-grove', 'alt' => __( 'Olive grove in the Sfax region', 'artisraw' ), 'caption' => __( 'Responsible olive wood sourcing', 'artisraw' ), 'w' => 1273, 'h' => 900, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-lathe', 'alt' => __( 'Artisan shaping olive wood on a lathe', 'artisraw' ), 'caption' => __( 'Handmade production', 'artisraw' ), 'w' => 1400, 'h' => 933, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-boards-drying', 'alt' => __( 'Dense-grain olive wood boards drying', 'artisraw' ), 'caption' => __( 'Chemlali dense grain', 'artisraw' ), 'w' => 1200, 'h' => 801, 'widths' => array( 600, 1200 ) ),
			array( 'base' => '/assets/ar-chess2', 'alt' => __( 'Finished olive wood chess set', 'artisraw' ), 'caption' => __( 'Premium B2B collections', 'artisraw' ), 'w' => 800, 'h' => 800, 'widths' => array( 600 ) ),
		),
		__( 'From tree, to workshop, to wholesale, to shelves', 'artisraw' )
	);
	?>
</div>

<!-- 4 · Color-block mosaic (§4): who we are · how it's made · sustainability -->
<?php
artisraw_color_block( array(
	'field' => 'sand', 'field_left' => true,
	'eyebrow' => __( 'Who we are', 'artisraw' ),
	'heading' => __( 'A Tunisian manufacturer with artisan roots', 'artisraw' ),
	'body'    => __( 'Founded in Sfax in 2019, ArtisRaw blends ancestral woodworking with modern, ISO 9001 production — handmade heritage built for export-ready wholesale.', 'artisraw' ),
	'link_label' => __( 'Our story & founders', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/about/' ),
	'img_base' => '/assets/ar-workshop', 'img_alt' => __( 'ArtisRaw artisans in the Sfax workshop', 'artisraw' ),
	'img_widths' => array( 600, 1200 ), 'w' => 1400, 'h' => 900,
) );
artisraw_color_block( array(
	'field' => 'espresso',
	'eyebrow' => __( 'How it’s made', 'artisraw' ),
	'heading' => __( 'From the tree to a finished, food-safe piece', 'artisraw' ),
	'body'    => __( 'Reclaimed Chemlali wood, controlled drying, CNC precision and hand-finishing — then unit-by-unit QC and export packing under one roof.', 'artisraw' ),
	'link_label' => __( 'See the production process', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/production-process/' ),
	'img_base' => '/assets/ar-boards', 'img_alt' => __( 'Olive wood board showing dense Chemlali grain', 'artisraw' ),
	'img_widths' => array( 600 ), 'w' => 548, 'h' => 365,
) );
artisraw_color_block( array(
	'field' => 'sand', 'field_left' => true,
	'eyebrow' => __( 'Sustainability', 'artisraw' ),
	'heading' => __( 'One tree used, two planted', 'artisraw' ),
	'body'    => __( 'We work only reclaimed, end-of-life olive wood and sponsor reforestation through trees.org — with full EUDR traceability for EU buyers.', 'artisraw' ),
	'link_label' => __( 'Our sustainability approach', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/sustainability/' ),
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
		<p class="hub-section__note"><?php artisraw_arrow_link( __( 'Browse all categories', 'artisraw' ), artisraw_localized_url( '/wholesale/' ) ); ?></p>
	</div>
</section>
<?php endif; ?>

<!-- 6 · Why buyers choose ArtisRaw (block formula) -->
<section class="container section hub-section">
	<div class="section-opener"><h2><?php esc_html_e( 'Why buyers choose ArtisRaw', 'artisraw' ); ?></h2></div>
	<div class="grid hub-services__grid">
		<?php
		$diffs = array(
			array( __( 'ISO 9001 quality', 'artisraw' ), __( 'Certified quality management with ≥96% first-pass yield and unit-by-unit inspection.', 'artisraw' ), artisraw_localized_url( '/quality-control/' ), __( 'See QC', 'artisraw' ) ),
			array( __( 'Import-ready', 'artisraw' ), __( 'Lacey Act data, EUDR traceability and ISPM-15 pallets prepared per shipment.', 'artisraw' ), artisraw_localized_url( '/shipping-logistics/' ), __( 'Logistics', 'artisraw' ) ),
			array( __( 'In-house private label', 'artisraw' ), __( 'Engraving, custom packaging and barcode-ready references under your brand.', 'artisraw' ), artisraw_localized_url( '/private-label-olive-wood/' ), __( 'Private label', 'artisraw' ) ),
			array( __( 'Fast fulfilment', 'artisraw' ), __( 'In-stock SKUs dispatched within 72 hours; custom runs in 6–8 weeks.', 'artisraw' ), artisraw_localized_url( '/how-to-order/' ), __( 'How to order', 'artisraw' ) ),
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
				array( __( 'Retailers', 'artisraw' ), __( 'Curated, sale-ready assortments for kitchenware, gift and décor shops.', 'artisraw' ), artisraw_localized_url( '/wholesale/' ) ),
				array( __( 'Distributors & importers', 'artisraw' ), __( 'Volume pricing, container loads and full export documentation.', 'artisraw' ), artisraw_localized_url( '/olive-wood-wholesale-supplier/' ) ),
				array( __( 'Hospitality', 'artisraw' ), __( 'Durable boards and serveware with custom branding for venues.', 'artisraw' ), artisraw_localized_url( '/olive-wood-wholesale-supplier/' ) ),
				array( __( 'Private-label brands', 'artisraw' ), __( 'Your logo, packaging and barcode-ready references, made in-house.', 'artisraw' ), artisraw_localized_url( '/private-label-olive-wood/' ) ),
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

<!-- 9 · Feature testimonial + trio wayfinding band (Figma) -->
<?php
artisraw_testimonial_feature( array(
	'heading'    => __( 'What they say…', 'artisraw' ),
	'quote'      => __( 'ArtisRaw is reliable, consistent and easy to work with. Their handmade olive wood products helped us build a premium retail collection with a strong Mediterranean story.', 'artisraw' ),
	'author'     => __( 'Retail buyer', 'artisraw' ),
	'role'       => __( 'Specialty retailer, United Kingdom', 'artisraw' ),
	'link_label' => __( 'Request a quote', 'artisraw' ),
	'link_url'   => artisraw_localized_url( '/request-quote/' ),
	'img_base'   => '/assets/ar-trade-fair',
	'img_alt'    => __( 'ArtisRaw booth at an international B2B trade fair', 'artisraw' ),
	'img_w'      => 1200, 'img_h' => 900, 'img_widths' => array( 600, 1200 ),
) );
artisraw_trio_band( array(
	array( 'label' => __( 'Discover the real olive wood quality', 'artisraw' ), 'cue' => __( 'Discover more', 'artisraw' ), 'href' => artisraw_localized_url( '/about/' ) ),
	array( 'label' => __( 'Our process', 'artisraw' ), 'cue' => __( 'Learn more', 'artisraw' ), 'href' => artisraw_localized_url( '/production-process/' ) ),
	array( 'label' => __( 'FAQs', 'artisraw' ), 'cue' => __( 'Read more', 'artisraw' ), 'href' => artisraw_localized_url( '/faq/' ) ),
) );
?>

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
		<p class="hub-section__note"><?php artisraw_arrow_link( __( 'Read the Magazine', 'artisraw' ), artisraw_localized_url( '/magazine/' ) ); ?></p>
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

