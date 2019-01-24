
// eslint-disable-next-line no-undef
phpbb.addAjaxCallback("close_notice", function(res) {
	"use strict";
	if (res.success) {
		// eslint-disable-next-line no-undef
		phpbb.toggleDisplay("fq_notice", -1);
	}
});
