<?php
/**
 * Fallback template (archives, blog, search). Phase-specific templates override
 * this later; for now it renders the shell + the loop inside <main>.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
artisraw_breadcrumbs();
?>
<div class="container section">
	<?php if ( have_posts() ) : ?>
		<header class="page-head">
			<h1><?php echo esc_html( wp_get_document_title() ); ?></h1>
		</header>
		<div class="grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="col-6" <?php post_class(); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="lead"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1><?php esc_html_e( 'Nothing found', 'artisraw' ); ?></h1>
		<p class="lead"><?php esc_html_e( 'No content matches your request yet.', 'artisraw' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
