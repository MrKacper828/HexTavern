(function () {
    const modal = document.getElementById('delete-character-modal');
    const modalCharacterName = document.getElementById('delete-modal-character-name');
    const cancelButton = document.querySelector('[data-modal-cancel]');
    const confirmButton = document.querySelector('[data-modal-confirm]');

    if (!modal || !modalCharacterName || !cancelButton || !confirmButton) {
        return;
    }

    const listHeading = document.getElementById('characters-list-heading');
    const charactersList = document.querySelector('.characters-list');
    const emptyState = document.getElementById('characters-empty');
    const feedback = document.getElementById('characters-feedback');
    const deleteTriggers = document.querySelectorAll('[data-delete-trigger]');

    let pendingCard = null;
    let pendingCharacterId = null;
    let pendingCharacterName = '';
    let lastTriggerButton = null;

    function updateCharacterCount() {
        const count = document.querySelectorAll('.characters-list .character-card').length;

        if (listHeading) {
            listHeading.textContent = `Character Scrolls (${count})`;
        }

        if (charactersList) {
            charactersList.hidden = count === 0;
        }

        if (emptyState) {
            emptyState.hidden = count > 0;
        }
    }

    function openDeleteModal(triggerButton) {
        pendingCard = triggerButton.closest('.character-card');
        lastTriggerButton = triggerButton;

        const rawId = triggerButton.getAttribute('data-character-id');
        pendingCharacterId = rawId && rawId.trim() !== '' ? rawId.trim() : null;

        const rawName = triggerButton.getAttribute('data-character-name');
        pendingCharacterName = rawName && rawName.trim() !== '' ? rawName.trim() : 'this character';

        modalCharacterName.textContent = pendingCharacterName;
        modal.showModal();
    }

    function closeDeleteModal() {
        if (modal.open) {
            modal.close();
        }
    }

    function clearPendingState() {
        pendingCard = null;
        pendingCharacterId = null;
        pendingCharacterName = '';
    }

    function handleConfirmDelete() {
        const removedName = pendingCharacterName;

        if (pendingCard) {
            pendingCard.remove();
        }

        clearPendingState();
        lastTriggerButton = null;
        updateCharacterCount();
        closeDeleteModal();

        if (feedback && removedName !== '') {
            feedback.textContent = `Removed character scroll: ${removedName}.`;
        }
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
        clearPendingState();

        if (lastTriggerButton && lastTriggerButton.isConnected) {
            lastTriggerButton.focus();
        }

        lastTriggerButton = null;
    });
})();