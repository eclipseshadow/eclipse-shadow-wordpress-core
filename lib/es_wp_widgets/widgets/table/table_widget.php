<?php

require_once 'classes/ES_Table_Trait.trait.php';
require_once 'classes/ES_Table_Widget.class.php';

// Registers WP Widget using trait::ES_Table_Trait's init() method
ES_WP_Widget_Admin::register_widget( 'ES_Table_Widget', basename(__DIR__) );