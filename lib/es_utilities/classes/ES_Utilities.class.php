<?php

class ES_Utilities {

	public function __construct() {

		add_action('admin_enqueue_scripts', array( $this, '_admin_enqueue_styles'));

		add_action('admin_print_scripts', array( $this, '_admin_print_scripts'), 11);
		add_action('admin_footer', array( $this, '_admin_print_footer_scripts'), 11);
		add_action('admin_print_footer_scripts', array( $this, '_media_post_id_hack'), 10000000);
		add_action('admin_print_styles', array( $this, '_admin_print_styles'), 11);

		add_action('wp_enqueue_scripts', array( $this, '_enqueue_styles'));
		add_action('wp_enqueue_scripts', array( $this, '_enqueue_scripts'));

		// -- Get Editor Markup
		$action = 'es_cfct_tabs_accordion_get_wp_editor';
		add_action('wp_ajax_'. $action, array( __CLASS__, '_ajax_get_wp_editor' ) );
	}

	/**
	 * WP_Query Date Range Extension
	 */
	public static function _wp_query_date_range( $where ) {

		global $wp_query;

		if (isset($wp_query->query['date_from']) && isset($wp_query->query['date_to'])) {
			$date_from = $wp_query->query['date_from'];
			$date_to = $wp_query->query['date_to'];
			$r = '/^\d{4}-\d{2}-\d{2}$/';

			$valid_from = preg_match($r, $date_from);
			$valid_to = preg_match($r, $date_to);

			if ( $valid_from && $valid_to ) {
				$where .= " AND post_date BETWEEN STR_TO_DATE('$date_from 00:00:00', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('$date_to 23:59:59', '%Y-%m-%d %H:%i:%s')";
			}
			else if ( $valid_from ) {
				$where .= " AND post_date >= STR_TO_DATE('$date_from 00:00:00', '%Y-%m-%d %H:%i:%s')";
			}
			else if ( $valid_to ) {
				$where .= " AND post_date <= STR_TO_DATE('$date_to 23:59:59', '%Y-%m-%d %H:%i:%s')";
			}
		}
		return $where;
	}

	/**
	 * Media Manager Doesn't like working without a Post ID, so we use WP's own Dashboard Quickpress Hack
	 */
	public function _media_post_id_hack() {

		global $post, $pagenow;

		// If on post page, no need to do this hack
		if ( ! in_array( $pagenow, array('widgets.php')) ) return;

		if ( $post && $post->ID ) return;

		/* Check if a new auto-draft (= no new post_ID) is needed or if the old can be used */
		$es_default_post_id = (int) get_user_option( 'es_media_manager_last_post_id' ); // Get the last post_ID
		if ( $es_default_post_id ) {
			$post = get_post( $es_default_post_id );
			if ( empty( $post ) || $post->post_status != 'auto-draft' ) { // auto-draft doesn't exists anymore
				$post = get_default_post_to_edit('post', true);
				update_user_option( get_current_user_id(), 'es_media_manager_last_post_id', (int) $post->ID ); // Save post_ID
			} else {
				$post->post_title = ''; // Remove the auto draft title
			}
		} else {
			$post = get_default_post_to_edit( 'post' , true);
			$user_id = get_current_user_id();
			// Don't create an option if this is a super admin who does not belong to this site.
			if ( ! ( is_super_admin( $user_id ) && ! in_array( get_current_blog_id(), array_keys( get_blogs_of_user( $user_id ) ) ) ) )
				update_user_option( $user_id, 'es_media_manager_last_post_id', (int) $post->ID ); // Save post_ID
		}

		$post_ID = (int) $post->ID;

		$media_settings = array(
			'id' => $post->ID,
			'nonce' => wp_create_nonce( 'update-post_' . $post->ID ),
		); ?>

		<script type="text/javascript">
			wp.media.view.settings.post = <?php echo json_encode( $media_settings ); // big juicy hack. ?>;
			wp.media.model.settings.post.id = <?php echo json_encode( $media_settings ); // big juicy hack. ?>;
		</script>

		<?php
	}

	public function _admin_enqueue_styles() {

		wp_enqueue_style( 'es_tokenized_input', plugins_url( '/js/build/jquery_tokenized_input/styles/token-input.css', dirname(__FILE__)), array(), 1.0 );
		wp_enqueue_style( 'es_tokenized_input_facebook', plugins_url( '/js/jquery_tokenized_input/styles/token-input-facebook.css', dirname(__FILE__)), array('es_tokenized_input'), 1.0 );

		wp_enqueue_style( 'es_dynatree', plugins_url( '/js/build/jquery_dynatree/skin/ui.dynatree.css', dirname(__FILE__)), array(), 1.0 );

		wp_enqueue_style( 'es_utilities', plugins_url( '/css/build/es_utilities_admin.css', dirname(__FILE__) ), array('es_wp_core_admin', 'es_tokenized_input_facebook'), 1.0 );
	}

	public function _admin_print_scripts() {

		global $pagenow;

		// If on post page, no need to enqueue post/media dependencies
		if ( in_array( $pagenow, array('widgets.php')) ) {

			wp_enqueue_media();

			wp_enqueue_script('editor');

			wp_enqueue_script('media-upload');

			wp_enqueue_script('word-count');

			wp_enqueue_script('wplink');

			wp_enqueue_script('wpdialogs-popup');

			wp_enqueue_script('quicktags');

			wp_enqueue_style('wp-jquery-ui-dialog'); // Not sure I need this

			//if ( wp_is_mobile() )
			//wp_enqueue_script( 'jquery-touch-punch' );
		}

		$path = SCRIPT_DEBUG ? '/js/src/es-word-count.js' : '/js/build/es-word-count.min.js';
		wp_enqueue_script('es_word_count', plugins_url( $path, dirname(__FILE__)), array('jquery'), false, 1);

		wp_enqueue_script('es_ace_editor', plugins_url('/js/build/ace/src-min-noconflict/ace.js', dirname(__FILE__)), array(), 1.0);

		wp_enqueue_script('es_tokenized_input', plugins_url('/js/build/jquery_tokenized_input/src/jquery.tokeninput.js', dirname(__FILE__)), array('jquery'), 1.0);

		wp_enqueue_script('es_dynatree', plugins_url('/js/build/jquery_dynatree/jquery.dynatree.js', dirname(__FILE__)), array('jquery'), 1.0);

		$path = SCRIPT_DEBUG ? '/js/src/es_utilities.js' : '/js/build/es_utilities.min.js';
		wp_enqueue_script('es_utilities', plugins_url( $path, dirname(__FILE__)), array('jquery', 'es_ace_editor', 'es_word_count', 'es_tokenized_input', 'es_dynatree'), 1.0);
	}

	public function _admin_print_styles() {

		global $pagenow;

		// If on post page, no need to enqueue post/media dependencies
		if ( ! in_array( $pagenow, array('widgets.php')) ) return;

		wp_print_styles('editor-buttons');
	}

	public function _admin_print_footer_scripts() {

		global $pagenow;

		if ( ! class_exists( '_WP_Editors' ) )
			require( ABSPATH . WPINC . '/class-wp-editor.php' );

		/**
		 * TODO: Make this not so hacky :)
		 *
		 * To get things setup so we can actually have TinyMCE loaded for use in the
		 * individual slide source editors, we need to get TinyMCE loaded on the page first.
		 * Unfortunately TinyMCE is not a registered script for WordPress yet, so its
		 * not as easy as using wp_enqueue_script() to get it in there. TinyMCE is however
		 * hard coded for output with the wp_editor() script. All we need to do is run it
		 * once to get it scheduled for loading and the _WP_Editors class takes care of
		 * the rest to get it loaded when we actually call the _WP_Editors::editor_js()
		 */
		ob_start();

		wp_editor( "", "es_rich_text_editor", array(
			'textarea_rows' => 12,
			'dfw' => false
		));

		ob_end_clean();

		// If on post page, no need to enqueue post/media dependencies
		if ( in_array( $pagenow, array('widgets.php')) ) {
			_WP_Editors::editor_js();
		}
	}

	public function _enqueue_styles() {

		wp_enqueue_style('es_utilities_front_end', plugins_url('/css/build/es_utilities_front_end.css', dirname(__FILE__)), array(), 1.0);
	}

	public function _enqueue_scripts() {

		$path = SCRIPT_DEBUG ? '/js/src/es_utilities_front_end.js' : '/js/build/es_utilities_front_end.min.js';
		wp_enqueue_script('es_utilities_front_end', plugins_url( $path, dirname(__FILE__)), array(), 1.0);
	}

	public static function _ajax_get_wp_editor() {

		$editor_id = $_POST['editor_id'];

		header('Content-Type: text/html');

		if ( ! class_exists( '_WP_Editors' ) )
			require( ABSPATH . WPINC . '/class-wp-editor.php' );

		wp_editor( '', $editor_id, array(
			'textarea_rows' => 12,
			'dfw' => false,
			'quicktags' => array(
				'id' => $editor_id
			)
		));

		die();
	}

}