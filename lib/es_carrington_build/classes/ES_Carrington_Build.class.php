<?php

/*
 * Carrington Build Wrapper Class
 *
 * @todo Carrington Build's Rich Text module loads a tinymce editor script into the dom for EVERY module instance - Fix this...
 */

class ES_Carrington_Build {

	private static $is_instantiated = false;

	public  $enabled_post_types = array('page');


	public function __construct( $widgets_dir, $widgets_url ) {

		if ( true == self::$is_instantiated ) return; // Singleton

		global $es_carrington_build;

		$es_carrington_build = $this;

		define('ES_CARRINGTON_WIDGETS_DIR', $widgets_dir );
		define('ES_CARRINGTON_WIDGETS_DIR_URL', $widgets_url );

		add_filter('cfct-module-dirs', array( $this, '_register_module_dir'), 11);
		add_action('es_load_carrington', array( $this, '_load_carrington'));
		add_action('wp_enqueue_scripts', array( $this, '_enqueue_styles' ));
		add_action('wp_enqueue_scripts', array( $this, '_enqueue_scripts' ));
		add_action('admin_enqueue_scripts', array( $this, '_enqueue_styles' ));
		add_action('admin_enqueue_scripts', array( $this, '_enqueue_scripts' ));
		add_action('admin_head', array( $this, '_default_to_cb' ));
		add_action('edit_form_advanced', array($this, '_display_page_builder_welcome'));
		add_action('edit_page_form', array($this, '_display_page_builder_welcome'));
		add_filter('cfct-build-enabled-post-types', array( $this, '_set_cb_post_types' ));

		self::$is_instantiated = true;

		do_action('es_carrington_build_loaded');

	}

	//
	// Internal Methods
	//

	public function _load_carrington() {

		add_action('init', array( $this, '_load_es_module_class' ), 2);
		require_once dirname(__FILE__) .'/../lib/carrington-build/carrington-build.php';

	}

	public function _load_es_module_class() {

		require_once dirname(__FILE__) .'/../classes/ES_Carrington_Module.class.php';

	}

	public function _enqueue_styles() {

		global $pagenow;
		$current_post_type = $this->get_post_type();

		$load_editor_css = false;
		if ( in_array($pagenow, array('post.php', 'post-new.php')) && in_array( $current_post_type, $this->enabled_post_types ) ) {
			$load_editor_css = true;
		}

		if ( is_admin() && true == $load_editor_css ) {
			wp_enqueue_style('es_carrington_admin', plugins_url('/css/es_carrington_admin.css', dirname(__FILE__)), array(), 0.1);
		}
		else {
			wp_enqueue_style('es_carrington_base', plugins_url('/css/es_carrington_front_end_base.css', dirname(__FILE__)), array(), 0.1);
			wp_enqueue_style('es_carrington_responsive', plugins_url('/css/es_carrington_front_end_responsive.css', dirname(__FILE__)), array(), 0.1);
		}

	}

	public function _enqueue_scripts() {

		global $pagenow;
		$current_post_type = $this->get_post_type();

		$load_editor_js = false;
		if ( in_array($pagenow, array('post.php', 'post-new.php')) && in_array( $current_post_type, $this->enabled_post_types ) ) {
			$load_editor_js = true;
		}

		if ( is_admin() && true == $load_editor_js ) {
			wp_enqueue_script('es_carrington_admin', plugins_url('/js/es_carrington_admin.js', dirname(__FILE__)), array('jquery'), 0.1);
		}
		else {
			if ( is_user_logged_in() ){
				wp_enqueue_script('carrington_edit_links', plugins_url('/js/es_carrington_front_end.js', dirname(__FILE__)), array(), 0.1);
			}
		}

	}

	public function _default_to_cb() {

		if ( !in_array( $this->get_post_type(), $this->enabled_post_types ) ) {
			return;
		}

		if ( !$this->is_new_post() ) {
			return;
		}

		// Hide WP Editor & Default to CB
		echo '
		<style>
		#cfct-build-data {
			display: block !important;
		}
		#postdivrich {
			display: none !important;
		}
		</style>';

		echo '
		<script>
		jQuery(document).ready(function(){

			var welcome = jQuery(".nebula-page-builder-welcome");

			welcome.insertAfter("#titlediv");

			jQuery("#cfct-sortables-add").click(function(){
				welcome.hide();
			});

		});
		</script>
		';

	}

	public function _disable_add_media_buttons() {

		if ( !in_array( $this->get_post_type(), $this->enabled_post_types ) ) {
			return;
		}

		if ( !$this->is_new_post() ) {
			return;
		}

		// Disable "Add Media" buttons when CB is being displayed

		echo '
			<style>
			#cfct-build-data {
				display: block !important;
			}
			#postdivrich {
				display: none !important;
			}
			</style>';

	}

	public function _display_page_builder_welcome() {

		if ( !in_array( $this->get_post_type(), $this->enabled_post_types ) ) {
			return;
		}

		if ( !$this->is_new_post() ) {
			return;
		}

		if ( $this->is_new_post() ) {
			echo '<div class="nebula-page-builder-welcome"></div>';
		}

	}

	public function _set_cb_post_types( $post_types ) {

		return apply_filters('es_carrington_build_enabled_post_types', $this->enabled_post_types);

	}

	public function _register_module_dir( $dirs ) {

		array_push($dirs, ES_CARRINGTON_WIDGETS_DIR);
		return $dirs;

	}

	// Helper Methods

	private function is_new_post() {

		global $pagenow;

		if (in_array($pagenow, array('post-new.php', 'page-new.php'))) {
			return true;
		}

		return false;

	}

	private function get_post_type() {

		global $pagenow;

		if (in_array($pagenow, array('post-new.php'))) {
			if (!empty($_GET['post_type'])) {
				// custom post type or wordpress 3.0 pages
				$type = esc_attr($_GET['post_type']);
			}
			else {
				$type = 'post';
			}
		}
		elseif (in_array( $pagenow, array('page-new.php'))) {
			// pre 3.0 new pages
			$type = 'page';
		}
		else {
			// post/page-edit
			if (isset($_GET['post']))
				$post_id = (int) $_GET['post'];
			elseif (isset($_POST['post_ID'])) {
				$post_id = (int) $_POST['post_ID'];
			}
			else {
				$post_id = 0;
			}

			$type = false;
			if ($post_id > 0) {
				$post = get_post($post_id, OBJECT, 'edit');

				if ($post && !empty($post->post_type) && !in_array($post->post_type, array('attachment', 'revision'))) {
					$type = $post->post_type;
				}
			}
		}

		return $type;

	}

}