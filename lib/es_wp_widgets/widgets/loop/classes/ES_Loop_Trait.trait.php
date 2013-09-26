<?php

/*
 * Trait from which both ES_Loop_Widget & ES_Loop_Module inherit their methods
 */

trait ES_Loop_Trait {

	public $default_options = array(
		'title' => 'My Loop',
		'loop_data' => '',
		'number-of-posts' => 1,
		'start-offset' => 0,
		'paginated' => '0',
		'page-nums-or-text-links' => 'text-links', // (page-numbers, text-links, both)
		'custom-page-link-text-next' => 'Next',
		'custom-page-link-text-prev' => 'Prev',
		'show-post-title' => '1',
		'post-title-position' => 'above', // (above/below)
		'link-post-title' => '1',
		'show-date' => '1',
		'show-featured-image' => '1',
		'link-featured-image' => '1',
		'featured-image-alignment' => 'left',
		'featured-image-size' => 'thumbnail',
		'excerpt-or-content' => 'excerpt', // (excerpt, content, none)
		'custom-template-markup' => '',
		'filters-relationship' => 'AND'
	);

	public static $acceptable_image_sizes = array(
		'thumbnail' => array('Thumbnail (Cropped)', 150, 150, true),
		'small' => array('Small', 150, 150, false),
		'medium' => array('Medium', 300, 300, false),
		'large' => array('Large', 1024, 1024, false),
		'full' => array('Full Size', null, null, null)
	);

	public static $available_post_statuses = array(
		array(
			'id' => 'publish',
			'name' => 'Publish'
		),
		array(
			'id' => 'pending',
			'name' => 'Pending'
		),
		array(
			'id' => 'draft',
			'name' => 'Draft'
		),
		array(
			'id' => 'auto-draft',
			'name' => 'Auto-Draft'
		),
		array(
			'id' => 'future',
			'name' => 'Future'
		),
		array(
			'id' => 'private',
			'name' => 'Private'
		),
		array(
			'id' => 'inherit',
			'name' => 'Inherit'
		),
		array(
			'id' => 'trash',
			'name' => 'Trash'
		),
		array(
			'id' => 'any',
			'name' => 'Any'
		)
	);

	//
	// Static Methods
	//

	public static function init() {

		// Call parent::init()

		parent::init( __CLASS__ );

		// Add Ajax Handlers

		// -- Get WP Data
		$action = 'es_widget_loop_get_wp_data';
		add_action('wp_ajax_'. $action, array( __CLASS__, '_get_wp_data' ) );

		// -- Return ID (jQuery TokenizedInput Hack)
		$action = 'es_widget_loop_get_post_id';
		add_action('wp_ajax_'. $action, array( __CLASS__, '_get_post_id' ) );
	}

	// AJAX

	public static function _get_post_id() {

		$data = array();

		array_push( $data, array( 'id' => $_GET['q'], 'name' => $_GET['q'] ));

		header('Content-type: application/json');

		echo json_encode( $data );
		die();
	}

	public static function _get_wp_data() {

		$data = new stdClass();

		// Taxonomies

		$data->taxonomies = array();

		$args = array(
			'public'   => true,
			'_builtin' => true
		);

		$taxonomies = get_taxonomies( $args, 'objects' );

		foreach( $taxonomies as $tax ) {

			$_terms = get_terms( $tax->name, array(
				'hide_empty' => false
			));
			$terms = array();

			foreach( $_terms as $_term ) {

				array_push( $terms, array(
					'id' => $_term->term_id,
					'name' => $_term->name,
					'parent_id' => $_term->parent,
					'other' => $_term
				));
			}

			$data->taxonomies[ $tax->name ] = array(
				'name' => $tax->name,
				'label' => $tax->labels->name,
				'terms' => $terms,
				'post_types' => $tax->object_type,
				'hierarchical' => $tax->hierarchical,
				'other' => $tax
			);
		}

		// Post Types

		$data->post_types = array();

		$args = array(
			'public'   => true,
			'_builtin' => true
		);

		$post_types = get_post_types( $args, 'objects' );

		foreach( $post_types as $pt ) {
			$data->post_types[ $pt->name ] = array(
				'name' => $pt->name,
				'label' => $pt->labels->name
			);
		}

		// Authors

		$data->authors = array();

		$authors = get_users(array(
			'who' => 1
		));

		foreach( $authors as $a ) {
			array_push( $data->authors, array(
				'id' => $a->ID,
				'name' => $a->display_name,

				'ID' => $a->ID,
				'display_name' => $a->display_name,
				'user_nicename' => $a->user_nicename,
				'email' => $a->user_email
			));
		}

		// Post Statuses

		$data->post_statuses = self::$available_post_statuses;

		header('Content-type: application/json');

		echo json_encode( $data );
		die();
	}

	//
	// Instance Methods
	//

	public function widget_form( $instance ) {

		$loop_data = isset( $instance[ $this->get_field_name('loop_data') ] ) ? $instance[ $this->get_field_name('loop_data') ] : $this->get_default('loop_data');

		$number_of_posts = isset( $instance[ $this->get_field_name('number-of-posts') ] ) ? $instance[ $this->get_field_name('number-of-posts') ] : $this->get_default('number-of-posts');

		$start_offset = isset( $instance[ $this->get_field_name('start-offset') ] ) ? $instance[ $this->get_field_name('start-offset') ] : $this->get_default('start-offset');

		$paginated = isset( $instance[ $this->get_field_name('paginated') ] ) ? $instance[ $this->get_field_name('paginated') ] : $this->get_default('paginated');

		$page_nums_or_text_links = isset( $instance[ $this->get_field_name('page-nums-or-text-links') ] ) ? $instance[ $this->get_field_name('page-nums-or-text-links') ] : $this->get_default('page-nums-or-text-links');

		$custom_page_link_text_next = isset( $instance[ $this->get_field_name('custom-page-link-text-next') ] ) ? $instance[ $this->get_field_name('custom-page-link-text-next') ] : $this->get_default('custom-page-link-text-next');

		$custom_page_link_text_prev = isset( $instance[ $this->get_field_name('custom-page-link-text-prev') ] ) ? $instance[ $this->get_field_name('custom-page-link-text-prev') ] : $this->get_default('custom-page-link-text-prev');

		$show_post_title = isset( $instance[ $this->get_field_name('show-post-title') ] ) ? $instance[ $this->get_field_name('show-post-title') ] : $this->get_default('show-post-title');

		$post_title_position = isset( $instance[ $this->get_field_name('post-title-position') ] ) ? $instance[ $this->get_field_name('post-title-position') ] : $this->get_default('post-title-position');

		$link_post_title = isset( $instance[ $this->get_field_name('link-post-title') ] ) ? $instance[ $this->get_field_name('link-post-title') ] : $this->get_default('link-post-title');

		$show_date = isset( $instance[ $this->get_field_name('show-date') ] ) ? $instance[ $this->get_field_name('show-date') ] : $this->get_default('show-date');

		$show_featured_image = isset( $instance[ $this->get_field_name('show-featured-image') ] ) ? $instance[ $this->get_field_name('show-featured-image') ] : $this->get_default('show-featured-image');

		$link_featured_image = isset( $instance[ $this->get_field_name('link-featured-image') ] ) ? $instance[ $this->get_field_name('link-featured-image') ] : $this->get_default('link-featured-image');

		$featured_image_alignment = isset( $instance[ $this->get_field_name('featured-image-alignment') ] ) ? $instance[ $this->get_field_name('featured-image-alignment') ] : $this->get_default('featured-image-alignment');

		$featured_image_size = isset( $instance[ $this->get_field_name('featured-image-size') ] ) ? $instance[ $this->get_field_name('featured-image-size') ] : $this->get_default('featured-image-size');

		$excerpt_or_content = isset( $instance[ $this->get_field_name('excerpt-or-content') ] ) ? $instance[ $this->get_field_name('excerpt-or-content') ] : $this->get_default('excerpt-or-content');

		$custom_template_markup = isset( $instance[ $this->get_field_name('custom-template-markup') ] ) ? $instance[ $this->get_field_name('custom-template-markup') ] : $this->get_default('custom-template-markup');

		$filters_relationship = isset( $instance[ $this->get_field_name('filters-relationship') ] ) ? $instance[ $this->get_field_name('filters-relationship') ] : $this->get_default('filters-relationship');

		$params = compact(
			'loop_data',
			'number_of_posts',
			'start_offset',
			'paginated',
			'page_nums_or_text_links',
			'custom_page_link_text_next',
			'custom_page_link_text_prev',
			'show_post_title',
			'post_title_position',
			'link_post_title',
			'show_date',
			'show_featured_image',
			'link_featured_image',
			'featured_image_alignment',
			'featured_image_size',
			'excerpt_or_content',
			'custom_template_markup',
			'filters_relationship'
		);

		return $this->load_admin_widget_view( $instance, $params );
	}

	public function widget_update( $new_instance, $old_instance ) {

		$instance = array();

		$instance[ $this->get_field_name('loop_data') ] = $new_instance[ $this->get_field_name('loop_data') ];

		$instance[ $this->get_field_name('number-of-posts') ] = $new_instance[ $this->get_field_name('number-of-posts') ];

		$instance[ $this->get_field_name('start-offset') ] = $new_instance[ $this->get_field_name('start-offset') ];

		$instance[ $this->get_field_name('paginated') ] = isset( $new_instance[ $this->get_field_name('paginated') ] ) ? '1' : '0';

		$instance[ $this->get_field_name('page-nums-or-text-links') ] = $new_instance[ $this->get_field_name('page-nums-or-text-links') ];

		$instance[ $this->get_field_name('custom-page-link-text-next') ] = $new_instance[ $this->get_field_name('custom-page-link-text-next') ];

		$instance[ $this->get_field_name('custom-page-link-text-prev') ] = $new_instance[ $this->get_field_name('custom-page-link-text-prev') ];

		$instance[ $this->get_field_name('show-post-title') ] = isset( $new_instance[ $this->get_field_name('show-post-title') ] ) ? '1' : '0';

		$instance[ $this->get_field_name('post-title-position') ] = $new_instance[ $this->get_field_name('post-title-position') ];

		$instance[ $this->get_field_name('link-post-title') ] = isset( $new_instance[ $this->get_field_name('link-post-title') ] ) ? '1' : '0';

		$instance[ $this->get_field_name('show-date') ] = isset( $new_instance[ $this->get_field_name('show-date') ] ) ? '1' : '0';

		$instance[ $this->get_field_name('show-featured-image') ] = isset( $new_instance[ $this->get_field_name('show-featured-image') ] ) ? '1' : '0';

		$instance[ $this->get_field_name('link-featured-image') ] = isset( $new_instance[ $this->get_field_name('link-featured-image') ] ) ? '1' : '0';

		$instance[ $this->get_field_name('featured-image-alignment') ] = $new_instance[ $this->get_field_name('featured-image-alignment') ];

		$instance[ $this->get_field_name('featured-image-size') ] = $new_instance[ $this->get_field_name('featured-image-size') ];

		$instance[ $this->get_field_name('excerpt-or-content') ] = $new_instance[ $this->get_field_name('excerpt-or-content') ];

		$instance[ $this->get_field_name('custom-template-markup') ] = $new_instance[ $this->get_field_name('custom-template-markup') ];

		$instance[ $this->get_field_name('filters-relationship') ] = $new_instance[ $this->get_field_name('filters-relationship') ];

		return $instance;

	}

	public function widget_display( $instance ) {

		global $post;

		$loop_data = isset( $instance[ $this->get_field_name('loop_data') ] ) ? $instance[ $this->get_field_name('loop_data') ] : $this->get_default('loop_data');

		$number_of_posts = isset( $instance[ $this->get_field_name('number-of-posts') ] ) ? $instance[ $this->get_field_name('number-of-posts') ] : $this->get_default('number-of-posts');

		$start_offset = isset( $instance[ $this->get_field_name('start-offset') ] ) ? $instance[ $this->get_field_name('start-offset') ] : $this->get_default('start-offset');

		$paginated = isset( $instance[ $this->get_field_name('paginated') ] ) ? $instance[ $this->get_field_name('paginated') ] : $this->get_default('paginated');

		$page_nums_or_text_links = isset( $instance[ $this->get_field_name('page-nums-or-text-links') ] ) ? $instance[ $this->get_field_name('page-nums-or-text-links') ] : $this->get_default('page-nums-or-text-links');

		$custom_page_link_text_next = isset( $instance[ $this->get_field_name('custom-page-link-text-next') ] ) ? $instance[ $this->get_field_name('custom-page-link-text-next') ] : $this->get_default('custom-page-link-text-next');

		$custom_page_link_text_prev = isset( $instance[ $this->get_field_name('custom-page-link-text-prev') ] ) ? $instance[ $this->get_field_name('custom-page-link-text-prev') ] : $this->get_default('custom-page-link-text-prev');

		$show_post_title = isset( $instance[ $this->get_field_name('show-post-title') ] ) ? $instance[ $this->get_field_name('show-post-title') ] : $this->get_default('show-post-title');

		$post_title_position = isset( $instance[ $this->get_field_name('post-title-position') ] ) ? $instance[ $this->get_field_name('post-title-position') ] : $this->get_default('post-title-position');

		$link_post_title = isset( $instance[ $this->get_field_name('link-post-title') ] ) ? $instance[ $this->get_field_name('link-post-title') ] : $this->get_default('link-post-title');

		$show_date = isset( $instance[ $this->get_field_name('show-date') ] ) ? $instance[ $this->get_field_name('show-date') ] : $this->get_default('show-date');

		$show_featured_image = isset( $instance[ $this->get_field_name('show-featured-image') ] ) ? $instance[ $this->get_field_name('show-featured-image') ] : $this->get_default('show-featured-image');

		$link_featured_image = isset( $instance[ $this->get_field_name('link-featured-image') ] ) ? $instance[ $this->get_field_name('link-featured-image') ] : $this->get_default('link-featured-image');

		$featured_image_alignment = isset( $instance[ $this->get_field_name('featured-image-alignment') ] ) ? $instance[ $this->get_field_name('featured-image-alignment') ] : $this->get_default('featured-image-alignment');

		$featured_image_size = isset( $instance[ $this->get_field_name('featured-image-size') ] ) ? $instance[ $this->get_field_name('featured-image-size') ] : $this->get_default('featured-image-size');

		$excerpt_or_content = isset( $instance[ $this->get_field_name('excerpt-or-content') ] ) ? $instance[ $this->get_field_name('excerpt-or-content') ] : $this->get_default('excerpt-or-content');

		$custom_template_markup = isset( $instance[ $this->get_field_name('custom-template-markup') ] ) ? $instance[ $this->get_field_name('custom-template-markup') ] : $this->get_default('custom-template-markup');

		$filters_relationship = isset( $instance[ $this->get_field_name('filters-relationship') ] ) ? $instance[ $this->get_field_name('filters-relationship') ] : $this->get_default('filters-relationship');

		// Begin Loop Output Creation

		$page = 1;
		if ( $paginated == '1' ) {

			if ( !class_exists('Zebra_Pagination') ) {
				require dirname(__FILE__) .'/../../../../es_utilities/classes/Zebra_Pagination/Zebra_Pagination.php';
			}

			$pagination = new Zebra_Pagination();

			$pagination->variable_name('es_zebra_page');

			$pagination->records_per_page( $number_of_posts );

			$page = $pagination->get_page();

			$pagination->labels( $custom_page_link_text_prev, $custom_page_link_text_next );

			$pagination->selectable_pages(3);
		}

		$filtered_data = $this->get_filtered_data( $loop_data, $number_of_posts, $start_offset, $filters_relationship, $page );

		$featured_image_size_atts = $size = self::$acceptable_image_sizes[ $featured_image_size ];

		$loop_html = '<div class="es_loop_widget_inner_wrap">';

		while( $filtered_data->have_posts() ) { $filtered_data->the_post();

			global $es_loop_widget_current_post_id;

			$es_loop_widget_current_post_id = $post->ID;

			$title = get_the_title( $post->ID );
			$permalink = get_permalink( $post->ID );
			$date = get_the_date();
			$content = get_the_content();
			$excerpt = get_the_excerpt();
			$featured_image = get_the_post_thumbnail( $post->ID, array( $featured_image_size_atts[1], $featured_image_size_atts[2] ) , array(
				'class'	=> "es_loop_featured_image_$featured_image_size",
				'alt'	=> trim(strip_tags( $excerpt )),
				'title'	=> trim(strip_tags( $title )),
			));

			if ( trim( $custom_template_markup ) == '' ) {
				$template = $this->get_default_template( $show_post_title, $link_post_title, $show_date, $post_title_position, $show_featured_image, $link_featured_image, $featured_image_alignment, $excerpt_or_content );
			}
			else {
				$template = html_entity_decode( $custom_template_markup );
			}

			$loop_html .= $this->bind_to_template( $template, $title, $permalink, $date, $featured_image, $content, $excerpt );
		}

		if ( $paginated == '1' ) {
			// Add Pagination Markup

			$pagination->records( $filtered_data->found_posts );

			$loop_html .= $pagination->render( true );
		}

		$loop_html .= '</div>';

		unset( $es_loop_widget_current_post_id );

		wp_reset_query();

		// Export View Parameters

		$params = compact(
			'loop_data',
			'number_of_posts',
			'start_offset',
			'paginated',
			'page_nums_or_text_links',
			'custom_page_link_text_next',
			'custom_page_link_text_prev',
			'show_post_title',
			'post_title_position',
			'link_post_title',
			'show_date',
			'show_featured_image',
			'link_featured_image',
			'featured_image_alignment',
			'featured_image_size',
			'excerpt_or_content',
			'custom_template_markup',
			'filters_relationship',
			'loop_html'
		);

		return $this->load_widget_view( $instance, $params );

	}

	public function css() {

		ob_start();

		require dirname(__FILE__) . '/../css/build/style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}

	public function admin_css() {

		ob_start();

		require dirname(__FILE__) . '/../css/build/admin_style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}

	public function get_filtered_data( $loop_data, $number_of_posts, $start_offset, $filters_relationship, $page ) {

		global $wp_query;

		$number_of_posts = is_numeric( $number_of_posts ) ? (int)$number_of_posts : $this->get_default( 'number-of-posts' );

		$start_offset = is_numeric( $start_offset ) && $start_offset >= 0 ? (int)$start_offset : $this->get_default( 'start-offset' );

		$loop_data = json_decode( html_entity_decode( $loop_data ) );

		$post_type = $loop_data->post_type;

		$filters = $loop_data->filters;

		$taxonomy_queries = array();
		$taxonomy_queries['relation'] = $filters_relationship;

		$author_queries = array();

		$post_id_in_queries = array();
		$post_id_not_in_queries = array();

		$undesired_post_statuses = array();
		$desired_post_statuses = array(); // None
		$post_status_any = false;

		$date_from = '';
		$date_to = '';

		foreach( $filters as $filter ) {
			switch( $filter->type ) {
				//
				// Taxonomy
				//
				case 'taxonomy':
					$terms = array();
					foreach( $filter->selection as $term ) {
						array_push( $terms, (int)$term->id );
					}

					array_push( $taxonomy_queries, array(
						'taxonomy' => $filter->taxonomy_name,
						'field' => 'id',
						'terms' => $terms,
						'operator' => $filter->filter_relationship
					));
					break;
				//
				// Author
				//
				case 'author':
					foreach( $filter->selection as $author ) {
						array_push( $author_queries, ( ('IN' == $filter->filter_relationship) ? $author->id : '-'. $author->id ) );
					}
					break;
				//
				// Post ID
				//
				case 'post_id':
					foreach( $filter->selection as $post_id ) {
						if ( 'IN' == $filter->filter_relationship ) {
							array_push( $post_id_in_queries, $post_id->id );
						}
						else {
							array_push( $post_id_not_in_queries, $post_id->id );
						}
					}
					break;
				//
				// Post Status
				//
				case 'post_status':
					if ( 'IN' == $filter->filter_relationship ) {
						if ( in_array('any', wp_list_pluck( $filter->selection , 'id' )) ) {
							$post_status_any = true;
							continue;
						}
						else {
							$desired_post_statuses = array_merge( $desired_post_statuses, wp_list_pluck( $filter->selection, 'id' ) );
						}
					}
					else {
						$post_statuses = wp_list_pluck( $filter->selection, 'id' );

						$any_key = array_search( 'any', $post_statuses );
						if ( false !== $any_key ) {
							unset( $post_statuses[ $any_key ] );
						}

						$undesired_post_statuses = array_merge( $undesired_post_statuses, $post_statuses );
					}
					break;
				//
				// Date Range
				//
				case 'date_range':
					if ( 'IN' == $filter->filter_relationship ) {
							$date_from = $filter->from;
							$date_to = $filter->to;
					}
					else {

					}
					break;
			}
		}

		// Taxonomy Queries

		if ( sizeof( $taxonomy_queries ) < 2 ) {
			$taxonomy_queries = null;
		}

		// Authors

		$str_author_queries = implode( ',', $author_queries );

		// Post Statuses

		if ( $post_status_any ) {
			$desired_post_statuses = wp_list_pluck( self::$available_post_statuses, 'id' );
			$any_key = array_search( 'any', $desired_post_statuses );
			unset( $desired_post_statuses[ $any_key ] );
		}

		foreach( $undesired_post_statuses as $ups ) {
			$key = array_search( $ups, $desired_post_statuses );

			if ( false !== $key ) {
				unset( $desired_post_statuses[ $key ] );
			}
		}

		// Query

		$old_query = $wp_query;

		$query_args = array(
			'post_type' => $post_type,
			'tax_query' => $taxonomy_queries,
			'author' => $str_author_queries,
			'post__in' => $post_id_in_queries,
			'post__not_in' => $post_id_not_in_queries,
			'post_status' => $desired_post_statuses,
			'offset' => $start_offset,
			'posts_per_page' => $number_of_posts,
			'paged' => $page,
			'date_from' => $date_from,
			'date_to' => $date_to
		);

		$wp_query = new WP_Query();

		add_filter('posts_where', array( 'ES_Utilities', '_wp_query_date_range'));

		$wp_query->query( $query_args );

		remove_filter('posts_where', array( 'ES_Utilities', '_wp_query_date_range'));

		$query = $wp_query;

		$wp_query = $old_query;

		return $query;
	}

	public function bind_to_template( $template, $title, $permalink, $date, $featured_image, $content, $excerpt ) {

		$template = str_replace( '{{title}}', $title, $template );
		$template = str_replace( '{{permalink}}', $permalink, $template );
		$template = str_replace( '{{date}}', $date, $template );
		$template = str_replace( '{{featured_image}}', $featured_image, $template );
		$template = str_replace( '{{content}}', $content, $template );
		$template = str_replace( '{{excerpt}}', $excerpt, $template );

		$template = preg_replace_callback( '|{{meta.*}}|', array( $this, 'parse_meta' ), $template );

		return $template;
	}

	public function parse_meta( $matches ) {

		global $es_loop_widget_current_post_id;

		$output = '';

		foreach( $matches as $match ) {
			if ( $match == '{{meta}}' ) {
				$meta_key = null;
			}
			else if ( strlen( $match ) < 10  ) {
				return '';
			}
			else {
				$meta_key = substr( $match, 7, -2 );
			}

			$_meta = get_post_meta( $es_loop_widget_current_post_id, $meta_key, true );

			if ( null == $meta_key ) {
				$metas = $_meta;
			}
			else {
				$metas = array( $_meta );
			}

			foreach( $metas as $meta ) {

				$output .= '<span class="es_loop_widget_meta">';

				if ( is_string( $meta ) ) {
					$output = $meta;
				}
				else if ( is_array( $meta ) ) {
					foreach( $meta as $meta_val ) {
						$output .= is_string( $meta_val ) ? '<span class="es_loop_widget_meta_value">'. $meta_val .'</span>' : '';
					}
				}

				$output .= '</span>';
			}
		}

		return $output;
	}

	public function get_default_template( $show_title, $link_post_title, $show_date, $post_title_position, $show_featured_image, $link_featured_image, $featured_image_alignment, $excerpt_or_content ) {

		$title_markup = '';
		if ( $show_title ) {
			if ( $link_post_title ) {
				$title_markup = '<h3><a href="{{permalink}}" title="View this Post">{{title}}</a></h3>';
			}
			else {
				$title_markup = '<h3>{{title}}</h3>';
			}
		}

		$date_markup = $show_date == '1' ? '<span class="es_loop_widget_date">{{date}}</span>' : '';

		if ( 'above' == $post_title_position ) {
			$title_markup_above = $title_markup;
			$title_markup_below = '';
		}
		else {
			$title_markup_above = '';
			$title_markup_below = $title_markup;
		}

		$featured_image_markup = '';
		if ( $show_featured_image ) {
			if ( $link_featured_image ) {
				$featured_image_markup = '<span class="es_loop_widget_featured_image es_loop_widget_featured_image_'. $featured_image_alignment .'"><a href="{{permalink}}" title="View this Post">{{featured_image}}</a></span>';
			}
			else {
				$featured_image_markup = '<span class="es_loop_widget_featured_image es_loop_widget_featured_image_'. $featured_image_alignment .'">{{featured_image}}</span>';
			}
		}

		switch( $excerpt_or_content ) {
			case 'none':
				$excerpt_or_content_markup = '';
				break;
			case 'excerpt':
				$excerpt_or_content_markup = '<div class="es_loop_widget_excerpt_or_content">{{excerpt}}</div>';
				break;
			case 'content':
				$excerpt_or_content_markup = '<div class="es_loop_widget_excerpt_or_content">{{content}}</div>';
				break;
		}

		$template = trim('

		<div class="es_loop_widget_post">
			<div class="es_loop_widget_post_inner_wrap">
				'. $title_markup_above .'
				'. $date_markup .'
				'. $featured_image_markup .'
				'. $excerpt_or_content_markup .'
				'. $title_markup_below .'
			</div>
		</div>

		');

		return $template;
	}

}