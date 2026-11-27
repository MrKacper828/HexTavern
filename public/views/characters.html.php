<?php
$stylesheets = ['/css/characters.css'];

$safeCampaignId = htmlspecialchars((string) ($campaign['id'] ?? $campaignId ?? 1), ENT_QUOTES, 'UTF-8');
$safeCampaignName = htmlspecialchars((string) ($campaign['name'] ?? 'Unknown Campaign'), ENT_QUOTES, 'UTF-8');
$characters = is_array($characters ?? null) ? $characters : [];

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
                    <span><?= $safeCampaignName ?></span>
                </p>
            </div>
        </header>

        <section class="characters-section" aria-labelledby="characters-list-heading">
            <h2 id="characters-list-heading" class="characters-section-title">
                Character Scrolls (<?= count($characters) ?>)
            </h2>

            <ul class="characters-list"<?= $characters === [] ? ' hidden' : '' ?>>
                <?php foreach ($characters as $character): ?>
                    <?php
                    $characterId = htmlspecialchars((string) ($character['id'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $characterName = htmlspecialchars((string) ($character['name'] ?? 'Unnamed Character'), ENT_QUOTES, 'UTF-8');
                    $rawAvatarPath = trim((string) ($character['avatar_path'] ?? ''));
                    $avatarPath = htmlspecialchars($rawAvatarPath, ENT_QUOTES, 'UTF-8');
                    ?>
                    <li class="character-card">
                        <div class="character-identity">
                            <?php if ($rawAvatarPath !== ''): ?>
                                <div class="character-avatar">
                                    <img src="<?= $avatarPath ?>" alt="Portrait of <?= $characterName ?>">
                                </div>
                            <?php else: ?>
                                <div class="character-avatar character-avatar-placeholder" aria-hidden="true">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            <?php endif; ?>

                            <div class="character-info">
                                <h3 class="character-name"><?= $characterName ?></h3>
                            </div>
                        </div>

                        <div class="character-actions">
                            <button
                                type="button"
                                class="open-scroll-btn"
                                data-character-id="<?= $characterId ?>"
                            >
                                <i class="fa-solid fa-scroll" aria-hidden="true"></i>
                                <span>Open Scroll</span>
                            </button>

                            <button
                                type="button"
                                class="delete-scroll-btn"
                                data-delete-trigger
                                data-character-id="<?= $characterId ?>"
                                data-character-name="<?= $characterName ?>"
                                aria-label="Delete character <?= $characterName ?>"
                                title="Delete character"
                            >
                                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div id="characters-empty" class="characters-empty"<?= $characters !== [] ? ' hidden' : '' ?>>
                <i class="fa-solid fa-scroll" aria-hidden="true"></i>
                <h3>No character scrolls yet</h3>
                <p>Character sheets created for this campaign will appear here.</p>
            </div>

            <span id="characters-feedback" class="sr-only" aria-live="polite"></span>
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
