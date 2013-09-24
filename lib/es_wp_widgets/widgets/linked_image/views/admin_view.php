<div class="es-cfct-form-inner-wrapper es-widget-form-wrapper">
	<input type="hidden" name="<?php echo $this->get_field_name('image_ids'); ?>" id="<?php echo $this->get_field_id('image_ids'); ?>" class="es-cfct-linked-image-image_ids" value="<?php echo $image_ids; ?>" />

	<p>
		<label><?php _e('Title <span class="es-widget-form-subtext">- Give your Image a name</span>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value='<?php echo esc_attr( $title ); ?>' />
	</p>

	<p class="es-cfct-linked-image-instructions">
		Add an image to your page! You can optionally choose what the image will do when you click on it. (See &quot;Advanced Options&quot;)
	</p>

	<p class="es-cfct-linked-image-image-preview">
		<!-- Image Preview -->
		<img src="<?php echo self::get_widget_dir_url() .'images/image_placeholder.jpg'; ?>" alt="" />
	</p>

	<p class="es-cfct-linked-image-choose-container">
		<!-- Choose Iamge -->
		<a class="button es-cfct-linked-image-choose-image-btn" href="#">Choose Image</a>

		<!-- Image Size -->
		<label>Choose a <strong>Size</strong> for your Image</label>
		<select name="<?php echo $this->get_field_name('image_size'); ?>" id="<?php echo $this->get_field_id('image_size'); ?>" class="es-cfct-linked-image-image_size" >
			<?php
			foreach( $acceptable_image_sizes as $slug => $size_data ) {
				echo '<option value="'. $slug .'" '. selected($image_size, $slug, false) .'>'. $size_data[0] .'</option>';
			}
			?>
		</select>

		<!-- Image Alignment -->
		<label>Choose the <strong>Alignment</strong> for your Image</label>
		<select name="<?php echo $this->get_field_name('image_alignment'); ?>" id="<?php echo $this->get_field_id('image_alignment'); ?>" class="es-cfct-linked-image-image_alignment" >
			<option value="left" <?php selected( $image_alignment, 'left' ); ?>>Left</option>
			<option value="right" <?php selected( $image_alignment, 'right' ); ?>>Right</option>
			<option value="center" <?php selected( $image_alignment, 'center' ); ?>>Center</option>
		</select>
	</p>

	<div class="es-cfct-popup-advanced-options es-widget-form-advanced-options">

		<div class="divider"></div>

		<div class="es-form-paragraph">
			<!-- Link To - Heading -->
			<span class="es-cfct-linked-image-form-faux-label">
				<?php echo _e('Link <span class="es-widget-form-subtext">- What do you want your image to do when you <strong>click</strong> on it?</span>'); ?>
			</span>

			<!-- Link to Nothing -->
			<label class="es-cfct-linked-image-form-sublabel">
				<input type="radio" name="<?php echo $this->get_field_name('link_to'); ?>" id="<?php echo $this->get_field_id('link_to_nothing'); ?>" class="es-cfct-linked-image-link_to_nothing es-cfct-linked-image-link-to-checkbox" value="nothing" <?php checked( $link_to, 'nothing' ); $this->is_default( 'link_to', $link_to ); ?> /> Nothing
			</label>

			<!-- Link to Lightbox -->
			<label class="es-cfct-linked-image-form-sublabel">
				<input type="radio" name="<?php echo $this->get_field_name('link_to'); ?>" id="<?php echo $this->get_field_id('link_to_lightbox'); ?>" class="es-cfct-linked-image-link_to_lightbox es-cfct-linked-image-link-to-checkbox" value="lightbox" <?php checked( $link_to, 'lightbox' ); $this->is_default( 'link_to', $link_to ); ?> /> Open in a <strong>Popup</strong> <span class="es-widget-form-subtext">- ie. A Lightbox</span>
			</label>

			<!-- Link to URL -->
			<label class="es-cfct-linked-image-form-sublabel">
				<input type="radio" name="<?php echo $this->get_field_name('link_to'); ?>" id="<?php echo $this->get_field_id('link_to_url'); ?>" class="es-cfct-linked-image-link_to_url es-cfct-linked-image-link-to-checkbox" value="url" <?php checked( $link_to, 'url' ); $this->is_default( 'link_to', $link_to ); ?> /> Link to a URL <span class="es-widget-form-subtext">- ie. http://www.somesite.com/</span>
			</label>

			<!-- Link to URL - Settings -->
			<div class="es-cfct-linked-image-link-to-settings es-cfct-linked-image-link-to-url-settings">
				<label>Please provide the URL that you want your image to link to!</label>
				<input type="text" name="<?php echo $this->get_field_name('link_to_url_url'); ?>" id="<?php echo $this->get_field_id('link_to_url_url'); ?>" class="es-cfct-linked-image-link_to_url_url" value="<?php echo esc_attr( $link_to_url_url ); ?>" />
			</div>

			<!-- Link to Object -->
			<label class="es-cfct-linked-image-form-sublabel">
				<input type="radio" name="<?php echo $this->get_field_name('link_to'); ?>" id="<?php echo $this->get_field_id('link_to_object'); ?>" class="es-cfct-linked-image-link_to_object es-cfct-linked-image-link-to-checkbox" value="object" <?php checked( $link_to, 'object' ); $this->is_default( 'link_to', $link_to ); ?> /> Link to something on <strong>My Website</strong> <span class="es-widget-form-subtext">- ie. Page, Post, News Article, Event...</span>
			</label>

			<!-- Link to Object - Settings -->
			<div class="es-cfct-linked-image-link-to-settings es-cfct-linked-image-link-to-object-settings">
				<input type="hidden" name="<?php echo $this->get_field_name('link_to_object_id'); ?>" id="<?php echo $this->get_field_id('link_to_object_id'); ?>" class="es-cfct-linked-image-link_to_object_id" value="<?php echo $link_to_object_id; ?>" />

				<label>Start typing the name of your Page, Post, etc to find it!</label>
				<input type="text" class="es-cfct-linked-image-link-to-object-search" value="" />

				<div class="es-cfct-linked-image-link-to-object-search-results"><!-- Silence --></div>

				<div class="es-cfct-linked-image-link-to-object-search-selection"><!-- Silence --></div>
			</div>
		</div>

		<div class="divider"></div>

		<!-- Show Title -->

		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-table-show-title" name="<?php echo $this->get_field_name('show-title' ); ?>" id="<?php echo $this->get_field_id('show-title'); ?>"<?php checked( $show_title, '1' ); $this->is_default( 'show-title', $show_title ); ?> />
				<?php _e( 'Show Title? <span class="es-widget-form-subtext">- Do you want to show your Image\'s <strong>Title</strong> above the Image?</span>'); ?>
			</label>
		</p>

		<!-- Responsive -->

		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" value="1" name="<?php echo $this->get_field_name('responsive' ); ?>" id="<?php echo $this->get_field_id('responsive'); ?>" class="es-cfct-linked-image-responsive" <?php if( $responsive ) echo ' checked="checked"'; $this->is_default( 'responsive', $responsive ); ?> />
				<?php echo _e('Responsive? <span class="es-widget-form-subtext">- Do you want your image to resize automatically based on it\'s surroundings?</span>'); ?>
			</label>
		</p>

		<!-- Text Wrap -->

		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" value="1" name="<?php echo $this->get_field_name('text_wrap' ); ?>" id="<?php echo $this->get_field_id('text_wrap'); ?>" class="es-cfct-linked-image-text_wrap" <?php if( $text_wrap ) echo ' checked="checked"'; $this->is_default( 'text_wrap', $text_wrap ); ?> />
				<?php echo _e('Text Wrap? <span class="es-widget-form-subtext">- Do you want <strong>Text</strong> to wrap around your image when it\'s aligned <strong>Left</strong> or <strong>Right</strong>?</span>'); ?>
			</label>
		</p>

	</div>
</div>