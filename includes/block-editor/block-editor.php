<?php

add_action( 'init', 'wpcf7_init_block_editor_assets', 10, 0 );

function wpcf7_init_block_editor_assets() {
	$assets = array();

	$asset_file = wpcf7_plugin_path(
		'includes/block-editor/index.asset.php'
	);

	if ( file_exists( $asset_file ) ) {
		$assets = include( $asset_file );
	}

	$assets = wp_parse_args( $assets, array(
		'dependencies' => array(
			'wp-api-fetch',
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-element',
			'wp-i18n',
			'wp-url',
		),
		'version' => WPCF7_VERSION,
	) );

	wp_register_script(
		'contact-form-7-block-editor',
		wpcf7_plugin_url( 'includes/block-editor/index.js' ),
		$assets['dependencies'],
		$assets['version']
	);

	wp_set_script_translations(
		'contact-form-7-block-editor',
		'contact-form-7'
	);
	
	/**
	 * wpcf7_plugin_path( 'block-editor' ) arise a Notice: Function WP_Block_Type_Registry::register was called incorrectly. Block type names must contain a namespace prefix. Example: my-plugin/my-custom-block-type Please see Debugging in WordPress for more information. (This message was added in version 5.0.0.) in wp-includes/functions.php on line 5835
	 * replace wpcf7_plugin_path( 'block-editor' ) this with 'contact-form-7/contact-form-selector' with this code. 
	 * @author lincolndu
	*/
	
	register_block_type(
		'contact-form-7/contact-form-selector',
		array(
			'editor_script' => 'contact-form-7-block-editor',
		)
	);

	$contact_forms = array_map(
		function ( $contact_form ) {
			return array(
				'id' => $contact_form->id(),
				'slug' => $contact_form->name(),
				'title' => $contact_form->title(),
				'locale' => $contact_form->locale(),
			);
		},
		WPCF7_ContactForm::find( array(
			'posts_per_page' => 20,
			'orderby' => 'modified',
			'order' => 'DESC',
		) )
	);

	wp_add_inline_script(
		'contact-form-7-block-editor',
		sprintf(
			'window.wpcf7 = {contactForms:%s};',
			json_encode( $contact_forms )
		),
		'before'
	);

}
