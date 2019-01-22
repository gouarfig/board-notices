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

	$(() => {
		$("input[data-rule='checkbox']").on('click', function(e) {
			// Enable or disable a row when selected by the checkbox
			var rowName = $(e.currentTarget).attr("data-id");
			var rowId = $("tr[data-id='" + rowName + "']");
			if (rowId.length !== 1) {
				console.error("Wrong element selected", rowId);
				return;
			}
			if (rowId.hasClass('inactive-rule')) {
				rowId.removeClass('inactive-rule').addClass('active-rule');
			} else {
				rowId.removeClass('active-rule').addClass('inactive-rule');
			}
		});
	});

})(jQuery);
