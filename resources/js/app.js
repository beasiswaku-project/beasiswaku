/**
 * Toggle visibility of a password input.
 *
 * Markup: a button with `data-password-toggle="<input id>"` containing
 * two children marked `data-icon="show"` and `data-icon="hide"`.
 */
document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-password-toggle]');

    if (!toggle) {
        return;
    }

    const input = document.getElementById(toggle.dataset.passwordToggle);
    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';
    toggle.setAttribute('aria-pressed', String(isHidden));
    toggle.querySelector('[data-icon="show"]').classList.toggle('hidden', isHidden);
    toggle.querySelector('[data-icon="hide"]').classList.toggle('hidden', !isHidden);
});
