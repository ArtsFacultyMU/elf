$(document).ready(function ($) {

	// delegate calls to data-toggle="lightbox"
	$(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(event) {
		event.preventDefault();
//		jQuery.noConflict();
		return $(this).ekkoLightbox({
//			gallery_parent_selector: '.box mod-folder-right',
			onShown: function() {
				if (window.console) {
					return console.log('onShown event fired');
				}
			},
			onContentLoaded: function() {
				if (window.console) {
					return console.log('onContentLoaded event fired');
				}
			},
			onNavigate: function(direction, itemIndex) {
				if (window.console) {
					return console.log('Navigating '+direction+'. Current item: '+itemIndex);
				}
			}
		});
	});

	
});