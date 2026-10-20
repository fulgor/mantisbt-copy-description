(function () {
	'use strict';

	var btn = document.getElementById('copydescription-btn');
	var wrapper = document.getElementById('copydescription-wrapper');
	if (!btn || !wrapper) {
		return;
	}

	// Place the button directly behind "history" button.
	// Fallback: header of the first widget box. If neither is found, the
	// button simply stays where MantisBT output it.
	var anchor = document.querySelector('a[href="#history"]');
	if (anchor) {
		anchor.insertAdjacentElement('afterend', btn);
		anchor.insertAdjacentText('afterend', ' ');
		wrapper.remove();
	} else {
		var header = document.querySelector('.widget-box .widget-header');
		if (header) {
			header.appendChild(wrapper);
		}
	}

	// Fallback for plain http:// installations, where navigator.clipboard
	// is not available.
	function fallbackCopy(text) {
		var area = document.createElement('textarea');
		area.value = text;
		area.setAttribute('readonly', '');
		area.style.position = 'fixed';
		area.style.top = '-1000px';
		document.body.appendChild(area);
		area.select();
		var ok = false;
		try {
			ok = document.execCommand('copy');
		} catch (e) {
			ok = false;
		}
		document.body.removeChild(area);
		return ok;
	}

	function copy(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text).then(
				function () { return true; },
				function () { return fallbackCopy(text); }
			);
		}
		return Promise.resolve(fallbackCopy(text));
	}

	function setLabel(label, iconClass) {
		btn.textContent = '';
		var icon = document.createElement('i');
		icon.className = 'fa ' + iconClass + ' ace-icon';
		btn.appendChild(icon);
		btn.appendChild(document.createTextNode(' ' + label));
	}

	var timer = null;
	btn.addEventListener('click', function () {
		copy(btn.getAttribute('data-text')).then(function (ok) {
			setLabel(
				ok ? btn.getAttribute('data-copied') : btn.getAttribute('data-failed'),
				ok ? 'fa-check' : 'fa-exclamation-triangle'
			);
			clearTimeout(timer);
			timer = setTimeout(function () {
				setLabel(btn.getAttribute('data-label'), 'fa-clipboard');
			}, 2000);
		});
	});
})();
