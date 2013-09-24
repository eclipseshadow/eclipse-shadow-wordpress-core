<div class="cfct-mod-content <?php echo $custom_css_classes; ?>">

<?php if ( $before_deck ) : ?>
	<div class="sd2-before"> <?php echo $before_deck; ?> </div>
<?php endif; ?>

<?php echo do_shortcode( $shortcode ); ?>

<?php if ( $after_deck ) : ?>
	<div class="sd2-after"> <?php echo $after_deck; ?> </div>
<?php endif; ?>

</div>