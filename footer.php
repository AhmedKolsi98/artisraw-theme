<?php
/**
 * Footer: NAP, ≤12 links, language switcher, WhatsApp (SPEC §4, §5.6).
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fallback footer links (≤12) used until a 'footer' menu is assigned.
 */
function artisraw_render_footer_nav() {
	$links = array(
		array( __( 'Wholesale Hub', 'artisraw' ), home_url( '/olive-wood-wholesale-supplier/' ) ),
		array( __( 'Catalogue', 'artisraw' ), home_url( '/wholesale/' ) ),
		array( __( 'Private Label', 'artisraw' ), home_url( '/private-label-olive-wood/' ) ),
		array( __( 'Certifications', 'artisraw' ), home_url( '/certifications/' ) ),
		array( __( 'Quality Control', 'artisraw' ), home_url( '/quality-control/' ) ),
		array( __( 'Shipping & Logistics', 'artisraw' ), home_url( '/shipping-logistics/' ) ),
		array( __( 'How to Order', 'artisraw' ), home_url( '/how-to-order/' ) ),
		array( __( 'References & Downloads', 'artisraw' ), home_url( '/references/' ) ),
		array( __( 'Olive Wood Guide', 'artisraw' ), home_url( '/olive-wood/' ) ),
		array( __( 'About', 'artisraw' ), home_url( '/about/' ) ),
		array( __( 'Contact', 'artisraw' ), home_url( '/contact/' ) ),
		array( __( 'Request a Quote', 'artisraw' ), home_url( '/request-quote/' ) ),
	);
	echo '<ul class="footer__links" role="list">';
	foreach ( $links as $link ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $link[1] ), esc_html( $link[0] ) );
	}
	echo '</ul>';
}
?>
</main><!-- #main -->

<footer class="site-footer on-dark" role="contentinfo">
	<div class="site-footer__inner container grid">

		<div class="site-footer__brand col-4">
			<span class="site-footer__wordmark">ArtisRaw<span class="site-header__reg">&reg;</span></span>
			<p class="site-footer__tagline"><?php esc_html_e( 'Olive wood manufacturer &amp; B2B supplier — handmade in Sfax, Tunisia since 2019.', 'artisraw' ); ?></p>
			<address class="site-footer__nap">
				Route Saltania, km 4.5<br>
				Sfax, Tunisia<br>
				ISO 9001:2015 · 30+ countries
			</address>
		</div>

		<nav class="site-footer__nav col-5" aria-label="<?php esc_attr_e( 'Footer', 'artisraw' ); ?>">
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer__links',
					'fallback_cb'    => 'artisraw_render_footer_nav',
					'depth'          => 1,
				) );
			} else {
				artisraw_render_footer_nav();
			}
			?>
		</nav>

		<div class="site-footer__contact col-3">
			<a class="footer__whatsapp" href="https://wa.me/21600000000" aria-label="<?php esc_attr_e( 'Chat with ArtisRaw on WhatsApp', 'artisraw' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm0 18.13h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.11.82.83-3.03-.2-.31a8.23 8.23 0 0 1-1.26-4.37c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.12-.15.16-.25.25-.42.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.23.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.1-.22-.16-.47-.28Z"/></svg>
				<span><?php esc_html_e( 'WhatsApp', 'artisraw' ); ?></span>
			</a>

			<div class="footer__lang" aria-label="<?php esc_attr_e( 'Language', 'artisraw' ); ?>">
				<?php
				if ( has_nav_menu( 'languages' ) ) {
					wp_nav_menu( array( 'theme_location' => 'languages', 'container' => false, 'menu_class' => 'footer__lang-list', 'depth' => 1 ) );
				} else {
					// Placeholder — real /fr/ links arrive in Phase 8.
					echo '<ul class="footer__lang-list" role="list"><li><a href="' . esc_url( home_url( '/' ) ) . '" aria-current="true">EN</a></li><li><span class="is-disabled" aria-disabled="true">FR</span></li></ul>';
				}
				?>
			</div>
		</div>

	</div>

	<div class="site-footer__legal">
		<div class="container">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> ArtisRaw&reg;. <?php esc_html_e( 'All rights reserved.', 'artisraw' ); ?>
				&middot; <?php esc_html_e( 'Lacey Act &amp; EUDR documentation available on request.', 'artisraw' ); ?></p>
		</div>
	</div>
</footer>

<?php
// Sticky mobile CTA bar (mobile-only; JS hides it while a form is in view).
if ( function_exists( 'artisraw_sticky_cta' ) ) {
	artisraw_sticky_cta();
}
wp_footer();
?>
</body>
</html>
