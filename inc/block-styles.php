<?php
/**
 * This file adds block styles to the Frost WordPress theme.
 *
 * @package Frost
 * @author  WP Engine
 * @license GNU General Public License v2 or later
 * @link    https://frostwp.com/
 */

/**
 * Register block styles.
 *
 * @since 0.8.5
 */
function frost_register_block_styles() {

	$block_styles = array(
		'core/button'          => array(
			'fill-white'    => __( 'Fill White', 'frost' ),
			'outline-white' => __( 'Outline White', 'frost' ),
		),
		'core/image'           => array(
			'issue'    => __( 'Issue', 'frost' ),
			'no-margin' => __( 'No Margin', 'frost' ),
		),
		'core/navigation-link' => array(
			'fill'          => __( 'Fill', 'frost' ),
			'fill-white'    => __( 'Fill White', 'frost' ),
			'outline'       => __( 'Outline', 'frost' ),
			'outline-white' => __( 'Outline White', 'frost' ),
		),
		'core/paragraph'       => array(
			'no-margin' => __( 'No Margin', 'frost' ),
			'issue-title' => __( 'Issue Title', 'frost' ),
		),
	);

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style_name => $style_label ) {
			register_block_style(
				$block,
				array(
					'name'  => $style_name,
					'label' => $style_label,
				)
			);
		}
	}
}
add_action( 'init', 'frost_register_block_styles' );
