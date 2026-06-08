<?php
/**
 * Template Name: Trust / Proof
 *
 * Generic proof page (certifications, quality, shipping, how-to-order, about,
 * references, supplier-USA, wholesale-account). Renders H1 + quick answer +
 * page body, optional document grid + FAQ, trust strip and a soft CTA.
 * Per-page copy lives in post_content; quick answer + flags in post meta.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pid = get_queried_object_id();
$qa  = get_post_meta( $pid, 'quick_answer', true );
$show_downloads = get_post_meta( $pid, 'trust_downloads', true );
$extras = get_post_meta( $pid, 'trust_extras', true ); // 'about' | 'process' | ''
$show_articles = get_post_meta( $pid, 'trust_articles', true ); // Guide pillar

get_header();
artisraw_breadcrumbs();
?>
<div class="container section hub-section">
	<header class="page-head"><h1><?php the_title(); ?></h1></header>
	<?php
	if ( $qa ) {
		artisraw_quick_answer( $qa );
	}
	if ( get_the_content() ) {
		echo '<div class="trust-body">';
		the_content();
		echo '</div>';
	}
	?>
</div>

<?php
/* ---- Page-specific enrichment blocks (Phase 5, component-rendered) ---- */
if ( 'about' === $extras ) :
	?>
	<section class="section--sand">
		<div class="container section hub-section">
			<header class="hub-section__head"><h2><?php esc_html_e( 'The ArtisRaw world in four pillars', 'artisraw' ); ?></h2></header>
			<ul class="pillars" role="list">
				<?php
				$pillars = array(
					array( __( 'Heritage', 'artisraw' ), __( 'A 3,000-year Mediterranean olive-wood tradition, made in Sfax, Tunisia.', 'artisraw' ) ),
					array( __( 'Craft 4.0', 'artisraw' ), __( '25+ registered artisans paired with CNC precision for consistent quality.', 'artisraw' ) ),
					array( __( 'Responsibility', 'artisraw' ), __( 'Reclaimed end-of-life wood, food-safe finishes and reforestation.', 'artisraw' ) ),
					array( __( 'Export', 'artisraw' ), __( 'ISO 9001 systems and documentation for 30+ countries.', 'artisraw' ) ),
				);
				foreach ( $pillars as $pl ) {
					echo '<li class="pillar"><h3>' . esc_html( $pl[0] ) . '</h3><p>' . esc_html( $pl[1] ) . '</p></li>';
				}
				?>
			</ul>
		</div>
	</section>

	<section class="container section hub-section">
		<?php
		artisraw_founders(
			array(
				array( __( 'Mohamed Bilel Cherif', 'artisraw' ), __( 'Co-founder & CEO', 'artisraw' ), __( 'Operations & strategy — building a reliable manufacturing system for professional buyers.', 'artisraw' ) ),
				array( __( 'Ihsen Triki', 'artisraw' ), __( 'Co-founder & Head of Design', 'artisraw' ), __( 'Product & artistry — fusing traditional Tunisian craft with contemporary luxury.', 'artisraw' ) ),
				array( __( 'Ahmed Sakka', 'artisraw' ), __( 'Co-founder', 'artisraw' ), __( 'Heritage & vision — keeping the story of olive wood alive through timeless products.', 'artisraw' ) ),
			),
			__( 'The founders behind ArtisRaw', 'artisraw' )
		);
		?>
		<figure class="trust-body" style="margin-top:var(--sp-4)">
			<blockquote><p>“ArtisRaw isn’t just about products; it’s about stories worth telling — tradition, sustainability and longevity.”</p></blockquote>
			<figcaption class="eyebrow">Ahmed Sakka · <?php esc_html_e( 'Co-founder', 'artisraw' ); ?></figcaption>
		</figure>
	</section>

	<section class="section--sand">
		<div class="container section hub-section">
			<header class="hub-section__head">
				<h2><?php esc_html_e( 'More than a workshop — a dedicated production facility', 'artisraw' ); ?></h2>
				<p class="lead"><?php esc_html_e( 'Our own factory pairs artisan handwork with controlled drying, CNC machining and an ISO 9001 quality system — so handmade character meets export-ready consistency.', 'artisraw' ); ?></p>
			</header>
			<?php
			artisraw_steps( array(
				array( '', __( 'Raw material sourcing', 'artisraw' ), __( 'Reclaimed, end-of-life Chemlali olive wood from licensed trees.', 'artisraw' ) ),
				array( '', __( 'Drying & curing', 'artisraw' ), __( 'Controlled drying to stabilise the wood before shaping.', 'artisraw' ) ),
				array( '', __( 'Machining & handcrafting', 'artisraw' ), __( 'CNC precision and artisan refinement on every piece.', 'artisraw' ) ),
				array( '', __( 'Food-safe finishing & QC', 'artisraw' ), __( 'Mineral-oil & beeswax finish, then unit-by-unit inspection and export packing.', 'artisraw' ) ),
			) );
			?>
			<p class="hub-section__note"><a class="btn btn--tertiary" href="<?php echo esc_url( artisraw_localized_url( '/production-process/' ) ); ?>"><?php esc_html_e( 'See the full production process', 'artisraw' ); ?></a></p>
		</div>
	</section>

<?php elseif ( 'process' === $extras ) : ?>

	<section class="section--sand">
		<div class="container section hub-section">
			<header class="hub-section__head"><h2><?php esc_html_e( 'Process overview', 'artisraw' ); ?></h2></header>
			<?php
			artisraw_steps( array(
				array( '', __( 'Responsible sourcing', 'artisraw' ), __( 'End-of-life Chemlali olive wood from licensed trees.', 'artisraw' ) ),
				array( '', __( 'Drying & curing', 'artisraw' ), __( 'Controlled drying for stable, lasting pieces.', 'artisraw' ) ),
				array( '', __( 'Cutting & shaping', 'artisraw' ), __( 'CNC precision to SKU dimensions.', 'artisraw' ) ),
				array( '', __( 'Hand finishing', 'artisraw' ), __( 'Artisans sand and refine each surface.', 'artisraw' ) ),
				array( '', __( 'Food-safe finish', 'artisraw' ), __( 'Mineral oil & beeswax, documented MSDS.', 'artisraw' ) ),
				array( '', __( 'Quality checks', 'artisraw' ), __( 'Unit-by-unit inspection, ≥96% first-pass yield.', 'artisraw' ) ),
				array( '', __( 'Export packing', 'artisraw' ), __( 'ISPM-15 pallets and retail-ready packaging.', 'artisraw' ) ),
				array( '', __( 'Documents & shipment', 'artisraw' ), __( 'Invoice, packing list and compliance per shipment.', 'artisraw' ) ),
			) );
			?>
		</div>
	</section>

	<section class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Quality-control timeline', 'artisraw' ); ?></h2></header>
		<?php
		artisraw_steps( array(
			array( __( 'Check 01', 'artisraw' ), __( 'Raw material', 'artisraw' ), __( 'Grade, moisture and grain checked before cutting.', 'artisraw' ) ),
			array( __( 'Check 02', 'artisraw' ), __( 'Cutting', 'artisraw' ), __( 'Dimensions verified against the SKU spec.', 'artisraw' ) ),
			array( __( 'Check 03', 'artisraw' ), __( 'Surface', 'artisraw' ), __( 'Sanding and finish quality inspected.', 'artisraw' ) ),
			array( __( 'Check 04', 'artisraw' ), __( 'Finish', 'artisraw' ), __( 'Food-safe finish applied and confirmed.', 'artisraw' ) ),
			array( __( 'Check 05', 'artisraw' ), __( 'Packing', 'artisraw' ), __( 'Counts, labels and packaging verified.', 'artisraw' ) ),
			array( __( 'Check 06', 'artisraw' ), __( 'Export', 'artisraw' ), __( 'Documents and pallet compliance checked.', 'artisraw' ) ),
		) );
		?>
		<p class="hub-section__note"><a class="btn btn--tertiary" href="<?php echo esc_url( artisraw_localized_url( '/quality-control/' ) ); ?>"><?php esc_html_e( 'How we control quality', 'artisraw' ); ?></a></p>
	</section>

<?php endif; ?>

<?php
/* Guide pillar: list the latest Guide articles. */
if ( $show_articles ) :
	$guide_q = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 6, 'ignore_sticky_posts' => true ) );
	if ( $guide_q->have_posts() ) : ?>
	<section class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Latest from the Guide', 'artisraw' ); ?></h2></header>
		<div class="grid">
			<?php foreach ( $guide_q->posts as $gp ) : ?>
				<div class="col-4"><?php artisraw_article_card( artisraw_post_to_card( $gp->ID ) ); ?></div>
			<?php endforeach; ?>
		</div>
		<p class="hub-section__note"><a class="btn btn--tertiary" href="<?php echo esc_url( artisraw_localized_url( '/magazine/' ) ); ?>"><?php esc_html_e( 'Visit the Magazine', 'artisraw' ); ?></a></p>
	</section>
	<?php endif; wp_reset_postdata();
endif;
?>

<?php if ( $show_downloads ) : ?>
<section class="section--sand">
	<div class="container section hub-section">
		<h2><?php esc_html_e( 'Download centre', 'artisraw' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Every claim has a document. Each download is logged so we can keep the set current.', 'artisraw' ); ?></p>
		<div class="grid">
			<?php
			$docs = array(
				array( 'title' => __( 'ISO 9001:2015 certificate', 'artisraw' ), 'type' => 'PDF', 'size' => '1.2 MB', 'updated' => 'May 2026', 'href' => artisraw_doc_url( 'iso_9001_2015.pdf' ), 'name' => 'iso_9001_2015' ),
				array( 'title' => __( 'Forestry licence #4684', 'artisraw' ), 'type' => 'PDF', 'size' => '640 KB', 'updated' => 'Feb 2026', 'href' => artisraw_doc_url( 'forestry_4684.pdf' ), 'name' => 'forestry_4684' ),
				array( 'title' => __( 'Food-contact finish MSDS', 'artisraw' ), 'type' => 'PDF', 'size' => '420 KB', 'updated' => 'Apr 2026', 'href' => artisraw_doc_url( 'finish_msds.pdf' ), 'name' => 'finish_msds' ),
				array( 'title' => __( 'Compliance pack (Lacey / EUDR)', 'artisraw' ), 'type' => 'ZIP', 'size' => '8.4 MB', 'updated' => 'Jun 2026', 'href' => artisraw_doc_url( 'compliance-pack.zip' ), 'name' => 'compliance-pack' ),
				array( 'title' => __( 'Wholesale line-sheet', 'artisraw' ), 'type' => 'PDF', 'size' => '2.1 MB', 'updated' => 'Jun 2026', 'href' => artisraw_doc_url( 'line-sheet.pdf' ), 'name' => 'line-sheet' ),
				array( 'title' => __( 'Sample export documents', 'artisraw' ), 'type' => 'PDF', 'size' => '900 KB', 'updated' => 'Mar 2026', 'href' => artisraw_doc_url( 'export-docs-sample.pdf' ), 'name' => 'export-docs-sample' ),
			);
			foreach ( $docs as $d ) {
				echo '<div class="col-4">';
				artisraw_doc_card( $d );
				echo '</div>';
			}
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- Trust strip + soft CTA -->
<div class="container section hub-section">
	<?php
	artisraw_trust_strip( array(
		array( __( 'ISO 9001:2015', 'artisraw' ), artisraw_localized_url( '/certifications/' ) ),
		array( __( 'Lacey Act data ready', 'artisraw' ), artisraw_localized_url( '/references/' ) ),
		array( __( 'EUDR traceability', 'artisraw' ), artisraw_localized_url( '/references/' ) ),
		array( __( '30+ countries', 'artisraw' ), artisraw_localized_url( '/shipping-logistics/' ) ),
	) );
	?>
	<p class="hub-hero__cta"><a class="btn btn--primary" href="<?php echo esc_url( artisraw_localized_url( '/request-quote/' ) ); ?>" data-ga="cta_click" data-ga-label="trust" data-ga-location="<?php echo esc_attr( get_post_field( 'post_name', $pid ) ); ?>"><?php esc_html_e( 'Request Line-Sheet & Compliance Pack', 'artisraw' ); ?></a></p>
</div>

<?php
get_footer();
