<?php

require_once 'classes/ES_Rich_Text_Trait.trait.php';
require_once 'classes/ES_Rich_Text_Widget.class.php';

// Registers WP Widget using trait::ES_Rich_Text_Trait's init() method
ES_WP_Widget_Admin::register_widget( 'ES_Rich_Text_Widget', basename(__DIR__) );