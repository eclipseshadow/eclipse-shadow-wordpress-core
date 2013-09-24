<div class="es-widget-form-wrapper">

	<p>
		<label><?php _e('Title <span class="es-widget-form-subtext">- Give your Slide Show a name</span>'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>

	<p>
		<label><strong><?php _e("Choose a Slide Show"); ?>:</strong></label>
		<br />
		<select data-slidedeck-id="<?php echo $slidedeck_id; ?>" name="<?php echo $this->get_field_name( 'slidedeck_id' ); ?>" id="<?php echo $this->get_field_id( 'slidedeck_id' ); ?>" class="widefat es-cfct-slidedeck-slidedeck-select">
			<!-- Slidedecks pulled in via Ajax -->
		</select>
		<?php
		$return_to_msg = 'Go back to Editing my Slide Show Widget';
		?>
		<a data-auto-save-on-unload="true" data-return-to-message="<?php echo $return_to_msg; ?>" data-return-to-url="<?php echo $return_to_url; ?>" class="button" id="es-cfct-slidedeck-add-new-btn" href="/wp-admin/admin.php?page=slidedeck2.php" >Manage Slide Shows</a>
	</p>

	<!--
	<p>
		<label><?php _e('Intro Text <span class="es-widget-form-subtext">- Place some text <strong>before</strong> your Slide Show</span>'); ?></label>
		<textarea class="widefat" id="<?php echo $this->get_field_id('_before_deck'); ?>" name="<?php echo $this->get_field_name('text_before_deck'); ?>"><?php echo esc_attr( $before_deck ); ?></textarea>
	</p>

	<p>
		<label><?php _e('Footer text <span class="es-widget-form-subtext">- Place some text <strong>after</strong> your Slide Show</span>'); ?></label>
		<textarea class="widefat" id="<?php echo $this->get_field_id('_after_deck'); ?>" name="<?php echo $this->get_field_name('text_after_deck'); ?>"><?php echo esc_attr( $after_deck ); ?></textarea>
	</p>
	-->

	<div class="es-widget-form-advanced-options">

		<!-- Show Title -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-code-editor-show-title" name="<?php echo $this->get_field_name('show-title' ); ?>" id="<?php echo $this->get_field_id('show-title'); ?>"<?php checked( $show_title, '1' ); $this->is_default( 'show-title', $show_title ); ?> />
				<?php _e( 'Show Title? <span class="es-widget-form-subtext">- Do you want to show your Slide Show\'s <strong>Title</strong> above the Slide Show?</span>'); ?>
			</label>
		</p>

		<!-- Responsive -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" value="1" name="<?php echo $this->get_field_name('use_ress' ); ?>" id="<?php echo $this->get_field_id('use_ress'); ?>"<?php checked( $use_ress ); $this->is_default( 'use_ress', $use_ress ); ?> />
				<?php _e( 'Make Slide Show Responsive?'); ?>
			</label>
		</p>

		<!-- Proportional Scaling -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" value="1" name="<?php echo $this->get_field_name('proportional' ); ?>" id="<?php echo $this->get_field_id('proportional'); ?>"<?php checked( $proportional ); $this->is_default( 'proportional', $proportional ); ?> />
				<?php _e( 'Scale Slide Show Proportionately? <span class="es-widget-form-subtext">- Only applies when slide show is <strong>responsive</strong></span>'); ?>
			</label>
		</p>

		<!-- iFrame -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" value="1" name="<?php echo $this->get_field_name('deploy_as_iframe' ); ?>" id="<?php echo $this->get_field_id('deploy_as_iframe'); ?>"<?php checked( $deploy_as_iframe ); $this->is_default( 'deploy_as_iframe', $deploy_as_iframe ); ?> />
				<?php _e( 'Deploy Slide Show using an iframe'); ?>
			</label>
		</p>

	</div>
</div>