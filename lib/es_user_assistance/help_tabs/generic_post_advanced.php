<?php
global $pagenow;
$imgs = ES_USER_ASSISTANCE_DIR .'/images/';
$post_type = ES_User_Assistance::get_post_type();
$post_type_label = ES_User_Assistance::get_post_type_label( $post_type );
?>

<h2>Here are some <em>Advanced Tips</em> for the power users among us.</h2>
<p>
	Once you've gotten comfortable with the interface, you can start changing around some of the options below:
</p>

<p><strong class="es_help_heading">Screen Options</strong> - <em>Top Right by the <strong>Help</strong> tab</em></p>
<p>
	The <strong>Screen Options</strong> tab allows you to show &amp; hide certain items on the screen. If you're looking
	for something and can't find it, chances are it's <em>hidden!</em>. To hide/show items on the screen, click the
	<strong>Screen Options</strong> tab and <em>check</em> or <em>uncheck</em> your desired items.
</p>
<img src="<?php echo $imgs; ?>screen_options_tab.jpg" alt="Screen Options Tab" />

<p><strong class="es_help_heading">Permalink</strong> - <a class="es_goto_link" data-parent-selector="self" href="#edit-slug-box" title="Click here to go to set your Permalink">Take Me There &gt;&gt;</a></p>
<p>
	The page <strong>Permalink</strong> setting allows you to change the <strong>URL</strong> of this <?php echo $post_type_label; ?>.
</p>
<p>
	<span class="es_help_sidenote">* It is recommended that you do not change this once your website has been live
	for a substantial amount of time as it cab negatively affect your <strong>Search Engine Placement</strong>.</span>
</p>
<img src="<?php echo $imgs; ?>generic_post_permalink.jpg" alt="Page Attributes - Order" />

<?php if ( 'page' == $post_type ): ?>

<p><strong class="es_help_heading">Page Attributes</strong> - <a class="es_goto_link" data-parent-selector="self" data-pad-parent-h="false" data-pad-parent-v="false" href="#pageparentdiv" title="Click here to go to set your Page Attributes">Take Me There &gt;&gt;</a></p>
<ol>
	<li>
		Page <strong>Parent</strong>
		<p>
			The page <strong>Parent</strong> setting allows you to choose which <em>other page</em> this page falls
			under <em>(is a child of)</em>. This will affect your URL <em>(ie. /parent-page/child-page/)</em> as well as
			your navigation menus in some cases.
		</p>
		<img src="<?php echo $imgs; ?>page_page_attributes_parent.jpg" alt="Page Attributes - Parent" />
	</li>
	<li>
		Page <strong>Template</strong>
		<p>
			The page <strong>Template</strong> setting allows you to select a <em>custom-coded page template</em> that your
			developer may have written for you.
		</p>
		<p>
			<span class="es_help_sidenote">* It is recommended that you leave this setting alone unless your developer has
			personally advised you to change it.</span>
		</p>
		<img src="<?php echo $imgs; ?>page_page_attributes_template.jpg" alt="Page Attributes - Template" />
	</li>
	<li>
		Page <strong>Order</strong>
		<p>
			The page <strong>Order</strong> setting allows you to control the <em>sort order</em> for your <strong>Pages</strong>.
			This can affect a number of things, but most commonly affects the order in which your pages appear in your <em>navigation menu(s)</em>.
		</p>
		<img src="<?php echo $imgs; ?>page_page_attributes_order.jpg" alt="Page Attributes - Order" />
	</li>
</ol>
<?php endif; ?>