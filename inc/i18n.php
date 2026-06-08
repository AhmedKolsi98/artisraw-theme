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

		// Art Direction (quote block, footer trust, arrow links)
		'Wholesale inquiries'        => 'Demandes de gros',
		'Request a Quote'            => 'Demander un devis',
		'Quote within 24 h'          => 'Devis sous 24 h',
		'Low MOQ'                    => 'MOQ réduit',
		'Custom branding'            => 'Personnalisation de marque',
			// Header — client login + language switcher
			'Client Login'               => 'Espace client',
			'English'                    => 'English',
			'Français'                   => 'Français',

			// Inner-page photo hero (shared)
			'Our process'                => 'Notre processus',
			'Premium olive wood, crafted in sustainability.' => 'Bois d’olivier premium, façonné dans la durabilité.',

			/* ---- About (tpl-about) ---- */
			'ArtisRaw olive wood — handmade product' => 'Bois d’olivier ArtisRaw — produit fait main',
			'The ArtisRaw world in four visuals' => 'L’univers ArtisRaw en quatre images',
			'Olive grove — responsible sourcing' => 'Oliveraie — approvisionnement responsable',
			'Responsible olive wood sourcing' => 'Approvisionnement responsable en bois d’olivier',
			'ArtisRaw workshop production in Sfax' => 'Production à l’atelier ArtisRaw de Sfax',
			'Own factory in Sfax'        => 'Notre usine à Sfax',
			'Dense Chemlali olive wood grain' => 'Grain dense du bois d’olivier Chemlali',
			'Chemlali dense grain'       => 'Grain dense Chemlali',
			'Wholesale collections ready for export' => 'Collections de gros prêtes à l’export',
			'Premium wholesale collections' => 'Collections de gros premium',
			'Premium quality &amp; craftsmanship' => 'Qualité premium & savoir-faire',
			'Crafted from premium Tunisian olive wood, each piece reveals the unique character, beauty and durability of nature.' => 'Façonnée dans un bois d’olivier tunisien premium, chaque pièce révèle le caractère, la beauté et la durabilité uniques de la nature.',
			'Excellence &amp; reliability' => 'Excellence & fiabilité',
			'Every product undergoes careful selection, quality control and food-safe finishing to meet the expectations of global buyers.' => 'Chaque produit fait l’objet d’une sélection rigoureuse, d’un contrôle qualité et d’une finition alimentaire pour répondre aux attentes des acheteurs du monde entier.',
			'Mediterranean heritage &amp; innovation' => 'Héritage méditerranéen & innovation',
			'Inspired by generations of craftsmanship, we combine traditional expertise with modern production standards.' => 'Inspirés par des générations de savoir-faire, nous associons l’expertise traditionnelle aux standards de production modernes.',
			'Sustainability &amp; impact' => 'Durabilité & impact',
			'We transform reclaimed olive wood into lasting creations, supporting responsible sourcing, local artisans and a more sustainable future.' => 'Nous transformons le bois d’olivier récupéré en créations durables, au service d’un approvisionnement responsable, des artisans locaux et d’un avenir plus durable.',
			'More than a workshop — a dedicated production facility' => 'Plus qu’un atelier — une unité de production dédiée',
			'ArtisRaw combines traditional hand tools with professional woodworking equipment, producing consistent quality at scale while keeping the authentic handmade character of olive wood.' => 'ArtisRaw associe outils traditionnels et équipements professionnels du bois pour produire une qualité constante à grande échelle, tout en préservant le caractère authentique du fait main.',
			'Location'                   => 'Emplacement',
			'Sfax'                       => 'Sfax',
			'Route Saltania Km 4.5, Tunisia, in the Mediterranean olive region.' => 'Route Saltania Km 4,5, Tunisie, au cœur de la région oléicole méditerranéenne.',
			'Facility type'              => 'Type de site',
			'Factory'                    => 'Usine',
			'Dedicated olive wood manufacturing plant with specialised production areas.' => 'Usine dédiée au bois d’olivier avec des zones de production spécialisées.',
			'Team'                       => 'Équipe',
			'Skilled artisans, designers, QC specialists and operations staff.' => 'Artisans qualifiés, designers, spécialistes qualité et équipes opérationnelles.',
			'Capacity'                   => 'Capacité',
			'Unit orders fulfilled regularly for B2B partners.' => 'Commandes à l’unité honorées régulièrement pour les partenaires B2B.',
			'End-to-end manufacturing process' => 'Processus de fabrication de bout en bout',
			'From raw material sourcing to export packaging, every step is built for traceability, consistency and quality control.' => 'De l’approvisionnement en matière première à l’emballage d’export, chaque étape est pensée pour la traçabilité, la régularité et le contrôle qualité.',
			'Raw material sourcing'      => 'Approvisionnement en matière première',
			'Selection of authentic Tunisian olive wood from end-of-life trees.' => 'Sélection de bois d’olivier tunisien authentique issu d’arbres en fin de vie.',
			'Drying &amp; curing'        => 'Séchage & stabilisation',
			'Wood stabilised before cutting and shaping to support durability.' => 'Bois stabilisé avant la découpe et le façonnage pour garantir la durabilité.',
			'Machining &amp; shaping'    => 'Usinage & façonnage',
			'Skilled preparation using professional equipment and controlled dimensions.' => 'Préparation experte avec un équipement professionnel et des dimensions contrôlées.',
			'Handcrafting'               => 'Travail à la main',
			'Artisans sand, polish and refine every piece by hand.' => 'Les artisans poncent, polissent et affinent chaque pièce à la main.',
			'Food-safe finishing'        => 'Finition alimentaire',
			'Mineral oil and beeswax applied to protect and enhance the grain.' => 'Huile minérale et cire d’abeille appliquées pour protéger et sublimer le grain.',
			'QC, packaging &amp; export' => 'Contrôle, emballage & export',
			'Inspection, retail or wholesale packing, documents and shipment preparation.' => 'Inspection, conditionnement détail ou gros, documents et préparation d’expédition.',
			'Certification'              => 'Certification',
			'World’s first ISO 9001 certified olive wood manufacturer' => 'Premier fabricant de bois d’olivier certifié ISO 9001 au monde',
			'ArtisRaw is certified for the design, production and sale (national and international) of olive wood articles — operating a documented quality management system across the full workflow.' => 'ArtisRaw est certifiée pour la conception, la production et la vente (nationale et internationale) d’articles en bois d’olivier — avec un système de management de la qualité documenté sur l’ensemble du flux.',
			'ISO 9001:2015 certified quality management system' => 'Système de management de la qualité certifié ISO 9001:2015',
			'Documented quality inspections and full batch traceability' => 'Inspections qualité documentées et traçabilité complète par lot',
			'Food-grade finishing with material safety documentation' => 'Finition de qualité alimentaire avec fiche de données de sécurité',
			'Export documents prepared per destination market' => 'Documents d’export préparés selon le marché de destination',
			'Ask for documents'         => 'Demander les documents',
			'The founders behind ArtisRaw' => 'Les fondateurs d’ArtisRaw',
			'Co-founder &amp; CEO'       => 'Cofondateur & PDG',
			'Leads ArtisRaw’s wholesale strategy and export partnerships across global markets.' => 'Pilote la stratégie de gros et les partenariats d’export d’ArtisRaw sur les marchés mondiaux.',
			'Head of Design — registered artisan' => 'Directeur du design — artisan inscrit',
			'Designs every collection and reviews each product family for craft, function and durability.' => 'Conçoit chaque collection et passe en revue chaque famille de produits pour l’artisanat, la fonction et la durabilité.',
			'Co-founder — Operations'    => 'Cofondateur — Opérations',
			'Runs production, quality systems and the factory workflow in Sfax.' => 'Dirige la production, les systèmes qualité et le flux d’usine à Sfax.',
			'Stories worth telling'      => 'Des histoires à raconter',
			'ArtisRaw isn’t just about products; it’s about stories worth telling — tradition, sustainability and longevity.' => 'ArtisRaw, ce ne sont pas que des produits ; ce sont des histoires à raconter — tradition, durabilité et longévité.',
			'Let’s work together'        => 'Travaillons ensemble',

			/* ---- Process (tpl-process) ---- */
			'ArtisRaw artisan carrying olive wood at the Sfax factory' => 'Artisan ArtisRaw transportant du bois d’olivier à l’usine de Sfax',
			'Responsible sourcing, drying, cutting, hand-finishing, food-safe protection, quality control, packaging and B2B export preparation.' => 'Approvisionnement responsable, séchage, découpe, finition à la main, protection alimentaire, contrôle qualité, emballage et préparation à l’export B2B.',
			'Process overview'           => 'Aperçu du processus',
			'Eight core phases that turn raw Chemlali olive wood into premium B2B collections' => 'Huit phases clés qui transforment le bois d’olivier Chemlali brut en collections B2B premium',
			'Responsible sourcing'       => 'Approvisionnement responsable',
			'Wood is stabilised before cutting and shaping to support durability.' => 'Le bois est stabilisé avant la découpe et le façonnage pour garantir la durabilité.',
			'Cutting &amp; shaping'      => 'Découpe & façonnage',
			'Hand finishing'             => 'Finition à la main',
			'Food-safe finish'           => 'Finition alimentaire',
			'Mineral oil and beeswax are applied to protect and enhance the grain.' => 'L’huile minérale et la cire d’abeille protègent et subliment le grain.',
			'Quality checks'             => 'Contrôles qualité',
			'Inspection of surface, dimensions, finish, packing and consistency.' => 'Inspection de la surface, des dimensions, de la finition, de l’emballage et de la régularité.',
			'Export packing'             => 'Emballage d’export',
			'Products prepared for retail, wholesale cartons or private-label needs.' => 'Produits préparés pour le détail, les cartons de gros ou la marque blanche.',
			'Documents &amp; shipment'   => 'Documents & expédition',
			'Packing list, invoice, certificates and export preparation for partners.' => 'Liste de colisage, facture, certificats et préparation d’export pour les partenaires.',
			'Step 01 — Raw material'     => 'Étape 01 — Matière première',
			'The process begins with Tunisian Chemlali olive wood, selected for dense grain, natural contrast and durability. We focus on responsible use of end-of-life olive trees and avoid waste by transforming raw material into high-value products.' => 'Le processus commence par le bois d’olivier Chemlali tunisien, choisi pour son grain dense, son contraste naturel et sa durabilité. Nous privilégions l’usage responsable des oliviers en fin de vie et évitons le gaspillage en transformant la matière première en produits à forte valeur.',
			'Our story'                  => 'Notre histoire',
			'Step 02 — Workshop'         => 'Étape 02 — Atelier',
			'Cutting, shaping and artisan work' => 'Découpe, façonnage et travail artisanal',
			'Each product is cut, shaped and sanded according to its category and SKU requirements. The workshop combines professional equipment with hand finishing to preserve the unique character of olive wood.' => 'Chaque produit est découpé, façonné et poncé selon sa catégorie et sa référence. L’atelier associe équipements professionnels et finition à la main pour préserver le caractère unique du bois d’olivier.',
			'Quality control'            => 'Contrôle qualité',
			'Step 03 — Quality'          => 'Étape 03 — Qualité',
			'Inspection, food-safe finish and packing' => 'Inspection, finition alimentaire et emballage',
			'Each product is checked, oiled with food-grade mineral oil and beeswax, then packed for retail or wholesale. Private-label requests, packaging needs and shipment preparation are handled before export.' => 'Chaque produit est contrôlé, huilé à l’huile minérale alimentaire et à la cire d’abeille, puis emballé pour le détail ou le gros. Les demandes de marque blanche, les besoins d’emballage et la préparation d’expédition sont gérés avant l’export.',
			'Quality control timeline'   => 'Calendrier du contrôle qualité',
			'A simple, buyer-facing view of the quality checkpoints every order passes through.' => 'Une vue simple et claire des points de contrôle qualité par lesquels passe chaque commande.',
			'Raw material check'         => 'Contrôle matière première',
			'Trunk integrity, density and natural defects reviewed before production.' => 'Intégrité du tronc, densité et défauts naturels vérifiés avant production.',
			'Cutting check'              => 'Contrôle de découpe',
			'Dimensions per SKU verified with controlled tolerances.' => 'Dimensions par référence vérifiées avec des tolérances contrôlées.',
			'Surface check'              => 'Contrôle de surface',
			'Smoothness, cracks and grain inspected piece by piece.' => 'Lissé, fissures et grain inspectés pièce par pièce.',
			'Finish check'               => 'Contrôle de finition',
			'Food-safe oiling and wax coverage verified on every unit.' => 'Huilage alimentaire et couverture de cire vérifiés sur chaque unité.',
			'Packing check'              => 'Contrôle d’emballage',
			'Protection, carton specs and labelling confirmed before closing.' => 'Protection, spécifications carton et étiquetage confirmés avant fermeture.',
			'Export check'               => 'Contrôle d’export',
			'Documents and batch traceability validated before shipment.' => 'Documents et traçabilité par lot validés avant expédition.',
			'Process FAQs'               => 'FAQ sur le processus',
			'What makes ArtisRaw a trusted wholesale partner?' => 'Pourquoi ArtisRaw est-il un partenaire de gros de confiance ?',
			'An ISO 9001:2015-certified manufacturer with its own factory in Sfax, documented quality control, food-safe finishing and export experience across 30+ countries — built for B2B ordering with sale-ready SKUs and MOQ-based logic.' => 'Un fabricant certifié ISO 9001:2015 avec sa propre usine à Sfax, un contrôle qualité documenté, une finition alimentaire et une expérience d’export dans plus de 30 pays — pensé pour la commande B2B avec des références prêtes à la vente et une logique de MOQ.',
			'Where does your olive wood come from?' => 'D’où provient votre bois d’olivier ?',
			'Exclusively from end-of-life Chemlali olive trees in the Sfax region of Tunisia. Productive trees are protected; only trees that no longer bear fruit enter the workshop.' => 'Exclusivement d’oliviers Chemlali en fin de vie de la région de Sfax, en Tunisie. Les arbres productifs sont protégés ; seuls ceux qui ne portent plus de fruits entrent à l’atelier.',
			'Are your products food-safe and certified?' => 'Vos produits sont-ils alimentaires et certifiés ?',
			'Yes. Every piece is finished with food-grade mineral oil and beeswax, with material safety documentation available, under an ISO 9001:2015 quality management system.' => 'Oui. Chaque pièce est finie à l’huile minérale alimentaire et à la cire d’abeille, avec fiche de données de sécurité disponible, sous un système qualité ISO 9001:2015.',
			'How do you ensure consistent quality?' => 'Comment garantissez-vous une qualité constante ?',
			'Through the six-checkpoint timeline above — raw material, cutting, surface, finish, packing and export checks — applied unit by unit with batch traceability.' => 'Grâce aux six points de contrôle ci-dessus — matière première, découpe, surface, finition, emballage et export — appliqués pièce par pièce avec traçabilité par lot.',
			'Can you customise products and packaging?' => 'Pouvez-vous personnaliser produits et emballages ?',
			'Yes. Engraving, co-designed SKUs and branded packaging are handled in-house as part of the private-label service.' => 'Oui. Gravure, références co-conçues et emballages de marque sont réalisés en interne dans le cadre du service de marque blanche.',
			'What are your minimum order quantities?' => 'Quelles sont vos quantités minimales de commande ?',
			'MOQ starts at 50 units and varies by SKU. Samples are available, with the cost deducted from your first order.' => 'Le MOQ démarre à 50 unités et varie selon la référence. Des échantillons sont disponibles, leur coût étant déduit de votre première commande.',
			'How long are production and delivery times?' => 'Quels sont les délais de production et de livraison ?',
			'In-stock items ship within 72 hours; custom production takes 6–8 weeks. Transit is typically 5–12 days by air or 25–40 days by ocean.' => 'Les articles en stock sont expédiés sous 72 heures ; la production sur mesure prend 6 à 8 semaines. Le transit est généralement de 5 à 12 jours par avion ou de 25 à 40 jours par mer.',
			'Do you export worldwide?' => 'Exportez-vous dans le monde entier ?',
			'Yes — ArtisRaw ships to 30+ countries with full export documentation, supporting FOB, CIF, DAP and DDP terms depending on destination.' => 'Oui — ArtisRaw expédie dans plus de 30 pays avec une documentation d’export complète, en FOB, CIF, DAP ou DDP selon la destination.',
			'Tell us about your project and our team will prepare a tailored proposal for your business.' => 'Parlez-nous de votre projet : notre équipe préparera une proposition sur mesure pour votre entreprise.',

			/* ---- Home Figma sections (caption grid · feature quote · trio) ---- */
			'From tree, to workshop, to wholesale, to shelves' => 'De l’arbre à l’atelier, du gros aux rayons',
			'Olive grove in the Sfax region' => 'Oliveraie de la région de Sfax',
			'Artisan shaping olive wood on a lathe' => 'Artisan façonnant le bois d’olivier au tour',
			'Handmade production'        => 'Production artisanale',
			'Dense-grain olive wood boards drying' => 'Planches de bois d’olivier à grain dense en séchage',
			'Finished olive wood chess set' => 'Jeu d’échecs en bois d’olivier fini',
			'Premium B2B collections'    => 'Collections B2B premium',
			'What they say…'             => 'Ce qu’ils disent…',
			'ArtisRaw is reliable, consistent and easy to work with. Their handmade olive wood products helped us build a premium retail collection with a strong Mediterranean story.' => 'ArtisRaw est fiable, régulier et facile à travailler. Leurs produits en bois d’olivier fait main nous ont permis de bâtir une collection de détail premium avec un fort récit méditerranéen.',
			'Retail buyer'               => 'Acheteur de détail',
			'Specialty retailer, United Kingdom' => 'Détaillant spécialisé, Royaume-Uni',
			'Discover the real olive wood quality' => 'Découvrez la vraie qualité du bois d’olivier',
			'Discover more'              => 'En découvrir plus',
			'FAQs'                       => 'FAQ',
			'Read more'                  => 'En savoir plus',
			'Premium Olive Wood for Wholesale Buyers' => 'Bois d’olivier premium pour acheteurs en gros',
			'Handmade Tunisian olive wood collections for retailers, distributors, hospitality groups, corporate gifts and private-label brands.' => 'Collections de bois d’olivier tunisien fait main pour détaillants, distributeurs, hôtellerie, cadeaux d’affaires et marques en marque blanche.',
			'Handmade Tunisian olive wood collections' => 'Collections de bois d’olivier tunisien fait main',
			'ISO 9001 olive wood manufacturer · Sfax, Tunisia' => 'Fabricant de bois d’olivier ISO 9001 · Sfax, Tunisie',
			'ArtisRaw is an ISO 9001:2015-certified olive wood manufacturer in Sfax, Tunisia, supplying retailers, distributors and private-label brands in 30+ countries. Wholesale cutting boards, serveware, utensils and chess sets — MOQ from 50 units, in-stock items ship within 72 hours, custom production in 6–8 weeks.' => 'ArtisRaw est un fabricant de bois d’olivier certifié ISO 9001:2015 à Sfax, en Tunisie, qui approvisionne détaillants, distributeurs et marques en marque blanche dans plus de 30 pays. Planches, service de table, ustensiles et jeux d’échecs en gros — MOQ dès 50 unités, articles en stock expédiés sous 72 heures, production sur mesure en 6 à 8 semaines.',
			'From olive tree to premium collections' => 'De l’olivier aux collections premium',
			'Raw olive wood, artisan production and food-safe finishing — shaped into ranges that sell on a shelf and online.' => 'Bois d’olivier brut, production artisanale et finition alimentaire — façonnés en gammes qui se vendent en rayon comme en ligne.',
			'Kitchen & boards' => 'Cuisine & planches',
			'Serveware & bowls' => 'Service de table & bols',
			'Gifts & chess' => 'Cadeaux & échecs',
			'Décor & lifestyle' => 'Décoration & lifestyle',
			'View the full catalogue' => 'Voir le catalogue complet',
			'Who we are' => 'Qui nous sommes',
			'A Tunisian manufacturer with artisan roots' => 'Un fabricant tunisien aux racines artisanales',
			'Founded in Sfax in 2019, ArtisRaw blends ancestral woodworking with modern, ISO 9001 production — handmade heritage built for export-ready wholesale.' => 'Fondée à Sfax en 2019, ArtisRaw allie le travail du bois ancestral à une production moderne certifiée ISO 9001 — un héritage fait main pensé pour la vente en gros prête à l’export.',
			'Our story & founders' => 'Notre histoire & fondateurs',
			'ArtisRaw artisans in the Sfax workshop' => 'Les artisans ArtisRaw à l’atelier de Sfax',
			'How it’s made' => 'Comment c’est fabriqué',
			'From the tree to a finished, food-safe piece' => 'De l’arbre à une pièce finie et alimentaire',
			'Reclaimed Chemlali wood, controlled drying, CNC precision and hand-finishing — then unit-by-unit QC and export packing under one roof.' => 'Bois Chemlali récupéré, séchage contrôlé, précision CNC et finition à la main — puis contrôle qualité pièce par pièce et emballage d’export, sous un même toit.',
			'See the production process' => 'Voir le processus de production',
			'Olive wood board showing dense Chemlali grain' => 'Planche en bois d’olivier au grain dense Chemlali',
			'One tree used, two planted' => 'Un arbre utilisé, deux plantés',
			'We work only reclaimed, end-of-life olive wood and sponsor reforestation through trees.org — with full EUDR traceability for EU buyers.' => 'Nous ne travaillons que du bois d’olivier récupéré en fin de vie et parrainons la reforestation via trees.org — avec une traçabilité RDUE complète pour les acheteurs de l’UE.',
			'Olive grove near Sfax' => 'Oliveraie près de Sfax',
			'Probably the most beautiful boards your shelves will carry.' => 'Probablement les plus belles planches que porteront vos rayons.',
			'Ready-to-Ship Bestsellers' => 'Best-sellers prêts à expédier',
			'Browse all categories' => 'Parcourir toutes les catégories',
			'Why buyers choose ArtisRaw' => 'Pourquoi les acheteurs choisissent ArtisRaw',
			'ISO 9001 quality' => 'Qualité ISO 9001',
			'Certified quality management with ≥96% first-pass yield and unit-by-unit inspection.' => 'Management de la qualité certifié, ≥96 % de conformité au premier passage et inspection pièce par pièce.',
			'See QC' => 'Voir le CQ',
			'Import-ready' => 'Prêt à l’import',
			'Lacey Act data, EUDR traceability and ISPM-15 pallets prepared per shipment.' => 'Données Lacey Act, traçabilité RDUE et palettes ISPM-15 préparées par expédition.',
			'Logistics' => 'Logistique',
			'In-house private label' => 'Marque blanche en interne',
			'Engraving, custom packaging and barcode-ready references under your brand.' => 'Gravure, emballage personnalisé et références prêtes au code-barres sous votre marque.',
			'Fast fulfilment' => 'Traitement rapide',
			'In-stock SKUs dispatched within 72 hours; custom runs in 6–8 weeks.' => 'Références en stock expédiées sous 72 heures ; séries sur mesure en 6 à 8 semaines.',
			'How to order' => 'Comment commander',
			'Built for your channel' => 'Conçu pour votre canal',
			'Retailers' => 'Détaillants',
			'Curated, sale-ready assortments for kitchenware, gift and décor shops.' => 'Assortiments prêts à vendre pour les boutiques d’arts de la table, de cadeaux et de décoration.',
			'Distributors & importers' => 'Distributeurs & importateurs',
			'Volume pricing, container loads and full export documentation.' => 'Tarifs au volume, chargements complets et documentation d’export complète.',
			'Hospitality' => 'Hôtellerie',
			'Durable boards and serveware with custom branding for venues.' => 'Planches et service de table durables avec marquage personnalisé pour les établissements.',
			'Private-label brands' => 'Marques en marque blanche',
			'Your logo, packaging and barcode-ready references, made in-house.' => 'Votre logo, votre emballage et des références prêtes au code-barres, réalisés en interne.',
			'Trees sponsored' => 'Arbres parrainés',
			'First-pass yield' => 'Conformité au premier passage',
			'Return rate' => 'Taux de retour',
			'Countries served' => 'Pays desservis',
			'Selected buyers &amp; partners' => 'Acheteurs & partenaires sélectionnés',
			'ArtisRaw booth at an international B2B trade fair' => 'Stand ArtisRaw lors d’un salon B2B international',
			'From the Olive Wood Guide' => 'Du Guide du bois d’olivier',
			'Read the Magazine' => 'Lire le magazine',
			'Olive wood boards' => 'Planches en bois d’olivier',
			'In the workshop' => 'À l’atelier',
			'Mortar & pestle' => 'Mortier & pilon',
			'Chess & gifts' => 'Échecs & cadeaux',
			'Carved bowls' => 'Bols sculptés',
			'Ready to ship' => 'Prêt à expédier',
			'Tell us your market and quantities — get a quote with MOQ, pricing and import documentation within 24 hours.' => 'Indiquez votre marché et vos quantités — recevez un devis avec MOQ, tarifs et documentation d’import sous 24 heures.',
			'1,000–5,000 units' => '1 000–5 000 unités',
			'250–1,000 units' => '250–1 000 unités',
			'5,000+ units' => '5 000+ unités',
			'50–250 units' => '50–250 unités',
			'From 50 units per SKU' => 'Dès 50 unités par référence',
			'MOQ from 50' => 'MOQ dès 50',
			'MOQ starts at 50 units per SKU and is confirmed on your quote. Mixed assortments are welcome.' => 'Le MOQ démarre à 50 unités par référence et est confirmé sur votre devis. Les assortiments mixtes sont les bienvenus.',
			'25+ registered artisans paired with CNC precision for consistent quality.' => 'Plus de 25 artisans inscrits associés à la précision CNC pour une qualité constante.',
			'30+ countries' => '30+ pays',
			'A 3,000-year Mediterranean olive-wood tradition, made in Sfax, Tunisia.' => 'Une tradition méditerranéenne du bois d’olivier de 3 000 ans, fabriquée à Sfax, en Tunisie.',
			'Ahmed Sakka' => 'Ahmed Sakka',
			'Artisans sand and refine each surface.' => 'Les artisans poncent et affinent chaque surface.',
			'CNC precision and artisan refinement on every piece.' => 'Précision CNC et finition artisanale sur chaque pièce.',
			'CNC precision to SKU dimensions.' => 'Précision CNC aux dimensions de la référence.',
			'Check 01' => 'Contrôle 01',
			'Check 02' => 'Contrôle 02',
			'Check 03' => 'Contrôle 03',
			'Check 04' => 'Contrôle 04',
			'Check 05' => 'Contrôle 05',
			'Check 06' => 'Contrôle 06',
			'Co-founder' => 'Cofondateur',
			'Co-founder & CEO' => 'Cofondateur & PDG',
			'Co-founder & Head of Design' => 'Cofondateur & directeur du design',
			'Controlled drying for stable, lasting pieces.' => 'Séchage contrôlé pour des pièces stables et durables.',
			'Controlled drying to stabilise the wood before shaping.' => 'Séchage contrôlé pour stabiliser le bois avant le façonnage.',
			'Counts, labels and packaging verified.' => 'Comptage, étiquettes et emballage vérifiés.',
			'Craft 4.0' => 'Artisanat 4.0',
			'Cutting' => 'Découpe',
			'Cutting & shaping' => 'Découpe & façonnage',
			'Dimensions verified against the SKU spec.' => 'Dimensions vérifiées par rapport à la fiche de la référence.',
			'Documents & shipment' => 'Documents & expédition',
			'Documents and pallet compliance checked.' => 'Documents et conformité des palettes vérifiés.',
			'Download centre' => 'Centre de téléchargement',
			'Drying & curing' => 'Séchage & stabilisation',
			'End-of-life Chemlali olive wood from licensed trees.' => 'Bois d’olivier Chemlali en fin de vie issu d’arbres licenciés.',
			'Export' => 'Export',
			'Export packing' => 'Emballage d’export',
			'Finish' => 'Finition',
			'Food-contact finish MSDS' => 'FDS de la finition au contact alimentaire',
			'Food-safe finish' => 'Finition alimentaire',
			'Food-safe finish applied and confirmed.' => 'Finition alimentaire appliquée et confirmée.',
			'Food-safe finishing & QC' => 'Finition alimentaire & contrôle qualité',
			'Forestry licence #4684' => 'Licence forestière #4684',
			'Grade, moisture and grain checked before cutting.' => 'Qualité, humidité et grain vérifiés avant la découpe.',
			'Hand finishing' => 'Finition à la main',
			'Heritage' => 'Héritage',
			'Heritage & vision — keeping the story of olive wood alive through timeless products.' => 'Héritage & vision — faire vivre l’histoire du bois d’olivier à travers des produits intemporels.',
			'How we control quality' => 'Comment nous contrôlons la qualité',
			'ISO 9001 systems and documentation for 30+ countries.' => 'Systèmes et documentation ISO 9001 pour plus de 30 pays.',
			'ISO 9001:2015' => 'ISO 9001:2015',
			'ISO 9001:2015 certificate' => 'Certificat ISO 9001:2015',
			'ISPM-15 pallets and retail-ready packaging.' => 'Palettes ISPM-15 et emballage prêt au détail.',
			'Ihsen Triki' => 'Ihsen Triki',
			'Invoice, packing list and compliance per shipment.' => 'Facture, liste de colisage et conformité par expédition.',
			'Lacey Act data ready' => 'Données Lacey Act prêtes',
			'Latest from the Guide' => 'Derniers articles du Guide',
			'Machining & handcrafting' => 'Usinage & travail à la main',
			'Mineral oil & beeswax, documented MSDS.' => 'Huile minérale & cire d’abeille, FDS documentée.',
			'Mineral-oil & beeswax finish, then unit-by-unit inspection and export packing.' => 'Finition huile minérale & cire d’abeille, puis inspection pièce par pièce et emballage d’export.',
			'Mohamed Bilel Cherif' => 'Mohamed Bilel Cherif',
			'Operations & strategy — building a reliable manufacturing system for professional buyers.' => 'Opérations & stratégie — bâtir un système de production fiable pour les acheteurs professionnels.',
			'Our own factory pairs artisan handwork with controlled drying, CNC machining and an ISO 9001 quality system — so handmade character meets export-ready consistency.' => 'Notre propre usine associe le travail artisanal au séchage contrôlé, à l’usinage CNC et à un système qualité ISO 9001 — le caractère du fait main rencontre une régularité prête à l’export.',
			'Packing' => 'Emballage',
			'Process overview' => 'Aperçu du processus',
			'Product & artistry — fusing traditional Tunisian craft with contemporary luxury.' => 'Produit & art — fusionner l’artisanat tunisien traditionnel et le luxe contemporain.',
			'Quality checks' => 'Contrôles qualité',
			'Quality-control timeline' => 'Calendrier du contrôle qualité',
			'Raw material' => 'Matière première',
			'Raw material sourcing' => 'Approvisionnement en matière première',
			'Reclaimed end-of-life wood, food-safe finishes and reforestation.' => 'Bois récupéré en fin de vie, finitions alimentaires et reforestation.',
			'Reclaimed, end-of-life Chemlali olive wood from licensed trees.' => 'Bois d’olivier Chemlali récupéré, en fin de vie, issu d’arbres licenciés.',
			'Responsibility' => 'Responsabilité',
			'Responsible sourcing' => 'Approvisionnement responsable',
			'Sample export documents' => 'Exemples de documents d’export',
			'Sanding and finish quality inspected.' => 'Ponçage et qualité de finition inspectés.',
			'See the full production process' => 'Voir tout le processus de production',
			'Surface' => 'Surface',
			'The ArtisRaw world in four pillars' => 'L’univers ArtisRaw en quatre piliers',
			'Unit-by-unit inspection, ≥96% first-pass yield.' => 'Inspection pièce par pièce, ≥96 % de conformité au premier passage.',
			'Visit the Magazine' => 'Visiter le magazine',
			'Wholesale line-sheet' => 'Tarif de gros (line-sheet)',
			'EUDR traceability' => 'Traçabilité RDUE',
			'72 h for stock · 2–4 weeks made-to-order' => '72 h pour le stock · 2–4 semaines sur mesure',
			'Are dimensions exact?' => 'Les dimensions sont-elles exactes ?',
			'Attribute' => 'Attribut',
			'Barcode-ready' => 'Prêt au code-barres',
			'Bowls & Serveware' => 'Bols & service de table',
			'Branded sleeves, gift boxes and retail-ready packaging.' => 'Manchons de marque, coffrets cadeaux et emballage prêt au détail.',
			'Can I private-label this category?' => 'Puis-je vendre cette catégorie en marque blanche ?',
			'Category FAQs' => 'FAQ de la catégorie',
			'Category specifications' => 'Spécifications de la catégorie',
			'Chess Sets' => 'Jeux d’échecs',
			'Custom packaging' => 'Emballage personnalisé',
			'Custom shapes, sizes and bundles developed for your market.' => 'Formes, tailles et coffrets personnalisés développés pour votre marché.',
			'Cutting Boards' => 'Planches à découper',
			'Detail' => 'Détail',
			'Décor & Bath' => 'Décoration & bain',
			'EAN/UPC labelling and reference logic for retail and marketplace.' => 'Étiquetage EAN/UPC et logique de référence pour le détail et les marketplaces.',
			'Engraving & custom packaging available' => 'Gravure & emballage personnalisé disponibles',
			'How fast can you ship?' => 'Sous quel délai expédiez-vous ?',
			'In-stock SKUs ship in 72 hours; made-to-order runs take 2–4 weeks.' => 'Les références en stock partent sous 72 heures ; les séries sur mesure prennent 2 à 4 semaines.',
			'Is the finish food-safe?' => 'La finition est-elle alimentaire ?',
			'Laser and pyrography engraving of your logo on boards, bowls and utensils.' => 'Gravure laser et pyrogravure de votre logo sur planches, bols et ustensiles.',
			'Lead time' => 'Délai',
			'Logo engraving' => 'Gravure de logo',
			'MOQ' => 'MOQ',
			'Material' => 'Matière',
			'Olea europaea (Chemlali) — food-safe mineral oil + beeswax' => 'Olea europaea (Chemlali) — huile minérale alimentaire + cire d’abeille',
			'Pieces are handmade from natural olive wood, so dimensions and grain vary slightly — part of the authentic value.' => 'Les pièces sont faites main en bois d’olivier naturel : dimensions et grain varient légèrement — cela fait partie de la valeur authentique.',
			'Private label' => 'Marque blanche',
			'Product development' => 'Développement produit',
			'Ready-to-ship SKUs' => 'Références prêtes à expédier',
			'Request a quote' => 'Demander un devis',
			'Request full PDF catalogue' => 'Demander le catalogue PDF complet',
			'Request the line-sheet for the full SKU list in this category.' => 'Demandez le tarif pour la liste complète des références de cette catégorie.',
			'Samples available — cost deducted from your first order.' => 'Échantillons disponibles — coût déduit de votre première commande.',
			'Specifications' => 'Spécifications',
			'Tell us your market and quantities — we reply within 24 hours with MOQ, pricing and import documentation.' => 'Indiquez votre marché et vos quantités — nous répondons sous 24 heures avec MOQ, tarifs et documentation d’import.',
			'Utensils' => 'Ustensiles',
			'What is the MOQ for this category?' => 'Quel est le MOQ pour cette catégorie ?',
			'Yes — food-contact items use a mineral oil + beeswax blend; MSDS available on request.' => 'Oui — les articles au contact alimentaire utilisent un mélange huile minérale + cire d’abeille ; FDS disponible sur demande.',
			'Yes. We engrave logos and produce custom packaging and barcode-ready references.' => 'Oui. Nous gravons les logos et produisons emballages personnalisés et références prêtes au code-barres.',
			'Your brand, our handmade expertise' => 'Votre marque, notre savoir-faire artisanal',
			'Browse the full range. Families with a dedicated page link through; the rest are available on request.' => 'Parcourez toute la gamme. Les familles avec une page dédiée sont cliquables ; les autres sont disponibles sur demande.',
			'Catalogue family index' => 'Index des familles du catalogue',
			'Every food-contact item is finished with a food-safe mineral oil and beeswax blend. Hand-wash, dry immediately and re-oil periodically — care guidance ships with each order, and the finish MSDS is available to buyers.' => 'Chaque article au contact alimentaire est fini avec un mélange alimentaire d’huile minérale et de cire d’abeille. Lavez à la main, séchez aussitôt et ré-huilez régulièrement — un guide d’entretien accompagne chaque commande, et la FDS de la finition est disponible pour les acheteurs.',
			'Featured families' => 'Familles à la une',
			'Fifteen olive wood product families, each with standardized sale SKUs, metric and imperial dimensions, natural-variation notes and MOQ by family. Request the full PDF catalogue and price list below.' => 'Quinze familles de produits en bois d’olivier, chacune avec des références standardisées, des dimensions métriques et impériales, des notes de variation naturelle et un MOQ par famille. Demandez le catalogue PDF complet et la liste de prix ci-dessous.',
			'Food-safe protection & maintenance' => 'Protection alimentaire & entretien',
			'Read the Olive Wood Guide' => 'Lire le Guide du bois d’olivier',
			'Request' => 'Demander',
			'Request the PDF catalogue & price list' => 'Demander le catalogue PDF & la liste de prix',
			'Tell us your market and we’ll send the full catalogue, line-sheet and price list within 24 hours.' => 'Indiquez votre marché et nous envoyons le catalogue complet, le tarif et la liste de prix sous 24 heures.',
			'View range' => 'Voir la gamme',
			'Want the complete PDF catalogue & price list?' => 'Vous voulez le catalogue PDF complet & la liste de prix ?',
			'Editor’s feature' => 'À la une de la rédaction',
			'Fair conversations become catalogue selections, samples and recurring wholesale orders.' => 'Les échanges en salon deviennent des sélections catalogue, des échantillons et des commandes de gros récurrentes.',
			'Fairs & international participation' => 'Salons & participation internationale',
			'From conversation to orders' => 'De la conversation aux commandes',
			'Get B2B updates & new stories' => 'Recevez les actualités B2B & nouveaux articles',
			'Latest stories' => 'Derniers articles',
			'Live demonstrations of carving, finishing and the natural Chemlali grain that sets our work apart.' => 'Démonstrations en direct de sculpture, de finition et du grain Chemlali naturel qui distingue notre travail.',
			'Meeting buyers face to face' => 'Rencontrer les acheteurs en personne',
			'New stories are published here regularly. In the meantime, explore the Olive Wood Guide.' => 'De nouveaux articles sont publiés ici régulièrement. En attendant, explorez le Guide du bois d’olivier.',
			'Showcasing Tunisian craft' => 'Mettre en valeur l’artisanat tunisien',
			'Stories worth telling for professional buyers — workshop notes, material science, compliance explainers and trade-show reports from Sfax.' => 'Des histoires à raconter pour les acheteurs professionnels — notes d’atelier, science des matériaux, explications de conformité et reportages de salons depuis Sfax.',
			'We exhibit at international B2B trade fairs to meet wholesale buyers and show the range in person.' => 'Nous exposons dans les salons B2B internationaux pour rencontrer les acheteurs de gros et présenter la gamme en personne.',
			'Air freight runs 5–12 days; ocean 25–40 days, on ISPM-15 pallets.' => 'Le fret aérien prend 5 à 12 jours ; la mer 25 à 40 jours, sur palettes ISPM-15.',
			'Are all products identical?' => 'Tous les produits sont-ils identiques ?',
			'Are the products food-safe?' => 'Les produits sont-ils alimentaires ?',
			'Can I reorder previous SKUs?' => 'Puis-je recommander d’anciennes références ?',
			'Can I request samples?' => 'Puis-je demander des échantillons ?',
			'Can you handle US and EU import paperwork?' => 'Gérez-vous les formalités d’import US et UE ?',
			'Chat with ArtisRaw on WhatsApp' => 'Discuter avec ArtisRaw sur WhatsApp',
			'Cutting boards, utensils, bowls and serveware, mortars, chess sets, jars, trays and décor — 16 product families in total.' => 'Planches à découper, ustensiles, bols et service de table, mortiers, jeux d’échecs, jarres, plateaux et décoration — 16 familles de produits au total.',
			'Do you create corporate gifts?' => 'Créez-vous des cadeaux d’affaires ?',
			'Do you have a minimum order quantity?' => 'Avez-vous une quantité minimale de commande ?',
			'Do you offer private label?' => 'Proposez-vous la marque blanche ?',
			'Do you provide quality control?' => 'Assurez-vous le contrôle qualité ?',
			'FOB Tunisia, CIF, DAP or DDP, depending on your preference and destination.' => 'FOB Tunisie, CIF, DAP ou DDP, selon votre préférence et votre destination.',
			'Factory &amp; contact' => 'Usine & contact',
			'Food-contact items are finished with a food-safe mineral oil and beeswax blend; finish MSDS is available to professional buyers.' => 'Les articles au contact alimentaire sont finis avec un mélange alimentaire d’huile minérale et de cire d’abeille ; la FDS de la finition est disponible pour les acheteurs professionnels.',
			'Hand-wash, dry immediately, avoid prolonged soaking, and refresh with food-safe oil when needed.' => 'Lavez à la main, séchez aussitôt, évitez le trempage prolongé et ravivez avec une huile alimentaire au besoin.',
			'How do I start an order?' => 'Comment démarrer une commande ?',
			'How is olive wood cared for?' => 'Comment entretenir le bois d’olivier ?',
			'ISO 9001:2015 quality management, plus forestry licence #4684. Certificates and reports are downloadable on the certifications page.' => 'Management de la qualité ISO 9001:2015, plus la licence forestière #4684. Certificats et rapports téléchargeables sur la page certifications.',
			'In-stock SKUs are dispatched within 72 hours. Custom and private-label production takes about 6–8 weeks before shipping.' => 'Les références en stock sont expédiées sous 72 heures. La production sur mesure et en marque blanche prend environ 6 à 8 semaines avant expédition.',
			'No — olive wood is natural, so grain, colour and small variations are part of the handmade value, not defects.' => 'Non — le bois d’olivier est naturel : grain, couleur et petites variations font partie de la valeur du fait main, ce ne sont pas des défauts.',
			'Quotes within 24 hours' => 'Devis sous 24 heures',
			'Send your company, country, client type, categories, estimated quantities, packaging needs and target date via the quote form.' => 'Envoyez via le formulaire de devis : société, pays, type de client, catégories, quantités estimées, besoins d’emballage et date cible.',
			'What are your shipping transit times?' => 'Quels sont vos délais de transit ?',
			'What certifications do you have?' => 'Quelles certifications possédez-vous ?',
			'WhatsApp' => 'WhatsApp',
			'Which Incoterms do you offer?' => 'Quels Incoterms proposez-vous ?',
			'Which product categories do you offer?' => 'Quelles catégories de produits proposez-vous ?',
			'Wholesale inquiries' => 'Demandes de gros',
			'Yes — Lacey Act declaration data, EUDR traceability, ISPM-15 pallets, commercial invoice and packing list per shipment.' => 'Oui — données de déclaration Lacey Act, traçabilité RDUE, palettes ISPM-15, facture commerciale et liste de colisage par expédition.',
			'Yes — in-house. We engrave logos and produce custom packaging, retail labels and barcode-ready references for approved projects.' => 'Oui — en interne. Nous gravons les logos et produisons emballages personnalisés, étiquettes de détail et références prêtes au code-barres pour les projets validés.',
			'Yes — personalised gift packs, chess sets, kitchen bundles and seasonal collections with branded packaging.' => 'Oui — coffrets cadeaux personnalisés, jeux d’échecs, ensembles de cuisine et collections saisonnières avec emballage de marque.',
			'Yes — unit-by-unit inspection, batch photo documentation and packing control, with ≥96% first-pass yield.' => 'Oui — inspection pièce par pièce, documentation photo par lot et contrôle d’emballage, avec ≥96 % de conformité au premier passage.',
			'Yes. MOQ starts at 50 units per SKU for most lines and is confirmed on your quote. Mixed assortments across SKU families are welcome.' => 'Oui. Le MOQ démarre à 50 unités par référence pour la plupart des lignes et est confirmé sur votre devis. Les assortiments mixtes entre familles sont les bienvenus.',
			'Yes. Samples are available and their cost is deducted from your first production order. Shipping depends on product type and destination.' => 'Oui. Des échantillons sont disponibles et leur coût est déduit de votre première commande de production. L’expédition dépend du type de produit et de la destination.',
			'Yes. We keep SKU references and order history to make replenishment fast.' => 'Oui. Nous conservons les références et l’historique des commandes pour accélérer le réassort.',
			'Air (5–12 days)' => 'Avion (5–12 jours)',
			'ArtisRaw works only reclaimed, end-of-life Chemlali olive wood — and for every wholesale order we sponsor reforestation through trees.org, so the cycle keeps giving back.' => 'ArtisRaw ne travaille que du bois d’olivier Chemlali récupéré, en fin de vie — et pour chaque commande de gros, nous parrainons la reforestation via trees.org, pour que le cycle continue de donner.',
			'Carton L×W×H' => 'Carton L×l×H',
			'Case pack' => 'Colisage',
			'Certified' => 'Certifié',
			'Certified ISO %s company' => 'Entreprise certifiée ISO %s',
			'Compliance pack (Lacey / EUDR)' => 'Dossier conformité (Lacey / RDUE)',
			'Custom branding' => 'Personnalisation de marque',
			'Dimensions' => 'Dimensions',
			'Discover more' => 'En découvrir plus',
			'Documents requested' => 'Documents demandés',
			'Finish MSDS' => 'FDS de la finition',
			'From our Instagram' => 'Sur notre Instagram',
			'Get a quote within 24 h' => 'Un devis sous 24 h',
			'ISO' => 'ISO',
			'ISO 9001 certificate' => 'Certificat ISO 9001',
			'Indicative EXW' => 'EXW indicatif',
			'Leave empty' => 'Laisser vide',
			'Leave this field empty' => 'Laissez ce champ vide',
			'Line-sheet' => 'Tarif (line-sheet)',
			'Low MOQ' => 'MOQ réduit',
			'Monthly volume' => 'Volume mensuel',
			'Not sure yet' => 'Pas encore sûr',
			'Ocean (25–40 days)' => 'Mer (25–40 jours)',
			'Preferred ship mode' => 'Mode d’expédition préféré',
			'Quick answer' => 'Réponse rapide',
			'Quote within 24 h' => 'Devis sous 24 h',
			'Rated %d out of 5' => 'Noté %d sur 5',
			'Reforestation program' => 'Programme de reforestation',
			'Request Quote' => 'Demander un devis',
			'Select…' => 'Choisir…',
			'Send these details' => 'Envoyer ces informations',
			'Stay in the know' => 'Restez informé',
			'Subscribe' => 'S’abonner',
			'Target delivery date' => 'Date de livraison souhaitée',
			'Trees sponsored to date' => 'Arbres parrainés à ce jour',
			'Unit weight' => 'Poids unitaire',
			'Updated' => 'Mis à jour',
			'We plant a tree for every order' => 'Nous plantons un arbre pour chaque commande',
			'Worldwide shipping' => 'Expédition mondiale',
			'contact page' => 'page contact',
			'you@company.com' => 'vous@entreprise.com',
			'A private space for approved wholesale buyers: build order lists from the catalogue, request quotes in one click and reorder faster. New accounts are reviewed before activation.' => 'Un espace privé pour les acheteurs de gros validés : composez des listes de commande depuis le catalogue, demandez des devis en un clic et réassortissez plus vite. Les nouveaux comptes sont examinés avant activation.',
			'Account created — it’s pending approval. We’ll email you within one business day.' => 'Compte créé — en attente d’approbation. Nous vous écrirons sous un jour ouvré.',
			'Account status' => 'Statut du compte',
			'Add' => 'Ajouter',
			'Add from the catalogue' => 'Ajouter depuis le catalogue',
			'Added to your order list.' => 'Ajouté à votre liste de commande.',
			'An account with that email already exists — try signing in.' => 'Un compte existe déjà avec cet e-mail — essayez de vous connecter.',
			'Clear list' => 'Vider la liste',
			'Company' => 'Société',
			'Create a B2B account' => 'Créer un compte B2B',
			'Create account' => 'Créer le compte',
			'Destination country' => 'Pays de destination',
			'Forgot password?' => 'Mot de passe oublié ?',
			'In list — add more' => 'Dans la liste — en ajouter',
			'Item removed.' => 'Article retiré.',
			'Keep me signed in' => 'Rester connecté',
			'No quote requests yet. Submitted order lists appear here so you can reorder faster.' => 'Aucune demande de devis pour l’instant. Les listes envoyées apparaissent ici pour réassortir plus vite.',
			'Note for our team (optional)' => 'Note pour notre équipe (facultatif)',
			'Order history' => 'Historique des commandes',
			'Order list cleared.' => 'Liste de commande vidée.',
			'Order list updated.' => 'Liste de commande mise à jour.',
			'Password' => 'Mot de passe',
			'Password (8+ characters)' => 'Mot de passe (8+ caractères)',
			'Please enter a valid email, your company, and a password of at least 8 characters.' => 'Saisissez un e-mail valide, votre société et un mot de passe d’au moins 8 caractères.',
			'Please sign in first.' => 'Veuillez d’abord vous connecter.',
			'Private-label needs, target date, packaging…' => 'Besoins de marque blanche, date cible, emballage…',
			'Product' => 'Produit',
			'Production status:' => 'Statut de production :',
			'Quantity' => 'Quantité',
			'Quote requested — we’ll reply within one business day. Your list is saved in history.' => 'Devis demandé — nous répondons sous un jour ouvré. Votre liste est enregistrée dans l’historique.',
			'Remove' => 'Retirer',
			'Request quote for this list' => 'Demander un devis pour cette liste',
			'SKU' => 'Référence',
			'Sign in' => 'Se connecter',
			'Sign out' => 'Se déconnecter',
			'Signed in.' => 'Connecté.',
			'Signed out.' => 'Déconnecté.',
			'Something went wrong with that item.' => 'Une erreur s’est produite avec cet article.',
			'Thanks, %s. We review every B2B application — usually within one business day. You’ll get an email the moment your account is active, then you can build order lists and request quotes here.' => 'Merci, %s. Nous examinons chaque demande B2B — généralement sous un jour ouvré. Vous recevrez un e-mail dès que votre compte est actif, puis vous pourrez composer des listes et demander des devis ici.',
			'Too many attempts — please wait a little and try again.' => 'Trop de tentatives — patientez un instant et réessayez.',
			'Update' => 'Mettre à jour',
			'We couldn’t create the account. Please try again.' => 'Nous n’avons pas pu créer le compte. Réessayez.',
			'We review every application. Your details are used only to set up and service your account — %s.' => 'Nous examinons chaque demande. Vos informations servent uniquement à créer et gérer votre compte — %s.',
			'Welcome back, %s.' => 'Bon retour, %s.',
			'Work email' => 'E-mail professionnel',
			'Wrong email or password.' => 'E-mail ou mot de passe incorrect.',
			'Your account is pending approval' => 'Votre compte est en attente d’approbation',
			'Your account isn’t approved yet.' => 'Votre compte n’est pas encore approuvé.',
			'Your order list' => 'Votre liste de commande',
			'Your order list is empty.' => 'Votre liste de commande est vide.',
			'Your order list is empty. Add SKUs from the catalogue below.' => 'Votre liste de commande est vide. Ajoutez des références depuis le catalogue ci-dessous.',
			'Your session expired — please try again.' => 'Votre session a expiré — réessayez.',
			'You’re already signed in.' => 'Vous êtes déjà connecté.',
			'privacy' => 'confidentialité',
			'quantity' => 'quantité',

		'Worldwide shipping'         => 'Expédition mondiale',
		'Send your market, categories and quantities — we reply within one business day with MOQ, pricing and import documentation.' => 'Indiquez votre marché, vos catégories et vos quantités — nous répondons sous un jour ouvré avec MOQ, tarifs et documents d’import.',
		'Handmade'                   => 'Fait main',
		'Sustainable'                => 'Durable',
		'Export Ready'               => 'Prêt à l’export',
	);
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
