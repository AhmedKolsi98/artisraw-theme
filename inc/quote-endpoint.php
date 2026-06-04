<?php
/**
 * Quote form REST endpoint: POST /wp-json/artisraw/v1/quote (SPEC §4, §5.5).
 *
 * Validates Step 1 (or Step 2 enrichment), checks honeypot + optional Cloudflare
 * Turnstile, emails the team inbox and an autoresponder with the line-sheet +
 * compliance-pack links. Returns JSON for forms.js. GA4 events fire client-side.
 *
 * Config (define in wp-config.php or via constants):
 *   ARTISRAW_QUOTE_INBOX        — recipient (default: admin_email)
 *   ARTISRAW_TURNSTILE_SECRET   — enables server-side Turnstile verification
 *   ARTISRAW_LINESHEET_URL / ARTISRAW_COMPLIANCE_URL — asset links in autoresponder
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'artisraw_register_quote_route' );

function artisraw_register_quote_route() {
	register_rest_route( 'artisraw/v1', '/quote', array(
		'methods'             => 'POST',
		'callback'            => 'artisraw_handle_quote',
		'permission_callback' => '__return_true', // public form; spam-gated below.
	) );
}

/**
 * Verify Cloudflare Turnstile, when a secret is configured. No secret = dev mode (skip).
 */
function artisraw_verify_turnstile( $token ) {
	if ( ! defined( 'ARTISRAW_TURNSTILE_SECRET' ) || ! ARTISRAW_TURNSTILE_SECRET ) {
		return true; // not configured (dev/staging) — don't block.
	}
	if ( ! $token ) {
		return false;
	}
	$resp = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
		'timeout' => 8,
		'body'    => array(
			'secret'   => ARTISRAW_TURNSTILE_SECRET,
			'response' => $token,
			'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
		),
	) );
	if ( is_wp_error( $resp ) ) {
		return false;
	}
	$data = json_decode( wp_remote_retrieve_body( $resp ), true );
	return ! empty( $data['success'] );
}

function artisraw_handle_quote( WP_REST_Request $request ) {
	$p = $request->get_json_params();
	if ( empty( $p ) ) {
		$p = $request->get_body_params();
	}

	// 1) Honeypot — silently accept so bots don't learn, but do nothing.
	if ( ! empty( $p['website'] ) ) {
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}

	$step = isset( $p['step'] ) ? (int) $p['step'] : 1;

	// 2) Turnstile (step 1 only; step 2 reuses the established session).
	if ( 1 === $step && ! artisraw_verify_turnstile( $p['turnstile_token'] ?? '' ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'message' => __( 'Spam check failed. Please retry.', 'artisraw' ) ), 422 );
	}

	// 3) Validate + sanitize.
	$email   = isset( $p['email'] ) ? sanitize_email( $p['email'] ) : '';
	$company = isset( $p['company'] ) ? sanitize_text_field( $p['company'] ) : '';
	$country = isset( $p['country'] ) ? sanitize_text_field( $p['country'] ) : '';

	if ( 1 === $step ) {
		$errors = array();
		if ( ! is_email( $email ) ) {
			$errors['email'] = __( 'Please enter a valid work email.', 'artisraw' );
		}
		if ( '' === $company ) {
			$errors['company'] = __( 'Please enter your company name.', 'artisraw' );
		}
		if ( '' === $country ) {
			$errors['country'] = __( 'Please select a destination country.', 'artisraw' );
		}
		if ( $errors ) {
			return new WP_REST_Response( array( 'ok' => false, 'errors' => $errors ), 422 );
		}
	}

	$fields = array(
		'Email'        => $email,
		'Company'      => $company,
		'Country'      => $country,
		'Target date'  => sanitize_text_field( $p['target_date'] ?? '' ),
		'Monthly vol.' => sanitize_text_field( $p['monthly_volume'] ?? '' ),
		'Ship mode'    => sanitize_text_field( $p['ship_mode'] ?? '' ),
		'Engraving'    => ! empty( $p['engraving'] ) ? 'yes' : '',
		'Broker/DDP'   => ! empty( $p['broker_ddp'] ) ? 'yes' : '',
		'Documents'    => is_array( $p['documents'] ?? null ) ? implode( ', ', array_map( 'sanitize_text_field', $p['documents'] ) ) : '',
		'Source'       => sanitize_text_field( $p['source'] ?? '' ),
		'UTM source'   => sanitize_text_field( $p['utm_source'] ?? '' ),
		'UTM medium'   => sanitize_text_field( $p['utm_medium'] ?? '' ),
		'UTM campaign' => sanitize_text_field( $p['utm_campaign'] ?? '' ),
		'Page'         => esc_url_raw( $p['page_url'] ?? '' ),
	);

	// 4) Email the team inbox.
	$inbox   = defined( 'ARTISRAW_QUOTE_INBOX' ) ? ARTISRAW_QUOTE_INBOX : get_option( 'admin_email' );
	$subject = sprintf( '[Quote · step %d] %s — %s', $step, $company ?: 'Unknown', $country ?: '' );
	$lines   = array();
	foreach ( $fields as $k => $v ) {
		if ( '' !== $v ) {
			$lines[] = $k . ': ' . $v;
		}
	}
	$body    = implode( "\n", $lines );
	$headers = array();
	if ( is_email( $email ) ) {
		$headers[] = 'Reply-To: ' . $email;
	}
	$sent = wp_mail( $inbox, $subject, $body, $headers );

	// 5) Autoresponder with line-sheet + compliance-pack links (step 1 only).
	if ( 1 === $step && is_email( $email ) ) {
		$linesheet  = defined( 'ARTISRAW_LINESHEET_URL' ) ? ARTISRAW_LINESHEET_URL : home_url( '/references/' );
		$compliance = defined( 'ARTISRAW_COMPLIANCE_URL' ) ? ARTISRAW_COMPLIANCE_URL : home_url( '/references/' );
		$ar_subject = __( 'Your ArtisRaw line-sheet & compliance pack', 'artisraw' );
		$ar_body    = sprintf(
			/* translators: 1: company, 2: line-sheet URL, 3: compliance URL */
			__( "Hi,\n\nThanks for your interest in ArtisRaw olive wood — handmade in Sfax, Tunisia.\n\nHere are your documents:\n• Line-sheet: %2\$s\n• Compliance pack (Lacey Act / EUDR): %3\$s\n\nWe’ll follow up within one business day with pricing for %1\$s. Reply to this email any time.\n\n— The ArtisRaw team\nRoute Saltania, km 4.5, Sfax, Tunisia", 'artisraw' ),
			$company ?: 'your team',
			$linesheet,
			$compliance
		);
		wp_mail( $email, $ar_subject, $ar_body );
	}

	if ( ! $sent ) {
		// Lead is still useful; log but report soft success so the buyer isn't blocked.
		error_log( 'ArtisRaw quote: wp_mail failed for ' . $inbox );
	}

	return new WP_REST_Response( array( 'ok' => true, 'step' => $step ), 200 );
}
