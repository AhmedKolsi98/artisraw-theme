<?php
/**
 * ACF field groups registered in code (version-controlled, SPEC §2).
 * Inert until the ACF plugin is active — guarded by function_exists().
 *
 * Phase 1: the global SEO & Schema group applied to every post type.
 * SKU / FAQ / stats / document groups are added in their phases.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'artisraw_register_acf_fields' );

function artisraw_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'    => 'group_artisraw_seo',
		'title'  => 'SEO & Schema',
		'fields' => array(
			array(
				'key'          => 'field_seo_title',
				'label'        => 'SEO title',
				'name'         => 'seo_title',
				'type'         => 'text',
				'instructions' => 'Overrides the document <title>. ≤60 chars. Pattern: {Primary keyword} | {Differentiator} | ArtisRaw®',
				'maxlength'    => 70,
			),
			array(
				'key'          => 'field_seo_meta_description',
				'label'        => 'Meta description',
				'name'         => 'seo_meta_description',
				'type'         => 'textarea',
				'instructions' => '≤155 chars, include one concrete number (MOQ 50 / 72 h / ISO 9001).',
				'maxlength'    => 160,
				'rows'         => 3,
				'new_lines'    => '',
			),
			array(
				'key'           => 'field_seo_og_image',
				'label'         => 'Social share image',
				'name'          => 'seo_og_image',
				'type'          => 'image',
				'instructions'  => '1200×630. Falls back to the featured image, then the theme default.',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
			),
			array(
				'key'           => 'field_seo_noindex',
				'label'         => 'Hide from search engines (noindex)',
				'name'          => 'seo_noindex',
				'type'          => 'true_false',
				'instructions'  => 'Adds noindex,nofollow. Leave off for live pages.',
				'ui'            => 1,
				'default_value' => 0,
			),
			array(
				'key'           => 'field_seo_schema_faq',
				'label'         => 'Output FAQPage schema',
				'name'          => 'seo_schema_faq',
				'type'          => 'true_false',
				'instructions'  => 'Emit FAQPage JSON-LD from this page’s FAQ items (when present).',
				'ui'            => 1,
				'default_value' => 0,
			),
		),
		'location' => array(
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ) ),
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'active'                => true,
		'show_in_rest'          => 0,
		'description'           => 'Per-URL SEO overrides and schema toggles (SPEC §6.2).',
	) );
}
