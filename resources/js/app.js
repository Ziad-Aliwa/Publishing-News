import './bootstrap';

document.querySelectorAll('[data-menu-toggle]').forEach((toggle) => {
	const menu = document.querySelector(toggle.dataset.menuToggle);

	if (!menu) {
		return;
	}

	toggle.addEventListener('click', () => {
		const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

		toggle.setAttribute('aria-expanded', String(!isExpanded));
		menu.classList.toggle('show', !isExpanded);
	});
});
