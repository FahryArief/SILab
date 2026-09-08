import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || form.dataset.allowResubmit === 'true') {
        return;
    }

    const submitter = event.submitter;
    if (!(submitter instanceof HTMLButtonElement || submitter instanceof HTMLInputElement)) {
        return;
    }

    window.setTimeout(() => {
        submitter.disabled = true;
        submitter.classList.add('opacity-70', 'cursor-not-allowed');
        if (submitter.tagName === 'BUTTON') {
            submitter.dataset.originalText = submitter.textContent;
            submitter.textContent = 'Memproses...';
        }
    }, 0);
});
