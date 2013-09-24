<?php

class ES_Media_Management {

	private static $is_instantiated = false;

	private $image_sizes = array(
		'thumbnail' => array(
			'width' => 150,
			'height' => 150,
			'crop' => true,
			'label' => 'Thumbnail (Cropped)',
			'pos' => 0
		),
		'small' => array(
			'width' => 150,
			'height' => 150,
			'crop' => false,
			'label' => 'Small',
			'pos' => 1
		),
		'medium' => array(
			'width' => 300,
			'height' => 300,
			'crop' => false,
			'label' => 'Medium',
			'pos' => 2
		),
		'large' => array(
			'width' => 1024,
			'height' => 1024,
			'crop' => false,
			'label' => 'Large',
			'pos' => 3
		)
		// Full Size is always available
	);

	public function __construct() {

		if ( true == self::$is_instantiated ) return; // Singleton

		global $es_media_management;

		$es_media_management = $this;

		add_action('after_setup_theme', array( $this, '_add_image_sizes' ));
		add_action('admin_enqueue_scripts', array( $this, '_enqueue_scripts'));

		self::$is_instantiated = true;

		do_action('es_media_management_loaded');

	}

	public function _enqueue_scripts() {

		wp_enqueue_script('es_media_management', plugins_url('/js/es_media_admin.js', dirname(__FILE__)), array('jquery'), 1.0);
	}

	public function _add_image_sizes() {

		add_theme_support('post-thumbnails');

		$registered_image_sizes = get_intermediate_image_sizes();

		foreach ( $this->image_sizes as $slug => $img_data ) {
			if ( false === array_search( $slug, $registered_image_sizes ) ) {
				add_image_size( $slug, $img_data['width'], $img_data['height'], $img_data['crop'] );
			}
		}

		add_filter( 'image_size_names_choose', array( $this, '_insert_image_sizes' ) );

	}

	public function _insert_image_sizes( $sizes ) {

		// Get the custom image sizes
		global $_wp_additional_image_sizes;

		// If there are none, just return the built-in sizes
		if ( empty( $_wp_additional_image_sizes ) ) {
			return $sizes;
		}

		// Add all the custom sizes to the built-in sizes
		foreach ( $_wp_additional_image_sizes as $id => $data ) {
			if ( array_key_exists( $id, $this->image_sizes ) ) {
				$sizes[ $id ] = $this->image_sizes[ $id ]['label'];
			}

		}

		return $sizes;

	}

}