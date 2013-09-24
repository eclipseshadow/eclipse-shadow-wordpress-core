<?php
global $pagenow;
$imgs = ES_USER_ASSISTANCE_DIR .'/images/';
$post_type_label = ES_User_Assistance::get_post_type_label( ES_User_Assistance::get_post_type() );
?>


<h2>What do all these options in the <em>Publish</em> box do?</h2>
<p>
	<a class="es_goto_link" data-parent-selector="self" data-pad-parent-h="false" data-pad-parent-v="false" href="#submitdiv" title="Click here to go to the Publish Options">Take Me to the Publish Options &gt;&gt;</a>
</p>
<p>
	You have 4 options when publishing &amp; updating your page. Let's teach you how to use them:
</p>
<ol>
	<li>
		<strong>Status</strong> (Published, Pending Review, Draft)
		<p>
			The publish <strong>Status</strong> determines which <em>stage</em> your <?php echo $post_type_label; ?>
			is in. When your status <strong>Published</strong>, it's <em>Live</em>. When your status is
			<strong>Draft</strong>, it's not visible to the public and it means you're <em>still working on it</em>.
			The last option is <strong>Pending Review</strong>. This applies mainly to Articles &amp; other pieces of
			content that needs to be reviewed by someone else <em>(such as an Editor)</em>. In most cases, you probably
			won't use this option.
		</p>
		<img src="<?php echo $imgs; ?>generic_post_publish_options_status.jpg" alt="Publish Options - Status" />
	</li>
	<li>
		<strong>Visibility</strong> (Public, Password Protected, Private)
		<p>
			The <strong>Visibility</strong> option determines <em>who</em> will be able to see your <?php echo $post_type_label; ?>
			when it's <em>live</em>. <strong>Public</strong> visibility allows <em>everyone</em> to see your <?php echo $post_type_label; ?>.
			<strong>Private</strong> visibility makes your <?php echo $post_type_label; ?> visible only to you. You're able to see
			your <?php echo $post_type_label; ?> in the <em>Administrative Interface</em> (you're here right now!) and on the
			<em>Front End</em> of your website when you're <strong>Logged In</strong>. The last option, <strong>Password Protected</strong>,
			allows only those who have your <strong>Chosen Password</strong> to see it when viewing your website.
		</p>
		<img src="<?php echo $imgs; ?>generic_post_publish_options_visibility.jpg" alt="Publish Options - Visibility" />
	</li>
	<li>
		<strong>Revisions</strong>
		<p>
			Your <strong>Revisions</strong> allow you to view a <em>history</em> of your <?php echo $post_type_label; ?>'s.
			If you want to <em>revert</em> your <?php echo $post_type_label; ?> back to a revision from the past, you may do
			so by clicking the <strong>Restore this Revision</strong> button in the revision editor.
		</p>
		<img src="<?php echo $imgs; ?>generic_post_publish_options_revisions.jpg" alt="Publish Options - Revisions" />
	</li>
	<li>
		<strong>Published On</strong> (Choose a Date)
		<p>
			The <strong>Published On</strong> option allows you to change the <em>Publish Date</em> of your <?php echo $post_type_label; ?>.
			This refers to the date that the <?php echo $post_type_label; ?> will go live!
		</p>
		<p><span class="es_help_sidenote">* This can be used to set your <?php echo $post_type_label; ?> to publish in the future automatically!.</span></p>
		<img src="<?php echo $imgs; ?>generic_post_publish_options_publish_on.jpg" alt="Publish Options - Published On" />
	</li>
</ol>