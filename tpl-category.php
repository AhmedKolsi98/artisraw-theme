<?php
/**
 * Template Name: Category / Catalogue
 *
 * Three modes via page meta (CONTENT page 3):
 *  - cat_term set  → CATEGORY page: definition → spec table → SKU grid → FAQ → CTA
 *  - cat_mode=private → PRIVATE-LABEL page
 *  - otherwise     → INDEX (/wholesale/): intro → category card grid → PDF CTA
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pid   = get_queried_object_id();
$term  = get_post_meta( $pid, 'cat_term', true );
$mode  = get_post_meta( $pid, 'cat_mode', true );
$qa    = get_post_meta( $pid, 'quick_answer', true );

get_header();
artisraw_breadcrumbs();

/* The 5 wholesale categories (commercial priority order). */
$categories = array(
	array( 'title' => __( 'Cutting Boards', 'artisraw' ), 'slug' => 'cutting-boards', 'href' => artisraw_localized_url( '/wholesale/olive-wood-cutting-boards/' ) ),
	array( 'title' => __( 'Bowls & Serveware', 'artisraw' ), 'slug' => 'bowls-serveware', 'href' => artisraw_localized_url( '/wholesale/olive-wood-bowls-serveware/' ) ),
	array( 'title' => __( 'Utensils', 'artisraw' ), 'slug' => 'utensils', 'href' => artisraw_localized_url( '/wholesale/olive-wood-utensils/' ) ),
	array( 'title' => __( 'Chess Sets', 'artisraw' ), 'slug' => 'chess-sets', 'href' => artisraw_localized_url( '/wholesale/olive-wood-chess-sets/' ) ),
	array( 'title' => __( 'Décor & Bath', 'artisraw' ), 'slug' => 'decor-bath', 'href' => artisraw_localized_url( '/wholesale/olive-wood-decor-bath/' ) ),
);
?>

<div class="container section hub-section">
	<header class="page-head">
		<h1><?php the_title(); ?></h1>
	</header>
	<?php if ( $qa ) { artisraw_quick_answer( $qa ); } ?>
</div>

<?php if ( $term ) : /* ---------------- CATEGORY PAGE ---------------- */ ?>

	<section class="container section hub-section">
		<h2><?php esc_html_e( 'Specifications', 'artisraw' ); ?></h2>
		<?php
		artisraw_data_table(
			__( 'Category specifications', 'artisraw' ),
			array( __( 'Attribute', 'artisraw' ), __( 'Detail', 'artisraw' ) ),
			array(
				array( __( 'Material', 'artisraw' ), __( 'Olea europaea (Chemlali) — food-safe mineral oil + beeswax', 'artisraw' ) ),
				array( __( 'MOQ', 'artisraw' ), __( 'From 50 units per SKU', 'artisraw' ) ),
				array( __( 'Lead time', 'artisraw' ), __( '72 h for stock · 2–4 weeks made-to-order', 'artisraw' ) ),
				array( __( 'Private label', 'artisraw' ), __( 'Engraving & custom packaging available', 'artisraw' ) ),
			),
			'properties'
		);
		?>
	</section>

	<section class="container section hub-section" id="skus">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Ready-to-ship SKUs', 'artisraw' ); ?></h2></header>
		<?php
		$ids = artisraw_get_skus_by_term( $term, 12 );
		if ( $ids ) {
			artisraw_sku_grid( array_map( 'artisraw_sku_to_array', $ids ) );
		} else {
			echo '<p>' . esc_html__( 'Request the line-sheet for the full SKU list in this category.', 'artisraw' ) . '</p>';
		}
		?>
		<p class="hub-section__note"><?php esc_html_e( 'Samples available — cost deducted from your first order.', 'artisraw' ); ?></p>
	</section>

	<section class="container section hub-section">
		<h2><?php esc_html_e( 'Category FAQs', 'artisraw' ); ?></h2>
		<?php
		artisraw_faq_accordion( array(
			array( __( 'What is the MOQ for this category?', 'artisraw' ), __( 'MOQ starts at 50 units per SKU and is confirmed on your quote. Mixed assortments are welcome.', 'artisraw' ) ),
			array( __( 'Are dimensions exact?', 'artisraw' ), __( 'Pieces are handmade from natural olive wood, so dimensions and grain vary slightly — part of the authentic value.', 'artisraw' ) ),
			array( __( 'Is the finish food-safe?', 'artisraw' ), __( 'Yes — food-contact items use a mineral oil + beeswax blend; MSDS available on request.', 'artisraw' ) ),
			array( __( 'Can I private-label this category?', 'artisraw' ), __( 'Yes. We engrave logos and produce custom packaging and barcode-ready references.', 'artisraw' ) ),
			array( __( 'How fast can you ship?', 'artisraw' ), __( 'In-stock SKUs ship in 72 hours; made-to-order runs take 2–4 weeks.', 'artisraw' ) ),
		), false, 'cat-faq' );
		?>
	</section>

<?php elseif ( 'private' === $mode ) : /* ---------------- PRIVATE LABEL ---------------- */ ?>

	<section class="section--sand">
		<div class="container section hub-section">
			<header class="hub-section__head"><h2><?php esc_html_e( 'Your brand, our handmade expertise', 'artisraw' ); ?></h2></header>
			<div class="grid hub-services__grid">
				<?php
				$pl = array(
					array( __( 'Logo engraving', 'artisraw' ), __( 'Laser and pyrography engraving of your logo on boards, bowls and utensils.', 'artisraw' ) ),
					array( __( 'Custom packaging', 'artisraw' ), __( 'Branded sleeves, gift boxes and retail-ready packaging.', 'artisraw' ) ),
					array( __( 'Barcode-ready', 'artisraw' ), __( 'EAN/UPC labelling and reference logic for retail and marketplace.', 'artisraw' ) ),
					array( __( 'Product development', 'artisraw' ), __( 'Custom shapes, sizes and bundles developed for your market.', 'artisraw' ) ),
				);
				foreach ( $pl as $s ) {
					echo '<div class="col-3"><div class="hub-service"><h3>' . esc_html( $s[0] ) . '</h3><p>' . esc_html( $s[1] ) . '</p></div></div>';
				}
				?>
			</div>
		</div>
	</section>

<?php else : /* ---------------- INDEX (/wholesale/) ---------------- */ ?>

	<section class="container section hub-section">
		<div class="grid">
			<?php
			foreach ( $categories as $c ) {
				$count = count( artisraw_get_skus_by_term( $c['slug'], 50 ) );
				$c['count'] = $count ? sprintf( _n( '%d SKU · MOQ from 50', '%d SKUs · MOQ from 50', $count, 'artisraw' ), $count ) : __( 'MOQ from 50', 'artisraw' );
				echo '<div class="col-4">';
				artisraw_category_card( $c );
				echo '</div>';
			}
			?>
		</div>
		<p class="hub-section__note"><a class="btn btn--secondary" href="#quote"><?php esc_html_e( 'Request full PDF catalogue', 'artisraw' ); ?></a></p>
	</section>

<?php endif; ?>

<!-- Shared CTA / quote form -->
<section class="container section hub-section" id="quote">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Request a quote', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Tell us your market and quantities — we reply within 24 hours with MOQ, pricing and import documentation.', 'artisraw' ); ?></p>
	</header>
	<div class="hub-form-wrap"><?php artisraw_quote_form( array( 'id' => 'cat-quote-' . $pid, 'location' => 'category' ) ); ?></div>
</section>

<?php
// Category/index pages: ItemList of the visible SKUs (SPEC §6.7).
if ( $term ) {
	$ids = $ids ?? artisraw_get_skus_by_term( $term, 12 );
	artisraw_product_itemlist( $ids, get_permalink() );
} elseif ( ! $mode ) {
	artisraw_product_itemlist( artisraw_get_ready_skus( 6 ), get_permalink() );
}

get_footer();
