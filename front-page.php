<?php
/**
 * Front page — tpl-home (CONTENT page 1).
 * Section order (value → proof → self-selection → action):
 * 1 hero · 2 trust · 3 categories · 4 differentiators · 5 who-we-serve
 * · 6 proof band · 7 sustainability · 8 latest guide · 9 quote form.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ref = home_url( '/references/' );
artisraw_set_hero_preload( '/assets/hero-wholesale', '(min-width: 1024px) 46vw, 100vw' );
get_header();
?>

<!-- 1 · Hero -->
<section class="section section--dark on-dark hub-hero" data-hero="home-hero">
	<div class="container hub-hero__grid">
		<div class="hub-hero__copy">
			<p class="eyebrow"><?php esc_html_e( 'ISO 9001 olive wood manufacturer · Sfax, Tunisia', 'artisraw' ); ?></p>
			<h1 class="h1-hero"><?php esc_html_e( 'Premium Olive Wood for Wholesale Buyers', 'artisraw' ); ?></h1>
			<p class="lead hub-hero__lead"><?php esc_html_e( 'Handmade Tunisian olive wood for retailers, distributors, hospitality groups and private-label brands in 30+ countries.', 'artisraw' ); ?></p>
			<p class="hub-hero__cta">
				<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/request-quote/' ) ); ?>" data-ga="cta_click" data-ga-label="hero" data-ga-location="home-hero"><?php esc_html_e( 'Request Line-Sheet & Compliance Pack', 'artisraw' ); ?></a>
				<a class="btn btn--tertiary hub-hero__alt" href="<?php echo esc_url( home_url( '/olive-wood-wholesale-supplier/' ) ); ?>"><?php esc_html_e( 'Explore wholesale', 'artisraw' ); ?></a>
			</p>
		</div>
		<div class="hub-hero__media">
			<?php artisraw_responsive_image( array( 'base' => '/assets/hero-wholesale', 'alt' => __( 'Handmade olive wood boards and serveware from Tunisia', 'artisraw' ), 'class' => 'hub-hero__img', 'width' => 1800, 'height' => 1200, 'sizes' => '(min-width: 1024px) 46vw, 100vw', 'eager' => true ) ); ?>
		</div>
	</div>
</section>

<!-- 2 · Trust strip -->
<div class="section--sand hub-trust">
	<div class="container">
		<?php
		artisraw_trust_strip( array(
			array( __( 'ISO 9001:2015', 'artisraw' ), home_url( '/certifications/' ) ),
			array( __( '30+ countries', 'artisraw' ), $ref ),
			array( __( 'MOQ 50', 'artisraw' ), home_url( '/how-to-order/' ) ),
			array( __( 'Ships in 72 h', 'artisraw' ), home_url( '/shipping-logistics/' ) ),
			array( __( 'Unit-by-unit QC', 'artisraw' ), home_url( '/quality-control/' ) ),
		) );
		?>
	</div>
</div>

<!-- Quick answer -->
<div class="container section hub-section">
	<?php artisraw_quick_answer( __( 'ArtisRaw is an ISO 9001:2015-certified olive wood manufacturer in Sfax, Tunisia, supplying retailers, distributors and private-label brands in 30+ countries. Wholesale cutting boards, serveware, utensils and chess sets — MOQ from 50 units, in-stock items ship within 72 hours, custom production in 6–8 weeks.', 'artisraw' ) ); ?>
</div>

<!-- 3 · Product categories -->
<section class="container section hub-section">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Wholesale olive wood categories', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Five core ranges, all sale-ready with full spec cards and private-label options.', 'artisraw' ); ?></p>
	</header>
	<div class="grid">
		<?php
		$cats = array(
			array( 'title' => __( 'Cutting Boards', 'artisraw' ), 'href' => home_url( '/wholesale/olive-wood-cutting-boards/' ) ),
			array( 'title' => __( 'Utensils', 'artisraw' ), 'href' => home_url( '/wholesale/olive-wood-utensils/' ) ),
			array( 'title' => __( 'Bowls & Serveware', 'artisraw' ), 'href' => home_url( '/wholesale/olive-wood-bowls-serveware/' ) ),
			array( 'title' => __( 'Chess Sets', 'artisraw' ), 'href' => home_url( '/wholesale/olive-wood-chess-sets/' ) ),
			array( 'title' => __( 'Décor & Bath', 'artisraw' ), 'href' => home_url( '/wholesale/olive-wood-decor-bath/' ) ),
		);
		foreach ( $cats as $c ) {
			echo '<div class="col-4">';
			artisraw_category_card( $c );
			echo '</div>';
		}
		?>
	</div>
</section>

<!-- 3b · Visual collections (lifestyle framing of the ranges) -->
<section class="container section hub-section">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Collections built for wholesale presentation', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Raw olive wood, artisan production and food-safe finishing — shaped into ranges that sell on a shelf and online.', 'artisraw' ); ?></p>
	</header>
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
			artisraw_responsive_image( array(
				'base'   => $col[2],
				'alt'    => $col[0] . ' — wholesale olive wood',
				'class'  => 'collection__img',
				'width'  => 600, 'height' => 750, 'widths' => $col[3],
				'sizes'  => '(min-width: 768px) 25vw, 50vw',
			) );
			echo '<span class="collection__label">' . esc_html( $col[0] ) . '</span></a></li>';
		}
		?>
	</ul>
</section>

<!-- 3c · Photo mosaic: from tree to workshop to shelves -->
<section class="section--sand">
	<div class="container section hub-section">
		<?php
		artisraw_photo_mosaic(
			array(
				array( 'base' => '/assets/ar-grove', 'alt' => __( 'Olive grove near Sfax, Tunisia', 'artisraw' ), 'label' => __( 'From the tree', 'artisraw' ), 'variant' => 'big', 'w' => 1273, 'h' => 900, 'widths' => array( 600, 1200 ) ),
				array( 'base' => '/assets/ar-workshop', 'alt' => __( 'ArtisRaw artisans shaping olive wood in the workshop', 'artisraw' ), 'label' => __( 'Handmade production', 'artisraw' ), 'variant' => 'wide', 'w' => 1400, 'h' => 358, 'widths' => array( 600, 1200 ) ),
				array( 'base' => '/assets/ar-boards', 'alt' => __( 'Olive wood board showing dense Chemlali grain', 'artisraw' ), 'label' => __( 'Chemlali dense grain', 'artisraw' ), 'w' => 548, 'h' => 365, 'widths' => array( 600 ) ),
				array( 'base' => '/assets/ar-collection', 'alt' => __( 'Range of olive wood products ready for wholesale', 'artisraw' ), 'label' => __( 'Premium B2B collections', 'artisraw' ), 'w' => 1920, 'h' => 960, 'widths' => array( 600, 1200 ) ),
			),
			__( 'From tree, to workshop, to wholesale shelves', 'artisraw' ),
			__( 'Responsible sourcing, handmade production, dense Chemlali grain and export-ready collections — the proof behind every order.', 'artisraw' )
		);
		?>
	</div>
</section>

<!-- 4 · Differentiators (each backed by a number → proof page) -->
<section class="section--sand">
	<div class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Why buyers choose ArtisRaw', 'artisraw' ); ?></h2></header>
		<div class="grid hub-services__grid">
			<?php
			$diffs = array(
				array( __( 'ISO 9001 quality', 'artisraw' ), __( 'Certified quality management with ≥96% first-pass yield and unit-by-unit inspection.', 'artisraw' ), home_url( '/quality-control/' ), __( 'See QC', 'artisraw' ) ),
				array( __( 'Import-ready', 'artisraw' ), __( 'Lacey Act data, EUDR traceability and ISPM-15 pallets prepared per shipment.', 'artisraw' ), home_url( '/shipping-logistics/' ), __( 'Logistics', 'artisraw' ) ),
				array( __( 'In-house private label', 'artisraw' ), __( 'Engraving, custom packaging and barcode-ready references under your brand.', 'artisraw' ), home_url( '/private-label-olive-wood/' ), __( 'Private label', 'artisraw' ) ),
				array( __( 'Fast fulfilment', 'artisraw' ), __( 'In-stock SKUs dispatched within 72 hours; custom runs in 6–8 weeks.', 'artisraw' ), home_url( '/how-to-order/' ), __( 'How to order', 'artisraw' ) ),
			);
			foreach ( $diffs as $d ) {
				echo '<div class="col-3"><div class="hub-service"><h3>' . esc_html( $d[0] ) . '</h3><p>' . esc_html( $d[1] ) . '</p><p><a class="btn btn--tertiary" href="' . esc_url( $d[2] ) . '">' . esc_html( $d[3] ) . '</a></p></div></div>';
			}
			?>
		</div>
		<p class="hub-hero__cta"><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/request-quote/' ) ); ?>" data-ga="cta_click" data-ga-label="after-diff" data-ga-location="home"><?php esc_html_e( 'Request Line-Sheet & Compliance Pack', 'artisraw' ); ?></a></p>
	</div>
</section>

<!-- 4b · Who we are (founders teaser → /about/) -->
<section class="container section hub-section">
	<div class="hub-hero__grid">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Who we are', 'artisraw' ); ?></p>
			<h2><?php esc_html_e( 'A Tunisian olive wood manufacturer with artisan roots', 'artisraw' ); ?></h2>
			<p class="lead" style="max-width:52ch"><?php esc_html_e( 'Founded in Sfax in 2019 by three Tunisians, ArtisRaw blends ancestral woodworking with modern, ISO 9001 production — handmade heritage built for export-ready wholesale.', 'artisraw' ); ?></p>
			<p><a class="btn btn--secondary" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'Our story & founders', 'artisraw' ); ?></a></p>
		</div>
		<ul class="world-legend" role="list">
			<li><strong>Mohamed Bilel Cherif</strong><?php esc_html_e( 'Co-founder & CEO — operations & strategy', 'artisraw' ); ?></li>
			<li><strong>Ihsen Triki</strong><?php esc_html_e( 'Co-founder & Head of Design', 'artisraw' ); ?></li>
			<li><strong>Ahmed Sakka</strong><?php esc_html_e( 'Co-founder — heritage & vision', 'artisraw' ); ?></li>
		</ul>
	</div>
</section>

<!-- 5 · Who we serve (self-selection) -->
<section class="container section hub-section">
	<header class="hub-section__head"><h2><?php esc_html_e( 'Built for your channel', 'artisraw' ); ?></h2></header>
	<div class="grid">
		<?php
		$segs = array(
			array( __( 'Retailers', 'artisraw' ), __( 'Curated, sale-ready assortments for kitchenware, gift and décor shops.', 'artisraw' ), home_url( '/wholesale/' ) ),
			array( __( 'Distributors & importers', 'artisraw' ), __( 'Volume pricing, container loads and full export documentation.', 'artisraw' ), home_url( '/olive-wood-wholesale-supplier/' ) ),
			array( __( 'Hospitality', 'artisraw' ), __( 'Durable boards and serveware with custom branding for venues.', 'artisraw' ), home_url( '/olive-wood-wholesale-supplier/' ) ),
			array( __( 'Private-label brands', 'artisraw' ), __( 'Your logo, packaging and barcode-ready references, made in-house.', 'artisraw' ), home_url( '/private-label-olive-wood/' ) ),
		);
		foreach ( $segs as $s ) {
			echo '<div class="col-3"><div class="hub-service"><h3>' . esc_html( $s[0] ) . '</h3><p>' . esc_html( $s[1] ) . '</p><p><a class="btn btn--tertiary" href="' . esc_url( $s[2] ) . '">' . esc_html__( 'Learn more', 'artisraw' ) . '</a></p></div></div>';
		}
		?>
	</div>
</section>

<!-- 6 · Quantified proof band + reference buyers -->
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
	<p class="hub-hero__cta" style="justify-content:center"><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/request-quote/' ) ); ?>" data-ga="cta_click" data-ga-label="after-proof" data-ga-location="home"><?php esc_html_e( 'Request Line-Sheet & Compliance Pack', 'artisraw' ); ?></a></p>
</div>

<!-- 6b · Testimonials -->
<section class="container section hub-section">
	<?php
	artisraw_testimonials(
		array(
			array( __( 'ArtisRaw is reliable, consistent and easy to work with. Their handmade olive wood helped us build a premium retail collection with a strong Mediterranean story.', 'artisraw' ), __( 'Retail buyer', 'artisraw' ), __( 'Wholesale partner, Europe', 'artisraw' ), 5 ),
			array( __( 'The export paperwork was ready before we asked — Lacey Act data, packing lists, the lot. Reordering is genuinely fast.', 'artisraw' ), __( 'Importer', 'artisraw' ), __( 'Distributor, USA', 'artisraw' ), 5 ),
			array( __( 'Private-label engraving and packaging came back exactly to brief. The handmade quality sells itself in our stores.', 'artisraw' ), __( 'Brand owner', 'artisraw' ), __( 'Concept stores, GCC', 'artisraw' ), 5 ),
		),
		__( 'What buyers say', 'artisraw' )
	);
	?>
</section>

<!-- 7 · Sustainability snapshot -->
<section class="section--sand">
	<div class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Sustainable by origin', 'artisraw' ); ?></h2></header>
		<p class="lead" style="max-width:65ch"><?php esc_html_e( 'We work end-of-life Chemlali olive wood — reclaimed from trees past fruit-bearing age — into lasting objects, and sponsor reforestation through trees.org. Responsible sourcing with full EUDR traceability.', 'artisraw' ); ?></p>
		<p><a class="btn btn--tertiary" href="<?php echo esc_url( home_url( '/sustainability/' ) ); ?>"><?php esc_html_e( 'Our sustainability approach', 'artisraw' ); ?></a></p>
	</div>
</section>

<!-- 7b · Plant a tree for every order -->
<?php artisraw_plant_a_tree(); ?>

<!-- 8 · Latest from the Guide -->
<section class="container section hub-section">
	<header class="hub-section__head"><h2><?php esc_html_e( 'From the Olive Wood Guide', 'artisraw' ); ?></h2></header>
	<div class="grid">
		<?php
		$arts = array(
			array( 'title' => __( 'Chemlali olive wood: why it resists knife scarring', 'artisraw' ), 'href' => home_url( '/olive-wood/' ), 'excerpt' => __( 'Density, low porosity and Janka hardness explained for buyers.', 'artisraw' ), 'author' => __( 'Reviewed by Ihsen Triki', 'artisraw' ), 'date' => __( 'Updated May 2026', 'artisraw' ) ),
			array( 'title' => __( 'Importing olive wood to the USA: Lacey Act basics', 'artisraw' ), 'href' => home_url( '/olive-wood/' ), 'excerpt' => __( 'The declaration data you need and how we supply it.', 'artisraw' ), 'author' => __( 'Reviewed by Ihsen Triki', 'artisraw' ), 'date' => __( 'Updated Apr 2026', 'artisraw' ) ),
			array( 'title' => __( 'Caring for olive wood in commercial kitchens', 'artisraw' ), 'href' => home_url( '/olive-wood/' ), 'excerpt' => __( 'Hand-wash, dry, re-oil — a simple care routine for longevity.', 'artisraw' ), 'author' => __( 'Reviewed by Ihsen Triki', 'artisraw' ), 'date' => __( 'Updated Mar 2026', 'artisraw' ) ),
		);
		foreach ( $arts as $a ) {
			echo '<div class="col-4">';
			artisraw_article_card( $a );
			echo '</div>';
		}
		?>
	</div>
</section>

<!-- 8b · Instagram strip -->
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

<!-- 9 · Quote form -->
<section class="container section hub-section" id="quote">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Start your wholesale order', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Tell us your market and quantities — get a quote with MOQ, pricing and import documentation within 24 hours.', 'artisraw' ); ?></p>
	</header>
	<div class="hub-form-wrap"><?php artisraw_quote_form( array( 'id' => 'home-quote', 'location' => 'home' ) ); ?></div>
</section>

<?php
get_footer();
