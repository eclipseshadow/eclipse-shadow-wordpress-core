<?php

class ES_Help_Tab {

	public  $name = '',
			$id = '',
			$content = '',
			$priority = 0,
			$callback = '',
			$pages = '',
			$post_types = '';

	public function __construct( $name = '', $id = '', $content = '', $priority = 0, $pages = '', $post_types = '' ) {

		$this->name = $name;
		$this->id = $id;
		$this->set_content( $content );
		$this->priority = $priority;
		$this->pages = $pages;
		$this->post_types = $post_types;

	}

	public function set_content( $content = '' ) {

		if ( is_array( $content ) && method_exists( $content[0], $content[1] ) ) {
			$this->callback = $content;
		}
		else if ( function_exists( $content ) ) {
			$this->callback = $content;
		}
		else {
			$this->content = $content;
		}

	}

}