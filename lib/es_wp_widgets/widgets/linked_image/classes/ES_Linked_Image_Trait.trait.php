<?php

/*
 * Trait from which both ES_Linked_Image_Widget & ES_Linked_Image_Module inherit their methods
 */

trait ES_Linked_Image_Trait {

	public $default_options = array(
		'image_ids' => '',
		'link_to' => 'nothing',
		'responsive' => '1',
		'link_to_url_url' => '',
		'link_to_object_id' => '-1',
		'image_size' => 'small',
		'image_alignment' => 'left',
		'text_wrap' => '0',
		'title' => 'My Image'
	);

	public static $acceptable_image_sizes = array(
		'thumbnail' => array('Thumbnail (Cropped)', 150, 150, true),
		'small' => array('Small', 150, 150, false),
		'medium' => array('Medium', 300, 300, false),
		'large' => array('Large', 1024, 1024, false),
		'full' => array('Full Size', null, null, null)
	);

	//
	// Static Methods
	//

	public static function init() {

		// Call parent::init()

		parent::init( __CLASS__ );

		// Enqueue Media Manager

		add_action('admin_enqueue_scripts', array( __CLASS__, '_enqueue_wp_media' ));

		// Add Ajax Handlers

		// -- Get Image Preview
		$action = 'es_cfct_linked_image_get_image_preview';
		add_action('wp_ajax_'. $action, array( __CLASS__, '_ajax_get_image_preview' ) );

		// -- Fetch Object Search Result (Link To)
		$action = 'es_cfct_linked_image_get_object_search_results';
		add_action('wp_ajax_'. $action, array( __CLASS__, '_ajax_get_object_search_results' ) );

		// -- Fetch Object Search Result Post Information (Link To)
		$action = 'es_cfct_linked_image_get_post_information';
		add_action('wp_ajax_'. $action, array( __CLASS__, '_ajax_get_post_information' ) );

		/* Hack needed to enable full media options when adding content form media library */
		/* (this is done excluding post_id parameter in Thickbox iframe url) */
		//add_filter('_upload_iframe_src', array( __CLASS__, 'black_studio_tinymce_upload_iframe_src') );


	}

	public static function _enqueue_wp_media() {

		wp_enqueue_media();

	}

	// Ajax

	public static function _ajax_get_image_preview() {

		//global $wpdb; // this is how you get access to the database

		$ids = explode( ',', $_POST['image_ids'] );

		$img_data = wp_get_attachment_image_src( $ids[0], 'thumbnail' );
		$img_url = $img_data[0];

		$html = '<img src="'. $img_url .'" alt="" />';

		header('Content-type: text/html');

		echo $html;

		die(); // this is required to return a proper result

	}

	public static function _ajax_get_object_search_results() {

		global $wpdb; // this is how you get access to the database

		$search_query = $_POST['search_query'];

		$query = new WP_Query(array(
			'post_type' => 'any',
			's' => $search_query,
			'post_status' => 'publish',
			'posts_per_page' => 50,
			'nopaging' => false,
			'order' => 'ASC',
			'orderby' => 'title'
		));

		$exclude_post_types = array('attachment', 'revision', 'link');

		$html = '<ul>';

		foreach( $query->posts as $post ) {
			if ( in_array( $post->post_type, $exclude_post_types) ) continue;

			$trailing_dots = strlen( $post->post_title ) > 35 ? '...' : '';

			$html .= '<li class="es-cfct-linked-image-link-to-object-search-result"" data-id="'. $post->ID .'">';
			$html .= '<span class="es-cfct-linked-image-link-to-object-search-result-name">';
			$html .= substr( $post->post_title, 0, 35 ) . $trailing_dots;
			$html .= '</span> ';
			$html .= '<span class="es-cfct-linked-image-form-subtext">- '. self::get_post_type_label( $post->post_type ) .'</span>';
			$html .= '</li>';
		}

		if ( sizeof( $query->posts ) < 1 ) {
			$html .= '<li>We didn\'t find anything...</li>';
		}

		$html .= '</ul>';

		header('Content-type: text/html');

		echo $html;

		die(); // this is required to return a proper result
	}

	public static function _ajax_get_post_information() {

		$post = null;

		if ( (int)$_POST['post_id'] > 0 ) {
			$post = get_post( $_POST['post_id'] );
		}

		$html = '';

		if ( !empty( $post ) ) {
			$html .= '<div class="es-cfct-linked-image-post-information">';

			$html .= '<span class="es-cfct-linked-image-post-information-selected-heading">Selected</span>';

			$html .= '<span class="es-cfct-linked-image-post-information-remove-selected"></span>';

			$trailing_dots = strlen( $post->post_excerpt ) > 35 ? '...' : '';

			$html .= get_the_post_thumbnail( $post->ID, array(50,50) );

			$html .= '<h1>'. substr( $post->post_title, 0, 35 ) . $trailing_dots .'<span class="es-cfct-linked-image-post-type">('. self::get_post_type_label( $post->post_type ) .')</span></h1>';

			$trailing_dots = strlen( get_the_excerpt() ) > 150 ? '...' : '';

			$html .= '<div class="es-cfct-linked-image-post-excerpt">'. substr( $post->post_excerpt, 0, 150) . $trailing_dots .'</div>';

			$html .= '</div>';
		}

		header('Content-type: text/html');

		echo $html;

		die(); // this is required to return a proper result
	}

	// Helpers

	private static function get_post_type_label( $post_type = '', $echo = false ) {

		static $post_types, $labels = '';

		// Get all post type *names*, that are shown in the admin menu
		empty( $post_types ) AND $post_types = get_post_types(
			array(
				'show_in_menu' => true
				//,'_builtin'     => false
			)
			,'objects'
		);

		empty( $labels ) AND $labels = wp_list_pluck( $post_types, 'labels' );
		$names = wp_list_pluck( $labels, 'singular_name' );
		$name = $names[ $post_type ];

		// return or print?
		return $echo ? print $name : $name;
	}

	//
	// Instance Methods
	//

	public function widget_form( $instance ) {

		$image_ids = isset( $instance[ $this->get_field_name('image_ids') ] ) ? $instance[ $this->get_field_name('image_ids') ] : $this->get_default('image_ids');

		$responsive = isset( $instance[ $this->get_field_name('responsive') ] ) ? $instance[ $this->get_field_name('responsive') ] : $this->get_default('responsive');

		$link_to = isset( $instance[ $this->get_field_name('link_to') ] ) ? $instance[ $this->get_field_name('link_to') ] : $this->get_default('link_to');

		$link_to_url_url = isset( $instance[ $this->get_field_name('link_to_url_url') ] ) ? $instance[ $this->get_field_name('link_to_url_url') ] : $this->get_default('link_to_url_url');

		$link_to_object_id = isset( $instance[ $this->get_field_name('link_to_object_id') ] ) ? $instance[ $this->get_field_name('link_to_object_id') ] : $this->get_default('link_to_object_id');

		$acceptable_image_sizes = self::$acceptable_image_sizes;

		$image_size = isset( $instance[ $this->get_field_name('image_size') ] ) ? $instance[ $this->get_field_name('image_size') ] : $this->get_default('image_size');

		$image_alignment = isset( $instance[ $this->get_field_name('image_alignment') ] ) ? $instance[ $this->get_field_name('image_alignment') ] : $this->get_default('image_alignment');

		$text_wrap = isset( $instance[ $this->get_field_name('text_wrap') ] ) ? $instance[ $this->get_field_name('text_wrap') ] : $this->get_default('text_wrap');

		$params = compact(
			'image_ids',
			'responsive',
			'link_to',
			'link_to_url_url',
			'link_to_object_id',
			'acceptable_image_sizes',
			'image_size',
			'image_alignment',
			'text_wrap'
		);

		return $this->load_admin_widget_view( $instance, $params );
	}

	public function widget_update( $new_instance, $old_instance ) {

		$instance = array();

		$instance[ $this->get_field_name('image_ids') ] = $new_instance[ $this->get_field_name('image_ids') ];
		$instance[ $this->get_field_name('link_to') ] = $new_instance[ $this->get_field_name('link_to') ];
		$instance[ $this->get_field_name('link_to_url_url') ] = $new_instance[ $this->get_field_name('link_to_url_url') ];
		$instance[ $this->get_field_name('link_to_object_id') ] = $new_instance[ $this->get_field_name('link_to_object_id') ];
		$instance[ $this->get_field_name('image_size') ] = $new_instance[ $this->get_field_name('image_size') ];
		$instance[ $this->get_field_name('image_alignment') ] = $new_instance[ $this->get_field_name('image_alignment') ];

		$instance[ $this->get_field_name('responsive') ] = isset( $new_instance[ $this->get_field_name('responsive') ] ) ? '1' : '0';
		$instance[ $this->get_field_name('text_wrap') ] = isset( $new_instance[ $this->get_field_name('text_wrap') ] ) ? '1' : '0';

		return $instance;

	}

	public function widget_display( $instance ) {

		$size = self::$acceptable_image_sizes[ $instance[ $this->get_field_name('image_size') ] ];

		$img = wp_get_attachment_image_src( $instance[ $this->get_field_name('image_ids') ], array( $size[1], $size[2] ) );

		$url = $img[0];
		$dims = array( $img[1], $img[2] );

		$full_size_url = wp_get_attachment_url( $instance[ $this->get_field_name('image_ids') ] );

		$responsive = isset( $instance[ $this->get_field_name('responsive') ] ) ? : $this->get_default('responsive');
		$responsive_class = $responsive == 1 ? 'es-cfct-linked-image-responsive-image' : 'es-cfct-linked-image-non-responsive-image';

		$alignment = isset( $instance[ $this->get_field_name('image_alignment') ] ) ? $instance[ $this->get_field_name('image_alignment') ] : $this->get_default('image_alignment');
		$alignment_class = $this->_id_base .'-image-align-'. $alignment;

		$params = compact(
			'url',
			'dims',
			'alignment_class',
			'responsive_class',
			'full_size_url'
		);

		return $this->load_widget_view( $instance, $params );

	}

	public function css() {

		ob_start();

		require dirname(__FILE__) . '/../css/build/style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}

	public function admin_css() {

		ob_start();

		require dirname(__FILE__) . '/../css/build/admin_style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}

}