(function($) {

'use strict';

phpbb.addAjaxCallback('row_last', function(res) {
	if (typeof res.success === 'undefined' || !res.success) {
		return;
	}

	var $firstTr = $(this).parents('tr'),
		$secondTr = $(this).parents('tbody').find('tr:last');

	$firstTr.insertAfter($secondTr);
});

phpbb.addAjaxCallback('row_first', function(res) {
	if (typeof res.success === 'undefined' || !res.success) {
		return;
	}

	var $secondTr = $(this).parents('tr'),
		$firstTr = $(this).parents('tbody').find('tr:first');

	$secondTr.insertBefore($firstTr);
});

})(jQuery);
