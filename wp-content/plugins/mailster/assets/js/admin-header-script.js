mailster = (function (mailster, $, window, document) {
	'use strict';

	var loaded = false;
	var beacon;
	var queue = [];
	var beacondata;
	var articles = [];
	var helpbtn = $('#mailster-admin-help');

	var beacon = function (method, options, data) {
		queue.push({
			method: method,
			options: options,
			data: data,
		});
	};

	mailster.$.document
		.on('click', '#mailster-admin-help', function () {
			if (!requireConsent()) {
				location.href = $(this).attr('href');
				return false;
			}

			beacon('navigate', '/');
			beacon('toggle');
			$(this).toggleClass('is-active');
		})
		.on('click', '[data-article]', function (event) {
			if (!requireConsent()) {
				window.open($(this).attr('href'));
				return false;
			}

			var mode = event.altKey ? 'modal' : $(this).data('mode') || 'sidebar';

			beacon('article', $(this).data('article'), {
				type: mode,
			});
			return false;
		})
		.on('click', 'a.mailster-support', function (e) {
			e.stopImmediatePropagation();
			if (!requireConsent()) {
				location.href = $(this).attr('href');
				return false;
			}

			beacon('open');
			beacon('navigate', '/');
			return false;
		});

	mailster.events.push('documentReady', function () {
		beacondata = getBeaconData();
		// no beacon data
		if (!beacondata) {
			requireConsent(false);
			return;
		}

		// init if we have messages
		if (beacondata.messages) {
			requireConsent(false);
		}
	});

	function getBeaconData() {
		beacondata = mailster.session.get('beacon');

		// if exists and not older than one hour and version is the same
		if (
			beacondata &&
			new Date() / 1000 - beacondata.timestamp < 3600 &&
			beacondata.version === mailster.version
		) {
			return beacondata;
		}

		return false;
	}

	beacon('on', 'ready', function () {
		if (beacon('info').status.isOpened) {
			helpbtn.addClass('is-active');
		}
	});
	beacon('on', 'open', function () {
		helpbtn.addClass('is-active');
	});
	beacon('on', 'close', function () {
		helpbtn.removeClass('is-active');
	});

	function loadBeaconData() {
		if (loaded) {
			return;
		}

		helpbtn.addClass('is-loading');

		if (typeof window.Beacon == 'undefined') {
			var script = document.createElement('script');
			script.id = 'beacon';
			script.text = `!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});`;
			document.head.appendChild(script);
		}

		if (getBeaconData()) {
			initBeacon();
			return;
		}

		mailster.util.ajax(
			'get_beacon_data',
			function (response) {
				beacondata = response.data;
				beacondata.timestamp = Math.round(new Date() / 1000);
				mailster.session.set('beacon', beacondata);
				!loaded && initBeacon();
			},
			function (jqXHR, textStatus, errorThrown) {
				console.error(textStatus, errorThrown);
			}
		);
	}

	function initBeacon() {
		beacon = function (method, options, data) {
			data = data || {};
			switch (method) {
				case 'init':
					Beacon('reset');
					Beacon('close');
					Beacon('identify', {
						name: beacondata.name,
						email: beacondata.email,
						avatar: beacondata.avatar,
					});
					Beacon('config', {
						docsEnabled: true,
						color: '#f0f0f1',
						messagingEnabled: mailster.has_support,
						messaging: {
							chatEnabled: mailster.has_support,
						},
						display: {
							style: 'manual',
						},
					});
					return Beacon('init', beacondata.id);
				case 'suggest':
					return Beacon('suggest', options, data);
				case 'show-message':
					data.force = true;
					return Beacon('show-message', options, data);
				default:
					return Beacon(method, options, data);
			}
		};

		beacon('init');

		for (var i in queue) {
			beacon(queue[i].method, queue[i].options, queue[i].data);
		}

		$('[data-article]').each(function () {
			if (articles.length >= 9) return;
			var id = $(this).data('article');
			if (!id) return;
			if (articles.includes(id)) return;
			articles.push(id);
		});

		if (articles.length) {
			beacon('suggest', articles);
		}

		if (beacondata.messages) {
			for (var i in beacondata.messages) {
				beacon('show-message', beacondata.messages[i]);
			}
		}

		loaded = true;
		helpbtn.removeClass('is-loading');
	}

	function requireConsent(ask = true) {
		if (!mailster.user.get('beacon')) {
			if (!ask || !confirm(mailster.l10n.beacon.consent)) {
				return false;
			}
			mailster.user.set('beacon', Math.round(Date.now() / 1000));
		}
		loadBeaconData();
		return true;
	}

	function countdowns() {
		var countdowns = $('[data-offset]');

		$.each(countdowns, function (i, el) {
			var btn = $(this),
				offset = btn.data('offset');

			if (!offset || offset > 86400) return;

			var b = btn.find('span')[0],
				format = btn.data('format') || '%s',
				e = new Date().getTime(),
				f = setInterval(function () {
					var x = offset - Math.ceil((new Date().getTime() - e) / 1000),
						t = new Date(x * 1000),
						h = t.getHours() - 1,
						m = t.getMinutes(),
						s = t.getSeconds(),
						o =
							(h < 10 ? '0' + h : h) +
							':' +
							(m < 10 ? '0' + m : m) +
							':' +
							(s < 10 ? '0' + s : s);

					if (x <= 0) {
						clearInterval(f);
					}
					b.innerHTML = mailster.util.sprintf(format, o);
				}, 1000);
		});
	}

	countdowns();

	mailster.beacon = beacon;
	mailster.requireConsent = requireConsent;
	mailster.loadBeaconData = loadBeaconData;

	return mailster;
})(mailster || {}, jQuery, window, document);
