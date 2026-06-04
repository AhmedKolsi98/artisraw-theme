<?php
/**
 * Front page — Phase-1 placeholder hero so "/" renders intentionally.
 * The full tpl-home is built in Phase 4 from the Content Package (page 1).
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="section section--dark on-dark home-hero">
	<div class="container">
		<p class="eyebrow"><?php esc_html_e( 'Olive wood manufacturer &amp; exporter · Sfax, Tunisia', 'artisraw' ); ?></p>
		<h1 class="h1-hero"><?php esc_html_e( 'Olive wood, made by hand and built for wholesale.', 'artisraw' ); ?></h1>
		<p class="lead home-hero__lead"><?php esc_html_e( 'ISO 9001 manufacturer of cutting boards, bowls, utensils and serveware for retailers, distributors and hospitality in 30+ countries. MOQ 50, stock ships in 72 h.', 'artisraw' ); ?></p>
		<p class="home-hero__cta">
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/request-quote/' ) ); ?>"><?php esc_html_e( 'Request Line-Sheet &amp; Compliance Pack', 'artisraw' ); ?></a>
			<a class="btn btn--tertiary home-hero__alt" href="<?php echo esc_url( home_url( '/olive-wood-wholesale-supplier/' ) ); ?>"><?php esc_html_e( 'Explore the wholesale catalogue', 'artisraw' ); ?></a>
		</p>
	</div>
</section>
<?php
get_footer();
