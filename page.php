<?php
/**
 * Default page template. Phase-specific templates (tpl-*) override per URL.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
artisraw_breadcrumbs();
?>
<article class="container section" <?php post_class(); ?>>
	<header class="page-head">
		<h1><?php the_title(); ?></h1>
	</header>
	<div class="page-content">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
</article>
<?php
get_footer();
