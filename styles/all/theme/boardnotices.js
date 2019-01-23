// Add an AJAX callback function
phpbb.addAjaxCallback('close_notice', function(res) {
	'use strict';
	if (res.success) {
		phpbb.toggleDisplay('fq_notice', -1);
	}
});
