<?php
/**
 * Breadcrumbs — visible <nav aria-label="Breadcrumb"> + BreadcrumbList JSON-LD.
 * The visible trail and the schema come from one array (SPEC §4, §6.7).
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the ordered trail of [ label, url ] pairs for the current view.
 * The final crumb (current page) has a null url.
 */
function artisraw_breadcrumb_trail() {
	$trail = array( array( 'label' => __( 'Home', 'artisraw' ), 'url' => home_url( '/' ) ) );

	if ( is_singular() ) {
		$post_id   = get_queried_object_id();
		$ancestors = array_reverse( get_post_ancestors( $post_id ) );
		foreach ( $ancestors as $ancestor_id ) {
			$trail[] = array( 'label' => get_the_title( $ancestor_id ), 'url' => get_permalink( $ancestor_id ) );
		}
		$trail[] = array( 'label' => get_the_title( $post_id ), 'url' => null );
	} elseif ( is_post_type_archive() ) {
		$trail[] = array( 'label' => post_type_archive_title( '', false ), 'url' => null );
	} elseif ( is_category() || is_tax() || is_tag() ) {
		$trail[] = array( 'label' => single_term_title( '', false ), 'url' => null );
	} elseif ( is_search() ) {
		$trail[] = array( 'label' => __( 'Search results', 'artisraw' ), 'url' => null );
	} elseif ( is_404() ) {
		$trail[] = array( 'label' => __( 'Page not found', 'artisraw' ), 'url' => null );
	}

	return $trail;
}

/**
 * Render the visible breadcrumb nav. No-op on the home page.
 */
function artisraw_breadcrumbs() {
	if ( is_front_page() || is_home() ) {
		return;
	}
	$trail = artisraw_breadcrumb_trail();
	if ( count( $trail ) < 2 ) {
		return;
	}

	echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'artisraw' ) . '">';
	echo '<ol class="breadcrumb__list container">';
	$last = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		echo '<li class="breadcrumb__item">';
		if ( $i < $last && ! empty( $crumb['url'] ) ) {
			printf( '<a href="%s">%s</a>', esc_url( $crumb['url'] ), esc_html( $crumb['label'] ) );
		} else {
			printf( '<span aria-current="page">%s</span>', esc_html( $crumb['label'] ) );
		}
		echo '</li>';
	}
	echo '</ol></nav>';

	// Matching BreadcrumbList JSON-LD.
	$items = array();
	foreach ( $trail as $i => $crumb ) {
		$item = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $crumb['label'],
		);
		if ( ! empty( $crumb['url'] ) ) {
			$item['item'] = $crumb['url'];
		}
		$items[] = $item;
	}
	if ( function_exists( 'artisraw_jsonld' ) ) {
		artisraw_jsonld( array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		) );
	}
}
