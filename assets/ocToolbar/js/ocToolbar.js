function ocallit_updateAriaChecked(event) {
    try {
        const checkbox = event.target;
        if (checkbox.type === 'checkbox') {
            checkbox.setAttribute('aria-checked', checkbox.checked ? 'true' : 'false');
        }
    } catch (e) {
        console.warn('ocallit_updateAriaChecked error:', e);
    }
}

document.querySelectorAll('.ocToolbar input[type="checkbox"]').forEach(cb => {
    cb.setAttribute('aria-checked', cb.checked ? 'true' : 'false');
    cb.emoveEventListener('change', ocallit_updateAriaChecked);
    cb.addEventListener('change', ocallit_updateAriaChecked);
});
