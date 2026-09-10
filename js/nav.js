(function () {
	'use strict';

	var toggle = document.getElementById('menu-toggle');
	var menuWrap = document.getElementById('primary-menu-wrap');
	if (!toggle || !menuWrap) return;

	toggle.addEventListener('click', function () {
		var isOpen = menuWrap.classList.toggle('is-open');
		toggle.classList.toggle('is-active', isOpen);
		toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
	});

	/* Add tap-to-expand toggles for items with a submenu (hover doesn't work on touch) */
	var parents = menuWrap.querySelectorAll('.menu-item-has-children');
	parents.forEach(function (li) {
		var link = li.querySelector('a');
		var sub = li.querySelector('.sub-menu');
		if (!link || !sub) return;

		var btn = document.createElement('button');
		btn.type = 'button';
		btn.className = 'submenu-toggle';
		btn.setAttribute('aria-expanded', 'false');
		btn.innerHTML = '<span aria-hidden="true">&#9662;</span><span class="screen-reader-text">Toggle submenu</span>';
		li.insertBefore(btn, sub);

		btn.addEventListener('click', function () {
			var isOpen = li.classList.toggle('submenu-open');
			btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});
	});
})();
