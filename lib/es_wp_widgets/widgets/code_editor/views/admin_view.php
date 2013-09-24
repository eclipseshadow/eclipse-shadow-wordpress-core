<div class="es-widget-form-wrapper">

	<input type="hidden" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" value="<?php echo esc_attr( $content ); ?>" class="es-cfct-code-editor-content" />
	<input type="hidden" id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>" value="<?php echo $mode; ?>" class="es-cfct-code-editor-mode" />

	<p class="es-widget-form-instructions">
		For our advanced users, you can code in some custom <strong>HTMl</strong> or <strong>Javascript</strong> for your page.
		Use the <strong>Syntax Mode</strong> buttons to choose between HTML and Javascript. Click the <strong>Fullscreen</strong>
		button to go fullscreen.
	</p>

	<p>
		<label><?php _e('Title <span class="es-widget-form-subtext">- Give your Raw Code a name</span>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value='<?php echo esc_attr( $title ); ?>' />
	</p>

	<div class="es_code_editor_container">
		<!-- Silence is Golden -->
	</div>

	<div class="es-widget-form-advanced-options">

		<!-- Show Title -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-code-editor-show-title" name="<?php echo $this->get_field_name('show-title' ); ?>" id="<?php echo $this->get_field_id('show-title'); ?>"<?php checked( $show_title, '1' ); $this->is_default( 'show-title', $show_title ); ?> />
				<?php _e( 'Show Title? <span class="es-widget-form-subtext">- Do you want to show your Code Editor\'s <strong>Title</strong> above its output?</span>'); ?>
			</label>
		</p>

	</div>

</div>