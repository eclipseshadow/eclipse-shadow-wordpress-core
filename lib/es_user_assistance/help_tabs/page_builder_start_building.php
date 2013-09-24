<?php
global $pagenow;
$imgs = ES_USER_ASSISTANCE_DIR .'/images/';
$current_user_role = ES_User_Assistance::get_user_role();
?>

<h2>Follow the steps below to create a page with the <em>Nebula Page Builder</em></h2>
<ol>
	<li>
		Give your page a <strong>Title</strong> - <a class="es_goto_link" data-parent-selector="self" href="#title" title="Click here to go to the Page Title">Take Me There &gt;&gt;</a>
		<p class="clearfix">
			<img src="<?php echo $imgs; ?>post_enter_title.jpg" alt="Post Enter Title" />
		</p>
	</li>
	<li>
		Add <strong>Rows</strong> to your page - <a class="es_goto_link" data-parent-selector="#cfct-sortables-add-container" href="#cfct-sortables-add" title="Click here to go to the Add Row button">Take Me There &gt;&gt;</a>
		<p class="clearfix">
			You can divide each row into up to <strong>3 columns</strong>. You can also choose to have 2 columns
			with <strong>one</strong> wider than the <strong>other</strong>.
			<br /><img src="<?php echo $imgs; ?>page_builder_add_row.jpg" alt="Page Builder - Add Row" />
		</p>
	</li>
	<li>
		Add <strong>Modules</strong> to your <strong>Rows</strong> - <a class="es_goto_link" data-parent-selector=".cfct-build-add-module" href=".cfct-add-new-module" title="Click here to go to the Add Module button">Take Me There &gt;&gt;</a>
		<p class="clearfix">
			<strong>Modules</strong> are all the neat little <strong>gizmos</strong> that make up your page.
			Some examples are <strong><em>Slide Shows, Image Galleries, Rich Text, etc</em></strong>.
			<br /><img src="<?php echo $imgs; ?>page_builder_add_module.jpg" alt="Page Builder - Add Module" />
		</p>
	</li>

	<?php if ( 'client_basic' != $current_user_role ): ?>
	<li>
		Choose a <strong>Page Layout</strong> - <a class="es_goto_link" data-parent-selector="self" data-pad-parent-h="false" data-pad-parent-v="false" href="#genesis_inpost_layout_box" title="Click here to go to the Page Layout Box">Take Me There &gt;&gt;</a>
		<p class="clearfix">
			If your website is set up to use <strong>Sidebars</strong>, you can choose whether or not this
			page displays those sidebars by choosing a <strong>Page Layout</strong>. This allows you to place the
			sidebar(s) on the <em>left</em>, the <em>right</em> or even have a <strong>Full Width</strong> page.
			<em>See the <a href="<?php echo admin_url('widgets.php'); ?>" title="Click here to go to your Widgets Admin Page">Widgets Admin Page</a>
			to set up your <strong>Sidebars</strong>.</em>
			<br /><img src="<?php echo $imgs; ?>post_page_layout.jpg" alt="Page Builder - Page Layout" />
		</p>
		<p><span class="es_help_sidenote">* It is recommended that you leave this setting alone unless you know for sure you want to change it.</span></p>
	</li>
	<?php endif; ?>

	<li>
		<?php
		$new_post = 'post-new.php' == $pagenow ? true : false;
		$btnurl = $new_post ? $imgs .'post_btn_publish.jpg' : $imgs .'post_btn_update.jpg';
		$save_word = $new_post ? 'Publish' : 'Update';
		$save_word_2 = $new_post ? 'finish creating' : 'save';
		?>
		Click <strong><?php echo $save_word; ?></strong>! - <a class="es_goto_link" data-parent-selector="#major-publishing-actions" href="#publish" title="Click here to go to the Publish/Update button">Take Me There &gt;&gt;</a>
		<p>
			Click <strong><?php echo $save_word; ?></strong> to <?php echo $save_word_2; ?> your page!
			<br /><img src="<?php echo $btnurl; ?>" alt="Post - Publish/Update Button" />
		</p>
	</li>
	<p><span class="es_help_sidenote">* Check out the other <a class="es_goto_link" data-parent-selector="self" data-pad-parent-h="false" data-pad-parent-v="false" href=".contextual-help-tabs ul">Help Tabs</a> to learn more about building beautiful pages!</span></p>
</ol>