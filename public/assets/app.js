// PHP provides the data. JavaScript only improves feedback in the browser.
const timestamp = document.querySelector('[data-local-time]');
if (timestamp) {
    timestamp.textContent = new Intl.DateTimeFormat('ar', {
        dateStyle: 'medium', timeStyle: 'medium',
    }).format(new Date(timestamp.dateTime));
}

document.querySelector('#refresh-form').addEventListener('submit', (event) => {
    const button = event.currentTarget.querySelector('button');
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.querySelector('[data-button-label]').textContent = 'جارٍ طلب البيانات…';
    document.querySelector('#loading-status').textContent = 'بانتظار رد SNMP Agent…';
});
