<?php
/**
 * Template Name: Magazine (editorial landing)
 *
 * Phase 8 — the Magazine (mockup page 5): quick answer → featured article →
 * latest stories grid (WP posts) → fairs/editorial band → newsletter CTA.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pid = get_queried_object_id();
$qa  = get_post_meta( $pid, 'quick_answer', true );

$posts_q = new WP_Query( array(
	'post_type'           => 'post',
	'posts_per_page'      => 9,
	'ignore_sticky_posts' => true,
) );

// artisraw_post_to_card() lives in inc/content.php (shared with the Guide pillar).

get_header();
artisraw_breadcrumbs();
?>

<div class="container section hub-section">
	<header class="page-head"><h1><?php the_title(); ?></h1></header>
	<?php if ( $qa ) { artisraw_quick_answer( $qa ); } ?>
	<p class="lead" style="max-width:70ch"><?php esc_html_e( 'Stories worth telling for professional buyers — workshop notes, material science, compliance explainers and trade-show reports from Sfax.', 'artisraw' ); ?></p>
</div>

<?php if ( $posts_q->have_posts() ) : ?>

	<?php
	// Featured = newest post.
	$ids = wp_list_pluck( $posts_q->posts, 'ID' );
	$feat = array_shift( $ids );
	?>
	<section class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Editor’s feature', 'artisraw' ); ?></h2></header>
		<div class="grid">
			<div class="col-8"><?php artisraw_article_card( artisraw_post_to_card( $feat ) ); ?></div>
		</div>
	</section>

	<?php if ( $ids ) : ?>
	<section class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Latest stories', 'artisraw' ); ?></h2></header>
		<div class="grid">
			<?php foreach ( $ids as $id ) : ?>
				<div class="col-4"><?php artisraw_article_card( artisraw_post_to_card( $id ) ); ?></div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php endif; ?>

<?php else : ?>
	<div class="container section hub-section"><p><?php esc_html_e( 'New stories are published here regularly. In the meantime, explore the Olive Wood Guide.', 'artisraw' ); ?></p></div>
<?php endif; wp_reset_postdata(); ?>

<!-- Fairs & participation -->
<section class="section--sand">
	<div class="container section hub-section">
		<header class="hub-section__head"><h2><?php esc_html_e( 'Fairs & international participation', 'artisraw' ); ?></h2></header>
		<div class="cell-grid cell-grid--3">
			<?php
			$fairs = array(
				array( __( 'Meeting buyers face to face', 'artisraw' ), __( 'We exhibit at international B2B trade fairs to meet wholesale buyers and show the range in person.', 'artisraw' ) ),
				array( __( 'Showcasing Tunisian craft', 'artisraw' ), __( 'Live demonstrations of carving, finishing and the natural Chemlali grain that sets our work apart.', 'artisraw' ) ),
				array( __( 'From conversation to orders', 'artisraw' ), __( 'Fair conversations become catalogue selections, samples and recurring wholesale orders.', 'artisraw' ) ),
			);
			foreach ( $fairs as $f ) {
				echo '<div class="cell"><h3>' . esc_html( $f[0] ) . '</h3><p>' . esc_html( $f[1] ) . '</p></div>';
			}
			?>
		</div>
	</div>
</section>

<!-- Newsletter CTA -->
<section class="container section hub-section">
	<?php artisraw_newsletter( array( 'id' => 'magazine-newsletter', 'location' => 'magazine', 'heading' => __( 'Get B2B updates & new stories', 'artisraw' ) ) ); ?>
</section>

<?php
get_footer();
