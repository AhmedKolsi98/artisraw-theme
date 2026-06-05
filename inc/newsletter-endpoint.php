<?php
/**
 * Newsletter signup REST endpoint: POST /wp-json/artisraw/v1/newsletter (Phase 5).
 *
 * Mirrors the quote-endpoint pattern: honeypot + email validation, notifies the
 * team inbox and sends a confirmation autoresponder. Stores the address in a
 * theme option so signups are not lost before a real ESP (Mailchimp/Brevo) is
 * wired in Phase 6. Returns JSON for components.js.
 *
 * Config:
 *   ARTISRAW_NEWSLETTER_INBOX — recipient (default: ARTISRAW_QUOTE_INBOX or admin_email)
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'artisraw_register_newsletter_route' );

function artisraw_register_newsletter_route() {
	register_rest_route( 'artisraw/v1', '/newsletter', array(
		'methods'             => 'POST',
		'callback'            => 'artisraw_handle_newsletter',
		'permission_callback' => '__return_true', // public form; spam-gated below.
	) );
}

function artisraw_handle_newsletter( WP_REST_Request $request ) {
	$p = $request->get_json_params();
	if ( empty( $p ) ) {
		$p = $request->get_body_params();
	}

	// Honeypot — silently accept so bots don't learn.
	if ( ! empty( $p['website'] ) ) {
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}

	$email = isset( $p['email'] ) ? sanitize_email( $p['email'] ) : '';
	if ( ! is_email( $email ) ) {
		return new WP_REST_Response( array(
			'ok'      => false,
			'message' => __( 'Please enter a valid email address.', 'artisraw' ),
		), 422 );
	}

	// Store the address (dedup) so signups survive until an ESP is connected.
	$list = get_option( 'artisraw_newsletter_list', array() );
	if ( ! is_array( $list ) ) {
		$list = array();
	}
	if ( ! in_array( $email, $list, true ) ) {
		$list[] = $email;
		update_option( 'artisraw_newsletter_list', $list, false );
	}

	// Notify the team.
	$inbox = defined( 'ARTISRAW_NEWSLETTER_INBOX' ) ? ARTISRAW_NEWSLETTER_INBOX
		: ( defined( 'ARTISRAW_QUOTE_INBOX' ) ? ARTISRAW_QUOTE_INBOX : get_option( 'admin_email' ) );
	$source = isset( $p['source'] ) ? sanitize_text_field( $p['source'] ) : '';
	wp_mail(
		$inbox,
		'[Newsletter] ' . $email,
		"New B2B newsletter signup\n\nEmail: {$email}\nSource: {$source}\nTotal subscribers: " . count( $list )
	);

	// Confirmation autoresponder.
	wp_mail(
		$email,
		__( 'You’re on the ArtisRaw list', 'artisraw' ),
		__( "Thanks for subscribing to ArtisRaw B2B updates.\n\nYou’ll receive product launches, catalogue news and wholesale updates — no more than monthly. Reply any time to reach our team.\n\n— The ArtisRaw team\nRoute Saltania, km 4.5, Sfax, Tunisia", 'artisraw' )
	);

	return new WP_REST_Response( array(
		'ok'      => true,
		'message' => __( 'You’re subscribed — check your inbox to confirm.', 'artisraw' ),
	), 200 );
}
