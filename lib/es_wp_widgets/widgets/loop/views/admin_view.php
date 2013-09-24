<div class="es-widget-form-wrapper">

	<input class="es-loop-data" type="hidden" id="<?php echo $this->get_field_id('loop_data'); ?>" name="<?php echo $this->get_field_name('loop_data'); ?>" value="<?php echo esc_attr( $loop_data ); ?>" />

	<input class="es-loop-custom-template-markup" type="hidden" id="<?php echo $this->get_field_id('custom-template-markup'); ?>" name="<?php echo $this->get_field_name('custom-template-markup'); ?>" value="<?php echo esc_attr( $custom_template_markup ); ?>" />

	<p>
		<label><?php _e('Title <span class="es-widget-form-subtext">- Give your Loop a name</span>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value='<?php echo esc_attr( $title ); ?>' />
	</p>

	<div class="es-loop-container">

		<p>
			<label><?php _e('Post Type <span class="es-widget-form-subtext">- Please choose which type of Content</span>'); ?></label>
			<br /><select class="es-loop-post-type"></select>
		</p>

		<p>
			<label><?php _e('Filters <span class="es-widget-form-subtext">- Narrow your selection of Content using Filters</span>'); ?></label>
		</p>

		<div class="es-loop-filters-container">
			<!-- Silence is Golden -->
		</div>
		<a class="es-loop-add-filter-btn button-primary" href="">Add Filter</a>

	</div>

	<div class="es-widget-form-advanced-options">

		<!-- Show Title -->

		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-loop-show-title" name="<?php echo $this->get_field_name('show-title' ); ?>" id="<?php echo $this->get_field_id('show-title'); ?>"<?php checked( $show_title, '1' ); $this->is_default( 'show-title', $show_title ); ?> />
				<?php _e( 'Show Title? <span class="es-widget-form-subtext">- Do you want to show your Loop\'s <strong>Title</strong> above the Loop?</span>'); ?>
			</label>
		</p>

		<!-- Number of Posts -->

		<p class="es-widget-form-field">
			<label class="es-widget-form-top-label" for="<?php echo $this->get_field_id('number-of-posts'); ?>">
				<?php _e( 'Number of Posts <span class="es-widget-form-subtext">- How many posts would you like to show?</span>'); ?>
			</label>

			<input type="text" class="es-loop-number-of-posts es-widget-form-field-small" name="<?php echo $this->get_field_name('number-of-posts'); ?>" id="<?php echo $this->get_field_id('number-of-posts'); ?>" value="<?php echo esc_attr( $number_of_posts ); ?>" <?php $this->is_default( 'number-of-posts', $number_of_posts ); ?> />
		</p>

		<!-- Start Offset -->

		<p class="es-widget-form-field">
			<label class="es-widget-form-top-label" for="<?php echo $this->get_field_id('start-offset'); ?>">
				<?php _e( 'Start Offset <span class="es-widget-form-subtext">- How many posts do you want to skip?</span>'); ?>
			</label>

			<input type="text" class="es-loop-start-offset es-widget-form-field-small" name="<?php echo $this->get_field_name('start-offset'); ?>" id="<?php echo $this->get_field_id('start-offset'); ?>" value="<?php echo esc_attr( $start_offset ); ?>" <?php $this->is_default( 'start-offset', $start_offset ); ?> />
		</p>

		<!-- Paginated -->

		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-loop-paginated" name="<?php echo $this->get_field_name('paginated' ); ?>" id="<?php echo $this->get_field_id('paginated'); ?>"<?php checked( $paginated, '1' ); $this->is_default( 'paginated', $paginated ); ?> />
				<?php _e( 'Paginated? <span class="es-widget-form-subtext">- Do you want to break your posts into <strong>Pages</strong>?</span>'); ?>
			</label>
		</p>

		<!-- Paginated - Page Nums or Text Links -->

		<!--
		<p class="es-loop-paginated-dependent es-widget-form-field-dependent-lvl-1 es-widget-form-field">
			<span class="es-widget-form-top-label"><?php _e( 'Page Numbers or Text? <span class="es-widget-form-subtext">- Do you want <strong>Page Numbers</strong> or <strong>Textual Links</strong>?</span>'); ?></span>

			<input type="radio" class="es-loop-page-nums-or-text-links-page-numbers" name="<?php echo $this->get_field_name('page-nums-or-text-links' ); ?>" id="<?php echo $this->get_field_id('page-nums-or-text-links-page-numbers'); ?>" value="page-numbers" <?php checked( $page_nums_or_text_links, 'page-numbers' ); ?> <?php $this->is_default( 'page-nums-or-text-links', $page_nums_or_text_links ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('page-nums-or-text-links-page-numbers'); ?>"> Page Numbers</label>

			<input type="radio" class="es-loop-page-nums-or-text-links-text-links" name="<?php echo $this->get_field_name('page-nums-or-text-links' ); ?>" id="<?php echo $this->get_field_id('page-nums-or-text-links-text-links'); ?>" value="text-links" <?php checked( $page_nums_or_text_links, 'text-links' ); ?> <?php $this->is_default( 'page-nums-or-text-links', $page_nums_or_text_links ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('page-nums-or-text-links-text-links'); ?>"> Textual Links</label>

			<input type="radio" class="es-loop-page-nums-or-text-links-both" name="<?php echo $this->get_field_name('page-nums-or-text-links' ); ?>" id="<?php echo $this->get_field_id('page-nums-or-text-links-both'); ?>" value="both" <?php checked( $page_nums_or_text_links, 'both' ); ?> <?php $this->is_default( 'page-nums-or-text-links', $page_nums_or_text_links ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('page-nums-or-text-links-both'); ?>"> Both</label>
		</p>
		-->

		<!-- Paginated - Custom Link Text -->

		<p class="es-loop-paginated-dependent es-widget-form-field-dependent-lvl-1 es-widget-form-field">
			<label class="es-widget-form-top-label" for="<?php echo $this->get_field_id('custom-page-link-text-next'); ?>">
				<?php _e( 'Custom Page Link Text <span class="es-widget-form-subtext">- Customized the text for your Page Links.</span>'); ?>
			</label>
			Next <input type="text" class="es-loop-custom-page-link-text-next es-widget-form-field-small es-widget-inline-form-field" name="<?php echo $this->get_field_name('custom-page-link-text-next'); ?>" id="<?php echo $this->get_field_id('custom-page-link-text-next'); ?>" value="<?php echo esc_attr( $custom_page_link_text_next ); ?>" <?php $this->is_default( 'custom-page-link-text-next', $custom_page_link_text_next ); ?> />
			Prev <input type="text" class="es-loop-custom-page-link-text-prev es-widget-form-field-small es-widget-inline-form-field" name="<?php echo $this->get_field_name('custom-page-link-text-prev'); ?>" id="<?php echo $this->get_field_id('custom-page-link-text-prev'); ?>" value="<?php echo esc_attr( $custom_page_link_text_prev ); ?>" <?php $this->is_default( 'custom-page-link-text-prev', $custom_page_link_text_prev ); ?> />
		</p>

		<!-- Show Post Title -->

		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-loop-show-post-title" name="<?php echo $this->get_field_name('show-post-title' ); ?>" id="<?php echo $this->get_field_id('show-post-title'); ?>"<?php checked( $show_post_title, '1' ); $this->is_default( 'show-post-title', $show_post_title ); ?> />
				<?php _e( 'Show Post Title? <span class="es-widget-form-subtext">- Do you want to show the <strong>Title</strong> of the Post?</span>'); ?>
			</label>
		</p>

		<!-- Link Post Title -->

		<p class="es-loop-show-post-title-dependency es-widget-form-field-dependent-lvl-1 es-widget-form-field">
			<label>
				<input type="checkbox" class="es-loop-link-post-title" name="<?php echo $this->get_field_name('link-post-title' ); ?>" id="<?php echo $this->get_field_id('link-post-title'); ?>"<?php checked( $link_post_title, '1' ); $this->is_default( 'link-post-title', $link_post_title ); ?> />
				<?php _e( 'Link Post Title? <span class="es-widget-form-subtext">- Do you want to link the <strong>Post Title</strong> to the Post?</span>'); ?>
			</label>
		</p>

		<!-- Post Title Position -->

		<p class="es-loop-show-post-title-dependency es-widget-form-field-dependent-lvl-1 es-widget-form-field">
			<span class="es-widget-form-top-label"><?php _e( 'Post Title Position <span class="es-widget-form-subtext">- Do you want the title of your post to show <strong>Above</strong> or <strong>Below</strong> the Post?</span>'); ?></span>

			<input type="radio" class="es-loop-post-title-position-above" name="<?php echo $this->get_field_name('post-title-position' ); ?>" id="<?php echo $this->get_field_id('post-title-position-above'); ?>" value="above" <?php checked( $post_title_position, 'above' ); ?> <?php $this->is_default( 'post-title-position', $post_title_position ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('post-title-position-above'); ?>"> Above</label>

			<input type="radio" class="es-loop-post-title-position-below" name="<?php echo $this->get_field_name('post-title-position' ); ?>" id="<?php echo $this->get_field_id('post-title-position-below'); ?>" value="below" <?php checked( $post_title_position, 'below' ); ?> <?php $this->is_default( 'post-title-position', $post_title_position ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('post-title-position-below'); ?>"> Below</label>
		</p>

		<!-- Show Date -->

		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-loop-show-date" name="<?php echo $this->get_field_name('show-date' ); ?>" id="<?php echo $this->get_field_id('show-date'); ?>"<?php checked( $show_date, '1' ); $this->is_default( 'show-date', $show_date ); ?> />
				<?php _e( 'Show Date? <span class="es-widget-form-subtext">- Do you want to show the <strong>Date</strong> of the Post?</span>'); ?>
			</label>
		</p>

		<!-- Show Featured Image -->

		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-loop-show-featured-image" name="<?php echo $this->get_field_name('show-featured-image' ); ?>" id="<?php echo $this->get_field_id('show-featured-image'); ?>"<?php checked( $show_featured_image, '1' ); $this->is_default( 'show-featured-image', $show_featured_image ); ?> />
				<?php _e( 'Show Featured Image? <span class="es-widget-form-subtext">- Do you want to show the Post\'s <strong>Featured Image</strong>?</span>'); ?>
			</label>
		</p>

		<!-- Link Featured Image -->

		<p class="es-loop-show-featured-image-dependency es-widget-form-field-dependent-lvl-1 es-widget-form-field">
			<label>
				<input type="checkbox" class="es-loop-link-featured-image" name="<?php echo $this->get_field_name('link-featured-image' ); ?>" id="<?php echo $this->get_field_id('link-featured-image'); ?>"<?php checked( $link_featured_image, '1' ); $this->is_default( 'link-featured-image', $link_featured_image ); ?> />
				<?php _e( 'Link Featured Image? <span class="es-widget-form-subtext">- Do you want to link the <strong>Featured Image</strong> to the Post?</span>'); ?>
			</label>
		</p>

		<!-- Featured Image Alignment -->

		<p class="es-loop-show-featured-image-dependency es-widget-form-field-dependent-lvl-1 es-widget-form-field">
			<span class="es-widget-form-top-label"><?php _e( 'Featured Image Alignment <span class="es-widget-form-subtext">- Do you want the Featured Image aligned <strong>Left</strong> or <strong>Right</strong>?</span>'); ?></span>

			<input type="radio" class="es-loop-featured-image-alignment-left" name="<?php echo $this->get_field_name('featured-image-alignment' ); ?>" id="<?php echo $this->get_field_id('featured-image-alignment-left'); ?>" value="left" <?php checked( $featured_image_alignment, 'left' ); ?> <?php $this->is_default( 'featured-image-alignment', $featured_image_alignment ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('featured-image-alignment-left'); ?>"> Left</label>

			<input type="radio" class="es-loop-featured-image-alignment-right" name="<?php echo $this->get_field_name('featured-image-alignment' ); ?>" id="<?php echo $this->get_field_id('featured-image-alignment-right'); ?>" value="right" <?php checked( $featured_image_alignment, 'right' ); ?> <?php $this->is_default( 'featured-image-alignment', $featured_image_alignment ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('featured-image-alignment-right'); ?>"> Right</label>
		</p>

		<!-- Featured Image Size -->

		<p class="es-loop-show-featured-image-dependency es-widget-form-field-dependent-lvl-1 es-widget-form-field">
			<span class="es-widget-form-top-label"><?php _e( 'Featured Image Size <span class="es-widget-form-subtext">- What size do you want your Featured Image to be?</span>'); ?></span>

			<input type="radio" class="es-loop-featured-image-size-thumbnail" name="<?php echo $this->get_field_name('featured-image-size' ); ?>" id="<?php echo $this->get_field_id('featured-image-size-thumbnail'); ?>" value="thumbnail" <?php checked( $featured_image_size, 'thumbnail' ); ?> <?php $this->is_default( 'featured-image-size', $featured_image_size ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('featured-image-size-thumbnail'); ?>"> Thumbnail</label>

			<input type="radio" class="es-loop-featured-image-size-small" name="<?php echo $this->get_field_name('featured-image-size' ); ?>" id="<?php echo $this->get_field_id('featured-image-size-small'); ?>" value="small" <?php checked( $featured_image_size, 'small' ); ?> <?php $this->is_default( 'featured-image-size', $featured_image_size ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('featured-image-size-small'); ?>"> Small</label>

			<input type="radio" class="es-loop-featured-image-size-med" name="<?php echo $this->get_field_name('featured-image-size' ); ?>" id="<?php echo $this->get_field_id('featured-image-size-medium'); ?>" value="medium" <?php checked( $featured_image_size, 'medium' ); ?> <?php $this->is_default( 'featured-image-size', $featured_image_size ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('featured-image-size-medium'); ?>"> Med</label>

			<input type="radio" class="es-loop-featured-image-size-large" name="<?php echo $this->get_field_name('featured-image-size' ); ?>" id="<?php echo $this->get_field_id('featured-image-size-large'); ?>" value="large" <?php checked( $featured_image_size, 'large' ); ?> <?php $this->is_default( 'featured-image-size', $featured_image_size ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('featured-image-size-large'); ?>"> Large</label>

			<input type="radio" class="es-loop-featured-image-size-full-size" name="<?php echo $this->get_field_name('featured-image-size' ); ?>" id="<?php echo $this->get_field_id('featured-image-size-full'); ?>" value="full" <?php checked( $featured_image_size, 'full' ); ?> <?php $this->is_default( 'featured-image-size', $featured_image_size ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('featured-image-size-full'); ?>"> Full Size</label>
		</p>

		<!-- Show Content -->

		<p class="es-widget-form-field">
			<span class="es-widget-form-top-label"><?php _e( 'Show Content? <span class="es-widget-form-subtext">- Do you want to show the Post <strong>Content</strong>, <strong>Excerpt</strong> or <strong>No Content</strong>?</span>'); ?></span>

			<input type="radio" class="es-loop-excerpt-or-content-content" name="<?php echo $this->get_field_name('excerpt-or-content' ); ?>" id="<?php echo $this->get_field_id('excerpt-or-content-content'); ?>" value="content" <?php checked( $excerpt_or_content, 'content' ); ?> <?php $this->is_default( 'excerpt-or-content', $excerpt_or_content ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('excerpt-or-content-content'); ?>"> Content</label>

			<input type="radio" class="es-loop-excerpt-or-content-excerpt" name="<?php echo $this->get_field_name('excerpt-or-content' ); ?>" id="<?php echo $this->get_field_id('excerpt-or-content-excerpt'); ?>" value="excerpt" <?php checked( $excerpt_or_content, 'excerpt' ); ?> <?php $this->is_default( 'excerpt-or-content', $excerpt_or_content ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('excerpt-or-content-excerpt'); ?>"> Excerpt</label>

			<input type="radio" class="es-loop-excerpt-or-content-none" name="<?php echo $this->get_field_name('excerpt-or-content' ); ?>" id="<?php echo $this->get_field_id('excerpt-or-content-none'); ?>" value="none" <?php checked( $excerpt_or_content, 'none' ); ?> <?php $this->is_default( 'excerpt-or-content', $excerpt_or_content ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('excerpt-or-content-none'); ?>"> None</label>
		</p>

		<!-- Filters Relationship -->

		<p class="es-widget-form-field">
			<span class="es-widget-form-top-label"><?php _e( 'Filters Relationship <span class="es-widget-form-subtext">- Choose your overall <strong>Relationship</strong> for your filters. (only applies to Taxonomy filters)</span>'); ?></span>

			<input type="radio" class="es-loop-filters-relationship-and" name="<?php echo $this->get_field_name('filters-relationship' ); ?>" id="<?php echo $this->get_field_id('filters-relationship-and'); ?>" value="AND" <?php checked( $filters_relationship, 'AND' ); ?> <?php $this->is_default( 'filters-relationship', $filters_relationship ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('filters-relationship-and'); ?>"> And</label>

			<input type="radio" class="es-loop-filters-relationship-or" name="<?php echo $this->get_field_name('filters-relationship' ); ?>" id="<?php echo $this->get_field_id('filters-relationship-or'); ?>" value="OR" <?php checked( $filters_relationship, 'OR' ); ?> <?php $this->is_default( 'filters-relationship', $filters_relationship ); ?> />
			<label class="es-widget-form-check-radio-label" for="<?php echo $this->get_field_id('filters-relationship-or'); ?>"> Or</label>
		</p>

		<!-- Custom Template -->

		<p>
			<a href="" class="es-custom-template-markup-btn button-primary">Custom HTML Template</a>
			<span class="es_loop_custom_template_markup_indicator"> * Custom Markup Detected</span>
		</p>

	</div>

</div>