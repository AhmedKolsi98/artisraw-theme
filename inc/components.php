<?php
/**
 * Reusable UI components (SPEC §4, §5.4, §5.5).
 *
 * Each component is a render function that takes a plain array, so it works with
 * sample data on /styleguide/ now and ACF data in later phases — no coupling.
 * All functions echo escaped HTML and return nothing.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
 * Quick-answer box (SPEC §4) — 40–60-word answer, first block under H1.
 * ====================================================================== */
function artisraw_quick_answer( $text, $label = null ) {
	if ( ! $text ) {
		return;
	}
	$label = $label ?: __( 'Quick answer', 'artisraw' );
	echo '<div class="quick-answer">';
	echo '<p class="quick-answer__label eyebrow">' . esc_html( $label ) . '</p>';
	echo '<p class="quick-answer__text">' . esc_html( $text ) . '</p>';
	echo '</div>';
}

/* =========================================================================
 * SKU spec card (SPEC §4) — <article> + <dl>. $sku keys:
 * name, image(url/alt/w/h), dimensions, unit_weight, case_pack,
 * carton, moq, lead_time, exw_tier, sku.
 * ====================================================================== */
function artisraw_sku_card( array $sku ) {
	$specs = array(
		__( 'Dimensions', 'artisraw' )  => $sku['dimensions'] ?? '',
		__( 'Unit weight', 'artisraw' ) => $sku['unit_weight'] ?? '',
		__( 'Case pack', 'artisraw' )   => $sku['case_pack'] ?? '',
		__( 'Carton L×W×H', 'artisraw' ) => $sku['carton'] ?? '',
		__( 'MOQ', 'artisraw' )         => $sku['moq'] ?? '',
		__( 'Lead time', 'artisraw' )   => $sku['lead_time'] ?? '',
		__( 'Indicative EXW', 'artisraw' ) => $sku['exw_tier'] ?? '',
	);
	echo '<article class="sku-card">';
	if ( ! empty( $sku['image']['url'] ) ) {
		printf(
			'<img class="sku-card__img" src="%s" alt="%s" width="%d" height="%d" loading="lazy" decoding="async">',
			esc_url( $sku['image']['url'] ),
			esc_attr( $sku['image']['alt'] ?? $sku['name'] ),
			(int) ( $sku['image']['w'] ?? 600 ),
			(int) ( $sku['image']['h'] ?? 600 )
		);
	} else {
		echo '<div class="sku-card__img sku-card__img--placeholder" aria-hidden="true"></div>';
	}
	echo '<div class="sku-card__body">';
	echo '<h3 class="sku-card__name">' . esc_html( $sku['name'] ?? '' ) . '</h3>';
	if ( ! empty( $sku['sku'] ) ) {
		echo '<p class="sku-card__ref eyebrow">' . esc_html( $sku['sku'] ) . '</p>';
	}
	echo '<dl class="sku-card__specs">';
	foreach ( $specs as $label => $value ) {
		if ( '' === $value ) {
			continue;
		}
		echo '<div class="sku-card__row"><dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $value ) . '</dd></div>';
	}
	echo '</dl>';
	echo '</div></article>';
}

function artisraw_sku_grid( array $skus ) {
	echo '<div class="sku-grid grid">';
	foreach ( $skus as $sku ) {
		echo '<div class="col-4">';
		artisraw_sku_card( $sku );
		echo '</div>';
	}
	echo '</div>';
}

/* =========================================================================
 * Data table (SPEC §4) — semantic <table> + <caption> + <th scope>.
 * $head = [labels]; $rows = [[cells]]. Stacks below 768px via data-label.
 * ====================================================================== */
function artisraw_data_table( $caption, array $head, array $rows, $preset = 'default' ) {
	echo '<div class="data-table data-table--' . esc_attr( $preset ) . '">';
	echo '<table>';
	if ( $caption ) {
		echo '<caption>' . esc_html( $caption ) . '</caption>';
	}
	echo '<thead><tr>';
	foreach ( $head as $th ) {
		echo '<th scope="col">' . esc_html( $th ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( $rows as $row ) {
		echo '<tr>';
		foreach ( $row as $i => $cell ) {
			$label = $head[ $i ] ?? '';
			if ( 0 === $i ) {
				echo '<th scope="row" data-label="' . esc_attr( $label ) . '">' . esc_html( $cell ) . '</th>';
			} else {
				echo '<td data-label="' . esc_attr( $label ) . '">' . esc_html( $cell ) . '</td>';
			}
		}
		echo '</tr>';
	}
	echo '</tbody></table></div>';
}

/* =========================================================================
 * Trust strip / badge row (SPEC §4). $chips: [ [label, href, ?title] ].
 * Inline SVG check icon, aria-hidden.
 * ====================================================================== */
function artisraw_trust_strip( array $chips ) {
	$icon = '<svg class="trust-chip__icon" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3.5 8.5l3 3 6-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	echo '<ul class="trust-strip" role="list">';
	foreach ( $chips as $chip ) {
		$label = $chip[0] ?? '';
		$href  = $chip[1] ?? '';
		echo '<li class="trust-chip">';
		if ( $href ) {
			printf( '<a class="trust-chip__link" href="%s">%s<span>%s</span></a>', esc_url( $href ), $icon, esc_html( $label ) );
		} else {
			printf( '<span class="trust-chip__link">%s<span>%s</span></span>', $icon, esc_html( $label ) );
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Reference-buyer logo band with type-only fallback (SPEC §4, §6.9).
 * $logos: [ [name, ?image_url] ]. Names render as text when no image/permission.
 */
function artisraw_logo_band( array $logos, $heading = '' ) {
	echo '<div class="logo-band">';
	if ( $heading ) {
		echo '<p class="logo-band__heading eyebrow">' . esc_html( $heading ) . '</p>';
	}
	echo '<ul class="logo-band__list" role="list">';
	foreach ( $logos as $logo ) {
		$name = $logo[0] ?? '';
		echo '<li class="logo-band__item">';
		if ( ! empty( $logo[1] ) ) {
			printf( '<img src="%s" alt="%s" loading="lazy" height="32">', esc_url( $logo[1] ), esc_attr( $name ) );
		} else {
			echo '<span class="logo-band__name">' . esc_html( $name ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ul></div>';
}

/* =========================================================================
 * FAQ accordion (SPEC §4) — WAI-ARIA, multiple-open, deep-linkable.
 * $items: [ [question, answer] ]. $emit_schema mirrors to FAQPage JSON-LD
 * from the SAME data (single source of truth).
 * ====================================================================== */
function artisraw_faq_accordion( array $items, $emit_schema = false, $id_prefix = 'faq' ) {
	echo '<div class="accordion" data-accordion>';
	foreach ( $items as $i => $item ) {
		$q = $item[0] ?? '';
		$a = $item[1] ?? '';
		$slug    = sanitize_title( $q );
		$btn_id  = $id_prefix . '-h-' . $i;
		$pan_id  = $id_prefix . '-p-' . $i;
		echo '<div class="accordion__item" id="' . esc_attr( $slug ) . '">';
		printf(
			'<h3 class="accordion__heading"><button type="button" class="accordion__trigger" id="%s" aria-expanded="false" aria-controls="%s"><span class="accordion__q">%s</span><svg class="accordion__chevron" width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 5l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></button></h3>',
			esc_attr( $btn_id ),
			esc_attr( $pan_id ),
			esc_html( $q )
		);
		printf(
			'<div class="accordion__panel" id="%s" role="region" aria-labelledby="%s" hidden><div class="accordion__answer"><p>%s</p></div></div>',
			esc_attr( $pan_id ),
			esc_attr( $btn_id ),
			esc_html( $a )
		);
		echo '</div>';
	}
	echo '</div>';

	if ( $emit_schema && function_exists( 'artisraw_jsonld' ) ) {
		$main = array();
		foreach ( $items as $item ) {
			$main[] = array(
				'@type'          => 'Question',
				'name'           => $item[0] ?? '',
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $item[1] ?? '' ),
			);
		}
		artisraw_jsonld( array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $main,
		) );
	}
}

/* =========================================================================
 * Stat counter band (SPEC §4) — numbers in HTML; count-up gated by JS +
 * prefers-reduced-motion. $stats: [ [value, label, ?suffix, ?target] ].
 * ====================================================================== */
function artisraw_stat_band( array $stats, $dark = true ) {
	$cls = 'stat-band section' . ( $dark ? ' section--dark on-dark' : '' );
	echo '<div class="' . esc_attr( $cls ) . '"><div class="container stat-band__grid">';
	foreach ( $stats as $stat ) {
		$value  = $stat[0] ?? '';
		$label  = $stat[1] ?? '';
		$target = $stat[2] ?? null;     // numeric target for count-up
		echo '<div class="stat">';
		if ( null !== $target ) {
			printf( '<span class="stat__value" data-count-to="%s">%s</span>', esc_attr( $target ), esc_html( $value ) );
		} else {
			echo '<span class="stat__value">' . esc_html( $value ) . '</span>';
		}
		echo '<span class="stat__label">' . esc_html( $label ) . '</span>';
		echo '</div>';
	}
	echo '</div></div>';
}

/* =========================================================================
 * Document / download card (SPEC §4) — file meta + GA4 doc_download.
 * $doc: title, type, size, updated, href, name(for GA4).
 * ====================================================================== */
function artisraw_doc_card( array $doc ) {
	$icon = '<svg class="doc-card__icon" width="22" height="22" viewBox="0 0 22 22" aria-hidden="true"><path d="M5 2h8l4 4v14H5z" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M13 2v4h4M8 12h6M8 15h6" fill="none" stroke="currentColor" stroke-width="1.4"/></svg>';
	$meta = array_filter( array( $doc['type'] ?? '', $doc['size'] ?? '' ) );
	echo '<a class="doc-card" href="' . esc_url( $doc['href'] ?? '#' ) . '" data-ga="doc_download" data-doc-name="' . esc_attr( $doc['name'] ?? ( $doc['title'] ?? '' ) ) . '" download>';
	echo $icon;
	echo '<span class="doc-card__body">';
	echo '<span class="doc-card__title">' . esc_html( $doc['title'] ?? '' ) . '</span>';
	echo '<span class="doc-card__meta">' . esc_html( implode( ' · ', $meta ) ) . '</span>';
	if ( ! empty( $doc['updated'] ) ) {
		echo '<span class="doc-card__updated">' . esc_html__( 'Updated', 'artisraw' ) . ' ' . esc_html( $doc['updated'] ) . '</span>';
	}
	echo '</span>';
	echo '<span class="doc-card__cta" aria-hidden="true">↓</span>';
	echo '</a>';
}

/* Article card (byline + date). $a: title, href, excerpt, author, date, image. */
function artisraw_article_card( array $a ) {
	echo '<article class="card article-card">';
	if ( ! empty( $a['image']['url'] ) ) {
		printf( '<img class="card__img" src="%s" alt="%s" loading="lazy" width="%d" height="%d">', esc_url( $a['image']['url'] ), esc_attr( $a['image']['alt'] ?? '' ), (int) ( $a['image']['w'] ?? 600 ), (int) ( $a['image']['h'] ?? 360 ) );
	}
	echo '<div class="card__body">';
	echo '<h3 class="card__title"><a class="card__link" href="' . esc_url( $a['href'] ?? '#' ) . '">' . esc_html( $a['title'] ?? '' ) . '</a></h3>';
	if ( ! empty( $a['excerpt'] ) ) {
		echo '<p class="card__excerpt">' . esc_html( $a['excerpt'] ) . '</p>';
	}
	$byline = array_filter( array( $a['author'] ?? '', $a['date'] ?? '' ) );
	if ( $byline ) {
		echo '<p class="card__meta eyebrow">' . esc_html( implode( ' · ', $byline ) ) . '</p>';
	}
	echo '</div></article>';
}

/* Category card with stretched-link (single <a> per card, SPEC §5.4). */
function artisraw_category_card( array $c ) {
	echo '<article class="card category-card">';
	if ( ! empty( $c['image']['url'] ) ) {
		printf( '<img class="card__img" src="%s" alt="%s" loading="lazy" width="%d" height="%d">', esc_url( $c['image']['url'] ), esc_attr( $c['image']['alt'] ?? '' ), (int) ( $c['image']['w'] ?? 600 ), (int) ( $c['image']['h'] ?? 400 ) );
	} else {
		echo '<div class="card__img card__img--placeholder" aria-hidden="true"></div>';
	}
	echo '<div class="card__body">';
	echo '<h3 class="card__title"><a class="card__link stretched-link" href="' . esc_url( $c['href'] ?? '#' ) . '">' . esc_html( $c['title'] ?? '' ) . '</a></h3>';
	if ( ! empty( $c['count'] ) ) {
		echo '<p class="card__meta eyebrow">' . esc_html( $c['count'] ) . '</p>';
	}
	echo '</div></article>';
}

/* =========================================================================
 * Sticky mobile CTA bar (SPEC §5.6) — hidden while a form is in view (JS).
 * ====================================================================== */
function artisraw_sticky_cta() {
	$wa = 'https://wa.me/19292381075';
	echo '<div class="sticky-cta" data-sticky-cta hidden>';
	echo '<a class="btn btn--primary sticky-cta__quote" href="' . esc_url( home_url( '/request-quote/' ) ) . '" data-ga="cta_click" data-ga-label="sticky" data-ga-location="sticky-bar">' . esc_html__( 'Request Quote', 'artisraw' ) . '</a>';
	echo '<a class="sticky-cta__wa" href="' . esc_url( $wa ) . '" aria-label="' . esc_attr__( 'Chat with ArtisRaw on WhatsApp', 'artisraw' ) . '" data-ga="whatsapp_click">';
	echo '<svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm5.52 11.97c-.25.7-1.47 1.36-2.02 1.4-.52.04-1.18.21-3.83-.8-3.23-1.27-5.27-4.59-5.43-4.8-.16-.21-1.3-1.73-1.3-3.3 0-1.57.82-2.34 1.11-2.66.29-.32.64-.4.85-.4l.61.01c.2 0 .46-.07.72.55.25.62.86 2.13.94 2.28.08.16.13.34.02.55-.1.21-.16.34-.31.52-.16.18-.33.41-.47.55-.16.16-.32.33-.14.64.18.31.81 1.34 1.74 2.17 1.2 1.07 2.21 1.4 2.52 1.56.31.16.49.13.67-.08.18-.21.77-.9.98-1.21.21-.31.41-.26.69-.16.28.1 1.79.84 2.1 1 .31.16.51.23.59.36.08.13.08.73-.17 1.43Z"/></svg>';
	echo '</a>';
	echo '</div>';
}

/* =========================================================================
 * Two-step quote form (SPEC §4, §5.5). Step 1 = 4 fields → success panel →
 * optional Step 2. Honeypot + Turnstile; posts to /artisraw/v1/quote; GA4
 * form_submit step 1/2 fire from forms.js. Enqueues forms.js on render.
 *
 * $args: id, location (GA/source), heading, compact (bool).
 * ====================================================================== */
function artisraw_quote_form( array $args = array() ) {
	$id       = $args['id'] ?? 'quote';
	$location = $args['location'] ?? 'inline';
	$heading  = $args['heading'] ?? __( 'Request your line-sheet & compliance pack', 'artisraw' );

	// Load the form JS only where a form actually renders (keeps other pages JS-free).
	wp_enqueue_script( 'artisraw-forms' );

	$endpoint = esc_url_raw( rest_url( 'artisraw/v1/quote' ) );
	$nonce    = wp_create_nonce( 'wp_rest' );
	$sitekey  = defined( 'ARTISRAW_TURNSTILE_SITEKEY' ) ? ARTISRAW_TURNSTILE_SITEKEY : '';

	$err_id = $id . '-errors';
	?>
	<form class="quote-form" id="<?php echo esc_attr( $id ); ?>" data-quote-form
		data-endpoint="<?php echo esc_attr( $endpoint ); ?>"
		data-nonce="<?php echo esc_attr( $nonce ); ?>"
		data-location="<?php echo esc_attr( $location ); ?>"
		novalidate>

		<noscript>
			<p class="quote-form__noscript"><?php
				printf(
					/* translators: 1: email link, 2: contact page link */
					wp_kses_post( __( 'This quick form needs JavaScript. You can also email %1$s or use our %2$s.', 'artisraw' ) ),
					'<a href="mailto:contact@artisraw.com">contact@artisraw.com</a>',
					'<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'contact page', 'artisraw' ) . '</a>'
				);
			?></p>
		</noscript>

		<div class="quote-form__step quote-form__step--1" data-step="1">
			<p class="quote-form__promise eyebrow"><?php esc_html_e( 'Get a quote within 24 h', 'artisraw' ); ?></p>
			<h3 class="quote-form__heading"><?php echo esc_html( $heading ); ?></h3>

			<div class="form-errors" id="<?php echo esc_attr( $err_id ); ?>" role="alert" aria-live="assertive" hidden></div>

			<div class="field">
				<label for="<?php echo esc_attr( $id ); ?>-email"><?php esc_html_e( 'Work email', 'artisraw' ); ?> <span class="field__req" aria-hidden="true">*</span></label>
				<input type="email" id="<?php echo esc_attr( $id ); ?>-email" name="email" autocomplete="email" required aria-required="true" aria-describedby="<?php echo esc_attr( $id ); ?>-email-err">
				<span class="field__msg" id="<?php echo esc_attr( $id ); ?>-email-err" role="alert"></span>
			</div>

			<div class="field">
				<label for="<?php echo esc_attr( $id ); ?>-company"><?php esc_html_e( 'Company', 'artisraw' ); ?> <span class="field__req" aria-hidden="true">*</span></label>
				<input type="text" id="<?php echo esc_attr( $id ); ?>-company" name="company" autocomplete="organization" required aria-required="true" aria-describedby="<?php echo esc_attr( $id ); ?>-company-err">
				<span class="field__msg" id="<?php echo esc_attr( $id ); ?>-company-err" role="alert"></span>
			</div>

			<div class="field-row">
				<div class="field">
					<label for="<?php echo esc_attr( $id ); ?>-country"><?php esc_html_e( 'Destination country', 'artisraw' ); ?> <span class="field__req" aria-hidden="true">*</span></label>
					<select id="<?php echo esc_attr( $id ); ?>-country" name="country" required aria-required="true" aria-describedby="<?php echo esc_attr( $id ); ?>-country-err">
						<option value=""><?php esc_html_e( 'Select…', 'artisraw' ); ?></option>
						<?php
						$countries = array( 'United States', 'Canada', 'United Kingdom', 'France', 'Germany', 'Spain', 'Italy', 'Netherlands', 'United Arab Emirates', 'Saudi Arabia', 'Qatar', 'Japan', 'Australia', 'Other' );
						foreach ( $countries as $c ) {
							echo '<option value="' . esc_attr( $c ) . '">' . esc_html( $c ) . '</option>';
						}
						?>
					</select>
					<span class="field__msg" id="<?php echo esc_attr( $id ); ?>-country-err" role="alert"></span>
				</div>
				<div class="field">
					<label for="<?php echo esc_attr( $id ); ?>-date"><?php esc_html_e( 'Target delivery date', 'artisraw' ); ?></label>
					<input type="date" id="<?php echo esc_attr( $id ); ?>-date" name="target_date">
					<span class="field__msg" id="<?php echo esc_attr( $id ); ?>-date-err" role="alert"></span>
				</div>
			</div>

			<?php // Honeypot — visually hidden, must stay empty. ?>
			<div class="hp-field" aria-hidden="true">
				<label for="<?php echo esc_attr( $id ); ?>-website"><?php esc_html_e( 'Leave this field empty', 'artisraw' ); ?></label>
				<input type="text" id="<?php echo esc_attr( $id ); ?>-website" name="website" tabindex="-1" autocomplete="off">
			</div>

			<?php // Hidden UTM/source capture (SPEC §4, §8). ?>
			<input type="hidden" name="source" value="<?php echo esc_attr( $location ); ?>">
			<input type="hidden" name="utm_source" value="">
			<input type="hidden" name="utm_medium" value="">
			<input type="hidden" name="utm_campaign" value="">
			<input type="hidden" name="page_url" value="">

			<?php if ( $sitekey ) : ?>
				<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $sitekey ); ?>" data-size="invisible"></div>
			<?php endif; ?>

			<div class="quote-form__actions">
				<button type="submit" class="btn btn--primary quote-form__submit" data-ga="cta_click" data-ga-label="quote-step-1" data-ga-location="<?php echo esc_attr( $location ); ?>">
					<span class="btn__label"><?php esc_html_e( 'Request Line-Sheet & Compliance Pack', 'artisraw' ); ?></span>
				</button>
			</div>
			<p class="quote-form__privacy"><?php esc_html_e( 'We reply within one business day. Your details are used only to prepare your quote —', 'artisraw' ); ?> <a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'privacy', 'artisraw' ); ?></a>.</p>
		</div>

		<?php // Success + Step 2 (revealed by JS, never a new page). ?>
		<div class="quote-form__success" data-step="success" hidden>
			<div class="quote-form__success-head">
				<svg width="40" height="40" viewBox="0 0 40 40" aria-hidden="true" class="quote-form__tick"><circle cx="20" cy="20" r="18" fill="none" stroke="currentColor" stroke-width="2"/><path d="M12 20l5 5 11-11" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
				<h3 class="quote-form__heading"><?php esc_html_e( 'Thanks — your line-sheet is on its way to', 'artisraw' ); ?> <span data-success-email></span></h3>
				<p><?php esc_html_e( 'Tell us a little more to speed up your quote (optional).', 'artisraw' ); ?></p>
			</div>

			<div class="quote-form__step quote-form__step--2" data-step="2">
				<div class="field-row">
					<div class="field">
						<label for="<?php echo esc_attr( $id ); ?>-volume"><?php esc_html_e( 'Monthly volume', 'artisraw' ); ?></label>
						<select id="<?php echo esc_attr( $id ); ?>-volume" name="monthly_volume">
							<option value=""><?php esc_html_e( 'Select…', 'artisraw' ); ?></option>
							<option><?php esc_html_e( '50–250 units', 'artisraw' ); ?></option>
							<option><?php esc_html_e( '250–1,000 units', 'artisraw' ); ?></option>
							<option><?php esc_html_e( '1,000–5,000 units', 'artisraw' ); ?></option>
							<option><?php esc_html_e( '5,000+ units', 'artisraw' ); ?></option>
						</select>
					</div>
					<div class="field">
						<label for="<?php echo esc_attr( $id ); ?>-ship"><?php esc_html_e( 'Preferred ship mode', 'artisraw' ); ?></label>
						<select id="<?php echo esc_attr( $id ); ?>-ship" name="ship_mode">
							<option value=""><?php esc_html_e( 'Select…', 'artisraw' ); ?></option>
							<option><?php esc_html_e( 'Air (5–12 days)', 'artisraw' ); ?></option>
							<option><?php esc_html_e( 'Ocean (25–40 days)', 'artisraw' ); ?></option>
							<option><?php esc_html_e( 'Not sure yet', 'artisraw' ); ?></option>
						</select>
					</div>
				</div>

				<fieldset class="field">
					<legend><?php esc_html_e( 'Documents requested', 'artisraw' ); ?></legend>
					<div class="check-row">
						<label class="check"><input type="checkbox" name="documents[]" value="line-sheet"> <?php esc_html_e( 'Line-sheet', 'artisraw' ); ?></label>
						<label class="check"><input type="checkbox" name="documents[]" value="compliance-pack"> <?php esc_html_e( 'Compliance pack (Lacey/EUDR)', 'artisraw' ); ?></label>
						<label class="check"><input type="checkbox" name="documents[]" value="iso-9001"> <?php esc_html_e( 'ISO 9001 certificate', 'artisraw' ); ?></label>
						<label class="check"><input type="checkbox" name="documents[]" value="msds"> <?php esc_html_e( 'Finish MSDS', 'artisraw' ); ?></label>
					</div>
				</fieldset>

				<div class="field-row">
					<label class="check"><input type="checkbox" name="engraving" value="yes"> <?php esc_html_e( 'I need logo engraving / private label', 'artisraw' ); ?></label>
					<label class="check"><input type="checkbox" name="broker_ddp" value="yes"> <?php esc_html_e( 'I’ll need a broker / DDP delivery', 'artisraw' ); ?></label>
				</div>

				<div class="quote-form__actions">
					<button type="button" class="btn btn--primary quote-form__submit2" data-ga="cta_click" data-ga-label="quote-step-2" data-ga-location="<?php echo esc_attr( $location ); ?>"><?php esc_html_e( 'Send these details', 'artisraw' ); ?></button>
					<a class="btn btn--tertiary" href="<?php echo esc_url( home_url( '/wholesale/' ) ); ?>"><?php esc_html_e( 'Browse the catalogue meanwhile', 'artisraw' ); ?></a>
				</div>
			</div>

			<p class="quote-form__step2-done" data-step="done" hidden><?php esc_html_e( 'Got it — we’ll include these in your quote. Talk soon.', 'artisraw' ); ?></p>
		</div>
	</form>
	<?php
}

/* =========================================================================
 * Phase 5 — design-parity components (mockup pages 1–5).
 * ====================================================================== */

/**
 * Numbered process steps (services 8-step flow, how-to-order, QC timeline).
 * $steps: [ [eyebrow, title, body] ]. Renders an ordered list of step cards.
 */
function artisraw_steps( array $steps, $start = 1 ) {
	echo '<ol class="steps" role="list">';
	foreach ( $steps as $i => $step ) {
		$num   = str_pad( (string) ( $start + $i ), 2, '0', STR_PAD_LEFT );
		$eyebrow = $step[0] ?? '';
		$title   = $step[1] ?? '';
		$body    = $step[2] ?? '';
		echo '<li class="step">';
		echo '<span class="step__num" aria-hidden="true">' . esc_html( $num ) . '</span>';
		echo '<div class="step__body">';
		if ( $eyebrow ) {
			echo '<p class="step__eyebrow eyebrow">' . esc_html( $eyebrow ) . '</p>';
		}
		echo '<h3 class="step__title">' . esc_html( $title ) . '</h3>';
		if ( $body ) {
			echo '<p class="step__text">' . esc_html( $body ) . '</p>';
		}
		echo '</div></li>';
	}
	echo '</ol>';
}

/**
 * Testimonial cards (mockup home "What they say"). $items: [ [quote, author, role, ?stars] ].
 * Stars default to 5; rendered with an accessible label.
 */
function artisraw_testimonials( array $items, $heading = '' ) {
	echo '<div class="testimonials">';
	if ( $heading ) {
		echo '<h2 class="testimonials__head">' . esc_html( $heading ) . '</h2>';
	}
	echo '<div class="testimonials__grid">';
	foreach ( $items as $t ) {
		$quote  = $t[0] ?? '';
		$author = $t[1] ?? '';
		$role   = $t[2] ?? '';
		$stars  = isset( $t[3] ) ? max( 1, min( 5, (int) $t[3] ) ) : 5;
		echo '<figure class="testimonial">';
		printf(
			'<p class="testimonial__stars" role="img" aria-label="%s">%s</p>',
			esc_attr( sprintf( /* translators: %d: rating out of 5 */ __( 'Rated %d out of 5', 'artisraw' ), $stars ) ),
			esc_html( str_repeat( '★', $stars ) . str_repeat( '☆', 5 - $stars ) )
		);
		echo '<blockquote class="testimonial__quote">' . esc_html( $quote ) . '</blockquote>';
		echo '<figcaption class="testimonial__by">';
		echo '<span class="testimonial__author">' . esc_html( $author ) . '</span>';
		if ( $role ) {
			echo '<span class="testimonial__role"> · ' . esc_html( $role ) . '</span>';
		}
		echo '</figcaption></figure>';
	}
	echo '</div></div>';
}

/**
 * Newsletter signup (mockup footer/home "Stay in the know"). Progressive
 * enhancement: posts to /artisraw/v1/newsletter via components.js; with JS off,
 * the form GETs /contact/ so the buyer still has a path. Honeypot included.
 * $args: id, heading, text, location, compact(bool).
 */
function artisraw_newsletter( array $args = array() ) {
	$id       = $args['id'] ?? 'newsletter';
	$heading  = $args['heading'] ?? __( 'Stay in the know', 'artisraw' );
	$text     = $args['text'] ?? __( 'B2B updates, product launches and catalogue news — no more than monthly.', 'artisraw' );
	$location = $args['location'] ?? 'inline';
	$compact  = ! empty( $args['compact'] );
	$endpoint = esc_url_raw( rest_url( 'artisraw/v1/newsletter' ) );
	$nonce    = wp_create_nonce( 'wp_rest' );
	$cls      = 'newsletter' . ( $compact ? ' newsletter--compact' : '' );
	?>
	<section class="<?php echo esc_attr( $cls ); ?>" aria-labelledby="<?php echo esc_attr( $id ); ?>-h">
		<div class="newsletter__copy">
			<h2 class="newsletter__head" id="<?php echo esc_attr( $id ); ?>-h"><?php echo esc_html( $heading ); ?></h2>
			<p class="newsletter__text"><?php echo esc_html( $text ); ?></p>
		</div>
		<form class="newsletter__form" id="<?php echo esc_attr( $id ); ?>" data-newsletter
			action="<?php echo esc_url( home_url( '/contact/' ) ); ?>" method="get"
			data-endpoint="<?php echo esc_attr( $endpoint ); ?>"
			data-nonce="<?php echo esc_attr( $nonce ); ?>"
			data-location="<?php echo esc_attr( $location ); ?>">
			<div class="newsletter__row">
				<label class="visually-hidden" for="<?php echo esc_attr( $id ); ?>-email"><?php esc_html_e( 'Work email', 'artisraw' ); ?></label>
				<input type="email" id="<?php echo esc_attr( $id ); ?>-email" name="email" autocomplete="email" required placeholder="<?php esc_attr_e( 'you@company.com', 'artisraw' ); ?>">
				<?php // Honeypot — must stay empty. ?>
				<span class="hp-field" aria-hidden="true"><label for="<?php echo esc_attr( $id ); ?>-hp"><?php esc_html_e( 'Leave empty', 'artisraw' ); ?></label><input type="text" id="<?php echo esc_attr( $id ); ?>-hp" name="website" tabindex="-1" autocomplete="off"></span>
				<button type="submit" class="btn btn--primary newsletter__submit" data-ga="cta_click" data-ga-label="newsletter" data-ga-location="<?php echo esc_attr( $location ); ?>"><span class="btn__label"><?php esc_html_e( 'Subscribe', 'artisraw' ); ?></span></button>
			</div>
			<p class="newsletter__msg" data-newsletter-msg role="status" aria-live="polite" hidden></p>
		</form>
	</section>
	<?php
}

/**
 * Founders / team cards (mockup about + home teaser). $people: [ [name, role, bio] ].
 */
function artisraw_founders( array $people, $heading = '' ) {
	echo '<div class="founders">';
	if ( $heading ) {
		echo '<h2 class="founders__head">' . esc_html( $heading ) . '</h2>';
	}
	echo '<div class="founders__grid">';
	foreach ( $people as $p ) {
		$name = $p[0] ?? '';
		$role = $p[1] ?? '';
		$bio  = $p[2] ?? '';
		$initials = '';
		foreach ( preg_split( '/\s+/', $name ) as $word ) {
			$initials .= function_exists( 'mb_substr' ) ? mb_substr( $word, 0, 1 ) : substr( $word, 0, 1 );
		}
		echo '<figure class="founder">';
		echo '<span class="founder__avatar" aria-hidden="true">' . esc_html( strtoupper( $initials ) ) . '</span>';
		echo '<figcaption class="founder__body">';
		echo '<p class="founder__role eyebrow">' . esc_html( $role ) . '</p>';
		echo '<h3 class="founder__name">' . esc_html( $name ) . '</h3>';
		if ( $bio ) {
			echo '<p class="founder__bio">' . esc_html( $bio ) . '</p>';
		}
		echo '</figcaption></figure>';
	}
	echo '</div></div>';
}

/**
 * Plant-a-tree program panel (mockup "Plant 1 tree for each order").
 * $args: heading, text, stat_value, stat_label, cta_label, cta_url.
 */
function artisraw_plant_a_tree( array $args = array() ) {
	$heading = $args['heading'] ?? __( 'We plant a tree for every order', 'artisraw' );
	$text    = $args['text'] ?? __( 'ArtisRaw works only reclaimed, end-of-life Chemlali olive wood — and for every wholesale order we sponsor reforestation through trees.org, so the cycle keeps giving back.', 'artisraw' );
	$value   = $args['stat_value'] ?? '10,790+';
	$label   = $args['stat_label'] ?? __( 'Trees sponsored to date', 'artisraw' );
	$cta_l   = $args['cta_label'] ?? __( 'Our sustainability approach', 'artisraw' );
	$cta_u   = $args['cta_url'] ?? home_url( '/sustainability/' );
	$leaf    = '<svg class="plant__icon" width="40" height="40" viewBox="0 0 40 40" aria-hidden="true"><path d="M20 34c0-9 5-15 14-16-1 9-6 15-14 16Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M20 34c0-7-4-12-11-13 .8 7 5 12 11 13Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M20 34V20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
	echo '<aside class="plant section--sand" aria-label="' . esc_attr__( 'Reforestation program', 'artisraw' ) . '">';
	echo '<div class="container plant__inner">';
	echo '<div class="plant__copy">' . $leaf;
	echo '<h2 class="plant__head">' . esc_html( $heading ) . '</h2>';
	echo '<p class="plant__text">' . esc_html( $text ) . '</p>';
	echo '<p><a class="btn btn--tertiary" href="' . esc_url( $cta_u ) . '">' . esc_html( $cta_l ) . '</a></p>';
	echo '</div>';
	echo '<div class="plant__stat"><span class="plant__value" data-count-to="' . esc_attr( preg_replace( '/[^0-9]/', '', $value ) ) . '">' . esc_html( $value ) . '</span><span class="plant__label">' . esc_html( $label ) . '</span></div>';
	echo '</div></aside>';
}

/**
 * Instagram strip (mockup home). Static, curated — no third-party script in the
 * critical path. $items: [ [caption, ?href, ?image_base, ?widths] ]; when an
 * image base is given a lazy WebP renders behind the caption, otherwise a
 * gradient tile. $handle links to the profile.
 */
function artisraw_instagram_strip( array $items, $handle = 'artisraw', $profile_url = '' ) {
	$profile_url = $profile_url ?: 'https://www.instagram.com/' . ltrim( $handle, '@' ) . '/';
	echo '<section class="ig" aria-labelledby="ig-h">';
	echo '<div class="ig__head"><h2 id="ig-h">' . esc_html__( 'From our Instagram', 'artisraw' ) . '</h2>';
	echo '<a class="ig__follow" href="' . esc_url( $profile_url ) . '" rel="noopener" target="_blank">@' . esc_html( ltrim( $handle, '@' ) ) . ' <span aria-hidden="true">↗</span></a></div>';
	echo '<ul class="ig__grid" role="list">';
	foreach ( $items as $item ) {
		$caption = $item[0] ?? '';
		$href    = $item[1] ?? $profile_url;
		$base    = $item[2] ?? '';
		$widths  = $item[3] ?? array( 600 );
		$has_img = $base ? ' ig__link--img' : '';
		echo '<li class="ig__cell"><a class="ig__link' . esc_attr( $has_img ) . '" href="' . esc_url( $href ) . '" rel="noopener" target="_blank">';
		if ( $base ) {
			artisraw_responsive_image( array(
				'base' => $base, 'alt' => $caption, 'class' => 'ig__img',
				'width' => 600, 'height' => 600, 'widths' => $widths,
				'sizes' => '(min-width: 900px) 16vw, 33vw',
			) );
		}
		echo '<span class="ig__caption">' . esc_html( $caption ) . '</span></a></li>';
	}
	echo '</ul></section>';
}

/* =========================================================================
 * Phase 11 — Art Direction components (Addendum v1.1).
 * ====================================================================== */

/** Block-formula arrow link (§5): "Label →", arrow nudges on hover. */
function artisraw_arrow_link( $label, $href, $args = array() ) {
	$ga = '';
	if ( ! empty( $args['ga'] ) ) {
		$ga = ' data-ga="cta_click" data-ga-label="' . esc_attr( $args['ga'] ) . '" data-ga-location="' . esc_attr( $args['loc'] ?? '' ) . '"';
	}
	printf(
		'<a class="arrow-link" href="%s"%s>%s <span class="arrow-link__arrow" aria-hidden="true">&rarr;</span></a>',
		esc_url( $href ),
		$ga,
		esc_html( $label )
	);
}

/**
 * Statement hero (§3): one duotone image + lowercase serif statement + support
 * line + amber CTA + numeric trust strip pinned at the lower edge.
 * $a: base, alt, eyebrow, statement, support, cta_label, cta_url, alt_label,
 *     alt_url, trust [[label,href]], as_h1(bool), loc, w, h, widths.
 */
function artisraw_statement_hero( array $a ) {
	$tag = ! empty( $a['as_h1'] ) ? 'h1' : 'p';
	$hero_attr = ! empty( $a['loc'] ) ? ' data-hero="' . esc_attr( $a['loc'] ) . '"' : '';
	echo '<section class="statement-hero on-dark"' . $hero_attr . '>';
	artisraw_responsive_image( array(
		'base' => $a['base'], 'alt' => $a['alt'] ?? '', 'class' => 'statement-hero__img',
		'width' => $a['w'] ?? 1800, 'height' => $a['h'] ?? 1200,
		'widths' => $a['widths'] ?? array( 600, 1200, 1800 ), 'sizes' => '100vw', 'eager' => true,
	) );
	echo '<div class="statement-hero__inner"><div class="container">';
	if ( ! empty( $a['eyebrow'] ) ) {
		echo '<p class="statement-hero__eyebrow eyebrow">' . esc_html( $a['eyebrow'] ) . '</p>';
	}
	echo '<' . $tag . ' class="statement-hero__statement">' . esc_html( $a['statement'] ) . '</' . $tag . '>';
	if ( ! empty( $a['support'] ) ) {
		echo '<p class="statement-hero__support">' . esc_html( $a['support'] ) . '</p>';
	}
	if ( ! empty( $a['cta_label'] ) ) {
		echo '<p class="statement-hero__cta"><a class="btn btn--primary" href="' . esc_url( $a['cta_url'] ) . '" data-ga="cta_click" data-ga-label="hero" data-ga-location="' . esc_attr( $a['loc'] ?? 'hero' ) . '">' . esc_html( $a['cta_label'] ) . '</a>';
		if ( ! empty( $a['alt_label'] ) ) {
			echo '<a class="btn btn--tertiary hub-hero__alt" href="' . esc_url( $a['alt_url'] ) . '">' . esc_html( $a['alt_label'] ) . '</a>';
		}
		echo '</p>';
	}
	if ( ! empty( $a['trust'] ) ) {
		echo '<div class="statement-hero__trust">';
		artisraw_trust_strip( $a['trust'] );
		echo '</div>';
	}
	echo '</div></div></section>';
}

/**
 * Color-block mosaic band (§4): 50/50 color field ↔ photo, alternating sides.
 * $a: field (sand|espresso|amber|leaf), eyebrow, heading, body, link_label,
 *     link_url, img_base, img_alt, img_widths, field_left(bool), w, h.
 */
function artisraw_color_block( array $a ) {
	$cls = 'color-block' . ( ! empty( $a['field_left'] ) ? ' color-block--field-left' : '' );
	echo '<section class="' . esc_attr( $cls ) . '">';
	echo '<div class="color-block__photo">';
	artisraw_responsive_image( array(
		'base' => $a['img_base'], 'alt' => $a['img_alt'] ?? '', 'class' => 'color-block__img',
		'width' => $a['w'] ?? 1200, 'height' => $a['h'] ?? 900,
		'widths' => $a['img_widths'] ?? array( 600, 1200 ), 'sizes' => '(min-width: 860px) 50vw, 100vw',
	) );
	echo '</div>';
	echo '<div class="color-block__field field--' . esc_attr( $a['field'] ?? 'sand' ) . '">';
	if ( ! empty( $a['eyebrow'] ) ) {
		echo '<p class="color-block__eyebrow eyebrow">' . esc_html( $a['eyebrow'] ) . '</p>';
	}
	echo '<h2 class="color-block__heading">' . esc_html( $a['heading'] ) . '</h2>';
	if ( ! empty( $a['body'] ) ) {
		echo '<p class="color-block__body">' . esc_html( $a['body'] ) . '</p>';
	}
	if ( ! empty( $a['link_label'] ) ) {
		artisraw_arrow_link( $a['link_label'], $a['link_url'] );
	}
	echo '</div></section>';
}

/**
 * Buyer voices (§6): one large quote per viewport (scroll-snap between).
 * $items: [ [quote, author, role, ?stars] ].
 */
function artisraw_buyer_voices( array $items, $heading = '' ) {
	echo '<div class="voices">';
	if ( $heading ) {
		echo '<div class="section-opener"><h2>' . esc_html( $heading ) . '</h2></div>';
	}
	echo '<ul class="voices__track" role="list">';
	foreach ( $items as $t ) {
		$quote  = $t[0] ?? '';
		$author = $t[1] ?? '';
		$role   = $t[2] ?? '';
		$stars  = isset( $t[3] ) ? max( 1, min( 5, (int) $t[3] ) ) : 5;
		echo '<li class="voice">';
		printf(
			'<p class="voice__stars" role="img" aria-label="%s">%s</p>',
			esc_attr( sprintf( /* translators: %d rating */ __( 'Rated %d out of 5', 'artisraw' ), $stars ) ),
			esc_html( str_repeat( '★', $stars ) )
		);
		echo '<blockquote class="voice__quote">' . esc_html( $quote ) . '</blockquote>';
		echo '<p class="voice__by"><strong>' . esc_html( $author ) . '</strong>' . ( $role ? ' · ' . esc_html( $role ) : '' ) . '</p>';
		echo '</li>';
	}
	echo '</ul></div>';
}

/**
 * Two-column quote block (§9.1): "WHOLESALE INQUIRIES" + benefits checklist on
 * the left, the two-step form as a white card on the right. Adopt site-wide.
 * $a: id, location, eyebrow, heading, intro, benefits[].
 */
function artisraw_quote_block( array $a = array() ) {
	$benefits = $a['benefits'] ?? array( __( 'Low MOQ', 'artisraw' ), __( 'Custom branding', 'artisraw' ), __( 'Worldwide shipping', 'artisraw' ) );
	echo '<div class="quote-block">';
	echo '<div class="quote-block__intro">';
	echo '<p class="eyebrow">' . esc_html( $a['eyebrow'] ?? __( 'Wholesale inquiries', 'artisraw' ) ) . '</p>';
	echo '<h2>' . esc_html( $a['heading'] ?? __( 'Request a Quote', 'artisraw' ) ) . '</h2>';
	if ( ! empty( $a['intro'] ) ) {
		echo '<p class="lead">' . esc_html( $a['intro'] ) . '</p>';
	}
	echo '<ul class="quote-block__benefits">';
	foreach ( $benefits as $b ) {
		echo '<li>' . esc_html( $b ) . '</li>';
	}
	echo '</ul>';
	echo '<p class="eyebrow">' . esc_html__( 'Quote within 24 h', 'artisraw' ) . '</p>';
	echo '</div>';
	echo '<div class="quote-block__card">';
	artisraw_quote_form( array( 'id' => $a['id'] ?? 'quote', 'location' => $a['location'] ?? 'inline', 'heading' => $a['form_heading'] ?? __( 'Request your line-sheet & compliance pack', 'artisraw' ) ) );
	echo '</div>';
	echo '</div>';
}

/**
 * Photo mosaic (mockup "From tree, to workshop, to wholesale shelves").
 * A labelled image grid with one feature tile. $tiles: each
 *   [ 'base'=>'/assets/ar-grove', 'alt'=>…, 'label'=>…, 'variant'=>'big'|'wide'|'',
 *     'w'=>, 'h'=>, 'widths'=>[600,1200], 'href'=>(optional) ].
 */
function artisraw_photo_mosaic( array $tiles, $heading = '', $intro = '' ) {
	echo '<div class="mosaic-wrap">';
	if ( $heading ) {
		echo '<h2 class="mosaic__head">' . esc_html( $heading ) . '</h2>';
	}
	if ( $intro ) {
		echo '<p class="lead mosaic__intro">' . esc_html( $intro ) . '</p>';
	}
	echo '<div class="mosaic">';
	foreach ( $tiles as $t ) {
		$variant = $t['variant'] ?? '';
		$cls     = 'mosaic__tile' . ( $variant ? ' mosaic__tile--' . $variant : '' );
		$href    = $t['href'] ?? '';
		$tag     = $href ? 'a' : 'figure';
		$attr    = $href ? ' href="' . esc_url( $href ) . '"' : '';
		echo '<' . $tag . ' class="' . esc_attr( $cls ) . '"' . $attr . '>';
		artisraw_responsive_image( array(
			'base'   => $t['base'],
			'alt'    => $t['alt'] ?? '',
			'class'  => 'mosaic__img',
			'width'  => $t['w'] ?? 1200,
			'height' => $t['h'] ?? 800,
			'widths' => $t['widths'] ?? array( 600, 1200 ),
			'sizes'  => 'big' === $variant ? '(min-width: 1024px) 50vw, 100vw' : '(min-width: 1024px) 25vw, 50vw',
		) );
		if ( ! empty( $t['label'] ) ) {
			echo '<span class="mosaic__label">' . esc_html( $t['label'] ) . '</span>';
		}
		echo '</' . $tag . '>';
	}
	echo '</div></div>';
}
