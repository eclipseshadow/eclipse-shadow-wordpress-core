<?php

require_once 'classes/ES_Tabs_Accordion_Trait.trait.php';
require_once 'classes/ES_Tabs_Accordion_Widget.class.php';

// Registers WP Widget using trait::ES_Tabs_Accordion_Trait's init() method
ES_WP_Widget_Admin::register_widget( 'ES_Tabs_Accordion_Widget', basename(__DIR__) );