jQuery(document).ready(function($) {
	 	
	  "use strict";
		// Init media buttons
		
		$('.pvct-upload-button').live('click', function(e) {
			var $button = $(this),
			$val = $(this).parents('.pvct-upload-container').find('input:text'),
			file;
			e.preventDefault();
			e.stopPropagation();
			// If the frame already exists, reopen it
			if (typeof(file) !== 'undefined') file.close();
			// Create WP media frame.
			file = wp.media.frames.perch_media_frame_2 = wp.media({
				// Title of media manager frame
				title: 'Upload image',
				button: {
					//Button text
					text: pvct.button_text
				},
				// Do not allow multiple files, if you want multiple, set true
				multiple: false
			});
			//callback for selected image
			file.on('select', function() {
				var attachment = file.state().get('selection').first().toJSON();
				$val.val(attachment.url).trigger('change');
				$val.closest('.pvct-upload-container').find('img').attr('src', attachment.url);
			});
			// Open modal
			file.open();
		});
});		