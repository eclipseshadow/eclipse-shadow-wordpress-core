<?php

$ua = $this;
$current_user_role = ES_User_Assistance::get_user_role();
$post_type = ES_User_Assistance::get_post_type();

$ua->remove_help_tab('_all');

/**
 * Generic Post Editing
 */

// Post/Page - Overview

if ( 'page' != $post_type ) {
	$ua->add_help_tab('Overview', 'generic_post_overview', 'es_generic_post_overview_help_tab', -1, array('post.php', 'post-new.php'));
}
function es_generic_post_overview_help_tab( $screen, $tab ) {

	require 'help_tabs/generic_post_overview.php';

}

// Post/Page - Feature Image

$ua->add_help_tab('Featured Image', 'generic_post_featured_image', 'es_generic_post_featured_image_help_tab', 100, array('post.php', 'post-new.php'));
function es_generic_post_featured_image_help_tab( $screen, $tab ) {

	require 'help_tabs/generic_post_featured_image.php';

}

// Post/Page - Publish Options

if ( 'client_basic' != $current_user_role ) {
	$ua->add_help_tab('Publish Options', 'generic_post_publish_options', 'es_generic_post_publish_options_help_tab', 101, array('post.php', 'post-new.php'));
}
function es_generic_post_publish_options_help_tab( $screen, $tab ) {

	require 'help_tabs/generic_post_publish_options.php';

}

// Advanced Help

if ( 'client_basic' != $current_user_role ) {
	$ua->add_help_tab('Advanced', 'generic_post_advanced', 'es_generic_post_advanced_help_tab', 102, array('post.php', 'post-new.php'));
}
function es_generic_post_advanced_help_tab( $screen, $tab ) {

	require 'help_tabs/generic_post_advanced.php';

}


/**
 * Page Builder
 */

// Page Builder Overview

$ua->add_help_tab('Overview', 'page-builder-overview', 'es_page_builder_overview_help_tab', 0, array('post.php', 'post-new.php'), array('page'));
function es_page_builder_overview_help_tab( $screen, $tab ) {

	require 'help_tabs/page_builder_overview.php';

}

// Page Builder - Start Building

$ua->add_help_tab('Start Building', 'page-builder-start-building', 'es_page_builder_start_building_help_tab', 1, array('post.php', 'post-new.php'), array('page'));
function es_page_builder_start_building_help_tab( $screen, $tab ) {

	require 'help_tabs/page_builder_start_building.php';

}

// Page Builder - Arranging Content

$ua->add_help_tab('Arranging Content', 'page-builder-arranging-content', 'es_page_builder_arranging_content_help_tab', 2, array('post.php', 'post-new.php'), array('page'));
function es_page_builder_arranging_content_help_tab( $screen, $tab ) {

	require 'help_tabs/page_builder_arranging_content.php';

}



