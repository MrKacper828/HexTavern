<?php
$stylesheets = ['/css/characters.css'];

$safeCampaignId = htmlspecialchars((string) ($campaignId ?? 1), ENT_QUOTES, 'UTF-8');

ob_start();
?>
<nav class="portal-nav" aria-label="Main navigation">
    <a href="/campaigns" class="portal-link">
        <span class="link-primary">Campaigns</span>
        <span class="link-secondary">List</span>
    </a>
    <a href="/campaigns/<?= $safeCampaignId ?>" class="portal-link">
        <span class="link-primary">Game</span>
        <span class="link-secondary">Table</span>
    </a>
    <a href="/campaigns/<?= $safeCampaignId ?>/characters" class="portal-link active" aria-current="page">
        <span class="link-primary">Characters</span>
        <span class="link-secondary">Scrolls</span>
    </a>
</nav>
<?php
$navbarContent = ob_get_clean();
?>

<main class="characters-chambers">
    <div class="characters-shell">
        <header class="characters-header">
            <div class="characters-header-text">
                <h1 class="characters-title">Character Scrolls</h1>
                <p class="characters-campaign-name">
                    <i class="fa-solid fa-dungeon" aria-hidden="true"></i>
                    <span><?= htmlspecialchars((string) ($campaignName ?? 'Curse of the Sunken Citadel'), ENT_QUOTES, 'UTF-8') ?></span>
                </p>
            </div>
        </header>

        <section class="characters-section" aria-labelledby="characters-list-heading">
            <h2 id="characters-list-heading" class="characters-section-title">
                Character Scrolls (3)
            </h2>

            <ul class="characters-list">
                <li class="character-card">
                    <div class="character-identity">
                        <div class="character-avatar character-avatar-placeholder" aria-hidden="true">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="character-info">
                            <h3 class="character-name">Brokk Ironbreaker</h3>
                        </div>
                    </div>

                    <div class="character-actions">
                        <button type="button" class="open-scroll-btn">
                            <i class="fa-solid fa-scroll" aria-hidden="true"></i>
                            <span>Open Scroll</span>
                        </button>

                        <button
                            type="button"
                            class="delete-scroll-btn"
                            data-delete-trigger
                            data-character-name="Brokk Ironbreaker"
                            aria-label="Delete character Brokk Ironbreaker"
                            title="Delete character"
                        >
                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                </li>

                <li class="character-card">
                    <div class="character-identity">
                        <div class="character-avatar character-avatar-placeholder" aria-hidden="true">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="character-info">
                            <h3 class="character-name">2nd placeholder</h3>
                        </div>
                    </div>

                    <div class="character-actions">
                        <button type="button" class="open-scroll-btn">
                            <i class="fa-solid fa-scroll" aria-hidden="true"></i>
                            <span>Open Scroll</span>
                        </button>

                        <button
                            type="button"
                            class="delete-scroll-btn"
                            data-delete-trigger
                            data-character-name="2nd placeholder"
                            aria-label="Delete character 2nd placeholder"
                            title="Delete character"
                        >
                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                </li>

                <li class="character-card">
                    <div class="character-identity">
                        <div class="character-avatar character-avatar-placeholder" aria-hidden="true">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="character-info">
                            <h3 class="character-name">3rd placeholder</h3>
                        </div>
                    </div>

                    <div class="character-actions">
                        <button type="button" class="open-scroll-btn">
                            <i class="fa-solid fa-scroll" aria-hidden="true"></i>
                            <span>Open Scroll</span>
                        </button>

                        <button
                            type="button"
                            class="delete-scroll-btn"
                            data-delete-trigger
                            data-character-name="3rd placeholder"
                            aria-label="Delete character 3rd placeholder"
                            title="Delete character"
                        >
                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                </li>
            </ul>
        </section>
    </div>
</main>

<dialog
    id="delete-character-modal"
    class="delete-modal"
    aria-labelledby="delete-modal-title"
    aria-describedby="delete-modal-desc"
>
    <div class="delete-modal-content">
        <div class="delete-modal-icon" aria-hidden="true">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h2 id="delete-modal-title" class="delete-modal-title">Do you want to delete this Character Scroll?</h2>
        <p id="delete-modal-desc" class="delete-modal-desc">
            Are you sure you want to permanently remove <strong id="delete-modal-character-name">this character</strong>? This action cannot be undone.
        </p>
        <div class="delete-modal-actions">
            <button type="button" class="modal-cancel-btn" data-modal-cancel>
                <span>Cancel</span>
            </button>

            <button type="button" class="modal-confirm-btn" data-modal-confirm>
                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                <span>Delete Scroll</span>
            </button>
        </div>
    </div>
</dialog>

<script src="/js/characters.js" defer></script>
