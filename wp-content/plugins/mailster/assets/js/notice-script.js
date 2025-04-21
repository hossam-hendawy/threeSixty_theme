mailster = (function (mailster, $, window, document) {
	'use strict';

	mailster.notices = mailster.notices || {};

	mailster.notices.$ = $('.mailster-notice');

	mailster.$.document.on(
		'click',
		'.mailster-notice .notice-dismiss, .mailster-notice .dismiss',
		function (event) {
			event.preventDefault();

			var el = $(this).closest('.mailster-notice'),
				ids = el.data('id');

			if (event.altKey) {
				el = mailster.notices.$;
				ids = el
					.map(function () {
						return $(this).data('id');
					})
					.get();
			}

			if (!ids) return;

			el.addClass('idle');
			mailster.util.ajax('notice_dismiss', { ids: ids }, function (response) {
				if (response.success) {
					el.fadeTo(100, 0, function () {
						el.slideUp(100, function () {
							el.remove();
							if (!$('.mailster-notice').length) {
								mailster.dom.body.classList.add('mailster-close-notices');
							}
						});
					});
				} else {
					el.removeClass('idle');
				}
			});
		}
	);

	return mailster;
})(mailster || {}, jQuery, window, document);
