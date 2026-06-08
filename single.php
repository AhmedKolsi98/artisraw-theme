<?php
/**
 * Single article (Olive Wood Guide / Magazine post) — Phase 8.
 *
 * Quick answer first, reviewer byline + Last-updated date, prose body, related
 * CTA, and BlogPosting JSON-LD with author/reviewer (E-E-A-T, SPEC §6.9).
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$pid      = get_the_ID();
	$qa       = get_post_meta( $pid, 'quick_answer', true );
	$reviewer = get_post_meta( $pid, 'article_reviewer', true ) ?: 'Ihsen Triki, Head of Design';
	$updated  = get_the_modified_date( 'F Y' );
	artisraw_breadcrumbs();
	?>
	<article class="container section hub-section" itemscope itemtype="https://schema.org/BlogPosting">
		<header class="page-head">
			<p class="eyebrow"><a href="<?php echo esc_url( artisraw_localized_url( '/olive-wood/' ) ); ?>"><?php esc_html_e( 'Olive Wood Guide', 'artisraw' ); ?></a></p>
			<h1 itemprop="headline"><?php the_title(); ?></h1>
			<p class="card__meta eyebrow"><?php
				/* translators: 1: reviewer name, 2: updated date */
				printf( esc_html__( 'Reviewed by %1$s · Updated %2$s', 'artisraw' ), esc_html( $reviewer ), esc_html( $updated ) );
			?></p>
		</header>

		<?php
		if ( $qa ) {
			artisraw_quick_answer( $qa );
		}
		if ( has_post_thumbnail() ) {
			echo '<figure class="article-hero">';
			the_post_thumbnail( 'artisraw-1200', array( 'class' => 'article-hero__img', 'loading' => 'eager' ) );
			echo '</figure>';
		}
		?>

		<div class="trust-body" itemprop="articleBody">
			<?php the_content(); ?>
		</div>

		<div class="account-status" style="margin-top:var(--sp-5)">
			<strong><?php esc_html_e( 'Buying olive wood at wholesale?', 'artisraw' ); ?></strong>
			<a href="<?php echo esc_url( artisraw_localized_url( '/request-quote/' ) ); ?>" data-ga="cta_click" data-ga-label="article" data-ga-location="<?php echo esc_attr( get_post_field( 'post_name', $pid ) ); ?>"><?php esc_html_e( 'Request a quote', 'artisraw' ); ?></a>
		</div>
	</article>

	<?php
	// BlogPosting JSON-LD with author + reviewer (E-E-A-T).
	if ( function_exists( 'artisraw_jsonld' ) ) {
		$data = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'BlogPosting',
			'headline'      => get_the_title(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'author'        => array( '@type' => 'Organization', 'name' => 'ArtisRaw' ),
			'reviewedBy'    => array( '@type' => 'Person', 'name' => $reviewer ),
			'publisher'     => array(
				'@type' => 'Organization',
				'name'  => 'ArtisRaw',
				'logo'  => array( '@type' => 'ImageObject', 'url' => ARTISRAW_URI . '/assets/artisraw-logo.png' ),
			),
			'mainEntityOfPage' => get_permalink(),
		);
		if ( $qa ) {
			$data['description'] = $qa;
		}
		if ( has_post_thumbnail() ) {
			$data['image'] = get_the_post_thumbnail_url( $pid, 'artisraw-1200' );
		}
		artisraw_jsonld( $data );
	}
	?>

	<?php
endwhile;

get_footer();
