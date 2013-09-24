<?php
global $pagenow, $post;
$imgs = ES_USER_ASSISTANCE_DIR .'/images/';
$post_type_label = ES_User_Assistance::get_post_type_label( ES_User_Assistance::get_post_type() );
$has_post_thumbnail = has_post_thumbnail( $post->ID );
?>

<h2>What's a <em>Featured Image</em>?</h2>
<p>
	A <strong>Featured Image</strong> is the <strong><em>Primary</em></strong> image for your
	<?php echo $post_type_label; ?>. This is the image that show's up everywhere on the site that
	<em>represents</em> this <?php echo $post_type_label; ?>.
	<br /><strong><em>Example:</em></strong>
	<br /><img src="<?php echo $imgs; ?>generic_post_featured_image_example.jpg" alt="Post/Page - Featured Image Example" />
</p>
<p><strong>Here's how to set your <em>Featured Image:</em></strong></p>
<ol>
	<li>
		Click on <strong>Set Featured Image</strong> - <a class="es_goto_link" data-parent-selector=".inside" href="<?php echo $has_post_thumbnail ? '#remove-post-thumbnail' : '#set-post-thumbnail'; ?>" title="Click here to go to Set your Featured Image">Take Me There &gt;&gt;</a>
		<p>
			This will bring up your <strong>Media Manager</strong>, where all your images, videos, etc are stored.
		</p>
		<?php if ( $has_post_thumbnail ) : ?>
			<p>
				<span class="es_help_sidenote">* It looks like you already have a <strong>Featured Image</strong> for
				this <?php echo $post_type_label; ?>! You can click <a class="es_goto_link" data-parent-selector=".inside" href="#remove-post-thumbnail" title="">Remove Featured Image</a>
				to remove the <strong>Featured Image</strong> and set a new one.</span>
			</p>
		<?php else : ?>
			<br />
		<?php endif; ?>
		<img src="<?php echo $imgs; ?>generic_post_set_featured_image.jpg" alt="Post/Page - Set Featured Image" />
		<img style="margin-left: 25px;" src="<?php echo $imgs; ?>generic_post_remove_featured_image.jpg" alt="Post/Page - Remove Featured Image" />
	</li>
	<li>
		Choose an image from your <strong>Media Library</strong>
		<p>
			If your image hasn't been <em>uploaded</em> yet, you can click on the <strong>Upload Files</strong> tab and
			either <em>Drag &amp; Drop</em> your image onto the window or click <strong>Select Files</strong> to choose
			an image <em>from your computer</em>.
			<br />
			<img src="<?php echo $imgs; ?>media_manager_tabs.jpg" alt="Media Manager - Media Library and Upload Files Tabs" />
			<img style="margin-left: 25px;" src="<?php echo $imgs; ?>media_manager_drop_files_anywhere.jpg" alt="Media Manager - Drop Files Anywhere" />
		</p>
	</li>
	<li>
		Click the <strong>Set Featured Image</strong> button in the <strong>Media Manager</strong>
		<p>
			<img src="<?php echo $imgs; ?>/media_manager_set_featured_image_btn.jpg" alt="Media Manager - Set Featured Image Button" />
		</p>
	</li>
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
</ol>
<p>
	<span class="es_help_sidenote">* <strong>Feature Images</strong> can be used for a number
	of purposes and usage varies from website to website. If you have questions about what the
	<strong>Featured Image</strong> is used for on your website, just
	<a href="<?php echo apply_filters('es_contact_support_admin_url', admin_url('support')); ?>"
	title="Click here to Contact Support">Contact Support!</a></span>
</p>