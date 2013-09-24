<div class="es-widget-form-wrapper">
	<p class="es-widget-form-instructions">
		Creating a <strong>Table</strong> is very simple. You just click inside the <em>cells</em> to edit them and click <strong>Save</strong> when you're
		finished.
	</p>
	<ul>
		<li>To <em>Add</em> <strong>Rows</strong> &amp; <strong>Columns</strong>, click the <em>"Add Row"</em> &amp; <em>"Add Column"</em> buttons.</li>
		<li>To <em>Remove</em> <strong>Rows</strong> &amp; <strong>Columns</strong>, click the <a href="#" class="es-cfct-sample-remove-btn"></a> buttons.</li>
		<li>To <em>Rearrange</em> <strong>Rows</strong>, just <em>Click-n-Drag</em> the row to where you want it.</li>
		<li>To <em>Rearrange</em> <strong>Columns</strong>, click the <a href="#" class="es-cfct-sample-move-btn"></a> buttons.</li>
	</ul>
	<span class="es-widget-form-subtext">* For more advanced options, click <strong>Show Advanced Options</strong></span>.

	<input type="hidden" id="<?php echo $this->get_field_id('table-data'); ?>" name="<?php echo $this->get_field_name('table-data'); ?>" class="es-cfct-table-data" value='<?php echo esc_attr( $table_data ); ?>' />

	<div class="divider"></div>

	<div class="es_table_container">
		<p>
			<label><?php _e('Title <span class="es-widget-form-subtext">- Give your Tabs a name</span>'); ?></label>
			<br /><input class="es-cfct-tabs-title" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value='<?php echo esc_attr( $title ); ?>' />
		</p>
		<div class="es-cfct-table-arrow-bg"></div>

		<!-- Silence is Golden -->
	</div>

	<div class="es-widget-form-advanced-options">

		<!-- Show Title -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-table-show-title" name="<?php echo $this->get_field_name('show-title' ); ?>" id="<?php echo $this->get_field_id('show-title'); ?>"<?php checked( $show_title, '1' ); $this->is_default( 'show-title', $show_title ); ?> />
				<?php _e( 'Show Title? <span class="es-widget-form-subtext">- Do you want to show your Table\'s <strong>Title</strong> above the Table?</span>'); ?>
			</label>
		</p>

		<!-- Use First Row as Headings -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-cfct-table-first-row-headings" name="<?php echo $this->get_field_name('first-row-headings' ); ?>" id="<?php echo $this->get_field_id('first-row-headings'); ?>"<?php checked( $first_row_headings, '1' ); $this->is_default( 'first-row-headings', $first_row_headings ); ?> />
				<?php _e( 'First Row Headings? <span class="es-widget-form-subtext">- Do you want to use the first row as Table Headings?</span>'); ?>
			</label>
		</p>

		<!-- Sortable Headings -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-cfct-table-sortable-headings" name="<?php echo $this->get_field_name('sortable-headings' ); ?>" id="<?php echo $this->get_field_id('sortable-headings'); ?>"<?php checked( $sortable_headings, '1' ); $this->is_default( 'sortable-headings', $sortable_headings ); ?> />
				<?php _e( 'Sortable Headings? <span class="es-widget-form-subtext">-Do you want users to be able to sort your table by clicking the table headings? <br/><em>(Only applies if the "Use first row as Table Headings" box is checked)</em></span>'); ?>
			</label>
		</p>

		<!-- Filterable Headings -->
		<!--
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-cfct-table-filterable" name="<?php echo $this->get_field_name('filterable' ); ?>" id="<?php echo $this->get_field_id('filterable'); ?>"<?php checked( $filterable, '1' ); $this->is_default( 'filterable', $filterable ); ?> />
				<?php _e( 'Filterable? <span class="es-widget-form-subtext">- Do you want users to be able to <strong>Filter</strong> your table using <strong>Keywords</strong>?</span>'); ?>
			</label>
		</p>
		-->

		<!-- Pagination -->
		<p class="es-widget-form-field">
			<label>
				<input type="checkbox" class="es-cfct-table-paginate" name="<?php echo $this->get_field_name('paginate' ); ?>" id="<?php echo $this->get_field_id('paginate'); ?>"<?php checked( $paginate, '1' ); $this->is_default( 'paginate', $paginate ); ?> />
				<?php _e( 'Paginated? <span class="es-widget-form-subtext">- Do you want to break up your table into <strong>Pages</strong> if it\'s too large?</span>'); ?>
			</label>
		</p>

		<div class="divider"></div>

		<a href="" class="es-cfct-keyboard-shortcuts-link">Show Keyboard Shortcuts</a>
		<table class="es-cfct-keyboard-shortcuts">
			<tr>
				<td colspan="2">Everything is also powered by <strong>Keyboard Shortcuts</strong>:</td>
			</tr>
			<tr>
				<td>Tab</td><td>Moves from one cell to another.</td>
			</tr>
			<tr>
				<td>Shift^Tab</td><td>Moves from one cell to another in reverse direction.</td>
			</tr>
			<tr>
				<td>Enter</td><td>Closes the edit box for any active cell.</td>
			</tr>
			<tr>
				<td>Alt^Up Arrow</td><td>Moves the active row <em>Up</em>.</td>
			</tr>
			<tr>
				<td>Alt^Down Arrow</td><td>Moves the active row <em>Down</em>.</td>
			</tr>
			<tr>
				<td>Alt^Left Arrow</td><td>Moves the active column <em>Left</em>.</td>
			</tr>
			<tr>
				<td>Alt^Right Arrow</td><td>Moves the active column <em>Right</em>.</td>
			</tr>
			<tr>
				<td>Alt^R</td><td>Creates a <em>New Row</em>.</td>
			</tr>
			<tr>
				<td>Alt^C</td><td>Creates a <em>New Column</em>.</td>
			</tr>
			<tr>
				<td>Alt^Delete</td><td>Removes the active <em>Row</em>.</td>
			</tr>
		</table>
	</div>
</div>