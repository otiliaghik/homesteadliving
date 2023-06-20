<?php
/**
 * Frost: Block Patterns
 *
 * @since Frost 0.8.0
 */

/**
 * Registers block patterns, categories, and type.
 *
 * @since Frost 0.8.0
 */
function frost_register_block_patterns() {

	if ( function_exists( 'register_block_pattern_category_type' ) ) {
		register_block_pattern_category_type( 'frost', array( 'label' => __( 'Frost', 'frost' ) ) );
	}

	$block_pattern_categories = array(
		'frost-page'    => array(
			'label'         => __( 'Page', 'frost' ),
			'categoryTypes' => array( 'frost' ),
		),
		'frost-query'   => array(
			'label'         => __( 'Query', 'frost' ),
			'categoryTypes' => array( 'frost' ),
		),
	);

	/**
	 * Filters the theme block pattern categories.
	 *
	 * @since Frost 0.8.0
	 *
	 * @param array[] $block_pattern_categories {
	 *     An associative array of block pattern categories, keyed by category name.
	 *
	 *     @type array[] $properties {
	 *         An array of block category properties.
	 *
	 *         @type string $label A human-readable label for the pattern category.
	 *     }
	 * }
	 */
	$block_pattern_categories = apply_filters( 'frost_block_pattern_categories', $block_pattern_categories );

	foreach ( $block_pattern_categories as $name => $properties ) {
		register_block_pattern_category( $name, $properties );
	}

	$block_patterns = array(
		'hidden/hidden-404',
		'page/page-home',
		'page/page-link',
		'page/page-link-black-background',
		'page/page-split-cover-text',
		'page/page-split-text-cover',
		'query/query-default',
		'query/query-grid',
		'query/query-list',
	);

	/**
	 * Filters the theme block patterns.
	 *
	 * @since Frost 0.8.0
	 *
	 * @param $block_patterns array List of block patterns by name.
	 */
	$block_patterns = apply_filters( 'frost_block_patterns', $block_patterns );

	foreach ( $block_patterns as $block_pattern ) {
		register_block_pattern(
			'frost/' . $block_pattern,
			require get_theme_file_path( '/inc/patterns/' . $block_pattern . '.php' )
		);
	}

}
add_action( 'init', 'frost_register_block_patterns', 9 );
