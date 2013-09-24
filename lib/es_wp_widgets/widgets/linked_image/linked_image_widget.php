<?php

require_once 'classes/ES_Linked_Image_Trait.trait.php';
require_once 'classes/ES_Linked_Image_Widget.class.php';

// Registers WP Widget using trait::ES_Linked_Image_Trait's init() method
ES_WP_Widget_Admin::register_widget( 'ES_Linked_Image_Widget', basename(__DIR__) );