<?php
/**
 * Phase 10 — French layer (lightweight, plugin-free i18n) (SPEC §9).
 *
 * French pages are real pages (meta page_lang=fr) that reuse the same templates;
 * their UI chrome is translated at runtime via the `gettext` filter against a PHP
 * dictionary (no .mo tooling needed), and their rich body copy lives in
 * post_content. EN↔FR counterparts are paired with the `alt_pair` meta, which
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

	if ( is_singular() ) {
		$id   = get_queried_object_id();
		$pair = (int) get_post_meta( $id, 'alt_pair', true );
		if ( artisraw_is_fr() ) {
			$fr_url = get_permalink( $id );
			$en_url = ( $pair && get_post_status( $pair ) === 'publish' ) ? get_permalink( $pair ) : $en_home;
		} else {
			$en_url = get_permalink( $id );
			$fr_url = ( $pair && get_post_status( $pair ) === 'publish' ) ? get_permalink( $pair ) : $fr_home;
		}
	}
	return array( 'current' => $current, 'en' => $en_url, 'fr' => $fr_url );
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
	$d = array(
		// Header / nav
		'Catalogue'                  => 'Catalogue',
		'Wholesale'                  => 'Vente en gros',
		'Services'                   => 'Services',
		'Why ArtisRaw'               => 'Pourquoi ArtisRaw',
		'Guide'                      => 'Guide',
		'Magazine'                   => 'Magazine',
		'Full Catalogue (PDF)'       => 'Catalogue complet (PDF)',
		'Compliance (Lacey / EUDR)'  => 'Conformité (Lacey / RDUE)',
		'Contact'                    => 'Contact',
		'Request Quote'              => 'Demander un devis',
		'Wholesale Login'            => 'Espace client',
		'Cutting Boards'             => 'Planches à découper',
		'Utensils'                   => 'Ustensiles',
		'Bowls & Serveware'          => 'Bols & service de table',
		'Chess Sets'                 => 'Jeux d’échecs',
		'Décor & Bath'               => 'Décoration & bain',
		'View all categories'        => 'Voir toutes les catégories',
		'Wholesale Hub'              => 'Pôle grossiste',
		'How to Order'               => 'Comment commander',
		'Shipping & Logistics'       => 'Expédition & logistique',
		'Worldwide / Export'         => 'International / export',
		'References & Downloads'     => 'Références & téléchargements',
		'Services Overview'          => 'Aperçu des services',
		'Private Label'              => 'Marque blanche',
		'Wholesale Production'       => 'Production en gros',
		'About ArtisRaw'             => 'À propos d’ArtisRaw',
		'Our Process'                => 'Notre processus',
		'Certifications'             => 'Certifications',
		'Quality Control'            => 'Contrôle qualité',
		'Sustainability'             => 'Durabilité',
		'Open menu'                  => 'Ouvrir le menu',
		'Close menu'                 => 'Fermer le menu',
		'Primary'                    => 'Principal',
		'ArtisRaw — home'            => 'ArtisRaw — accueil',
		'Skip to content'            => 'Aller au contenu',
		'Language'                   => 'Langue',
		'French coming soon'         => 'Version française',
		/* translators: nav toggle */
		'Toggle %s submenu'          => 'Ouvrir le sous-menu %s',

		// Buttons / CTAs
		'Request Line-Sheet & Compliance Pack' => 'Demander le tarif & le dossier conformité',
		'Explore wholesale'          => 'Découvrir la vente en gros',
		'Learn more'                 => 'En savoir plus',
		'Request a quote'            => 'Demander un devis',
		'Our sustainability approach' => 'Notre démarche durable',

		// Footer
		'Olive wood manufacturer &amp; B2B supplier — handmade in Sfax, Tunisia since 2019.' => 'Fabricant de bois d’olivier & fournisseur B2B — fait main à Sfax, Tunisie, depuis 2019.',
		'Footer'                     => 'Pied de page',
		'WhatsApp'                   => 'WhatsApp',
		'All rights reserved.'       => 'Tous droits réservés.',
		'Lacey Act &amp; EUDR documentation available on request.' => 'Documentation Lacey Act & RDUE disponible sur demande.',
		'Olive Wood Guide'           => 'Guide du bois d’olivier',
		'About'                      => 'À propos',
		'Request a Quote'            => 'Demander un devis',

		// Newsletter
		'Stay in the know'           => 'Restez informé',
		'B2B updates, product launches and catalogue news — no more than monthly.' => 'Actualités B2B, nouveautés et catalogue — au maximum une fois par mois.',
		'Subscribe'                  => 'S’abonner',
		'you@company.com'            => 'vous@entreprise.com',

		// Breadcrumb
		'Home'                       => 'Accueil',
		'Breadcrumb'                 => 'Fil d’Ariane',

		// Trust strip chips
		'ISO 9001:2015'              => 'ISO 9001:2015',
		'30+ countries'              => '30+ pays',
		'MOQ 50'                     => 'MOQ 50',
		'Ships in 72 h'              => 'Expédié en 72 h',
		'Unit-by-unit QC'            => 'Contrôle pièce par pièce',
		'Lacey Act data ready'       => 'Données Lacey Act prêtes',
		'EUDR traceability'          => 'Traçabilité RDUE',

		// tpl-trust chrome
		'Download centre'            => 'Centre de téléchargement',
		'Every claim has a document. Each download is logged so we can keep the set current.' => 'Chaque affirmation a son document. Chaque téléchargement est enregistré pour garder le dossier à jour.',
		'Updated'                    => 'Mis à jour',

		// Quote form
		'Get a quote within 24 h'    => 'Un devis sous 24 h',
		'Request your line-sheet & compliance pack' => 'Demandez votre tarif & dossier conformité',
		'Work email'                 => 'E-mail professionnel',
		'Company'                    => 'Société',
		'Destination country'        => 'Pays de destination',
		'Select…'                    => 'Choisir…',
		'Target delivery date'       => 'Date de livraison souhaitée',
		'We reply within one business day. Your details are used only to prepare your quote —' => 'Nous répondons sous un jour ouvré. Vos informations servent uniquement à préparer votre devis —',
		'privacy'                    => 'confidentialité',
		'Thanks — your line-sheet is on its way to' => 'Merci — votre tarif est en route vers',
		'Tell us a little more to speed up your quote (optional).' => 'Donnez-nous quelques détails pour accélérer votre devis (facultatif).',
		'Monthly volume'             => 'Volume mensuel',
		'Preferred ship mode'        => 'Mode d’expédition préféré',
		'Documents requested'        => 'Documents demandés',
		'Line-sheet'                 => 'Tarif (line-sheet)',
		'Compliance pack (Lacey/EUDR)' => 'Dossier conformité (Lacey/RDUE)',
		'ISO 9001 certificate'       => 'Certificat ISO 9001',
		'Finish MSDS'                => 'FDS de la finition',
		'I need logo engraving / private label' => 'J’ai besoin de gravure de logo / marque blanche',
		'I’ll need a broker / DDP delivery' => 'J’aurai besoin d’un transitaire / livraison DDP',
		'Send these details'         => 'Envoyer ces informations',
		'Browse the catalogue meanwhile' => 'Parcourir le catalogue en attendant',
		'Got it — we’ll include these in your quote. Talk soon.' => 'C’est noté — nous les ajouterons à votre devis. À bientôt.',
		'This quick form needs JavaScript. You can also email %1$s or use our %2$s.' => 'Ce formulaire nécessite JavaScript. Vous pouvez aussi écrire à %1$s ou utiliser notre %2$s.',
		'contact page'               => 'page contact',

		// Contact NAP
		'Factory &amp; contact'      => 'Usine & contact',
		'Quotes within 24 hours'     => 'Devis sous 24 heures',
		'Quick answer'               => 'Réponse rapide',
	);
	return $d;
}

/* =========================================================================
 * French page seeding (idempotent). Runs after the EN seeder (priority 31) so
 * EN counterparts exist for pairing. Pages reuse the prose templates; the rich
 * French body lives in post_content. Bump ARTISRAW_FR_VER to add/update pages.
 * ====================================================================== */
define( 'ARTISRAW_FR_VER', 1 );

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

	return array(
		array(
			'slug' => 'fr', 'en' => 'home', 'template' => $T_TRUST,
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
			'slug' => 'a-propos', 'parent' => 'fr', 'en' => 'about', 'template' => $T_TRUST,
			'title' => 'À propos d’ArtisRaw',
			'seo_title' => 'À propos d’ArtisRaw | Fabricant tunisien de bois d’olivier depuis 2019',
			'seo_desc'  => 'Fondée à Sfax en 2019, ArtisRaw allie 25+ artisans et une production « Crafts 4.0 » certifiée ISO 9001 pour les acheteurs professionnels du monde entier.',
			'qa'        => 'ArtisRaw a été fondée à Sfax (Tunisie) en 2019 par Mohamed Bilel Cherif, Ahmed Sakka et Ihsen Triki. L’entreprise associe plus de 25 artisans inscrits à la précision CNC et à un système qualité ISO 9001 — un modèle « Crafts 4.0 » au service de partenaires dans plus de 30 pays.',
			'content'   => '<h2>De la terre de l’olivier</h2><p>Née sur les rives méditerranéennes de la Tunisie, ArtisRaw porte un héritage de 3 000 ans de travail du bois d’olivier aux acheteurs professionnels. Nous transformons le bois d’olivier Chemlali de fin de vie en pièces premium faites main.</p><h2>Crafts 4.0</h2><p>Plus de 25 artisans inscrits, la précision CNC et un système qualité ISO 9001 : le caractère du fait main rencontre une qualité constante, prête à l’export.</p>',
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
	);
}
