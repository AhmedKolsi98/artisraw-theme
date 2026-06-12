<?php
/**
 * Phase 10 — French layer (lightweight, plugin-free i18n) (SPEC §9).
 *
 * French pages are real pages (meta page_lang=fr) that reuse the same templates;
 * their UI chrome is translated at runtime via the `gettext` filter against the
 * compiled gettext catalogue in languages/fr_FR.po/.mo (edit with Poedit, then
 * recompile the .mo), and their rich body copy lives in post_content. Because EN
 * and FR pages share one site locale, translations are switched per page rather
 * than by WP's locale loader. EN↔FR counterparts are paired with the `alt_pair` meta, which
 * drives both hreflang and the header language toggle. <html lang> follows the
 * page language. Strings not in the dictionary fall back to English gracefully.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Resolve + cache whether the current request is a French page. */
function artisraw_set_lang_flag() {
	$id = get_queried_object_id();
	$GLOBALS['artisraw_is_fr'] = ( $id && 'fr' === get_post_meta( $id, 'page_lang', true ) );
}
add_action( 'wp', 'artisraw_set_lang_flag' );

function artisraw_is_fr() {
	return ! empty( $GLOBALS['artisraw_is_fr'] );
}

/** <html lang="fr-FR"> on French pages. */
function artisraw_language_attributes( $output ) {
	if ( artisraw_is_fr() ) {
		return 'lang="fr-FR"';
	}
	return $output;
}
add_filter( 'language_attributes', 'artisraw_language_attributes' );

/* =========================================================================
 * Runtime translation of theme UI strings on French pages.
 * ====================================================================== */
function artisraw_gettext( $translation, $text, $domain ) {
	if ( 'artisraw' !== $domain || ! artisraw_is_fr() ) {
		return $translation;
	}
	$dict = artisraw_fr_dict();
	return $dict[ $text ] ?? $translation;
}
add_filter( 'gettext', 'artisraw_gettext', 10, 3 );

/* =========================================================================
 * hreflang alternates (EN ↔ FR + x-default) from the alt_pair meta.
 * ====================================================================== */
function artisraw_hreflang() {
	if ( ! is_singular() ) {
		return;
	}
	$id   = get_queried_object_id();
	$pair = (int) get_post_meta( $id, 'alt_pair', true );
	if ( ! $pair || get_post_status( $pair ) !== 'publish' ) {
		return;
	}
	$is_fr   = artisraw_is_fr();
	$en_url  = $is_fr ? get_permalink( $pair ) : get_permalink( $id );
	$fr_url  = $is_fr ? get_permalink( $id ) : get_permalink( $pair );
	printf( '<link rel="alternate" hreflang="en" href="%s">' . "\n", esc_url( $en_url ) );
	printf( '<link rel="alternate" hreflang="fr" href="%s">' . "\n", esc_url( $fr_url ) );
	printf( '<link rel="alternate" hreflang="x-default" href="%s">' . "\n", esc_url( $en_url ) );
}
add_action( 'wp_head', 'artisraw_hreflang', 3 );

/* =========================================================================
 * Language toggle data for the header. Returns the EN + FR target URLs and
 * which is current. Untranslated pages point FR at the /fr/ landing.
 * ====================================================================== */
function artisraw_lang_links() {
	$fr_home = home_url( '/fr/' );
	$en_home = home_url( '/' );
	$current = artisraw_is_fr() ? 'fr' : 'en';
	$en_url  = $en_home;
	$fr_url  = $fr_home;
	// Whether a real counterpart exists for THIS page (vs. the home fallback).
	// The current language always exists; the other side only if paired.
	$en_exists = true;
	$fr_exists = true;

	if ( is_singular() ) {
		$id     = get_queried_object_id();
		$pair   = (int) get_post_meta( $id, 'alt_pair', true );
		$paired = $pair && 'publish' === get_post_status( $pair );
		if ( artisraw_is_fr() ) {
			$fr_url    = get_permalink( $id );
			$en_url    = $paired ? get_permalink( $pair ) : $en_home;
			$en_exists = $paired;
		} else {
			$en_url    = get_permalink( $id );
			$fr_url    = $paired ? get_permalink( $pair ) : $fr_home;
			$fr_exists = $paired;
		}
	} else {
		// Archives, search, 404, blog index: no per-page counterpart.
		if ( artisraw_is_fr() ) {
			$en_exists = false;
		} else {
			$fr_exists = false;
		}
	}
	return array(
		'current'   => $current,
		'en'        => $en_url,
		'fr'        => $fr_url,
		'en_exists' => $en_exists,
		'fr_exists' => $fr_exists,
	);
}

/**
 * Language-aware internal URL. Given an English path (e.g. '/wholesale/' or
 * '/wholesale/olive-wood-cutting-boards/'), returns the English URL on English
 * pages and the paired French counterpart on French pages. Falls back to the
 * English URL when no French translation exists. Used by the nav, footer and
 * breadcrumbs so chrome links stay within the visitor's language.
 */
function artisraw_localized_url( $en_path ) {
	$en_path = '/' . ltrim( (string) $en_path, '/' );
	if ( ! artisraw_is_fr() ) {
		return home_url( $en_path );
	}
	if ( '/' === $en_path ) {
		return home_url( '/fr/' ); // site root → French home.
	}
	static $cache = array();
	if ( array_key_exists( $en_path, $cache ) ) {
		return $cache[ $en_path ];
	}
	$url  = home_url( $en_path ); // fallback: English (no FR counterpart).
	$page = get_page_by_path( trim( $en_path, '/' ) );
	if ( $page ) {
		$pair = (int) get_post_meta( $page->ID, 'alt_pair', true );
		if ( $pair && 'publish' === get_post_status( $pair ) ) {
			$url = get_permalink( $pair );
		}
	}
	$cache[ $en_path ] = $url;
	return $url;
}

/**
 * Inline SVG flag for the language switcher. Trusted static markup; no IDs so
 * it can be rendered multiple times safely. FR tricolore / GB union flag.
 */
function artisraw_flag_svg( $lang ) {
	if ( 'fr' === $lang ) {
		return '<svg class="lang-switch__flag" viewBox="0 0 3 2" aria-hidden="true" focusable="false">'
			. '<rect width="3" height="2" fill="#ED2939"/><rect width="2" height="2" fill="#fff"/><rect width="1" height="2" fill="#002395"/></svg>';
	}
	return '<svg class="lang-switch__flag" viewBox="0 0 60 30" aria-hidden="true" focusable="false">'
		. '<rect width="60" height="30" fill="#012169"/>'
		. '<path d="M0,0 60,30 M60,0 0,30" stroke="#fff" stroke-width="6"/>'
		. '<path d="M0,0 60,30 M60,0 0,30" stroke="#C8102E" stroke-width="3"/>'
		. '<path d="M30,0 V30 M0,15 H60" stroke="#fff" stroke-width="10"/>'
		. '<path d="M30,0 V30 M0,15 H60" stroke="#C8102E" stroke-width="6"/></svg>';
}

/**
 * Language switcher dropdown (flag + name). Uses a native <details> so it works
 * without JS and is keyboard-accessible. Each option links to its EN/FR
 * counterpart via artisraw_lang_links().
 */
function artisraw_lang_switch() {
	$links   = artisraw_lang_links();
	$current = $links['current']; // 'en' | 'fr'
	$names   = array( 'en' => 'English', 'fr' => 'Français' );

	echo '<details class="lang-switch">';
	printf(
		'<summary class="lang-switch__summary" aria-label="%s">%s<span class="lang-switch__name">%s</span><svg class="lang-switch__chev" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6"/></svg></summary>',
		esc_attr__( 'Language', 'artisraw' ),
		artisraw_flag_svg( $current ),
		esc_html( $names[ $current ] )
	);
	echo '<ul class="lang-switch__menu" role="list">';
	foreach ( array( 'en', 'fr' ) as $l ) {
		$is_current = ( $l === $current );
		$exists     = $is_current || ! empty( $links[ $l . '_exists' ] );
		if ( $exists ) {
			printf(
				'<li><a class="lang-switch__opt" href="%s" hreflang="%s"%s>%s<span>%s</span></a></li>',
				esc_url( $links[ $l ] ),
				esc_attr( $l ),
				$is_current ? ' aria-current="true"' : '',
				artisraw_flag_svg( $l ),
				esc_html( $names[ $l ] )
			);
		} else {
			// No counterpart for this page — show the option disabled, not a
			// misleading link to the /fr/ (or /) home.
			printf(
				'<li><span class="lang-switch__opt is-disabled" aria-disabled="true" title="%s">%s<span>%s</span></span></li>',
				esc_attr__( 'This page is not available in this language yet', 'artisraw' ),
				artisraw_flag_svg( $l ),
				esc_html( $names[ $l ] )
			);
		}
	}
	echo '</ul></details>';
}

/* =========================================================================
 * French dictionary — global chrome (nav, footer, buttons, forms, breadcrumb,
 * trust strip) + the prose-template chrome used by the French pages. Body copy
 * is translated in post_content, not here.
 * ====================================================================== */
function artisraw_fr_dict() {
	static $d = null;
	if ( null !== $d ) {
		return $d;
	}
	// Load translations from the compiled gettext catalogue (Poedit-friendly,
	// no hand-synced PHP keys). EN→FR map; missing keys fall back to English in
	// artisraw_gettext(). See languages/fr_FR.po.
	$d       = array();
	$mo_file = ARTISRAW_DIR . '/languages/fr_FR.mo';
	if ( is_readable( $mo_file ) ) {
		if ( ! class_exists( 'MO' ) ) {
			require_once ABSPATH . WPINC . '/pomo/mo.php';
		}
		$mo = new MO();
		if ( $mo->import_from_file( $mo_file ) ) {
			foreach ( $mo->entries as $original => $entry ) {
				if ( isset( $entry->translations[0] ) && '' !== $entry->translations[0] ) {
					$d[ $original ] = $entry->translations[0];
				}
			}
		}
	}
	return $d;
}

/* =========================================================================
 * French page seeding (idempotent). Runs after the EN seeder (priority 31) so
 * EN counterparts exist for pairing. Pages reuse the prose templates; the rich
 * French body lives in post_content. Bump ARTISRAW_FR_VER to add/update pages.
 * ====================================================================== */
define( 'ARTISRAW_FR_VER', 5 );

function artisraw_seed_fr_pages() {
	if ( (int) get_option( 'artisraw_fr_ver' ) >= ARTISRAW_FR_VER ) {
		return;
	}
	$pages = artisraw_fr_page_data();
	$ids   = array();

	for ( $pass = 0; $pass < 2; $pass++ ) {
		foreach ( $pages as $p ) {
			if ( isset( $ids[ $p['slug'] ] ) ) {
				continue;
			}
			$path     = ! empty( $p['parent'] ) ? $p['parent'] . '/' . $p['slug'] : $p['slug'];
			$existing = get_page_by_path( $path );
			$pid      = $existing ? $existing->ID : 0;

			if ( ! $pid ) {
				if ( ! empty( $p['parent'] ) && empty( $ids[ $p['parent'] ] ) ) {
					continue; // wait for parent.
				}
				$pid = wp_insert_post( array(
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'post_title'     => $p['title'],
					'post_name'      => $p['slug'],
					'post_content'   => $p['content'] ?? '',
					'post_parent'    => ! empty( $p['parent'] ) ? $ids[ $p['parent'] ] : 0,
					'comment_status' => 'closed',
				) );
				if ( is_wp_error( $pid ) || ! $pid ) {
					continue;
				}
			} else {
				wp_update_post( array( 'ID' => $pid, 'post_title' => $p['title'], 'post_content' => $p['content'] ?? '' ) );
			}
			$ids[ $p['slug'] ] = $pid;

			update_post_meta( $pid, '_wp_page_template', $p['template'] );
			update_post_meta( $pid, 'page_lang', 'fr' );
			update_post_meta( $pid, 'seo_title', $p['seo_title'] );
			update_post_meta( $pid, 'seo_meta_description', $p['seo_desc'] );
			if ( ! empty( $p['qa'] ) ) {
				update_post_meta( $pid, 'quick_answer', $p['qa'] );
			}
			foreach ( ( $p['meta'] ?? array() ) as $k => $v ) {
				update_post_meta( $pid, $k, $v );
			}

			// Pair EN ↔ FR for hreflang + the toggle.
			$en_id = ( 'home' === $p['en'] ) ? (int) get_option( 'page_on_front' ) : 0;
			if ( ! $en_id && ! empty( $p['en'] ) ) {
				$en = get_page_by_path( $p['en'] );
				$en_id = $en ? $en->ID : 0;
			}
			if ( $en_id ) {
				update_post_meta( $pid, 'alt_pair', $en_id );
				update_post_meta( $en_id, 'alt_pair', $pid );
			}
		}
	}
	update_option( 'artisraw_fr_ver', ARTISRAW_FR_VER );
}
add_action( 'init', 'artisraw_seed_fr_pages', 31 );

function artisraw_fr_page_data() {
	$T_TRUST = 'tpl-trust.php';
	$T_CF    = 'tpl-contact-faq.php';
	$T_ABOUT = 'tpl-about.php';
	$T_PROC  = 'tpl-process.php';
	$T_CAT   = 'tpl-category.php';
	$T_CAT2  = 'tpl-catalogue.php';
	$T_MAG   = 'tpl-magazine.php';
	$T_ACC   = 'tpl-account.php';

	return array(
		array(
			'slug' => 'fr', 'en' => 'home', 'template' => 'tpl-home.php',
			'title' => 'Bois d’olivier en gros — fabricant tunisien & export B2B',
			'seo_title' => 'Bois d’olivier en gros | Fabricant tunisien certifié ISO 9001 | ArtisRaw®',
			'seo_desc'  => 'ArtisRaw, fabricant de bois d’olivier à Sfax (Tunisie), fournit détaillants, distributeurs et marques en marque blanche dans plus de 30 pays. MOQ dès 50 unités.',
			'qa'        => 'ArtisRaw est un fabricant de bois d’olivier certifié ISO 9001:2015 à Sfax, en Tunisie, qui approvisionne détaillants, distributeurs et marques en marque blanche dans plus de 30 pays. Planches, ustensiles, bols et jeux d’échecs en gros — MOQ dès 50 unités, articles en stock expédiés sous 72 heures.',
			'content'   => '<h2>Bois d’olivier fait main, prêt pour le commerce de gros</h2><p>Nous transformons le bois d’olivier Chemlali tunisien en planches à découper, service de table, ustensiles et objets de décoration pour les acheteurs professionnels. Production sous système qualité ISO 9001, finition alimentaire, et documentation d’export complète.</p><h2>Pour votre canal</h2><ul><li><strong>Détaillants</strong> — assortiments prêts à vendre.</li><li><strong>Distributeurs &amp; importateurs</strong> — prix au volume et documents d’export.</li><li><strong>Marques en marque blanche</strong> — gravure de logo et emballage personnalisé.</li></ul>',
		),
		array(
			'slug' => 'fournisseur-bois-olivier-grossiste', 'parent' => 'fr', 'en' => 'olive-wood-wholesale-supplier', 'template' => $T_TRUST,
			'title' => 'Fournisseur de bois d’olivier en gros',
			'seo_title' => 'Fournisseur de bois d’olivier en gros | Export depuis la Tunisie | ArtisRaw®',
			'seo_desc'  => 'Fournisseur B2B de bois d’olivier : MOQ dès 50, articles en stock sous 72 h, marque blanche et documentation d’export (Lacey Act / RDUE). Devis sous 24 h.',
			'qa'        => 'ArtisRaw est un fournisseur de bois d’olivier en gros basé à Sfax (Tunisie) : MOQ dès 50 unités, lignes en stock expédiées sous 72 heures, production personnalisée en 6 à 8 semaines, marque blanche en interne et documentation d’export complète pour les États-Unis et l’Union européenne.',
			'content'   => '<h2>L’offre B2B</h2><ul><li>MOQ dès 50 unités par référence</li><li>Stock expédié sous 72 h ; sur mesure en 6–8 semaines</li><li>Gravure de logo, emballage personnalisé, références prêtes au code-barres</li><li>Incoterms FOB / CIF / DAP / DDP, palettes ISPM-15</li></ul><h2>Confiance à l’import</h2><p>Données de déclaration Lacey Act pour les États-Unis, traçabilité RDUE pour l’Union européenne, et certificat ISO 9001:2015 sur demande.</p>',
		),
		array(
			'slug' => 'services', 'parent' => 'fr', 'en' => 'services', 'template' => $T_TRUST,
			'title' => 'Services B2B en bois d’olivier',
			'seo_title' => 'Services B2B en bois d’olivier | Gros, marque blanche, export | ArtisRaw®',
			'seo_desc'  => 'Six services B2B : production en gros, marque blanche, cadeaux d’affaires, commandes sur mesure, contrôle qualité et accompagnement à l’export.',
			'qa'        => 'ArtisRaw propose six services B2B — production en gros, marque blanche, cadeaux d’affaires, commandes sur mesure, contrôle qualité et accompagnement à l’export — pour les détaillants, distributeurs, acteurs de l’hôtellerie et marques. Un parcours simple, du brief à la réassort.',
			'content'   => '<h2>Nos services principaux</h2><ul><li><strong>Production en gros</strong> — commandes régulières et réassort saisonnier.</li><li><strong>Marque blanche</strong> — gravure, emballage et étiquettes de marque.</li><li><strong>Cadeaux d’affaires</strong> — coffrets, jeux d’échecs et articles personnalisés.</li><li><strong>Commandes sur mesure</strong> — formes, tailles et finitions selon votre marché.</li><li><strong>Contrôle qualité</strong> — inspection et documentation photo par lot.</li><li><strong>Export</strong> — documents commerciaux, palettes ISPM-15 et logistique.</li></ul>',
		),
		array(
			'slug' => 'monde', 'parent' => 'fr', 'en' => 'worldwide', 'template' => $T_TRUST,
			'title' => 'Export mondial : de la Tunisie vers plus de 30 pays',
			'seo_title' => 'Export de bois d’olivier dans le monde | 30+ pays depuis la Tunisie | ArtisRaw®',
			'seo_desc'  => 'ArtisRaw exporte le bois d’olivier depuis Sfax : Amérique du Nord, Europe, Golfe et Asie — avec Lacey Act, RDUE, Incoterms et documentation complète par expédition.',
			'qa'        => 'ArtisRaw exporte du bois d’olivier fait main depuis Sfax (Tunisie) vers plus de 30 pays en Amérique du Nord, en Europe, dans le Golfe et en Asie. Chaque expédition inclut la documentation d’export (facture, liste de colisage, palettes ISPM-15, conformité Lacey Act / RDUE).',
			'content'   => '<h2>Soutien par marché</h2><ul><li><strong>États-Unis &amp; Canada</strong> — données Lacey Act, classification HTS 4419, facturation en USD.</li><li><strong>Europe</strong> — traçabilité RDUE et programmes d’échantillons.</li><li><strong>Golfe (CCG)</strong> — fret consolidé et livraison DDP.</li><li><strong>Asie</strong> — distribution premium et marque blanche prête au détail.</li></ul>',
		),
		array(
			'slug' => 'a-propos', 'parent' => 'fr', 'en' => 'about', 'template' => $T_ABOUT,
			'title' => 'À propos d’ArtisRaw',
			'seo_title' => 'À propos d’ArtisRaw | Fabricant tunisien de bois d’olivier depuis 2019',
			'seo_desc'  => 'Fondée à Sfax en 2019, ArtisRaw allie 25+ artisans et une production « Crafts 4.0 » certifiée ISO 9001 pour les acheteurs professionnels du monde entier.',
			'qa'        => 'ArtisRaw a été fondée à Sfax (Tunisie) en 2019 par Mohamed Bilel Cherif, Ahmed Sakka et Ihsen Triki. L’entreprise associe plus de 25 artisans inscrits à la précision CNC et à un système qualité ISO 9001 — un modèle « Crafts 4.0 » au service de partenaires dans plus de 30 pays.',
			'content'   => '<h2>De la terre de l’olivier</h2><p>Née sur les rives méditerranéennes de la Tunisie, ArtisRaw porte un héritage de 3 000 ans de travail du bois d’olivier aux acheteurs professionnels. Nous transformons le bois d’olivier Chemlali de fin de vie en pièces premium faites main.</p><h2>Crafts 4.0</h2><p>Plus de 25 artisans inscrits, la précision CNC et un système qualité ISO 9001 : le caractère du fait main rencontre une qualité constante, prête à l’export.</p>',
		),
		array(
			'slug' => 'processus', 'parent' => 'fr', 'en' => 'production-process', 'template' => $T_PROC,
			'title' => 'De l’olivier à la commande prête à l’export',
			'seo_title' => 'Processus de fabrication du bois d’olivier | De l’arbre à l’export | ArtisRaw®',
			'seo_desc'  => 'Le processus ArtisRaw : approvisionnement responsable, séchage, découpe, finition à la main, protection alimentaire, contrôle qualité, emballage et préparation à l’export B2B.',
			'qa'        => 'La production d’ArtisRaw va du bois d’olivier Chemlali récupéré à la commande prête à l’export : approvisionnement sous licence, séchage contrôlé, usinage CNC, finition à la main, finition alimentaire huile-cire, puis contrôle qualité, palettes ISPM-15 et export — sous système qualité ISO 9001 à Sfax, en Tunisie.',
			'content'   => '<h2>Une fabrication de bout en bout</h2><p>Chaque pièce démarre d’un approvisionnement responsable et passe par la préparation, le façonnage, la finition artisanale, le traitement alimentaire et le contrôle qualité final. Pour les acheteurs B2B, le processus se poursuit avec la confirmation des références, la validation du MOQ, la marque blanche, l’emballage, les documents d’export et la préparation de l’expédition.</p>',
		),
		array(
			'slug' => 'certifications', 'parent' => 'fr', 'en' => 'certifications', 'template' => $T_TRUST,
			'title' => 'Certifications et preuves qualité',
			'seo_title' => 'Fabricant de bois d’olivier certifié ISO 9001 | Preuves & documents | ArtisRaw®',
			'seo_desc'  => 'Téléchargez le certificat ISO 9001:2015 d’ArtisRaw, la licence forestière #4684, la FDS et les rapports qualité. Chaque affirmation a son document.',
			'qa'        => 'ArtisRaw est le premier fabricant de bois d’olivier certifié ISO 9001:2015 en Tunisie. Les acheteurs peuvent télécharger le certificat ISO, la licence forestière #4684, la FDS alimentaire, les rapports qualité et des documents d’export, et demander des photos de lot avant expédition.',
			'meta'      => array( 'trust_downloads' => '1' ),
			'content'   => '<h2>La confiance est un processus</h2><p>Chaque affirmation de ce site est appuyée par un document téléchargeable. ArtisRaw détient la certification ISO 9001:2015 pour la conception, la production et la commercialisation d’articles en bois d’olivier.</p>',
		),
		array(
			'slug' => 'contact', 'parent' => 'fr', 'en' => 'contact', 'template' => $T_CF,
			'title' => 'Construisons votre collection en bois d’olivier',
			'seo_title' => 'Contact ArtisRaw | Demandez un devis en gros sous 24 h',
			'seo_desc'  => 'Envoyez votre demande B2B : catalogue, devis, marque blanche ou export. Usine : Route Saltania Km 4,5, Sfax, Tunisie. Devis sous 24 heures.',
			'qa'        => 'Contactez ArtisRaw pour des devis en gros, des projets en marque blanche, des cadeaux d’affaires et l’accompagnement à l’export. Utilisez le formulaire pour une proposition sous 24 heures, ou écrivez à contact@artisraw.com.',
			'meta'      => array( 'cf_mode' => 'contact' ),
		),
		/* ---- Catalogue index + categories (tpl-category) ---- */
		array(
			'slug' => 'gros', 'parent' => 'fr', 'en' => 'wholesale', 'template' => $T_CAT,
			'title' => 'Catalogue de bois d’olivier en gros',
			'seo_title' => 'Catalogue de bois d’olivier en gros | 15 familles | ArtisRaw®',
			'seo_desc'  => 'Le catalogue de gros ArtisRaw : planches, ustensiles, bols, mortiers, jeux d’échecs et décoration. Commande par MOQ ; catalogue PDF sur demande.',
			'qa'        => 'Le catalogue de gros ArtisRaw couvre 15 familles de produits en bois d’olivier — des planches à découper et de charcuterie aux cuillères, mortiers, jarres, jeux d’échecs et objets de décoration — chacune avec des références standardisées, des dimensions et un MOQ par famille.',
		),
		array(
			'slug' => 'planches-a-decouper-bois-olivier', 'parent' => 'fr', 'en' => 'wholesale/olive-wood-cutting-boards', 'template' => $T_CAT,
			'title' => 'Planches à découper en bois d’olivier en gros',
			'seo_title' => 'Planches à découper en bois d’olivier en gros | MOQ 50 | ArtisRaw®',
			'seo_desc'  => 'Planches à découper et de charcuterie en bois d’olivier en gros depuis la Tunisie. Finition alimentaire, marque blanche, stock expédié en 72 h. MOQ 50.',
			'qa'        => 'ArtisRaw fournit des planches à découper et de charcuterie en bois d’olivier en gros depuis Sfax, en Tunisie — formes rondes, rectangulaires et rustiques, finition alimentaire à l’huile minérale, MOQ dès 50 unités, avec gravure en marque blanche et lignes en stock expédiées sous 72 heures.',
			'meta'      => array( 'cat_term' => 'cutting-boards' ),
		),
		array(
			'slug' => 'ustensiles-bois-olivier', 'parent' => 'fr', 'en' => 'wholesale/olive-wood-utensils', 'template' => $T_CAT,
			'title' => 'Ustensiles en bois d’olivier en gros',
			'seo_title' => 'Ustensiles en bois d’olivier en gros | Cuillères, spatules & coffrets | ArtisRaw®',
			'seo_desc'  => 'Cuillères, spatules, louches et coffrets d’ustensiles en bois d’olivier en gros. Finition alimentaire, marque blanche, MOQ dès 50. Stock en 72 h.',
			'qa'        => 'ArtisRaw produit des ustensiles en bois d’olivier en gros — cuillères, spatules, louches, pelles et coffrets cadeaux — faits main en Tunisie avec une finition alimentaire, MOQ dès 50 (coffrets dès 100), gravure en marque blanche et expédition sous 72 heures sur les lignes en stock.',
			'meta'      => array( 'cat_term' => 'utensils' ),
		),
		array(
			'slug' => 'bols-service-bois-olivier', 'parent' => 'fr', 'en' => 'wholesale/olive-wood-bowls-serveware', 'template' => $T_CAT,
			'title' => 'Bols & service de table en bois d’olivier en gros',
			'seo_title' => 'Bols & service de table en bois d’olivier en gros | MOQ 50 | ArtisRaw®',
			'seo_desc'  => 'Bols, plats de service, mortiers et coupelles en bois d’olivier en gros depuis la Tunisie. Alimentaire, marque blanche, MOQ 50, expédié en 72 h.',
			'qa'        => 'ArtisRaw fournit des bols et du service de table en bois d’olivier en gros — saladiers, mortiers et pilons, et coupelles — faits main en Tunisie, finition alimentaire, MOQ dès 50 unités, avec options de marque blanche et lignes en stock expédiées sous 72 heures.',
			'meta'      => array( 'cat_term' => 'bowls-serveware' ),
		),
		array(
			'slug' => 'jeux-echecs-bois-olivier', 'parent' => 'fr', 'en' => 'wholesale/olive-wood-chess-sets', 'template' => $T_CAT,
			'title' => 'Jeux d’échecs en bois d’olivier en gros',
			'seo_title' => 'Jeux d’échecs en bois d’olivier en gros | Faits main en Tunisie | ArtisRaw®',
			'seo_desc'  => 'Jeux d’échecs et échiquiers en bois d’olivier faits main en gros depuis la Tunisie. Cadeaux et détail premium, marque blanche, MOQ dès 50.',
			'qa'        => 'ArtisRaw produit des jeux d’échecs et échiquiers en bois d’olivier en gros — faits main à Sfax avec le contraste naturel du Chemlali, idéals pour le cadeau et le détail premium. MOQ dès 50, avec gravure et emballage en marque blanche ; demandez le tarif pour la gamme actuelle.',
			'meta'      => array( 'cat_term' => 'chess-sets' ),
		),
		array(
			'slug' => 'decoration-bain-bois-olivier', 'parent' => 'fr', 'en' => 'wholesale/olive-wood-decor-bath', 'template' => $T_CAT,
			'title' => 'Décoration & bain en bois d’olivier en gros',
			'seo_title' => 'Décoration & bain en bois d’olivier en gros | MOQ 50 | ArtisRaw®',
			'seo_desc'  => 'Décoration et bain en bois d’olivier en gros : plateaux, jarres, porte-savons et accessoires depuis la Tunisie. Finition alimentaire, marque blanche, MOQ dès 50.',
			'qa'        => 'ArtisRaw fournit des produits de décoration et de bain en bois d’olivier en gros — plateaux, jarres, porte-savons et accessoires lifestyle — faits main en Tunisie avec une finition naturelle. MOQ dès 50 unités, avec options de marque blanche ; demandez le tarif pour la gamme complète.',
			'meta'      => array( 'cat_term' => 'decor-bath' ),
		),
		array(
			'slug' => 'marque-blanche-bois-olivier', 'parent' => 'fr', 'en' => 'private-label-olive-wood', 'template' => $T_CAT,
			'title' => 'Bois d’olivier en marque blanche',
			'seo_title' => 'Bois d’olivier en marque blanche | Gravure & emballage personnalisé | ArtisRaw®',
			'seo_desc'  => 'Vendez le bois d’olivier sous votre marque : gravure de logo en interne, emballage personnalisé, références prêtes au code-barres et développement produit. MOQ dès 50.',
			'qa'        => 'ArtisRaw propose la marque blanche en bois d’olivier en interne : gravure de logo, emballage personnalisé, étiquettes de détail et références prêtes au code-barres, ainsi que le développement produit pour votre marché. MOQ dès 50 unités, avec échantillons validés avant production et documentation d’export complète.',
			'meta'      => array( 'cat_mode' => 'private' ),
		),

		/* ---- Trust / proof (tpl-trust) ---- */
		array(
			'slug' => 'controle-qualite', 'parent' => 'fr', 'en' => 'quality-control', 'template' => $T_TRUST,
			'title' => 'Contrôle qualité du bois d’olivier',
			'seo_title' => 'Contrôle qualité du bois d’olivier | Inspection pièce par pièce | ArtisRaw®',
			'seo_desc'  => 'Comment ArtisRaw maîtrise la qualité : inspection pièce par pièce, ≥96 % de conformité au premier passage, ≤0,5 % de retours, documentation photo par lot.',
			'qa'        => 'ArtisRaw applique un contrôle qualité pièce par pièce sous système ISO 9001 : chaque pièce est inspectée pour la finition, les dimensions et le grain, avec ≥96 % de conformité au premier passage et ≤0,5 % de retours. Documentation photo par lot et contrôles d’emballage disponibles avant chaque expédition.',
			'content'   => '<h2>Une inspection à chaque étape</h2><p>La qualité se construit, elle ne se contrôle pas seulement à la fin. La matière première est triée, le séchage est maîtrisé, et l’usinage comme la finition à la main comportent leurs points de contrôle. Chaque pièce finie passe une inspection unitaire avant emballage.</p><h2>Les chiffres</h2><ul><li>≥96 % de conformité au premier passage</li><li>≤0,5 % de taux de retour</li><li>Documentation photo par lot sur demande</li></ul>',
		),
		array(
			'slug' => 'expedition-logistique', 'parent' => 'fr', 'en' => 'shipping-logistics', 'template' => $T_TRUST,
			'title' => 'Logistique mondiale : de Sfax à votre marché',
			'seo_title' => 'Export & expédition du bois d’olivier | Incoterms, délais, 30+ pays | ArtisRaw®',
			'seo_desc'  => 'FOB Tunisie, CIF, DAP ou DDP. Stock expédié en 72 h ; sur mesure en 6–8 semaines. Air 5–12 jours, mer 25–40 jours. Palettes ISPM-15 et documents d’export complets.',
			'qa'        => 'ArtisRaw exporte ses produits en bois d’olivier vers plus de 30 pays en FOB Tunisie, CIF, DAP ou DDP. Les commandes en stock partent sous 72 heures ; la production sur mesure prend 6 à 8 semaines. Transit : 5 à 12 jours par avion, 25 à 40 jours par mer, sur palettes ISPM-15 avec documentation d’export complète.',
			'content'   => '<h2>Délais & Incoterms</h2><p>Fret aérien 5–12 jours ; mer (LCL/FCL) 25–40 jours. Incoterms FOB / CIF / DAP / DDP selon la destination. MOQ dès 50.</p><h2>Soutien par destination</h2><ul><li><strong>États-Unis & Canada</strong> — dossier Lacey Act, classification HTS 4419, facturation en USD.</li><li><strong>Europe</strong> — traçabilité RDUE et échantillons.</li><li><strong>Golfe & Asie</strong> — fret consolidé et DDP sur demande.</li></ul><h2>Emballage</h2><p>Cartons prêts à l’export sur palettes ISPM-15, emballage sans plastique lorsque c’est possible, et emballage prêt au détail pour les commandes en marque blanche.</p>',
		),
		array(
			'slug' => 'comment-commander', 'parent' => 'fr', 'en' => 'how-to-order', 'template' => $T_TRUST,
			'title' => 'Comment commander du bois d’olivier en gros',
			'seo_title' => 'Comment commander du bois d’olivier en gros | MOQ & échantillons | ArtisRaw®',
			'seo_desc'  => 'Commandez en cinq étapes : brief, devis avec MOQ, validation des échantillons, production avec contrôle qualité, puis export. MOQ dès 50.',
			'qa'        => 'Commander chez ArtisRaw se fait en cinq étapes : envoi d’un brief acheteur, réception d’un devis avec MOQ et tarifs, validation des échantillons (coût déduit de la première commande), production avec points de contrôle qualité, puis export avec documentation complète. MOQ dès 50 unités ; devis sous 24 heures.',
			'content'   => '<h2>Cinq étapes vers votre première commande</h2><ol><li><strong>Brief acheteur</strong> — marché, catégories, quantités et date cible.</li><li><strong>Devis & MOQ</strong> — tarifs, options d’emballage et infos export sous 24 heures.</li><li><strong>Échantillon / marquage</strong> — échantillons ou épreuves de marque blanche validés avant production.</li><li><strong>Production & contrôle qualité</strong> — fabrication avec points de contrôle et photos de lot.</li><li><strong>Export</strong> — palettes ISPM-15, facture commerciale et liste de colisage.</li></ol><p>Des échantillons sont disponibles, leur coût étant déduit de votre première commande de production.</p>',
		),
		array(
			'slug' => 'documents-telechargements', 'parent' => 'fr', 'en' => 'references', 'template' => $T_TRUST,
			'title' => 'Documents & téléchargements bois d’olivier',
			'seo_title' => 'Documents & téléchargements bois d’olivier | ISO, FDS, conformité | ArtisRaw®',
			'seo_desc'  => 'Centre de téléchargement : certificat ISO 9001, licence forestière #4684, FDS de finition, dossier conformité (Lacey/RDUE), tarif et documents d’export.',
			'qa'        => 'Le centre de téléchargement ArtisRaw réunit les preuves derrière chaque affirmation : certificat ISO 9001:2015, licence forestière #4684, FDS alimentaire, dossier conformité Lacey/RDUE, tarif de gros et documents d’export — accessibles aux acheteurs professionnels.',
			'meta'      => array( 'trust_downloads' => '1' ),
			'content'   => '<p>Chaque affirmation de ce site renvoie ici. Téléchargez le document dont vous avez besoin, ou demandez des photos de lot pour une commande précise.</p>',
		),
		array(
			'slug' => 'durabilite', 'parent' => 'fr', 'en' => 'sustainability', 'template' => $T_TRUST,
			'title' => 'Durable par origine',
			'seo_title' => 'Bois d’olivier durable | Bois récupéré & reforestation | ArtisRaw®',
			'seo_desc'  => 'ArtisRaw travaille le bois d’olivier récupéré en fin de vie, minimise les déchets et parraine la reforestation via trees.org — avec traçabilité RDUE complète.',
			'qa'        => 'La durabilité d’ArtisRaw est intégrée à l’approvisionnement : nous utilisons du bois d’olivier Chemlali récupéré en fin de vie, exploitons chaque partie de l’arbre pour minimiser les déchets et parrainons la reforestation via trees.org. Les acheteurs de l’UE reçoivent une traçabilité RDUE complète.',
			'content'   => '<h2>Une matière responsable</h2><p>Nous travaillons uniquement du bois d’olivier récupéré, en fin de vie — des arbres ayant dépassé l’âge de production — et en utilisons chaque partie pour réduire les déchets. Les finitions sont alimentaires et sans revêtements synthétiques.</p><h2>Rendre à la terre</h2><p>Nous parrainons la reforestation via trees.org et fournissons une traçabilité RDUE complète pour les importateurs de l’UE.</p>',
		),
		array(
			'slug' => 'fournisseur-bois-olivier-usa', 'parent' => 'fr', 'en' => 'olive-wood-supplier-usa', 'template' => $T_TRUST,
			'title' => 'Fournisseur de bois d’olivier pour les États-Unis',
			'seo_title' => 'Fournisseur de bois d’olivier USA | Conforme Lacey Act, import rapide | ArtisRaw®',
			'seo_desc'  => 'Importateurs américains : bois d’olivier en gros avec données Lacey Act, classification HTS 4419, facturation en USD et fret aérien en 5–12 jours. MOQ 50.',
			'qa'        => 'ArtisRaw fournit aux acheteurs américains des planches, du service de table et des ustensiles en bois d’olivier — avec données de déclaration Lacey Act, classification HTS 4419 et facturation en USD. Le fret aérien prend 5 à 12 jours ; MOQ dès 50, avec gravure en marque blanche et contrôle qualité ISO 9001.',
			'content'   => '<h2>Pensé pour les importateurs américains</h2><p>Nous préparons les documents attendus par votre courtier en douane et facturons en USD. Le fret aérien atteint les États-Unis en 5–12 jours ; la mer en 25–40.</p><ul><li>Données de déclaration Lacey Act (PPQ 505) par expédition</li><li>Aide à la classification HTS 4419</li><li>Facturation en USD et options DDP</li></ul>',
		),
		array(
			'slug' => 'fournisseur-bois-olivier-europe', 'parent' => 'fr', 'en' => 'olive-wood-supplier-europe', 'template' => $T_TRUST,
			'title' => 'Fournisseur de bois d’olivier pour l’Europe',
			'seo_title' => 'Fournisseur de bois d’olivier Europe | Conforme RDUE, fret rapide | ArtisRaw®',
			'seo_desc'  => 'Importateurs européens : bois d’olivier en gros avec traçabilité RDUE, facturation en EUR et fret mer/air depuis la Tunisie. MOQ dès 50, marque blanche disponible.',
			'qa'        => 'ArtisRaw fournit aux acheteurs européens des planches, du service de table et des ustensiles en bois d’olivier — avec traçabilité RDUE, facturation en EUR et une porte d’entrée à Marseille. Le fret maritime prend 25 à 40 jours, l’aérien 5 à 12 ; MOQ dès 50, avec gravure en marque blanche et contrôle qualité ISO 9001.',
			'content'   => '<h2>Pensé pour les importateurs de l’UE</h2><ul><li>Documentation de diligence RDUE.</li><li>Facturation en EUR et aide à l’étiquetage conforme.</li><li>Porte d’entrée à Marseille ; expérience du détail spécialisé au Royaume-Uni et en France.</li></ul>',
		),
		array(
			'slug' => 'guide-bois-olivier', 'parent' => 'fr', 'en' => 'olive-wood', 'template' => $T_TRUST,
			'title' => 'Le guide du bois d’olivier',
			'seo_title' => 'Guide du bois d’olivier | Approvisionnement, entretien & conformité | ArtisRaw®',
			'seo_desc'  => 'Le guide ArtisRaw du bois d’olivier pour les acheteurs B2B : propriétés du bois Chemlali, entretien alimentaire, conformité (Lacey Act, RDUE) et approvisionnement.',
			'qa'        => 'Le Guide du bois d’olivier ArtisRaw est un pôle de connaissances B2B sur le bois d’olivier Chemlali : propriétés de la matière, entretien alimentaire et conformité à l’import (Lacey Act, RDUE). Des articles approfondis, chacun avec une réponse rapide, des données et des sources, y sont publiés régulièrement.',
			'meta'      => array( 'trust_articles' => '1' ),
			'content'   => '<h2>Ce que vous y trouverez</h2><p>Des réponses concrètes et sourcées pour les acheteurs professionnels — du pourquoi le bois d’olivier Chemlali résiste aux marques de couteau jusqu’à l’import aux États-Unis et dans l’UE. De nouveaux articles sont ajoutés régulièrement.</p>',
		),
		array(
			'slug' => 'conformite', 'parent' => 'fr', 'en' => 'compliance', 'template' => $T_TRUST,
			'title' => 'Conformité à l’import du bois d’olivier',
			'seo_title' => 'Conformité à l’import du bois d’olivier | Lacey Act & RDUE | ArtisRaw®',
			'seo_desc'  => 'Comment ArtisRaw soutient des imports conformes : données de déclaration Lacey Act pour les USA et diligence RDUE pour l’UE, avec documents par expédition.',
			'qa'        => 'ArtisRaw soutient des imports conformes de bois d’olivier avec les données de déclaration Lacey Act pour les États-Unis et la documentation de diligence RDUE pour l’Union européenne — fournies par expédition, avec la facture commerciale, la liste de colisage et la conformité des palettes ISPM-15.',
			'content'   => '<h2>La conformité par destination</h2><ul><li><strong>Lacey Act (USA)</strong> — espèce, pays de récolte, quantité et valeur par expédition.</li><li><strong>RDUE (UE)</strong> — géolocalisation, preuve de récolte légale et traçabilité.</li></ul>',
		),
		array(
			'slug' => 'conformite-lacey-act', 'parent' => 'fr', 'en' => 'compliance/lacey-act', 'template' => $T_TRUST,
			'title' => 'Conformité Lacey Act pour l’import de bois d’olivier',
			'seo_title' => 'Bois d’olivier & Lacey Act | Données de déclaration | ArtisRaw®',
			'seo_desc'  => 'ArtisRaw fournit les données de déclaration Lacey Act (PPQ 505) pour l’import de bois d’olivier aux USA : Olea europaea, récolté en Tunisie, avec aide HTS 4419.',
			'qa'        => 'Pour les imports américains, ArtisRaw fournit les données de déclaration Lacey Act (formulaire PPQ 505) : genre et espèce (Olea europaea), pays de récolte (Tunisie), quantité et valeur — ainsi que l’aide à la classification HTS 4419 et les documents d’export complets à chaque expédition.',
			'content'   => '<h2>Ce que nous fournissons</h2><ul><li>Champs de déclaration PPQ 505 par expédition.</li><li>Aide à la classification HTS 4419.</li><li>Facture commerciale, liste de colisage, palettes ISPM-15.</li></ul><p>Fret aérien 5–12 jours ; mer 25–40. Facturation en USD avec DDP disponible.</p>',
		),
		array(
			'slug' => 'rdue', 'parent' => 'fr', 'en' => 'compliance/eudr', 'template' => $T_TRUST,
			'title' => 'Conformité RDUE pour le bois d’olivier',
			'seo_title' => 'Conformité RDUE bois d’olivier | Traçabilité pour acheteurs UE | ArtisRaw®',
			'seo_desc'  => 'Diligence RDUE pour le bois d’olivier : géolocalisation de la récolte, preuve de récolte légale et traçabilité pour les importateurs UE. Bois Chemlali récupéré, licence #4684.',
			'qa'        => 'Pour les acheteurs de l’UE, ArtisRaw fournit la documentation de conformité RDUE : géolocalisation de la zone de récolte, preuve de récolte légale (licence forestière #4684) et traçabilité de la chaîne pour le bois d’olivier Chemlali récupéré — fournie par expédition pour votre diligence raisonnable.',
			'content'   => '<h2>Prêt pour le RDUE</h2><ul><li>Bois d’olivier Chemlali récupéré, en fin de vie, de sources licenciées.</li><li>Géolocalisation et preuve de récolte légale.</li><li>Déclarations de diligence par expédition.</li></ul>',
		),

		/* ---- Catalogue & Magazine ---- */
		array(
			'slug' => 'catalogue', 'parent' => 'fr', 'en' => 'catalogue', 'template' => $T_CAT2,
			'title' => 'Catalogue de bois d’olivier : 15 familles de produits',
			'seo_title' => 'Catalogue de bois d’olivier | 15 familles de produits en gros | ArtisRaw®',
			'seo_desc'  => 'Parcourez les 15 familles de produits en bois d’olivier d’ArtisRaw et demandez le catalogue PDF complet et la liste de prix. MOQ par famille ; finition alimentaire ; marque blanche.',
			'qa'        => 'Le catalogue ArtisRaw couvre 15 familles de produits en bois d’olivier — planches, cuillères, ustensiles, mortiers, bols, paniers, jarres, arts de la table, jeux d’échecs, coffrets cadeaux, décoration, bain, mosaïque et artisanat naturel — chacune avec ses références, dimensions et MOQ par famille. Demandez le catalogue PDF complet et la liste de prix ci-dessous.',
		),
		array(
			'slug' => 'magazine', 'parent' => 'fr', 'en' => 'magazine', 'template' => $T_MAG,
			'title' => 'Le magazine ArtisRaw',
			'seo_title' => 'Magazine ArtisRaw | Histoires de bois d’olivier pour acheteurs B2B',
			'seo_desc'  => 'Notes d’atelier, science des matériaux, explications de conformité et reportages de salons d’ArtisRaw — des histoires de bois d’olivier pour les acheteurs professionnels.',
			'qa'        => 'Le magazine ArtisRaw publie des histoires pour les acheteurs professionnels : notes d’atelier, science du bois Chemlali, explications de conformité à l’import (Lacey Act, RDUE) et reportages de salons — chaque article relu par notre directeur du design et daté.',
		),

		/* ---- Contact / FAQ (tpl-contact-faq) ---- */
		array(
			'slug' => 'faq', 'parent' => 'fr', 'en' => 'faq', 'template' => $T_CF,
			'title' => 'Questions fréquentes des acheteurs B2B',
			'seo_title' => 'FAQ bois d’olivier en gros | MOQ, échantillons, marque blanche, alimentaire',
			'seo_desc'  => 'Des réponses claires avant de demander un devis : quantités minimales, échantillons, délais, marque blanche, certifications, sécurité alimentaire et documents d’export.',
			'qa'        => 'Les questions de gros les plus fréquentes : le MOQ démarre à 50 unités (selon la référence) ; des échantillons sont disponibles, leur coût déduit de la première commande ; les articles en stock partent en 72 heures et la production sur mesure prend 6 à 8 semaines ; la gravure en marque blanche est réalisée en interne ; chaque produit utilise des finitions alimentaires avec FDS disponible.',
			'meta'      => array( 'cf_mode' => 'faq' ),
		),
		array(
			'slug' => 'demander-un-devis', 'parent' => 'fr', 'en' => 'request-quote', 'template' => $T_CF,
			'title' => 'Demander un devis de gros',
			'seo_title' => 'Demander un devis | Bois d’olivier en gros & marque blanche | ArtisRaw®',
			'seo_desc'  => 'Obtenez un devis de bois d’olivier en gros sous 24 heures : tarif, MOQ, prix et documentation d’import. Gravure en marque blanche disponible. MOQ 50.',
			'qa'        => 'Demandez un devis de bois d’olivier en gros et ArtisRaw répond sous 24 heures avec votre tarif, MOQ, prix et documentation d’import (Lacey Act / RDUE). Indiquez votre destination, vos catégories et vos quantités — la gravure en marque blanche et les échantillons sont disponibles.',
			'meta'      => array( 'cf_mode' => 'quote' ),
		),

		/* ---- Account & legal ---- */
		array(
			'slug' => 'espace-client', 'parent' => 'fr', 'en' => 'wholesale-account', 'template' => $T_ACC,
			'title' => 'Espace client de gros',
			'seo_title' => 'Espace client de gros | Commandez le bois d’olivier en direct | ArtisRaw®',
			'seo_desc'  => 'Ouvrez un compte de gros ArtisRaw : tarifs B2B validés, historique des références, réassorts plus rapides et marque blanche. MOQ dès 50 unités.',
			'qa'        => 'Un compte de gros donne aux acheteurs professionnels validés un accès direct aux tarifs B2B, aux références enregistrées, à des réassorts plus rapides et au soutien marque blanche. Inscrivez-vous avec les informations de votre société et votre marché ; devis et conditions confirmés sous 24 heures.',
			'meta'      => array( 'seo_noindex' => '1' ),
			'content'   => '<h2>Pourquoi ouvrir un compte</h2><ul><li>Tarifs de gros validés</li><li>Références et historique de commandes enregistrés</li><li>Réassorts et renouvellements plus rapides</li><li>Soutien marque blanche et cadeaux d’affaires</li></ul><p>Inscrivez-vous via le formulaire de devis avec le nom de votre société, votre marché de destination et vos quantités estimées.</p>',
		),
		array(
			'slug' => 'confidentialite', 'parent' => 'fr', 'en' => 'privacy', 'template' => 'default',
			'title' => 'Politique de confidentialité',
			'seo_title' => 'Politique de confidentialité | ArtisRaw®',
			'seo_desc'  => 'Comment ArtisRaw collecte et utilise les informations que vous partagez via nos formulaires de devis et e-mails — utilisées uniquement pour préparer et traiter votre demande de gros.',
			'content'   => '<p>ArtisRaw utilise les informations que vous transmettez via nos formulaires et e-mails uniquement pour préparer votre devis et traiter votre demande de gros. Nous ne vendons pas vos données. Des champs masqués enregistrent la page et la source de campagne afin de répondre en contexte.</p><p>Pour demander l’accès à vos données ou leur suppression, écrivez à <a href="mailto:contact@artisraw.com">contact@artisraw.com</a>.</p>',
		),
	);
}
