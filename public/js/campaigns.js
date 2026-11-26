(function () {
    const copyFeedback = document.getElementById('copy-feedback');
    const copyButtons = document.querySelectorAll('[data-copy-code]');
    const activeTimers = new WeakMap();

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }

        const helper = document.createElement('textarea');
        helper.value = text;
        helper.setAttribute('readonly', '');
        helper.style.position = 'fixed';
        helper.style.opacity = '0';
        document.body.appendChild(helper);
        helper.select();
        document.execCommand('copy');
        document.body.removeChild(helper);

        return Promise.resolve();
    }

    function showCopiedState(button, code) {
        const icon = button.querySelector('i');
        const previousTimer = activeTimers.get(button);

        if (previousTimer !== undefined) {
            window.clearTimeout(previousTimer);
        }

        button.classList.add('is-copied');
        button.setAttribute('aria-label', 'Copied room code ' + code);
        if (icon) {
            icon.className = 'fa-solid fa-check';
        }
        if (copyFeedback) {
            copyFeedback.textContent = 'Room code ' + code + ' copied to clipboard.';
        }

        const timerId = window.setTimeout(() => {
            button.classList.remove('is-copied');
            button.setAttribute('aria-label', 'Copy room code ' + code);
            if (icon) {
                icon.className = 'fa-regular fa-copy';
            }
        }, 1800);

        activeTimers.set(button, timerId);
    }

    copyButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const code = button.getAttribute('data-copy-code');
            if (!code) {
                return;
            }

            copyText(code).then(() => {
                showCopiedState(button, code);
            }).catch(() => {
                if (copyFeedback) {
                    copyFeedback.textContent = 'Could not copy room code.';
                }
            });
        });
    });
})();
