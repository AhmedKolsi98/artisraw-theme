<?php
/**
 * Custom post types + structured-data helpers (SPEC §2, §4).
 *
 * SKU: structured product data for the spec-card grid (hub + category pages).
 * Stored as post meta with the same keys ACF uses, so it reads correctly whether
 * or not the ACF plugin is active.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * SKU custom post type (structured data, not a front-facing URL).
 * ---------------------------------------------------------------------- */
function artisraw_register_sku_cpt() {
	register_post_type( 'sku', array(
		'labels'       => array(
			'name'          => __( 'SKUs', 'artisraw' ),
			'singular_name' => __( 'SKU', 'artisraw' ),
			'add_new_item'  => __( 'Add SKU', 'artisraw' ),
			'edit_item'     => __( 'Edit SKU', 'artisraw' ),
		),
		'public'       => false,      // queried directly; no standalone URLs.
		'show_ui'      => true,
		'show_in_menu' => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-screenoptions',
		'menu_position' => 25,
		'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
		'has_archive'  => false,
		'rewrite'      => false,
	) );
}
add_action( 'init', 'artisraw_register_sku_cpt' );

/**
 * SKU category taxonomy (mirrors the 5 wholesale category URLs).
 */
function artisraw_register_sku_taxonomy() {
	register_taxonomy( 'sku_category', 'sku', array(
		'labels'       => array( 'name' => __( 'SKU categories', 'artisraw' ), 'singular_name' => __( 'SKU category', 'artisraw' ) ),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'hierarchical' => true,
		'rewrite'      => false,
	) );
	// Ensure the canonical terms exist.
	$terms = array(
		'cutting-boards'  => 'Cutting Boards',
		'utensils'        => 'Utensils',
		'bowls-serveware' => 'Bowls & Serveware',
		'chess-sets'      => 'Chess Sets',
		'decor-bath'      => 'Décor & Bath',
	);
	foreach ( $terms as $slug => $name ) {
		if ( ! term_exists( $slug, 'sku_category' ) ) {
			wp_insert_term( $name, 'sku_category', array( 'slug' => $slug ) );
		}
	}
}
add_action( 'init', 'artisraw_register_sku_taxonomy', 5 );

/**
 * Assign seeded SKUs to category terms (idempotent, keyed by SKU code).
 */
function artisraw_assign_sku_terms() {
	if ( get_option( 'artisraw_sku_terms_done' ) ) {
		return;
	}
	$map = array(
		'AR-CB-30' => 'cutting-boards', 'AR-CB-45' => 'cutting-boards',
		'AR-BW-25' => 'bowls-serveware', 'AR-MP-12' => 'bowls-serveware', 'AR-PB-4' => 'bowls-serveware',
		'AR-UT-3'  => 'utensils',
	);
	$skus = get_posts( array( 'post_type' => 'sku', 'posts_per_page' => -1, 'fields' => 'ids' ) );
	if ( empty( $skus ) ) {
		return; // seed not run yet; try again next load.
	}
	foreach ( $skus as $id ) {
		$code = get_post_meta( $id, 'sku_code', true );
		if ( isset( $map[ $code ] ) ) {
			wp_set_object_terms( $id, $map[ $code ], 'sku_category' );
		}
	}
	update_option( 'artisraw_sku_terms_done', 1 );
}
add_action( 'init', 'artisraw_assign_sku_terms', 25 );

/* -------------------------------------------------------------------------
 * Field reader: ACF when present, else raw post meta (same keys).
 * ---------------------------------------------------------------------- */
function artisraw_get( $key, $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$val = get_field( $key, $post_id );
		if ( null !== $val && '' !== $val ) {
			return $val;
		}
	}
	return get_post_meta( $post_id, $key, true );
}

/**
 * Build the array consumed by artisraw_sku_card() from a SKU post.
 */
function artisraw_sku_to_array( $post_id ) {
	$image = array();
	if ( has_post_thumbnail( $post_id ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'artisraw-600' );
		if ( $src ) {
			$image = array( 'url' => $src[0], 'w' => $src[1], 'h' => $src[2], 'alt' => get_the_title( $post_id ) . ' — olive wood' );
		}
	}
	return array(
		'name'        => get_the_title( $post_id ),
		'sku'         => artisraw_get( 'sku_code', $post_id ),
		'dimensions'  => artisraw_get( 'dimensions', $post_id ),
		'unit_weight' => artisraw_get( 'unit_weight', $post_id ),
		'case_pack'   => artisraw_get( 'case_pack', $post_id ),
		'carton'      => artisraw_get( 'carton', $post_id ),
		'moq'         => artisraw_get( 'moq', $post_id ),
		'lead_time'   => artisraw_get( 'lead_time', $post_id ),
		'exw_tier'    => artisraw_get( 'exw_tier', $post_id ),
		'image'       => $image,
	);
}

/**
 * Ready-to-ship SKUs, ordered by menu_order.
 *
 * @return int[] post IDs
 */
function artisraw_get_ready_skus( $limit = 6 ) {
	$q = new WP_Query( array(
		'post_type'      => 'sku',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
		'meta_query'     => array(
			array( 'key' => 'ready_to_ship', 'value' => '1', 'compare' => '=' ),
		),
	) );
	$ids = wp_list_pluck( $q->posts, 'ID' );
	wp_reset_postdata();
	// Fallback: if none flagged (e.g. fresh import), return latest.
	if ( empty( $ids ) ) {
		$ids = get_posts( array( 'post_type' => 'sku', 'posts_per_page' => $limit, 'fields' => 'ids', 'orderby' => 'menu_order', 'order' => 'ASC' ) );
	}
	return $ids;
}

/**
 * SKU IDs in a given category term.
 *
 * @return int[]
 */
function artisraw_get_skus_by_term( $term_slug, $limit = 12 ) {
	return get_posts( array(
		'post_type'      => 'sku',
		'posts_per_page' => $limit,
		'fields'         => 'ids',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'tax_query'      => array(
			array( 'taxonomy' => 'sku_category', 'field' => 'slug', 'terms' => $term_slug ),
		),
	) );
}

/* -------------------------------------------------------------------------
 * One-time seed of 6 ready-to-ship SKUs (dev convenience until SKUs are
 * entered in admin). Idempotent: runs once, guarded by an option flag.
 * Delete the 'artisraw_skus_seeded' option to re-seed.
 * ---------------------------------------------------------------------- */
function artisraw_seed_skus() {
	if ( get_option( 'artisraw_skus_seeded' ) ) {
		return;
	}
	if ( ! post_type_exists( 'sku' ) ) {
		return;
	}
	$existing = get_posts( array( 'post_type' => 'sku', 'posts_per_page' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		update_option( 'artisraw_skus_seeded', 1 );
		return;
	}

	$skus = array(
		array( 'title' => 'Classic Olive Wood Cutting Board 30 cm', 'sku_code' => 'AR-CB-30', 'dimensions' => '30 × 18 × 2 cm', 'unit_weight' => '0.7 kg', 'case_pack' => '12', 'carton' => '40 × 30 × 26 cm', 'moq' => '50', 'lead_time' => '72 h (stock)', 'exw_tier' => 'on request' ),
		array( 'title' => 'Large Charcuterie Board 45 cm', 'sku_code' => 'AR-CB-45', 'dimensions' => '45 × 24 × 2.5 cm', 'unit_weight' => '1.2 kg', 'case_pack' => '8', 'carton' => '50 × 36 × 28 cm', 'moq' => '50', 'lead_time' => '72 h (stock)', 'exw_tier' => 'on request' ),
		array( 'title' => 'Olive Wood Serving Bowl Ø25 cm', 'sku_code' => 'AR-BW-25', 'dimensions' => 'Ø25 × 9 cm', 'unit_weight' => '0.8 kg', 'case_pack' => '10', 'carton' => '54 × 28 × 30 cm', 'moq' => '50', 'lead_time' => '72 h (stock)', 'exw_tier' => 'on request' ),
		array( 'title' => 'Utensil Set — Spoon, Spatula & Fork', 'sku_code' => 'AR-UT-3', 'dimensions' => '30 cm', 'unit_weight' => '0.25 kg', 'case_pack' => '24', 'carton' => '42 × 30 × 24 cm', 'moq' => '100', 'lead_time' => '72 h (stock)', 'exw_tier' => 'on request' ),
		array( 'title' => 'Olive Wood Mortar & Pestle 12 cm', 'sku_code' => 'AR-MP-12', 'dimensions' => 'Ø12 × 10 cm', 'unit_weight' => '0.6 kg', 'case_pack' => '12', 'carton' => '40 × 30 × 24 cm', 'moq' => '50', 'lead_time' => '2–3 weeks', 'exw_tier' => 'on request' ),
		array( 'title' => 'Spice Pinch Bowls — Set of 4', 'sku_code' => 'AR-PB-4', 'dimensions' => 'Ø8 × 4 cm', 'unit_weight' => '0.3 kg', 'case_pack' => '20', 'carton' => '38 × 28 × 22 cm', 'moq' => '100', 'lead_time' => '72 h (stock)', 'exw_tier' => 'on request' ),
	);

	foreach ( $skus as $i => $sku ) {
		$id = wp_insert_post( array(
			'post_type'   => 'sku',
			'post_status' => 'publish',
			'post_title'  => $sku['title'],
			'menu_order'  => $i,
		) );
		if ( $id && ! is_wp_error( $id ) ) {
			unset( $sku['title'] );
			foreach ( $sku as $k => $v ) {
				update_post_meta( $id, $k, $v );
			}
			update_post_meta( $id, 'ready_to_ship', '1' );
		}
	}
	update_option( 'artisraw_skus_seeded', 1 );
}
add_action( 'init', 'artisraw_seed_skus', 20 );
