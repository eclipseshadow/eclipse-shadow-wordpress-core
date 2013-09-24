<?php

require_once 'classes/ES_Code_Editor_Trait.trait.php';
require_once 'classes/ES_Code_Editor_Widget.class.php';

// Registers WP Widget using trait::ES_Code_Editor_Trait's init() method
ES_WP_Widget_Admin::register_widget( 'ES_Code_Editor_Widget', basename(__DIR__) );