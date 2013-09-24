<?php
$imgs = ES_USER_ASSISTANCE_DIR .'/images/';
$post_type_label = ES_User_Assistance::get_post_type_label( ES_User_Assistance::get_post_type() );
?>

<h2>Welcome to the <em><?php echo $post_type_label; ?> Editor!</em></h2>
<p>
	Below is where you fill in all the content for your <?php echo $post_type_label; ?>.
	You can take a look at the <a class="es_goto_link" data-parent-selector="self" data-pad-parent-h="false" data-pad-parent-v="false" href=".contextual-help-tabs ul" title="Click here to go to highlight the other Help Tabs">Other Help Tabs</a> <em>on the left</em>
	to learn more about how to use the <?php echo $post_type_label; ?> Editor.
</p>
<p>
	<a class="es_goto_link" data-parent-selector="self" data-pad-parent-h="false" href="#post-body-content" title="Click here to go to the Editor">Take Me to the <?php echo $post_type_label; ?> Editor &gt;&gt;</a>
</p>