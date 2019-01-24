(function($, boardNoticeBgColor, boardNoticeDefaultBgColor) {

	"use strict";

	// eslint-disable-next-line no-undef
	phpbb.addAjaxCallback("row_last", function(res) {
		if (typeof res.success === "undefined" || !res.success) {
			return;
		}

		var $firstTr = $(this).parents("tr"),
			$secondTr = $(this).parents("tbody").find("tr:last");

		$firstTr.insertAfter($secondTr);
	});

	// eslint-disable-next-line no-undef
	phpbb.addAjaxCallback("row_first", function(res) {
		if (typeof res.success === "undefined" || !res.success) {
			return;
		}

		var $secondTr = $(this).parents("tr"),
			$firstTr = $(this).parents("tbody").find("tr:first");

		$secondTr.insertBefore($firstTr);
	});

	$(function() {
		$("input[data-rule='checkbox']").on("click", function(e) {
			// Enable or disable a row when selected by the checkbox
			var rowName = $(e.currentTarget).attr("data-id");
			var rowId = $("tr[data-id='" + rowName + "']");
			if (rowId.length !== 1) {
				// console.error("Wrong element selected", rowId);
				return;
			}
			if (rowId.hasClass("inactive-rule")) {
				rowId.removeClass("inactive-rule").addClass("active-rule");
			} else {
				rowId.removeClass("active-rule").addClass("inactive-rule");
			}
		});

		function getColpickConfig(colorpick) {
			return {
				layout: "hex",
				submit: 0,
				onBeforeShow: function() {
					if (colorpick !== "") {
						$(this).colpickSetColor(colorpick);
						colorpick = "";
					}
				},
				onChange: function(hsb, hex, rgb, el, bySetColor) {
					$(el).css({
						"border-right-color": "#" + hex,
						"border-right-width": "20px",
						"border-right-type": "solid"
					});
					if (!bySetColor) {
						$(el).val(hex);
					}
				}
			};
		}

		$("#board_notice_bgcolor").colpick(
			getColpickConfig(boardNoticeBgColor)
		).keyup(function() {
			$(this).colpickSetColor(this.value || "ffffff");
		});

		$("#board_notice_default_bgcolor").colpick(
			getColpickConfig(boardNoticeDefaultBgColor)
		).keyup(function() {
			$(this).colpickSetColor(this.value || "ffffff");
		});

	});

// eslint-disable-next-line no-undef
})(jQuery, boardNoticeBgColor, boardNoticeDefaultBgColor);
