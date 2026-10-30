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

/**
 * Open a form dialog and prepare its form for creating or editing a record.
 *
 * Markup: a trigger with `data-dialog-target="<dialog id>"` and optionally
 * `data-dialog-title`, `data-action`, `data-method` and `data-fields` (JSON
 * object mapping input names to values).
 */
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-dialog-target]');

    if (!trigger) {
        return;
    }

    const dialog = document.getElementById(trigger.dataset.dialogTarget);
    const form = dialog.querySelector('form');

    if (form) {
        resetValidationState(form);

        if (trigger.dataset.action) {
            form.action = trigger.dataset.action;
        }

        const methodInput = form.elements.namedItem('_method');
        if (methodInput) {
            methodInput.value = trigger.dataset.method ?? 'POST';
            methodInput.disabled = !trigger.dataset.method;
        }

        const fields = JSON.parse(trigger.dataset.fields ?? '{}');
        Object.entries(fields).forEach(([name, value]) => {
            const input = form.elements.namedItem(name);
            if (input) {
                input.value = value ?? '';
            }
        });
    }

    const title = dialog.querySelector('[data-dialog-title]');
    if (title && trigger.dataset.dialogTitle) {
        title.textContent = trigger.dataset.dialogTitle;
    }

    dialog.showModal();
    dialog.querySelector('input:not([type="hidden"]), textarea')?.focus();
});

document.addEventListener('click', (event) => {
    event.target.closest('[data-dialog-close]')?.closest('dialog').close();
});

/**
 * Ask for confirmation before submitting a form marked with `data-confirm`.
 */
document.addEventListener('submit', (event) => {
    const message = event.target.dataset.confirm;

    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

document.querySelectorAll('dialog[data-open-on-load]').forEach((dialog) => {
    dialog.showModal();
    dialog.querySelector('[aria-invalid="true"]')?.focus();
});

function resetValidationState(form) {
    form.querySelectorAll('[data-error]').forEach((element) => element.remove());
    form.querySelectorAll('[aria-invalid]').forEach((element) => element.removeAttribute('aria-invalid'));
}
