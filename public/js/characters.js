(function () {
    const modal = document.getElementById('delete-character-modal');
    const modalCharacterName = document.getElementById('delete-modal-character-name');
    const cancelButton = document.querySelector('[data-modal-cancel]');
    const confirmButton = document.querySelector('[data-modal-confirm]');

    if (!modal || !modalCharacterName || !cancelButton || !confirmButton) {
        return;
    }

    const listHeading = document.getElementById('characters-list-heading');
    const deleteTriggers = document.querySelectorAll('[data-delete-trigger]');

    let pendingCard = null;
    let lastTriggerButton = null;

    function updateCharacterCount() {
        if (!listHeading) {
            return;
        }

        const count = document.querySelectorAll('.characters-list .character-card').length;
        listHeading.textContent = `Character Scrolls (${count})`;
    }

    function openDeleteModal(triggerButton) {
        pendingCard = triggerButton.closest('.character-card');
        lastTriggerButton = triggerButton;

        const rawName = triggerButton.getAttribute('data-character-name');
        const characterName = rawName && rawName.trim() !== '' ? rawName.trim() : 'this character';

        modalCharacterName.textContent = characterName;
        modal.showModal();
    }

    function closeDeleteModal() {
        if (modal.open) {
            modal.close();
        }
    }

    function handleConfirmDelete() {
        if (pendingCard) {
            pendingCard.remove();
        }

        pendingCard = null;
        lastTriggerButton = null;
        updateCharacterCount();
        closeDeleteModal();
    }

    deleteTriggers.forEach((button) => {
        button.addEventListener('click', () => {
            openDeleteModal(button);
        });
    });

    cancelButton.addEventListener('click', closeDeleteModal);
    confirmButton.addEventListener('click', handleConfirmDelete);

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeDeleteModal();
        }
    });

    modal.addEventListener('close', () => {
        pendingCard = null;

        if (lastTriggerButton && lastTriggerButton.isConnected) {
            lastTriggerButton.focus();
        }

        lastTriggerButton = null;
    });
})();