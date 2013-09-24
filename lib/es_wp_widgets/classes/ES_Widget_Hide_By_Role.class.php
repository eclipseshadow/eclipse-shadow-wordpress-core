<?php

/**
 * Hide_widgets_role_based
 * @author Ohad Raz <admin@bainternet.info>
 */
class ES_Widget_Hide_By_Role {

	public $has_selector = false;
	public $js_selector = '';
	public $roles_hide = array();

	/**
	 * class constructor
	 * @author Ohad Raz <admin@bainternet.info>
	 * @param array $args [description]
	 */
	function __construct( $args = array() ) {

		if ( is_admin() ) {
			add_action('widgets_init', array($this,'hide', 999));
		}
	}

	/**
	 * the money function that hides the widgets on the admin side when the user has a specific role
	 * @return void
	 */
	public function hide() {

		global $pagenow;

		if ( $pagenow == 'widgets.php' ) {
			global $current_user;
			get_currentuserinfo();
			$sperator = "";
			foreach ($this->roles_hide as $role => $widgets) {
				if ($this->has_role($role)){
					foreach ((array)$widgets as $w_id) {
						unregister_widget( $w_id );
					}
				}
			}
		}
	}


	/**
	 * add a widget to hide per role
	 * @param string $role   role name
	 * @param string $widget widget id
	 */
	public function addHide( $role, $widget ) {

		if ( is_array($widget) ) {
			$tmp = isset($roles_hide[$role])? $roles_hide[$role]: array();
			$roles_hide[$role] = array_merge($tmp, (array)$widget);
		}
		else {
			$roles_hide[$role][] = $widget;
		}
	}

	/**
	 * has_role check if a user has a role
	 * @param  int  $user_id user id
	 * @return boolean
	 */
	public function has_role( $user_id = null, $role ) {

		if ( is_numeric( $user_id ) ) {
			$user = get_userdata( $user_id );
		}
		else {
			$user = wp_get_current_user();
		}

		if ( empty( $user ) ) {
			return false;
		}

		return in_array( $role, (array) $user->roles );
	}
}

/*
 * Example Usage
 *
 * $widgets_hide = new ES_Widget_Hide_By_Role();
 * $widgets_hide->addHide('contributor',array('WP_Widget_Pages','WP_Widget_Calendar','WP_Widget_Links'));
 * $widgets_hide->addHide('editor','WP_Widget_Links');
 *
 */