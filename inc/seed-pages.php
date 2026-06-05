<?php
/**
 * Idempotent seeding of Phase-1 pages (CONTENT pages 3,5–10 + BLUEPRINT).
 *
 * Creates each page once (keyed by slug) with its template, SEO title/meta,
 * quick answer, mode flags and body copy. Bump ARTISRAW_PAGES_VER to add pages
 * in a later pass. Existing pages (home, hub, styleguide) are left untouched.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARTISRAW_PAGES_VER', 5 );

function artisraw_seed_pages() {
	if ( (int) get_option( 'artisraw_pages_ver' ) >= ARTISRAW_PAGES_VER ) {
		return;
	}

	$pages = artisraw_phase1_page_data();

	// First pass: create pages without parents; collect slug → ID.
	$ids = array();
	// Two passes so children resolve their parent ID.
	for ( $pass = 0; $pass < 2; $pass++ ) {
		foreach ( $pages as $p ) {
			if ( isset( $ids[ $p['slug'] ] ) ) {
				continue; // already handled this run (prevents pass-2 duplicates).
			}
			// Child pages live at parent/slug — check the full path.
			$path     = ! empty( $p['parent'] ) ? $p['parent'] . '/' . $p['slug'] : $p['slug'];
			$existing = get_page_by_path( $path );
			if ( $existing ) {
				$ids[ $p['slug'] ] = $existing->ID;
				// Refresh content/meta for pages flagged for update (enrichment passes).
				if ( ! empty( $p['update'] ) ) {
					artisraw_apply_page_fields( $existing->ID, $p );
				}
				continue;
			}
			if ( ! empty( $p['parent'] ) && empty( $ids[ $p['parent'] ] ) ) {
				continue; // wait for parent in next pass.
			}
			$pid = wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $p['title'],
				'post_name'    => $p['slug'],
				'post_content' => $p['content'] ?? '',
				'post_parent'  => ! empty( $p['parent'] ) ? $ids[ $p['parent'] ] : 0,
				'comment_status' => 'closed',
			) );
			if ( $pid && ! is_wp_error( $pid ) ) {
				$ids[ $p['slug'] ] = $pid;
				artisraw_apply_page_fields( $pid, $p );
			}
		}
	}

	update_option( 'artisraw_pages_ver', ARTISRAW_PAGES_VER );
}
add_action( 'init', 'artisraw_seed_pages', 30 );

/**
 * Apply template, SEO, quick-answer and custom meta to a page. When $p has
 * 'update' set, also refresh post_title and post_content (enrichment passes).
 */
function artisraw_apply_page_fields( $pid, array $p ) {
	if ( ! empty( $p['update'] ) ) {
		wp_update_post( array(
			'ID'           => $pid,
			'post_title'   => $p['title'],
			'post_content' => $p['content'] ?? '',
		) );
	}
	update_post_meta( $pid, '_wp_page_template', $p['template'] );
	update_post_meta( $pid, 'seo_title', $p['seo_title'] );
	update_post_meta( $pid, 'seo_meta_description', $p['seo_desc'] );
	if ( ! empty( $p['qa'] ) ) {
		update_post_meta( $pid, 'quick_answer', $p['qa'] );
	}
	foreach ( ( $p['meta'] ?? array() ) as $k => $v ) {
		update_post_meta( $pid, $k, $v );
	}
}

/**
 * Phase-1 page definitions.
 */
function artisraw_phase1_page_data() {
	$T_CAT   = 'tpl-category.php';
	$T_TRUST = 'tpl-trust.php';
	$T_CF    = 'tpl-contact-faq.php';
	$T_SVC   = 'tpl-services.php';
	$T_WORLD = 'tpl-worldwide.php';

	return array(
		/* ---- Catalogue index + categories (tpl-category) ---- */
		array(
			'slug' => 'wholesale', 'title' => 'Olive Wood Wholesale Catalogue', 'template' => $T_CAT,
			'seo_title' => 'Olive Wood Wholesale Catalogue | 16 Product Families | ArtisRaw®',
			'seo_desc'  => "ArtisRaw's wholesale olive wood catalogue: cutting boards, utensils, bowls, mortars, chess sets and decor. MOQ-based ordering; PDF catalogue on request.",
			'qa'        => 'The ArtisRaw wholesale catalogue covers 16 olive wood product families — from cutting and charcuterie boards to spoons, mortars, jars, chess sets and decor — each with standardized sale SKUs, metric and imperial dimensions, natural-variation notes and MOQ by product family.',
		),
		array(
			'slug' => 'olive-wood-cutting-boards', 'parent' => 'wholesale', 'title' => 'Olive Wood Cutting Boards Wholesale', 'template' => $T_CAT,
			'seo_title' => 'Olive Wood Cutting Boards Wholesale & Bulk | MOQ 50 | ArtisRaw®',
			'seo_desc'  => 'Wholesale olive wood cutting & charcuterie boards from Tunisia. Food-safe finish, private label, stock ships in 72 h. MOQ 50.',
			'qa'        => 'ArtisRaw supplies wholesale olive wood cutting and charcuterie boards from Sfax, Tunisia — round, rectangular and rustic shapes, food-safe mineral-oil finish, MOQ from 50 units, with private-label engraving and in-stock lines that ship in 72 hours.',
			'meta'      => array( 'cat_term' => 'cutting-boards' ),
		),
		array(
			'slug' => 'olive-wood-utensils', 'parent' => 'wholesale', 'title' => 'Wholesale Olive Wood Utensils', 'template' => $T_CAT,
			'seo_title' => 'Wholesale Olive Wood Utensils | Spoons, Spatulas & Sets | ArtisRaw®',
			'seo_desc'  => 'Bulk olive wood spoons, spatulas, ladles and utensil sets. Food-safe finish, private label, MOQ from 50. Stock ships in 72 h.',
			'qa'        => 'ArtisRaw produces wholesale olive wood utensils — cooking spoons, spatulas, ladles, scoops and gift sets — handmade in Tunisia with a food-safe finish, MOQ from 50 (sets from 100), private-label engraving and 72-hour dispatch on in-stock lines.',
			'meta'      => array( 'cat_term' => 'utensils' ),
		),
		array(
			'slug' => 'olive-wood-bowls-serveware', 'parent' => 'wholesale', 'title' => 'Olive Wood Bowls & Serveware Wholesale', 'template' => $T_CAT,
			'seo_title' => 'Olive Wood Bowls & Serveware Wholesale | MOQ 50 | ArtisRaw®',
			'seo_desc'  => 'Wholesale olive wood bowls, serving dishes, mortars and pinch bowls from Tunisia. Food-safe, private label, MOQ 50, ships in 72 h.',
			'qa'        => 'ArtisRaw supplies wholesale olive wood bowls and serveware — serving bowls, mortars and pestles, and pinch-bowl sets — handmade in Tunisia, food-safe finished, MOQ from 50 units, with private-label options and in-stock lines dispatched within 72 hours.',
			'meta'      => array( 'cat_term' => 'bowls-serveware' ),
		),
		array(
			'slug' => 'olive-wood-chess-sets', 'parent' => 'wholesale', 'title' => 'Olive Wood Chess Sets Wholesale', 'template' => $T_CAT,
			'seo_title' => 'Olive Wood Chess Sets Wholesale | Handmade in Tunisia | ArtisRaw®',
			'seo_desc'  => 'Wholesale handmade olive wood chess sets and boards from Tunisia. Premium gifting and retail, private label, MOQ from 50. Stock and custom runs.',
			'qa'        => 'ArtisRaw produces wholesale olive wood chess sets and boards — handmade in Sfax with natural Chemlali contrast, ideal for premium gifting and retail. MOQ from 50, with private-label engraving and packaging; request the line-sheet for the current range.',
			'meta'      => array( 'cat_term' => 'chess-sets' ),
		),
		array(
			'slug' => 'olive-wood-decor-bath', 'parent' => 'wholesale', 'title' => 'Olive Wood Home Décor & Bath Wholesale', 'template' => $T_CAT,
			'seo_title' => 'Olive Wood Home Decor & Bath Wholesale | MOQ 50 | ArtisRaw®',
			'seo_desc'  => 'Wholesale olive wood décor and bath: trays, jars, soap dishes and accessories from Tunisia. Food-safe finish, private label, MOQ from 50.',
			'qa'        => 'ArtisRaw supplies wholesale olive wood home décor and bath products — trays, jars, soap dishes and lifestyle accessories — handmade in Tunisia with a natural finish. MOQ from 50 units, with private-label options; request the line-sheet for the full range.',
			'meta'      => array( 'cat_term' => 'decor-bath' ),
		),
		array(
			'slug' => 'private-label-olive-wood', 'title' => 'Private Label Olive Wood', 'template' => $T_CAT,
			'seo_title' => 'Private Label Olive Wood | Engraving & Custom Packaging | ArtisRaw®',
			'seo_desc'  => 'Sell olive wood under your brand: in-house logo engraving, custom packaging, barcode-ready references and product development. MOQ from 50.',
			'qa'        => 'ArtisRaw offers in-house private-label olive wood: logo engraving, custom packaging, retail labels and barcode-ready references, plus product development for your market. MOQ from 50 units, with samples validated before production and full export documentation.',
			'meta'      => array( 'cat_mode' => 'private' ),
		),

		/* ---- Trust / proof (tpl-trust) ---- */
		array(
			'slug' => 'certifications', 'title' => 'Certifications and Quality Proof for B2B Buyers', 'template' => $T_TRUST,
			'seo_title' => 'ISO 9001 Certified Olive Wood Manufacturer | Proof & Documents | ArtisRaw®',
			'seo_desc'  => "Download ArtisRaw's ISO 9001:2015 certificate, forestry licence #4684, MSDS and QC documentation. Trust is a process — every claim has a document.",
			'qa'        => 'ArtisRaw is the first ISO 9001:2015-certified olive wood manufacturer in Tunisia. Buyers can download the ISO certificate, forestry licence #4684, food-contact MSDS, quality reports and export documents, and request batch-level proof photos before shipment.',
			'meta'      => array( 'trust_downloads' => '1' ),
			'content'   => "<h2>Trust is a process, not a logo</h2><p>Every claim on this site is backed by a downloadable document. ArtisRaw holds ISO 9001:2015 certification for the design, production and commercialization of olive wood products, and won the MEA Business Award for Best Artisan Olive Wood Products Company in North Africa.</p><h2>What the documents prove</h2><ul><li><strong>Quality</strong> — ISO 9001:2015 certificate and QC reports.</li><li><strong>Legality &amp; sourcing</strong> — forestry licence #4684 and an EUDR-readiness statement.</li><li><strong>Food safety</strong> — finish MSDS for food-contact items.</li><li><strong>Export</strong> — ISPM-15 and sample export documents.</li></ul>",
		),
		array(
			'slug' => 'quality-control', 'title' => 'Olive Wood Quality Control', 'template' => $T_TRUST,
			'seo_title' => 'Olive Wood Quality Control | Unit-by-Unit QC | ArtisRaw®',
			'seo_desc'  => 'How ArtisRaw controls quality: unit-by-unit inspection, ≥96% first-pass yield, ≤0.5% returns, batch photo documentation and packing control.',
			'qa'        => 'ArtisRaw runs unit-by-unit quality control under an ISO 9001 system: every piece is inspected for finish, dimensions and grain, achieving ≥96% first-pass yield and ≤0.5% returns. Batch photo documentation and packing checks are available before each shipment.',
			'content'   => "<h2>Inspection at every step</h2><p>Quality is built in, not inspected at the end. Raw material is graded, drying is controlled, and machining and hand-finishing each carry checkpoints. Finished pieces pass a unit-by-unit inspection before packing.</p><h2>The numbers</h2><ul><li>≥96% first-pass yield</li><li>≤0.5% return rate</li><li>Batch photo documentation on request</li></ul>",
		),
		array(
			'slug' => 'shipping-logistics', 'title' => 'Global Logistics: From Sfax to Your Market', 'template' => $T_TRUST,
			'seo_title' => 'Olive Wood Export & Shipping | Incoterms, Lead Times, 30+ Countries | ArtisRaw®',
			'seo_desc'  => 'FOB Tunisia, CIF, DAP or DDP. Stock ships in 72 h; custom in 6–8 weeks. Air 5–12 days, ocean 25–40 days. ISPM-15 pallets and full export documents.',
			'qa'        => 'ArtisRaw exports olive wood products to 30+ countries under FOB Tunisia, CIF, DAP or DDP terms. In-stock orders ship within 72 hours; custom production takes 6–8 weeks. Transit: 5–12 days by air, 25–40 days by ocean, on ISPM-15 pallets with complete export documentation.',
			'content'   => "<h2>Lead times &amp; Incoterms</h2><table><thead><tr><th>Mode</th><th>Transit</th><th>MOQ</th><th>Incoterms</th></tr></thead><tbody><tr><td>Air freight</td><td>5–12 days</td><td>50</td><td>FOB / CIF / DAP / DDP</td></tr><tr><td>Ocean (LCL)</td><td>25–40 days</td><td>50</td><td>FOB / CIF / DAP</td></tr><tr><td>Ocean (FCL)</td><td>25–40 days</td><td>500</td><td>FOB / CIF / DDP</td></tr></tbody></table><h2>Destination support</h2><ul><li><strong>USA &amp; Canada</strong> — Lacey Act packet, HTS 4419 classification, USD invoicing.</li><li><strong>Europe</strong> — EUDR traceability and micro-hub samples.</li><li><strong>GCC &amp; Asia</strong> — consolidated freight and DDP on request.</li></ul><h2>Packaging</h2><p>Export-ready cartons on ISPM-15 pallets, zero-plastic packing where possible, and retail-ready packaging for private-label orders.</p>",
		),
		array(
			'slug' => 'how-to-order', 'title' => 'How to Order Wholesale Olive Wood', 'template' => $T_TRUST,
			'seo_title' => 'How to Order Wholesale Olive Wood | MOQ & Samples | ArtisRaw®',
			'seo_desc'  => 'Order in five steps: brief, quote with MOQ, sample validation, production with QC, then export. MOQ from 50; samples deducted from your first order.',
			'qa'        => 'Ordering from ArtisRaw takes five steps: send a buyer brief, receive a quote with MOQ and pricing, validate samples (cost deducted from your first order), approve production with QC checkpoints, then export with full documentation. MOQ starts at 50 units; quotes within 24 hours.',
			'content'   => "<h2>Five steps to your first order</h2><ol><li><strong>Buyer brief</strong> — market, categories, quantities and target date.</li><li><strong>Quote &amp; MOQ</strong> — pricing, packaging options and export info within 24 hours.</li><li><strong>Sample / branding</strong> — samples or private-label proofs validated before production.</li><li><strong>Production &amp; QC</strong> — manufacture with quality checkpoints and batch photos.</li><li><strong>Export</strong> — ISPM-15 pallets, commercial invoice and packing list.</li></ol><p>Samples are available with their cost deducted from your first production order.</p>",
		),
		array(
			'slug' => 'references', 'title' => 'Olive Wood Documents & Downloads', 'template' => $T_TRUST,
			'seo_title' => 'Olive Wood Documents & Downloads | ISO, MSDS, Compliance | ArtisRaw®',
			'seo_desc'  => 'Download centre: ISO 9001 certificate, forestry licence #4684, finish MSDS, compliance pack (Lacey/EUDR), line-sheet and sample export documents.',
			'qa'        => 'The ArtisRaw download centre holds the proof behind every claim: ISO 9001:2015 certificate, forestry licence #4684, food-contact MSDS, the Lacey/EUDR compliance pack, the wholesale line-sheet and sample export documents — all available to professional buyers.',
			'meta'      => array( 'trust_downloads' => '1' ),
			'content'   => '<p>Every claim on this site links here. Download the document you need, or request batch-level proof photos for a specific order.</p>',
		),
		array(
			'slug' => 'about', 'title' => 'Mediterranean Heritage, Crafted in Sustainable Luxury', 'template' => $T_TRUST,
			'seo_title' => 'About ArtisRaw | Tunisian Olive Wood Manufacturer Since 2019',
			'seo_desc'  => 'Founded in Sfax in 2019, ArtisRaw combines 25+ artisans with Crafts 4.0 production. Meet the founders and the four pillars behind our collections.',
			'qa'        => 'ArtisRaw was founded in Sfax, Tunisia in 2019 by Mohamed Bilel Cherif, Ahmed Sakka and Ihsen Triki. The company pairs 25+ registered artisans with CNC precision and an ISO 9001 quality system — a “Crafts 4.0” model serving wholesale partners in more than 30 countries.',
			'content'   => "<h2>From the land of the olive tree</h2><p>Born along the Mediterranean shores of Tunisia, ArtisRaw brings a 3,000-year legacy of olive-wood craftsmanship to professional buyers. We transform reclaimed, end-of-life Chemlali olive wood into premium handmade pieces for kitchenware, hospitality, retail and gifting.</p><h2>Crafts 4.0</h2><p>We pair 25+ registered artisans with CNC precision and an ISO 9001 quality system, so handmade character meets consistent, export-ready quality — the world's first ISO 9001:2015-certified olive wood manufacturer.</p><h2>Why Chemlali olive wood is superior</h2><p>The Chemlali variety grown around Sfax has a dense grain, low porosity and beautiful natural contrast. That density resists knife scarring and water absorption — exactly what professional kitchen, retail and hospitality buyers need from a working surface.</p>",
				'meta'      => array( 'trust_extras' => 'about' ),
				'update'    => true,
			),
		array(
			'slug' => 'olive-wood-supplier-usa', 'title' => 'Olive Wood Supplier for the USA', 'template' => $T_TRUST,
			'seo_title' => 'Olive Wood Supplier USA | Lacey Act Ready, Fast Import | ArtisRaw®',
			'seo_desc'  => 'US importers: olive wood wholesale with Lacey Act declaration data, HTS 4419 classification, USD invoicing and air freight in 5–12 days. MOQ 50.',
			'qa'        => 'ArtisRaw supplies US wholesale buyers with olive wood boards, serveware and utensils — complete with Lacey Act declaration data, HTS 4419 classification and USD invoicing. Air freight runs 5–12 days; MOQ from 50, with private-label engraving and ISO 9001 quality control.',
			'content'   => "<h2>Built for US importers</h2><p>We prepare the paperwork your customs broker needs and quote in USD. Air freight reaches the US in 5–12 days; ocean in 25–40.</p><ul><li>Lacey Act PPQ 505 declaration data per shipment</li><li>HTS 4419 classification guidance</li><li>USD invoicing and DDP options</li></ul>",
		),
		array(
			'slug' => 'wholesale-account', 'title' => 'Wholesale Account', 'template' => 'tpl-account.php',
			'meta' => array( 'seo_noindex' => '1' ), 'update' => true,
			'seo_title' => 'Wholesale Account | Order Olive Wood Direct | ArtisRaw®',
			'seo_desc'  => 'Apply for an ArtisRaw wholesale account: validated B2B pricing, SKU history, faster reorders and private-label support. MOQ from 50 units.',
			'qa'        => 'A wholesale account gives validated professional buyers direct access to B2B pricing, saved SKU references, faster reorders and private-label support. Apply with your company details and destination market; quotes and terms are confirmed within 24 hours.',
			'content'   => "<h2>Why open an account</h2><ul><li>Validated wholesale pricing</li><li>Saved SKU references and order history</li><li>Faster reorders and replenishment</li><li>Private-label and corporate-gift support</li></ul><p>Apply using the quote form with your company name, destination market and estimated quantities.</p>",
		),

		/* ---- Services & Worldwide (Phase 5 design parity) ---- */
		array(
			'slug' => 'services', 'title' => 'B2B Olive Wood Services: Wholesale, Private Label & Export', 'template' => $T_SVC,
			'seo_title' => 'Olive Wood B2B Services | Wholesale, Private Label, Export | ArtisRaw®',
			'seo_desc'  => 'ArtisRaw B2B services: wholesale production, private label, corporate gifts, custom orders, quality control and export support — for retailers, distributors and hospitality.',
			'qa'        => 'ArtisRaw offers six core B2B services — wholesale production, private label, corporate gifts, custom orders, quality control and export support — for retailers, distributors, hospitality buyers and private-label brands. A simple eight-step process takes you from buyer brief to reorder, with MOQ from 50 units.',
		),
		array(
			'slug' => 'worldwide', 'title' => 'Worldwide Olive Wood Export: From Tunisia to 30+ Countries', 'template' => $T_WORLD,
			'seo_title' => 'Worldwide Olive Wood Export | 30+ Countries from Tunisia | ArtisRaw®',
			'seo_desc'  => 'ArtisRaw exports olive wood worldwide from Sfax, Tunisia: USA & Canada, Europe, GCC and Asia — with Lacey Act, EUDR, Incoterms and full export documentation per shipment.',
			'qa'        => 'ArtisRaw exports handmade olive wood from Sfax, Tunisia to more than 30 countries across North America, Europe, the GCC/Middle East and Asia. Each shipment includes export documentation — commercial invoice, packing list, ISPM-15 pallets and compliance data (Lacey Act / EUDR) — under the Incoterms your broker prefers.',
		),

		/* ---- Nav-linked pages needed for a clean Phase-1 crawl (fuller copy in Phase 2/7) ---- */
		array(
			'slug' => 'production-process', 'title' => 'From Olive Tree to Export-Ready Order', 'template' => $T_TRUST,
			'seo_title' => 'Olive Wood Factory Tunisia | Our Production Process | ArtisRaw®',
			'seo_desc'  => 'Inside the ArtisRaw olive wood factory in Sfax: sourcing, drying, machining, handcrafting, food-safe finishing and export — with QC at every step.',
			'qa'        => 'ArtisRaw’s production runs from reclaimed Chemlali olive wood to export-ready orders: licensed sourcing, controlled drying, CNC machining, hand-finishing, food-safe oil-and-beeswax finishing, then QC, ISPM-15 packing and export — all under an ISO 9001 quality system in Sfax, Tunisia.',
			'content'   => "<h2>End-to-end manufacturing</h2><ol><li><strong>Sourcing</strong> — premium Chemlali olive wood from licensed, end-of-life trees.</li><li><strong>Drying &amp; curing</strong> — controlled drying to stabilise the wood.</li><li><strong>Machining</strong> — CNC precision for consistent dimensions.</li><li><strong>Handcrafting</strong> — artisans refine each piece.</li><li><strong>Food-safe finishing</strong> — mineral oil + beeswax, documented.</li><li><strong>QC, packing &amp; export</strong> — inspection, ISPM-15 pallets, export documents.</li></ol>",
				'meta'      => array( 'trust_extras' => 'process' ),
				'update'    => true,
		),
		array(
			'slug' => 'sustainability', 'title' => 'Sustainable by Origin', 'template' => $T_TRUST,
			'seo_title' => 'Sustainable Olive Wood | Reclaimed Wood & Reforestation | ArtisRaw®',
			'seo_desc'  => 'ArtisRaw works reclaimed end-of-life olive wood, minimises waste and sponsors reforestation via trees.org — with full EUDR traceability for EU buyers.',
			'qa'        => 'ArtisRaw’s sustainability is built into sourcing: we use reclaimed, end-of-life Chemlali olive wood, use every part of the tree to minimise waste, and sponsor reforestation through trees.org. EU buyers receive full EUDR traceability and due-diligence documentation.',
			'content'   => "<h2>Responsible material</h2><p>We work only reclaimed, end-of-life olive wood — trees past fruit-bearing age — and use every part to reduce waste. Finishes are food-safe and free of synthetic coatings.</p><h2>Giving back</h2><p>We sponsor reforestation through trees.org and provide full EUDR traceability for EU importers.</p>",
		),
		array(
			'slug' => 'olive-wood', 'title' => 'The Olive Wood Guide', 'template' => $T_TRUST,
			'seo_title' => 'Olive Wood Guide | Sourcing, Care & Compliance | ArtisRaw®',
			'seo_desc'  => 'The ArtisRaw olive wood guide for B2B buyers: Chemlali wood properties, food-safe care, import compliance (Lacey Act, EUDR) and sourcing — with sourced facts.',
			'qa'        => 'The ArtisRaw Olive Wood Guide is a B2B knowledge hub on Chemlali olive wood: material properties, food-safe care, and import compliance (Lacey Act, EUDR). In-depth articles, each with a quick answer, data and sources, are published here on an ongoing basis.',
			'content'   => "<h2>What you’ll find here</h2><p>Practical, sourced answers for professional buyers — from why Chemlali olive wood resists knife scarring to importing into the US and EU. New articles are added regularly.</p><ul><li><a href=\"/certifications/\">Certifications &amp; documents</a></li><li><a href=\"/shipping-logistics/\">Export, Incoterms &amp; lead times</a></li><li><a href=\"/quality-control/\">Quality control</a></li></ul>",
		),
		array(
			'slug' => 'privacy', 'title' => 'Privacy Policy', 'template' => 'default',
			'seo_title' => 'Privacy Policy | ArtisRaw®',
			'seo_desc'  => 'How ArtisRaw collects and uses the information you share through our quote forms and email — used only to prepare and fulfil your wholesale request.',
			'content'   => "<p>ArtisRaw uses the details you submit through our forms and email solely to prepare your quote and fulfil your wholesale request. We do not sell your data. Hidden form fields capture the page and campaign source so we can respond in context.</p><p>To request access to, or deletion of, your data, email <a href=\"mailto:contact@artisraw.com\">contact@artisraw.com</a>.</p>",
		),

		/* ---- Contact / FAQ (tpl-contact-faq) ---- */
		array(
			'slug' => 'faq', 'title' => 'Frequently Asked Questions for B2B Buyers', 'template' => $T_CF,
			'seo_title' => 'Olive Wood Wholesale FAQ | MOQ, Samples, Private Label, Food Safety',
			'seo_desc'  => 'Clear answers before you request a quote: minimum order quantities, samples, lead times, private label, certifications, food safety and export documents.',
			'qa'        => 'The most common wholesale questions: MOQ starts at 50 units (varies by SKU); samples are available with cost deducted from the first order; in-stock items ship in 72 hours and custom production takes 6–8 weeks; private-label engraving is done in-house; every product uses food-safe finishes with MSDS available.',
			'meta'      => array( 'cf_mode' => 'faq' ),
		),
		array(
			'slug' => 'contact', 'title' => 'Let’s Build Your Olive Wood Collection', 'template' => $T_CF,
			'seo_title' => 'Contact ArtisRaw | Request a Wholesale Quote Within 24 h',
			'seo_desc'  => 'Send your B2B request: catalogue, quotation, private label or export support. Factory: Route Saltania Km 4.5, Sfax, Tunisia. Quotes within 24 hours.',
			'qa'        => 'Contact ArtisRaw for wholesale quotations, private-label projects, corporate gifts and export support. Send the two-step quote form for a tailored proposal within 24 hours, or reach the factory directly: Route Saltania Km 4.5, Sfax, Tunisia — email contact@artisraw.com or WhatsApp.',
			'meta'      => array( 'cf_mode' => 'contact' ),
		),
		array(
			'slug' => 'request-quote', 'title' => 'Request a Wholesale Quote', 'template' => $T_CF,
			'seo_title' => 'Request a Quote | Olive Wood Wholesale & Private Label | ArtisRaw®',
			'seo_desc'  => 'Get a wholesale olive wood quote within 24 hours: line-sheet, MOQ, pricing and import documentation. Private-label engraving available. MOQ 50.',
			'qa'        => 'Request a wholesale olive wood quote and ArtisRaw replies within 24 hours with your line-sheet, MOQ, pricing and import documentation (Lacey Act / EUDR). Share your destination, categories and quantities — private-label engraving and samples are available.',
			'meta'      => array( 'cf_mode' => 'quote' ),
		),
	);
}
