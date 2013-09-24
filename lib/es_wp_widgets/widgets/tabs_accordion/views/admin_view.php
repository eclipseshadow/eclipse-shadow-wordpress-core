<div class="es-widget-form-wrapper">

	<input type="hidden" id="<?php echo $this->get_field_id('tabs-accordion-data'); ?>" name="<?php echo $this->get_field_name('tabs-accordion-data'); ?>" value="<?php echo esc_attr( $tabs_accordion_data ); ?>" class="es-cfct-tabs-accordion-data" />

	<p class="es-widget-form-instructions">
		Breaking content up into <strong>Tabs</strong> allows you to display a lot of information to a user in very little space.
		The user can then click through your tabs to see all your information.
	</p>
	<ul>
		<li>To <em>Add</em> a <strong>Tab</strong>, click the <em>"Add Tab"</em> button.</li>
		<li>To <em>Remove</em> a <strong>Tab</strong>, click the <em>X</em> icon on the tab.</li>
		<li>To <em>Rename</em> a <strong>Tab</strong>, click the <em>pencil</em> icon on the tab.</li>
		<li>You can <em>Rearrange</em> <strong>Tabs</strong> by <em>Clicking</em> and <em>Dragging</em> them.</li>
		<li>You have 4 different types of <em>Items</em> you can add to your <strong>Tabs</strong>: <em>Rich Text</em>,
			<em>Raw HTML/JS</em>, an entire <em>Widget Area</em>, and a <em>Server Page (advanced users only)</em>. Click the
			<strong>Add Item</strong> button to <em>get started!</em></li>
		<li>You can <em>Rearrange</em> <strong>Tab Items</strong> by <em>Clicking</em> and <em>Dragging</em> them.</li>
	</ul>

	<div class="divider"></div>

	<div class="es_tabs_container">
		<p>
			<label><?php _e('Title <span class="es-widget-form-subtext">- Give your Tabs a name</span>'); ?></label>
			<br /><input class="es-cfct-tabs-title" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value='<?php echo esc_attr( $title ); ?>' />
		</p>

		<!-- Silence is Golden -->
	</div>

	<div class="es-widget-form-advanced-options">

		<!-- Show Title -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-tabs-show-title" name="<?php echo $this->get_field_name('show-title' ); ?>" id="<?php echo $this->get_field_id('show-title'); ?>"<?php checked( $show_title, '1' ); $this->is_default( 'show-title', $show_title ); ?> />
				<?php _e( 'Show Title? <span class="es-widget-form-subtext">- Do you want to show your Tabs\' <strong>Title</strong> above the Tabs?</span>'); ?>
			</label>
		</p>

		<!-- Display as Accordion -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" value="1" class="es-tabs-accordion-display-as-accordion" name="<?php echo $this->get_field_name('display-as-accordion' ); ?>" id="<?php echo $this->get_field_id('display-as-accordion'); ?>"<?php checked( $display_as_accordion, '1' ); $this->is_default( 'display-as-accordion', $display_as_accordion ); ?> />
				<?php _e( 'Display as an Accordion? <span class="es-widget-form-subtext">- Do you want to display your <strong>Tabs</strong> as an <strong>Accordion</strong> instead?</span>'); ?>
			</label>
		</p>
	</div>

</div>