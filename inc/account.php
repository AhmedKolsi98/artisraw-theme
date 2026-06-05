<?php
/**
 * Phase 9 — Client Area: B2B ordering portal.
 *
 * WordPress users back the accounts. New registrations are PENDING until an
 * admin approves them (a checkbox on the user-edit screen). Approved buyers get
 * an order-list builder over the SKU catalogue and can submit it as a quote,
 * which routes through the same email pipeline as the public quote form and is
 * saved to their order history with a production status.
 *
 * All portal forms are nonce-protected; register/login are rate-limited.
 * The portal page is noindex + auth-gated; nothing private is server-rendered
 * for logged-out visitors.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---- Helpers ---------------------------------------------------------- */

function artisraw_account_url() {
	return home_url( '/wholesale-account/' );
}

function artisraw_user_approved( $user_id = null ) {
	$user_id = $user_id ?: get_current_user_id();
	return $user_id && '1' === get_user_meta( $user_id, '_artisraw_approved', true );
}

/** Read the buyer's saved order list: [ sku_id => qty ]. */
function artisraw_get_cart( $user_id = null ) {
	$user_id = $user_id ?: get_current_user_id();
	$cart    = get_user_meta( $user_id, '_artisraw_cart', true );
	return is_array( $cart ) ? $cart : array();
}

function artisraw_save_cart( $cart, $user_id = null ) {
	$user_id = $user_id ?: get_current_user_id();
	update_user_meta( $user_id, '_artisraw_cart', $cart );
}

function artisraw_get_orders( $user_id = null ) {
	$user_id = $user_id ?: get_current_user_id();
	$orders  = get_user_meta( $user_id, '_artisraw_orders', true );
	return is_array( $orders ) ? $orders : array();
}

/* ---- Rate limiting (simple per-IP transient) -------------------------- */

function artisraw_rl_ip() {
	$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
	return preg_replace( '/[^0-9a-f:.]/i', '', $ip );
}

function artisraw_rl_hit( $bucket, $max = 12, $window = HOUR_IN_SECONDS ) {
	$key   = 'artisraw_rl_' . $bucket . '_' . md5( artisraw_rl_ip() );
	$count = (int) get_transient( $key );
	if ( $count >= $max ) {
		return false;
	}
	set_transient( $key, $count + 1, $window );
	return true;
}

/* ---- Form processing (PRG: process → redirect with ?notice=) ---------- */

function artisraw_handle_account_forms() {
	if ( empty( $_POST['artisraw_acct'] ) ) {
		return;
	}
	$action = sanitize_key( $_POST['artisraw_acct'] );
	$nonce  = $_POST['artisraw_nonce'] ?? '';
	if ( ! wp_verify_nonce( $nonce, 'artisraw_acct_' . $action ) ) {
		artisraw_redirect_notice( 'bad_nonce' );
	}

	switch ( $action ) {
		case 'register':
			artisraw_process_register();
			break;
		case 'login':
			artisraw_process_login();
			break;
		case 'logout':
			wp_logout();
			artisraw_redirect_notice( 'logged_out' );
			break;
		case 'add':
		case 'update':
		case 'remove':
		case 'clear':
		case 'submit':
			if ( ! is_user_logged_in() ) {
				artisraw_redirect_notice( 'login_required' );
			}
			artisraw_process_cart_action( $action );
			break;
	}
}
add_action( 'init', 'artisraw_handle_account_forms', 5 );

function artisraw_redirect_notice( $code, $extra = array() ) {
	$args = array_merge( array( 'notice' => $code ), $extra );
	wp_safe_redirect( add_query_arg( $args, artisraw_account_url() ) );
	exit;
}

function artisraw_process_register() {
	if ( is_user_logged_in() ) {
		artisraw_redirect_notice( 'already_in' );
	}
	if ( ! artisraw_rl_hit( 'reg' ) ) {
		artisraw_redirect_notice( 'rate_limited' );
	}
	$email   = sanitize_email( $_POST['email'] ?? '' );
	$company = sanitize_text_field( $_POST['company'] ?? '' );
	$country = sanitize_text_field( $_POST['country'] ?? '' );
	$pass    = (string) ( $_POST['password'] ?? '' );

	if ( ! is_email( $email ) || '' === $company || strlen( $pass ) < 8 ) {
		artisraw_redirect_notice( 'reg_invalid' );
	}
	if ( email_exists( $email ) ) {
		artisraw_redirect_notice( 'email_taken' );
	}
	$uid = wp_insert_user( array(
		'user_login'   => $email,
		'user_email'   => $email,
		'user_pass'    => $pass,
		'display_name' => $company,
		'role'         => 'subscriber',
	) );
	if ( is_wp_error( $uid ) ) {
		artisraw_redirect_notice( 'reg_failed' );
	}
	update_user_meta( $uid, '_artisraw_approved', '0' );
	update_user_meta( $uid, '_artisraw_company', $company );
	update_user_meta( $uid, '_artisraw_country', $country );

	// Notify the team to review/approve.
	$inbox = defined( 'ARTISRAW_QUOTE_INBOX' ) ? ARTISRAW_QUOTE_INBOX : get_option( 'admin_email' );
	wp_mail(
		$inbox,
		'[Account] New wholesale registration — ' . $company,
		"A new buyer registered and is pending approval:\n\nCompany: {$company}\nEmail: {$email}\nCountry: {$country}\n\nApprove in WP Admin → Users → edit user → \"Approved wholesale buyer\"."
	);
	wp_mail(
		$email,
		__( 'Your ArtisRaw wholesale account — pending approval', 'artisraw' ),
		__( "Thanks for registering for an ArtisRaw wholesale account.\n\nWe review every B2B application. Once approved (usually within one business day) you'll be able to build order lists and request quotes directly from your account.\n\n— The ArtisRaw team", 'artisraw' )
	);

	wp_set_auth_cookie( $uid, true );
	wp_set_current_user( $uid );
	artisraw_redirect_notice( 'registered' );
}

function artisraw_process_login() {
	if ( ! artisraw_rl_hit( 'login' ) ) {
		artisraw_redirect_notice( 'rate_limited' );
	}
	$login = sanitize_text_field( $_POST['email'] ?? '' );
	$pass  = (string) ( $_POST['password'] ?? '' );
	// Allow login by email.
	if ( is_email( $login ) ) {
		$u = get_user_by( 'email', $login );
		if ( $u ) {
			$login = $u->user_login;
		}
	}
	$user = wp_signon( array(
		'user_login'    => $login,
		'user_password' => $pass,
		'remember'      => ! empty( $_POST['remember'] ),
	), is_ssl() );
	if ( is_wp_error( $user ) ) {
		artisraw_redirect_notice( 'login_failed' );
	}
	artisraw_redirect_notice( 'logged_in' );
}

function artisraw_process_cart_action( $action ) {
	$cart = artisraw_get_cart();

	if ( 'add' === $action || 'update' === $action ) {
		$sku_id = absint( $_POST['sku_id'] ?? 0 );
		$qty    = max( 0, absint( $_POST['qty'] ?? 0 ) );
		if ( $sku_id && get_post_type( $sku_id ) === 'sku' ) {
			if ( $qty > 0 ) {
				$cart[ $sku_id ] = $qty;
			} else {
				unset( $cart[ $sku_id ] );
			}
			artisraw_save_cart( $cart );
			artisraw_redirect_notice( 'add' === $action ? 'added' : 'updated', array( '_' => $sku_id ) );
		}
		artisraw_redirect_notice( 'cart_error' );
	}

	if ( 'remove' === $action ) {
		$sku_id = absint( $_POST['sku_id'] ?? 0 );
		unset( $cart[ $sku_id ] );
		artisraw_save_cart( $cart );
		artisraw_redirect_notice( 'removed' );
	}

	if ( 'clear' === $action ) {
		artisraw_save_cart( array() );
		artisraw_redirect_notice( 'cleared' );
	}

	if ( 'submit' === $action ) {
		if ( ! artisraw_user_approved() ) {
			artisraw_redirect_notice( 'not_approved' );
		}
		if ( empty( $cart ) ) {
			artisraw_redirect_notice( 'cart_empty' );
		}
		artisraw_submit_cart_as_quote( $cart );
	}
}

function artisraw_submit_cart_as_quote( $cart ) {
	$uid     = get_current_user_id();
	$user    = wp_get_current_user();
	$company = get_user_meta( $uid, '_artisraw_company', true ) ?: $user->display_name;
	$country = get_user_meta( $uid, '_artisraw_country', true );
	$note    = sanitize_textarea_field( $_POST['note'] ?? '' );

	$items = array();
	$lines = array();
	foreach ( $cart as $sku_id => $qty ) {
		$name = get_the_title( $sku_id );
		$code = artisraw_get( 'sku_code', $sku_id );
		$items[] = array( 'name' => $name, 'sku' => $code, 'qty' => (int) $qty );
		$lines[] = sprintf( '- %s (%s) × %d', $name, $code, $qty );
	}

	// Email the team + autoresponder (same inbox as the public quote form).
	$inbox   = defined( 'ARTISRAW_QUOTE_INBOX' ) ? ARTISRAW_QUOTE_INBOX : get_option( 'admin_email' );
	$body    = "Order-list quote from the wholesale portal\n\n"
		. "Company: {$company}\nEmail: {$user->user_email}\nCountry: {$country}\n\n"
		. "Items:\n" . implode( "\n", $lines )
		. ( $note ? "\n\nNote:\n{$note}" : '' );
	wp_mail( $inbox, '[Portal quote] ' . $company . ' — ' . count( $items ) . ' items', $body, array( 'Reply-To: ' . $user->user_email ) );
	wp_mail(
		$user->user_email,
		__( 'Your ArtisRaw order list — quote requested', 'artisraw' ),
		__( "Thanks — we've received your order list and will reply within one business day with pricing, MOQ and lead times.\n\n", 'artisraw' ) . implode( "\n", $lines ) . "\n\n— The ArtisRaw team"
	);

	// Save to history with a production status, then clear the cart.
	$orders = artisraw_get_orders();
	array_unshift( $orders, array(
		'date'   => current_time( 'mysql' ),
		'items'  => $items,
		'note'   => $note,
		'status' => 'Quote requested',
	) );
	update_user_meta( $uid, '_artisraw_orders', array_slice( $orders, 0, 30 ) );
	artisraw_save_cart( array() );

	artisraw_redirect_notice( 'submitted' );
}

/* ---- Admin: approval + production-status fields on the user editor ----- */

function artisraw_user_admin_fields( $user ) {
	if ( ! current_user_can( 'edit_users' ) ) {
		return;
	}
	$approved = '1' === get_user_meta( $user->ID, '_artisraw_approved', true );
	$status   = get_user_meta( $user->ID, '_artisraw_production_status', true );
	$company  = get_user_meta( $user->ID, '_artisraw_company', true );
	$country  = get_user_meta( $user->ID, '_artisraw_country', true );
	wp_nonce_field( 'artisraw_user_fields', 'artisraw_user_nonce' );
	?>
	<h2>ArtisRaw wholesale</h2>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="artisraw_approved">Approved wholesale buyer</label></th>
			<td>
				<label><input type="checkbox" name="artisraw_approved" id="artisraw_approved" value="1" <?php checked( $approved ); ?>> Allow this buyer to build order lists and request quotes</label>
				<?php if ( $company ) : ?><p class="description">Company: <?php echo esc_html( $company ); ?><?php echo $country ? ' · ' . esc_html( $country ) : ''; ?></p><?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="artisraw_production_status">Production status note</label></th>
			<td>
				<input type="text" class="regular-text" name="artisraw_production_status" id="artisraw_production_status" value="<?php echo esc_attr( $status ); ?>">
				<p class="description">Shown on the buyer's dashboard (e.g. "Order #A-1042 in production — ships week of …").</p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'edit_user_profile', 'artisraw_user_admin_fields' );
add_action( 'show_user_profile', 'artisraw_user_admin_fields' );

function artisraw_save_user_admin_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	if ( ! isset( $_POST['artisraw_user_nonce'] ) || ! wp_verify_nonce( $_POST['artisraw_user_nonce'], 'artisraw_user_fields' ) ) {
		return;
	}
	update_user_meta( $user_id, '_artisraw_approved', empty( $_POST['artisraw_approved'] ) ? '0' : '1' );
	update_user_meta( $user_id, '_artisraw_production_status', sanitize_text_field( $_POST['artisraw_production_status'] ?? '' ) );

	// Notify the buyer the first time they're approved.
	if ( ! empty( $_POST['artisraw_approved'] ) && ! get_user_meta( $user_id, '_artisraw_approved_notified', true ) ) {
		$u = get_userdata( $user_id );
		if ( $u ) {
			wp_mail(
				$u->user_email,
				__( 'Your ArtisRaw wholesale account is approved', 'artisraw' ),
				__( "Good news — your wholesale account is approved.\n\nYou can now sign in, build order lists from the catalogue and request quotes directly: ", 'artisraw' ) . artisraw_account_url()
			);
			update_user_meta( $user_id, '_artisraw_approved_notified', '1' );
		}
	}
}
add_action( 'edit_user_profile_update', 'artisraw_save_user_admin_fields' );
add_action( 'personal_options_update', 'artisraw_save_user_admin_fields' );
