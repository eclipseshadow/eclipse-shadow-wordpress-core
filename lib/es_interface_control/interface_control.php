<?php

$ic = $this;
$current_user_role = ES_Interface_Control::get_user_role();
$post_type = ES_Interface_Control::get_post_type();
global $pagenow;

//if ( 'administrator' == $current_user_role ) return;

/**
 * Admin Bar
 */

// Remove - wp-logo & all its children
$ic->remove_admin_bar_link(false, 'wp-logo');

// Remove - About WordPress link
$ic->remove_admin_bar_link(false, 'about');

// Remove - WordPress.org link
$ic->remove_admin_bar_link(false, 'wporg');

// Remove - WordPress documentation link
$ic->remove_admin_bar_link(false, 'documentation');

// Remove - Support forums link
$ic->remove_admin_bar_link(false, 'support-forums');

// Remove - Feedback link
$ic->remove_admin_bar_link(false, 'feedback');

// Remove - Site name menu
//$ic->remove_admin_bar_link(false, 'wporg');

// Remove - View site link
$ic->remove_admin_bar_link(false, 'view-site');

// Remove - Updates link
//$ic->remove_admin_bar_link(false, 'updates'); - Disabled by role

// Remove - Comments link
$ic->remove_admin_bar_link(false, 'comments');

// Remove - Content link
//$ic->remove_admin_bar_link(false, 'new-content');

// Remove - W3TC link
$ic->remove_admin_bar_link(true, 'w3tc');

// Remove - User details tab
//$ic->remove_admin_bar_link(false, 'my-account');

/**
 * Menu Items
 */

/* All Roles */

// Reword - Slidedeck2
$ic->replace_interface_text( false, '/SlideDeck\s+2/','Slide Shows', array('#adminmenu'));

// Remove - Landing Pages
$ic->remove_menu_item( false, 'edit.php?post_type=cftl-tax-landing' );

// Remove - Dashboard -> Updates
//$ic->remove_submenu_item( true, 'index.php', 'update-core.php' );

// Remove - Genesis Theme Settings
$ic->remove_menu_item( true, 'genesis' );

// Remove - Appearance -> Customize
$ic->remove_submenu_item( true, 'themes.php', 'customize.php' );

// Remove - Appearance -> Editor
$ic->remove_submenu_item( true, 'themes.php', 'theme-editor.php' );

// Remove - Plugins
//$ic->remove_menu_item( true, 'plugins.php' );

// Remove - Users
$ic->remove_menu_item( true, 'users.php' );

// Remove - Settings
$ic->remove_menu_item( true, 'options-general.php' );

/* Client Basic */

if ( 'client_basic' == $current_user_role ) {
	// Remove - Appearance
	$ic->remove_menu_item( false, 'themes.php' );

	// Remove - Tools
	$ic->remove_menu_item( false, 'tools.php' );

	// Remove - Gravity Forms
	$ic->remove_menu_item( false, 'gf_edit_forms' );
}

/* Client Advanced */

if ( 'client_advanced' == $current_user_role ) {
	//...
}

/*
 * Screen Options - Hide by default
 */

/* All Roles */

// Disable - Genesis SEO Metabox (#genesis_inpost_seo_box)
// Disable - Genesis Scripts Metabox (#genesis_inpost_scripts_box)
// Hide - Custom Fields Metabox (#postcustom)
// Hide - Discussion Metabox (#commentstatusdiv)
// Hide - Slug Metabox (#slugdiv)
// Hide - Author Metabox (#authordiv)
// Hide - Revisions (#revisionsdiv)
// Hide - Excerpt (#postexcerpt)
// Hide - Trackbacks (#trackbacksdiv)
// Hide - Comments (#commentsdiv)

/* Client Basic */

if ( 'client_basic' == $current_user_role ) {
	// Hide - Per Page CSS Metabox (#pp_css_editor_box)
	// Hide - Per Page JS Metabox (#pp_js_editor_box)
	// Hide - Page Attributes Metabox (#pageparentdiv)
	// Hide - Genesis Page Layout Metabox
}

/* Client Advanced */

if ( 'client_advanced' == $current_user_role ) {
	//...
}

/**
 * Post Editing (Any Type)
 */

// Genesis Layout Box

/* All Roles */

// Hide - Genesis Layout Metabox Advanced Items
$ic->hide_element_by_css(false, array('#genesis_inpost_layout_box .inside > p'));

// Hide - Gensis Layout Metabox Defaults
$ic->hide_element_by_css(false, array('#genesis_inpost_layout_box .inside .genesis-layout-selector p:first-child'));

// Hide - Add Media buttons on pages
if ( in_array( $pagenow, array('post.php', 'post-new.php')) && 'page' == $post_type ) {
	$ic->hide_element_by_css(false, array('#wp-content-media-buttons'));
}

/* Client Basic */

if ( 'client_basic' == $current_user_role ) {
	// Hide - Post/Page Settings (Status, Visibility, Date)
	$ic->hide_element_by_css(false, array('#misc-publishing-actions'));

	// Hide - Permalink
	$ic->hide_element_by_css(false, array('#edit-slug-box strong', '#sample-permalink', '#edit-slug-buttons'));
}

/* Client Advanced */

if ( 'client_advanced' == $current_user_role ) {
	//...
}

/**
 * SlideDeck
 */

// Hide - SlideDeck Logo on admin page
$ic->hide_element_by_css(false, array('#slidedeck-types h1'));

// Hide - SlideDeck -> Manage SlideDecks - Sidebar right
$ic->hide_element_by_css(false, array('#slidedeck-table .right'));

// Hide - SlideDeck -> Manage SlideDecks - News & Updates
$ic->hide_element_by_css(false, array('#slidedeck-manage-footer'));

// Expand SlideDeck Manage page left column to full width of page
$ic->apply_css_rules(false, array('#slidedeck-table .float-wrapper'), 'padding-right: 0');

// Manage SlideDecks - Add Slide Shows Heading
if ( isset($_GET['page']) && $_GET['page'] == 'slidedeck2.php' && 'admin.php' == $pagenow ) {
	add_action('all_admin_notices', create_function('','echo "<h1>Slide Shows</h1>";'));
}


/**
 * Native Widgets (Non-ES Widgets)
 */

// Dashboard

// Remove - Right Now
$ic->remove_dashboard_widget(false, 'dashboard_right_now');
// Remove - Recent Comments
$ic->remove_dashboard_widget(false, 'dashboard_recent_comments');
// Remove - Incoming Links
$ic->remove_dashboard_widget(false, 'dashboard_incoming_links');
// Remove - Plugins
$ic->remove_dashboard_widget(false, 'dashboard_plugins');
// Remove - Forms
$ic->remove_dashboard_widget(false, 'rg_forms_dashboard');
// Remove - QuickPress
$ic->remove_dashboard_widget(false, 'dashboard_quick_press');
// Remove - Recent Drafts
$ic->remove_dashboard_widget(false, 'dashboard_recent_drafts');
// Remove - WordPress Blog
$ic->remove_dashboard_widget(false, 'dashboard_primary');
// Remove - Other WordPress News
$ic->remove_dashboard_widget(false, 'dashboard_secondary');

// Widgets Admin

// Remove - Pages
$ic->remove_native_widget( false, 'WP_Widget_Pages' );

// Remove - Archives
$ic->remove_native_widget( false, 'WP_Widget_Archives' );

// Remove - Meta
$ic->remove_native_widget( false, 'WP_Widget_Meta' );

// Remove - Categories
$ic->remove_native_widget( false, 'WP_Widget_Categories' );

// Remove - Recent Comments
$ic->remove_native_widget( false, 'WP_Widget_Recent_Comments' );

// Remove - RSS
$ic->remove_native_widget( false, 'WP_Widget_RSS' );

// Remove - Tag Cloud
$ic->remove_native_widget( false, 'WP_Widget_Tag_Cloud' );

// Remove - Custom Menu
$ic->remove_native_widget( false, 'WP_Nav_Menu_Widget' );

// Remove - SlideDeck 2
$ic->remove_native_widget( false, 'SlideDeck2Widget' );

/* Client Advanced */

if ( 'client_advanced' == $current_user_role ) {
	//...
}

/**
 * Footer
 */

// Replace - Footer text
$ic->replace_admin_footer_text(false, 'Thank you for creating with <a href="http://eclipseshadow.com/">Eclipse Shadow\'s Nebula Web Builder</a>');

/**
 * Misc
 */

// Welcome Panel
$ic->hide_element_by_css(false, array('#welcome-panel'));

//add_action('in_admin_header', create_function('','echo "in_admin_header<br/><br/>";'));
//add_action('admin_notices', create_function('','echo "admin_notices<br/><br/>";'));
//add_action('all_admin_notices', create_function('','echo "all_admin_notices<br/><br/>";'));
