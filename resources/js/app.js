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

document.querySelectorAll('[data-language-toggle]').forEach((toggle) => {
	toggle.addEventListener('change', () => {
		const form = toggle.closest('[data-language-form]');
		form.querySelector('input[name="locale"]').value = toggle.checked ? 'en' : 'ar';
		form.requestSubmit();
	});
});

const setFeedback = (form, message, isError = false) => {
	const feedback = form.querySelector('.async-feedback');

	if (feedback) {
		feedback.textContent = message;
		feedback.classList.toggle('is-error', isError);
	}
};

const sendJsonRequest = async (url, options = {}) => {
	const response = await fetch(url, {
		...options,
		headers: {
			Accept: 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
			...options.headers,
		},
	});
	const payload = await response.json().catch(() => ({}));

	if (!response.ok) {
		const message = Object.values(payload.errors ?? {}).flat()[0]
			?? payload.message
			?? document.body.dataset.asyncError;

		throw new Error(message);
	}

	return payload;
};

const updateCommentCount = (postId, count) => {
	document.querySelectorAll(`[data-post-comment-count="${postId}"]`).forEach((counter) => {
		counter.textContent = count;
		const toggle = counter.closest('[data-comments-toggle]');

		if (toggle) {
			const label = toggle.dataset.commentsLabel
				.replace(':count', count)
				.replace(':title', toggle.dataset.postTitle);
			toggle.setAttribute('aria-label', label);
		}
	});
};

document.addEventListener('click', async (event) => {
	const toggle = event.target.closest('[data-comments-toggle]');

	if (!toggle) {
		return;
	}

	const panel = document.getElementById(toggle.getAttribute('aria-controls'));

	if (!panel) {
		return;
	}

	if (!panel.hidden) {
		panel.hidden = true;
		toggle.setAttribute('aria-expanded', 'false');
		return;
	}

	toggle.disabled = true;

	try {
		if (panel.dataset.loaded !== 'true') {
			const payload = await sendJsonRequest(toggle.dataset.commentsUrl);
			panel.innerHTML = payload.html;
			panel.dataset.loaded = 'true';
		}

		panel.hidden = false;
		toggle.setAttribute('aria-expanded', 'true');
	} catch (error) {
		panel.replaceChildren();
		const message = document.createElement('p');
		message.className = 'async-feedback is-error';
		message.textContent = error.message;
		panel.append(message);
		panel.hidden = false;
	} finally {
		toggle.disabled = false;
	}
});

document.addEventListener('submit', async (event) => {
	const form = event.target.closest('[data-async-form]');

	if (!form) {
		return;
	}

	event.preventDefault();
	const submitter = event.submitter;
	const formType = form.dataset.asyncForm;
	const body = new FormData(form);

	if (submitter?.name) {
		body.set(submitter.name, submitter.value);
	}

	const submitButton = submitter ?? form.querySelector('button[type="submit"]');
	if (submitButton) {
		submitButton.disabled = true;
	}
	form.setAttribute('aria-busy', 'true');
	setFeedback(form, '');

	try {
		const payload = await sendJsonRequest(form.action, {
			method: form.method.toUpperCase(),
			body,
		});

		if (formType === 'reaction') {
			const postId = form.action.match(/\/posts\/(\d+)\/reaction/)?.[1];
			const likeButton = form.querySelector('[name="reaction"][value="like"]');
			const dislikeButton = form.querySelector('[name="reaction"][value="dislike"]');

			[[likeButton, 'like', payload.likes_count], [dislikeButton, 'dislike', payload.dislikes_count]]
				.forEach(([button, reaction, count]) => {
					const selected = payload.viewer_reaction === reaction;

					button.classList.toggle('is-selected', selected);
					button.setAttribute('aria-pressed', String(selected));
					button.setAttribute('aria-label', button.dataset.labelTemplate
						.replace(':reaction', button.dataset.reactionLabel)
						.replace(':count', count));
					button.querySelector('.reaction-count').textContent = count;
				});

			form.dataset.postId = postId;
			return;
		}

		const thread = form.closest('[data-comments-thread]');
		const postId = thread.dataset.postId;

		if (formType === 'comment') {
			const parentId = body.get('parent_id');

			if (parentId) {
				const parent = thread.querySelector(`[data-comment-id="${parentId}"]`);
				let replies = parent.querySelector('.reply-list');

				if (!replies) {
					replies = document.createElement('div');
					replies.className = 'reply-list';
					parent.querySelector('.comment-content').append(replies);
				}

				replies.insertAdjacentHTML('beforeend', payload.html);
				form.closest('details')?.removeAttribute('open');
			} else {
				thread.querySelector('.comments-empty')?.remove();
				thread.querySelector('[data-comment-list]').insertAdjacentHTML('beforeend', payload.html);
			}

			form.reset();
			thread.querySelector('[data-comments-count]').textContent = payload.comments_count;
			updateCommentCount(postId, payload.comments_count);
			setFeedback(form, payload.message);
			return;
		}

		if (formType === 'delete-comment') {
			payload.deleted_comment_ids.forEach((commentId) => {
				thread.querySelector(`[data-comment-id="${commentId}"]`)?.remove();
			});

			thread.querySelector('[data-comments-count]').textContent = payload.comments_count;
			updateCommentCount(postId, payload.comments_count);

			if (payload.comments_count === 0) {
				const emptyMessage = document.createElement('p');
				emptyMessage.className = 'comments-empty';
				emptyMessage.textContent = document.body.dataset.emptyCommentsText;
				thread.querySelector('[data-comment-list]').replaceChildren(emptyMessage);
			}
		}
	} catch (error) {
		setFeedback(form, error.message, true);
	} finally {
		form.removeAttribute('aria-busy');
		if (submitButton?.isConnected) {
			submitButton.disabled = false;
		}
	}
});
