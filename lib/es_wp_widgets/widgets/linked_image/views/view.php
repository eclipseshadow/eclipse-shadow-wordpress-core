<div class="cfct-mod-content <?php echo $alignment_class .' '. $custom_css_classes; ?>">

	<?php

	$show_link = false;
	$lightbox_rel = '';
	if ( in_array( $instance[ $this->get_field_name('link_to') ], array('url', 'object', 'lightbox') ) ) {
		$show_link = true;

		switch( $instance[ $this->get_field_name('link_to') ] ) {
			case 'url':
				$link_url = $instance[ $this->get_field_name('link_to_url_url') ];
				break;
			case 'object':
				$link_url = get_permalink( $instance[ $this->get_field_name('link_to_object_id') ] );
				break;
			case 'lightbox':
				$link_url = $full_size_url;
				$lightbox_rel = 'rel="es-lightbox"';
				break;
		}
	}

	if ( $show_link ) {
		echo '<a href="'. $link_url .'" title="" '. $lightbox_rel .'>';
	} ?>

	<img class="<?php echo $responsive_class; ?>" src="<?php echo $url; ?>" WIDTH="<?php echo $dims[0]; ?>" HEIGHT="<?php echo $dims[1]; ?>" alt="" />

	<?php if ( $show_link ) {
		echo '</a>';
	} ?>

</div>
<?php
if ( '1' != $instance[ $this->get_field_name('text_wrap') ] ) {
	echo '<div class="es-cfct-linked-image-clear"></div>';
}
?>