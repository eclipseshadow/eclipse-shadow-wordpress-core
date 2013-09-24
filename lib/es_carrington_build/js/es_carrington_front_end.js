jQuery(document).ready(function(){
	jQuery(".cfct-mod-content").hover(function(){
		var container = jQuery(this);
		var link = container.parents(".cfct-module-border").find(".cfct-build-module-edit-link");
		link.appendTo(container);
		link.show();

	}, function(){
		var container = jQuery(this);
		var parent = container.parents(".cfct-module-border");
		container.find(".cfct-build-module-edit-link").appendTo(parent).hide();
	});
});