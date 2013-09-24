<div class="es-widget-form-wrapper">

	<input type="hidden" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" value="<?php echo esc_attr( $content ); ?>" class="es-cfct-rich-text-content" />

	<p class="es-widget-form-instructions">
		Below you will find the same Wordpress Rich Text Editor you're used to when writing content for <em>News Feeds</em>, <em>Posts</em>,
		etc. It's just like using <strong>Microsoft Word</strong>. Click the <strong>Add Media</strong> button to add Images, etc to your
		<em>Rich Text</em>
	</p>

	<p>
		<label><?php _e('Title <span class="es-widget-form-subtext">- Give your Rich Text a name</span>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value='<?php echo esc_attr( $title ); ?>' />
	</p>

	<div class="es_rich_text_container"></div>

	<div class="es-widget-form-advanced-options">

		<!-- Show Title -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-code-editor-show-title" name="<?php echo $this->get_field_name('show-title' ); ?>" id="<?php echo $this->get_field_id('show-title'); ?>"<?php checked( $show_title, '1' ); $this->is_default( 'show-title', $show_title ); ?> />
				<?php _e( 'Show Title? <span class="es-widget-form-subtext">- Do you want to show your Rich Text\'s <strong>Title</strong> above the Text?</span>'); ?>
			</label>
		</p>

	</div>

</div>