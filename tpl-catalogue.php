<?php
/**
 * Template Name: Catalogue (full 15-family index)
 *
 * Phase 8 — the magazine-style catalogue (mockup page 4): quick answer →
 * featured families → full 15-family index → care note → gated PDF + price-list
 * request (the quote form). Families come from artisraw_catalogue_families().
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pid      = get_queried_object_id();
$qa       = get_post_meta( $pid, 'quick_answer', true );
$families = artisraw_catalogue_families();

get_header();
artisraw_breadcrumbs();
?>

<div class="container section hub-section">
	<header class="page-head"><h1><?php the_title(); ?></h1></header>
	<?php if ( $qa ) { artisraw_quick_answer( $qa ); } ?>
	<p class="lead" style="max-width:70ch"><?php esc_html_e( 'Fifteen olive wood product families, each with standardized sale SKUs, metric and imperial dimensions, natural-variation notes and MOQ by family. Request the full PDF catalogue and price list below.', 'artisraw' ); ?></p>
</div>

<!-- Featured families -->
<section class="section--sand">
	<div class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Featured families', 'artisraw' ); ?></h2></header>
		<div class="grid">
			<?php
			$featured = array(
				array( 'Cutting Boards', '/assets/ar-boards', array( 600 ), artisraw_localized_url( '/wholesale/olive-wood-cutting-boards/' ) ),
				array( 'Chess & Board Games', '/assets/ar-chess', array( 600 ), artisraw_localized_url( '/wholesale/olive-wood-chess-sets/' ) ),
				array( 'Bowls & Serveware', '/assets/ar-mortar', array( 600 ), artisraw_localized_url( '/wholesale/olive-wood-bowls-serveware/' ) ),
			);
			foreach ( $featured as $f ) {
				echo '<div class="col-4"><a class="collection" href="' . esc_url( $f[3] ) . '">';
				artisraw_responsive_image( array( 'base' => $f[1], 'alt' => $f[0] . ' — olive wood', 'class' => 'collection__img', 'width' => 600, 'height' => 600, 'widths' => $f[2], 'sizes' => '(min-width:768px) 33vw, 100vw' ) );
				echo '<span class="collection__label">' . esc_html( $f[0] ) . '</span></a></div>';
			}
			?>
		</div>
	</div>
</section>

<!-- Full family index -->
<section class="container section hub-section">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Catalogue family index', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Browse the full range. Families with a dedicated page link through; the rest are available on request.', 'artisraw' ); ?></p>
	</header>
	<div class="cell-grid cell-grid--4">
		<?php
		$i = 0;
		foreach ( $families as $slug => $f ) {
			$i++;
			$href  = ! empty( $f['page'] ) ? artisraw_localized_url( '/wholesale/' . $f['page'] . '/' ) : artisraw_localized_url( '/request-quote/' );
			$label = ! empty( $f['page'] ) ? __( 'View range', 'artisraw' ) : __( 'Request', 'artisraw' );
			echo '<div class="cell">';
			echo '<p class="cell__eyebrow eyebrow">' . esc_html( sprintf( '%02d', $i ) ) . '</p>';
			echo '<h3>' . esc_html( $f['name'] ) . '</h3>';
			echo '<p>' . esc_html( $f['blurb'] ) . '</p>';
			echo '<p><a class="btn btn--tertiary" href="' . esc_url( $href ) . '">' . esc_html( $label ) . '</a></p>';
			echo '</div>';
		}
		?>
	</div>
</section>

<!-- Care note -->
<section class="section--sand">
	<div class="container section hub-section">
		<h2><?php esc_html_e( 'Food-safe protection & maintenance', 'artisraw' ); ?></h2>
		<p class="lead" style="max-width:65ch"><?php esc_html_e( 'Every food-contact item is finished with a food-safe mineral oil and beeswax blend. Hand-wash, dry immediately and re-oil periodically — care guidance ships with each order, and the finish MSDS is available to buyers.', 'artisraw' ); ?></p>
		<p><a class="btn btn--tertiary" href="<?php echo esc_url( artisraw_localized_url( '/olive-wood/' ) ); ?>"><?php esc_html_e( 'Read the Olive Wood Guide', 'artisraw' ); ?></a></p>
	</div>
</section>

<!-- Gated PDF catalogue + price list -->
<section class="container section hub-section" id="quote">
	<header class="hub-section__head">
		<h2><?php esc_html_e( 'Want the complete PDF catalogue & price list?', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Tell us your market and we’ll send the full catalogue, line-sheet and price list within 24 hours.', 'artisraw' ); ?></p>
	</header>
	<div class="hub-form-wrap"><?php artisraw_quote_form( array( 'id' => 'catalogue-quote', 'location' => 'catalogue', 'heading' => __( 'Request the PDF catalogue & price list', 'artisraw' ) ) ); ?></div>
</section>

<?php
get_footer();
