<?php

require_once 'classes/ES_Loop_Trait.trait.php';
require_once 'classes/ES_Loop_Widget.class.php';

// Registers WP Widget using trait::ES_Loop_Trait's init() method
ES_WP_Widget_Admin::register_widget( 'ES_Loop_Widget', basename(__DIR__) );