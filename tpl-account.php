<?php
/**
 * Template Name: Client Area (account portal)
 *
 * Phase 9 — the B2B ordering portal. Routes by state:
 *  - logged out          → login + register
 *  - logged in, pending  → "awaiting approval"
 *  - logged in, approved → dashboard: order-list builder + history
 *
 * Engine + form processing live in inc/account.php. Always noindex.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$GLOBALS['artisraw_force_noindex'] = true; // never index the account area.

/** Emit the nonce + action hidden fields for a portal form. */
function artisraw_acct_fields( $action ) {
	wp_nonce_field( 'artisraw_acct_' . $action, 'artisraw_nonce' );
	echo '<input type="hidden" name="artisraw_acct" value="' . esc_attr( $action ) . '">';
}

/* Notice banner from the PRG redirect. */
$notices = array(
	'registered'     => array( 'ok', __( 'Account created — it’s pending approval. We’ll email you within one business day.', 'artisraw' ) ),
	'logged_in'      => array( 'ok', __( 'Signed in.', 'artisraw' ) ),
	'logged_out'     => array( 'ok', __( 'Signed out.', 'artisraw' ) ),
	'added'          => array( 'ok', __( 'Added to your order list.', 'artisraw' ) ),
	'updated'        => array( 'ok', __( 'Order list updated.', 'artisraw' ) ),
	'removed'        => array( 'ok', __( 'Item removed.', 'artisraw' ) ),
	'cleared'        => array( 'ok', __( 'Order list cleared.', 'artisraw' ) ),
	'submitted'      => array( 'ok', __( 'Quote requested — we’ll reply within one business day. Your list is saved in history.', 'artisraw' ) ),
	'reg_invalid'    => array( 'err', __( 'Please enter a valid email, your company, and a password of at least 8 characters.', 'artisraw' ) ),
	'email_taken'    => array( 'err', __( 'An account with that email already exists — try signing in.', 'artisraw' ) ),
	'reg_failed'     => array( 'err', __( 'We couldn’t create the account. Please try again.', 'artisraw' ) ),
	'login_failed'   => array( 'err', __( 'Wrong email or password.', 'artisraw' ) ),
	'login_required' => array( 'err', __( 'Please sign in first.', 'artisraw' ) ),
	'not_approved'   => array( 'err', __( 'Your account isn’t approved yet.', 'artisraw' ) ),
	'cart_empty'     => array( 'err', __( 'Your order list is empty.', 'artisraw' ) ),
	'cart_error'     => array( 'err', __( 'Something went wrong with that item.', 'artisraw' ) ),
	'rate_limited'   => array( 'err', __( 'Too many attempts — please wait a little and try again.', 'artisraw' ) ),
	'bad_nonce'      => array( 'err', __( 'Your session expired — please try again.', 'artisraw' ) ),
	'already_in'     => array( 'ok', __( 'You’re already signed in.', 'artisraw' ) ),
);
$notice_code = isset( $_GET['notice'] ) ? sanitize_key( $_GET['notice'] ) : '';
$notice      = $notices[ $notice_code ] ?? null;

get_header();
artisraw_breadcrumbs();
?>
<div class="container section hub-section account">
	<header class="page-head"><h1><?php the_title(); ?></h1></header>

	<?php if ( $notice ) : ?>
		<p class="account-notice account-notice--<?php echo esc_attr( $notice[0] ); ?>" role="status"><?php echo esc_html( $notice[1] ); ?></p>
	<?php endif; ?>

	<?php if ( ! is_user_logged_in() ) : /* ---------------- LOGGED OUT ---------------- */ ?>

		<p class="lead" style="max-width:60ch"><?php esc_html_e( 'A private space for approved wholesale buyers: build order lists from the catalogue, request quotes in one click and reorder faster. New accounts are reviewed before activation.', 'artisraw' ); ?></p>

		<div class="account-grid">
			<section class="account-card">
				<h2><?php esc_html_e( 'Sign in', 'artisraw' ); ?></h2>
				<form method="post" action="<?php echo esc_url( artisraw_account_url() ); ?>" class="account-form">
					<?php artisraw_acct_fields( 'login' ); ?>
					<div class="field"><label for="login-email"><?php esc_html_e( 'Work email', 'artisraw' ); ?></label>
						<input type="email" id="login-email" name="email" autocomplete="email" required></div>
					<div class="field"><label for="login-pass"><?php esc_html_e( 'Password', 'artisraw' ); ?></label>
						<input type="password" id="login-pass" name="password" autocomplete="current-password" required></div>
					<label class="check"><input type="checkbox" name="remember" value="1"> <?php esc_html_e( 'Keep me signed in', 'artisraw' ); ?></label>
					<p><button type="submit" class="btn btn--primary"><?php esc_html_e( 'Sign in', 'artisraw' ); ?></button>
						<a class="account-forgot" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot password?', 'artisraw' ); ?></a></p>
				</form>
			</section>

			<section class="account-card">
				<h2><?php esc_html_e( 'Create a B2B account', 'artisraw' ); ?></h2>
				<form method="post" action="<?php echo esc_url( artisraw_account_url() ); ?>" class="account-form">
					<?php artisraw_acct_fields( 'register' ); ?>
					<div class="field"><label for="reg-company"><?php esc_html_e( 'Company', 'artisraw' ); ?></label>
						<input type="text" id="reg-company" name="company" autocomplete="organization" required></div>
					<div class="field"><label for="reg-email"><?php esc_html_e( 'Work email', 'artisraw' ); ?></label>
						<input type="email" id="reg-email" name="email" autocomplete="email" required></div>
					<div class="field"><label for="reg-country"><?php esc_html_e( 'Destination country', 'artisraw' ); ?></label>
						<input type="text" id="reg-country" name="country" autocomplete="country-name"></div>
					<div class="field"><label for="reg-pass"><?php esc_html_e( 'Password (8+ characters)', 'artisraw' ); ?></label>
						<input type="password" id="reg-pass" name="password" autocomplete="new-password" minlength="8" required></div>
					<p><button type="submit" class="btn btn--primary"><?php esc_html_e( 'Create account', 'artisraw' ); ?></button></p>
					<p class="account-fine"><?php
						printf(
							wp_kses_post( __( 'We review every application. Your details are used only to set up and service your account — %s.', 'artisraw' ) ),
							'<a href="' . esc_url( artisraw_localized_url( '/privacy/' ) ) . '">' . esc_html__( 'privacy', 'artisraw' ) . '</a>'
						);
					?></p>
				</form>
			</section>
		</div>

	<?php elseif ( ! artisraw_user_approved() ) : /* ---------------- PENDING ---------------- */ ?>

		<?php $u = wp_get_current_user(); ?>
		<div class="account-card account-card--wide">
			<p class="eyebrow"><?php esc_html_e( 'Account status', 'artisraw' ); ?></p>
			<h2><?php esc_html_e( 'Your account is pending approval', 'artisraw' ); ?></h2>
			<p class="lead" style="max-width:60ch"><?php
				printf(
					/* translators: %s: company/display name */
					esc_html__( 'Thanks, %s. We review every B2B application — usually within one business day. You’ll get an email the moment your account is active, then you can build order lists and request quotes here.', 'artisraw' ),
					esc_html( $u->display_name )
				);
			?></p>
			<p><a class="btn btn--secondary" href="<?php echo esc_url( artisraw_localized_url( '/wholesale/' ) ); ?>"><?php esc_html_e( 'Browse the catalogue meanwhile', 'artisraw' ); ?></a></p>
			<form method="post" action="<?php echo esc_url( artisraw_account_url() ); ?>" class="account-logout">
				<?php artisraw_acct_fields( 'logout' ); ?>
				<button type="submit" class="btn btn--tertiary"><?php esc_html_e( 'Sign out', 'artisraw' ); ?></button>
			</form>
		</div>

	<?php else : /* ---------------- APPROVED DASHBOARD ---------------- */ ?>

		<?php
		$u        = wp_get_current_user();
		$cart     = artisraw_get_cart();
		$orders   = artisraw_get_orders();
		$prod     = get_user_meta( $u->ID, '_artisraw_production_status', true );
		$sku_ids  = artisraw_get_ready_skus( 50 );
		$sku_map  = array();
		foreach ( $sku_ids as $id ) {
			$sku_map[ $id ] = artisraw_sku_to_array( $id );
		}
		?>

		<div class="account-head">
			<p class="lead" style="margin:0"><?php printf( esc_html__( 'Welcome back, %s.', 'artisraw' ), esc_html( $u->display_name ) ); ?></p>
			<form method="post" action="<?php echo esc_url( artisraw_account_url() ); ?>" class="account-logout">
				<?php artisraw_acct_fields( 'logout' ); ?>
				<button type="submit" class="btn btn--tertiary"><?php esc_html_e( 'Sign out', 'artisraw' ); ?></button>
			</form>
		</div>

		<?php if ( $prod ) : ?>
			<p class="account-status"><strong><?php esc_html_e( 'Production status:', 'artisraw' ); ?></strong> <?php echo esc_html( $prod ); ?></p>
		<?php endif; ?>

		<!-- Order-list builder -->
		<section class="account-section">
			<h2><?php esc_html_e( 'Your order list', 'artisraw' ); ?></h2>
			<?php if ( empty( $cart ) ) : ?>
				<p><?php esc_html_e( 'Your order list is empty. Add SKUs from the catalogue below.', 'artisraw' ); ?></p>
			<?php else : ?>
				<div class="data-table">
					<table>
						<thead><tr><th scope="col"><?php esc_html_e( 'Product', 'artisraw' ); ?></th><th scope="col"><?php esc_html_e( 'SKU', 'artisraw' ); ?></th><th scope="col"><?php esc_html_e( 'MOQ', 'artisraw' ); ?></th><th scope="col"><?php esc_html_e( 'Quantity', 'artisraw' ); ?></th><th scope="col"></th></tr></thead>
						<tbody>
						<?php foreach ( $cart as $sku_id => $qty ) :
							$s = $sku_map[ $sku_id ] ?? artisraw_sku_to_array( $sku_id ); ?>
							<tr>
								<th scope="row" data-label="<?php esc_attr_e( 'Product', 'artisraw' ); ?>"><?php echo esc_html( $s['name'] ); ?></th>
								<td data-label="<?php esc_attr_e( 'SKU', 'artisraw' ); ?>"><?php echo esc_html( $s['sku'] ); ?></td>
								<td data-label="<?php esc_attr_e( 'MOQ', 'artisraw' ); ?>"><?php echo esc_html( $s['moq'] ); ?></td>
								<td data-label="<?php esc_attr_e( 'Quantity', 'artisraw' ); ?>">
									<form method="post" action="<?php echo esc_url( artisraw_account_url() ); ?>" class="qty-form">
										<?php artisraw_acct_fields( 'update' ); ?>
										<input type="hidden" name="sku_id" value="<?php echo esc_attr( $sku_id ); ?>">
										<input type="number" name="qty" value="<?php echo esc_attr( $qty ); ?>" min="1" inputmode="numeric" aria-label="<?php esc_attr_e( 'Quantity', 'artisraw' ); ?>">
										<button type="submit" class="btn btn--tertiary"><?php esc_html_e( 'Update', 'artisraw' ); ?></button>
									</form>
								</td>
								<td>
									<form method="post" action="<?php echo esc_url( artisraw_account_url() ); ?>">
										<?php artisraw_acct_fields( 'remove' ); ?>
										<input type="hidden" name="sku_id" value="<?php echo esc_attr( $sku_id ); ?>">
										<button type="submit" class="btn btn--tertiary account-remove"><?php esc_html_e( 'Remove', 'artisraw' ); ?></button>
									</form>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<form method="post" action="<?php echo esc_url( artisraw_account_url() ); ?>" class="account-submit">
					<?php artisraw_acct_fields( 'submit' ); ?>
					<div class="field"><label for="order-note"><?php esc_html_e( 'Note for our team (optional)', 'artisraw' ); ?></label>
						<textarea id="order-note" name="note" rows="3" placeholder="<?php esc_attr_e( 'Private-label needs, target date, packaging…', 'artisraw' ); ?>"></textarea></div>
					<p>
						<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Request quote for this list', 'artisraw' ); ?></button>
					</p>
				</form>
				<form method="post" action="<?php echo esc_url( artisraw_account_url() ); ?>">
					<?php artisraw_acct_fields( 'clear' ); ?>
					<button type="submit" class="btn btn--tertiary"><?php esc_html_e( 'Clear list', 'artisraw' ); ?></button>
				</form>
			<?php endif; ?>
		</section>

		<!-- Product picker -->
		<section class="account-section">
			<h2><?php esc_html_e( 'Add from the catalogue', 'artisraw' ); ?></h2>
			<div class="grid">
				<?php foreach ( $sku_map as $sku_id => $s ) : ?>
					<div class="col-4">
						<div class="account-pick">
							<h3 class="account-pick__name"><?php echo esc_html( $s['name'] ); ?></h3>
							<p class="account-pick__meta eyebrow"><?php echo esc_html( $s['sku'] ); ?> · <?php esc_html_e( 'MOQ', 'artisraw' ); ?> <?php echo esc_html( $s['moq'] ); ?></p>
							<form method="post" action="<?php echo esc_url( artisraw_account_url() ); ?>" class="qty-form">
								<?php artisraw_acct_fields( 'add' ); ?>
								<input type="hidden" name="sku_id" value="<?php echo esc_attr( $sku_id ); ?>">
								<input type="number" name="qty" value="<?php echo esc_attr( max( 1, (int) $s['moq'] ) ); ?>" min="1" inputmode="numeric" aria-label="<?php echo esc_attr( $s['name'] . ' ' . __( 'quantity', 'artisraw' ) ); ?>">
								<button type="submit" class="btn btn--secondary"><?php echo isset( $cart[ $sku_id ] ) ? esc_html__( 'In list — add more', 'artisraw' ) : esc_html__( 'Add', 'artisraw' ); ?></button>
							</form>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- Order history -->
		<section class="account-section">
			<h2><?php esc_html_e( 'Order history', 'artisraw' ); ?></h2>
			<?php if ( empty( $orders ) ) : ?>
				<p><?php esc_html_e( 'No quote requests yet. Submitted order lists appear here so you can reorder faster.', 'artisraw' ); ?></p>
			<?php else : ?>
				<ul class="account-history" role="list">
					<?php foreach ( $orders as $o ) : ?>
						<li class="account-history__item">
							<p class="account-history__head"><strong><?php echo esc_html( mysql2date( get_option( 'date_format' ), $o['date'] ) ); ?></strong> · <span class="account-history__status"><?php echo esc_html( $o['status'] ); ?></span> · <?php echo esc_html( sprintf( _n( '%d item', '%d items', count( $o['items'] ), 'artisraw' ), count( $o['items'] ) ) ); ?></p>
							<p class="account-history__items"><?php
								$names = array_map( function ( $it ) {
									return $it['name'] . ' ×' . $it['qty'];
								}, $o['items'] );
								echo esc_html( implode( ', ', $names ) );
							?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>

	<?php endif; ?>
</div>

<?php
get_footer();
