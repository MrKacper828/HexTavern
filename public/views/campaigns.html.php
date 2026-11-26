<?php
$stylesheets = ['/css/campaigns.css'];

$activeId = $activeCampaignId ?? null;
$gameTableUrl = $activeId !== null
    ? '/campaigns/' . htmlspecialchars((string) $activeId, ENT_QUOTES, 'UTF-8')
    : '/campaigns';
$charactersUrl = $activeId !== null
    ? '/campaigns/' . htmlspecialchars((string) $activeId, ENT_QUOTES, 'UTF-8') . '/characters'
    : '/campaigns';

ob_start();
?>
<nav class="portal-nav" aria-label="Main navigation">
    <a href="/campaigns" class="portal-link active" aria-current="page">
        <span class="link-primary">Campaigns</span>
        <span class="link-secondary">List</span>
    </a>
    <a href="<?= $gameTableUrl ?>" class="portal-link">
        <span class="link-primary">Game</span>
        <span class="link-secondary">Table</span>
    </a>
    <a href="<?= $charactersUrl ?>" class="portal-link">
        <span class="link-primary">Characters</span>
        <span class="link-secondary">Scrolls</span>
    </a>
</nav>
<?php
$navbarContent = ob_get_clean();
?>

<main class="campaigns-chamber">
    <div class="campaigns-shell">
        <header class="campaigns-header">
            <h1 class="campaigns-title">Campaign Manager</h1>
            <button type="button" class="forge-campaign-btn" title="Creating new campaigns will be available soon">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                <span>Forge New Campaign</span>
            </button>
        </header>

        <section class="campaigns-section" aria-labelledby="campaigns-list-heading">
            <h2 id="campaigns-list-heading" class="campaigns-section-title">
                Campaigns (<?= count($campaigns) ?>)
            </h2>

            <?php if ($campaigns === []): ?>
                <div class="campaigns-empty">
                    <i class="fa-solid fa-scroll" aria-hidden="true"></i>
                    <h3>No campaigns forged yet</h3>
                    <p>Create your first campaign to open a table and invite players with a room code.</p>
                </div>
            <?php else: ?>
                <ul class="campaigns-list">
                    <?php foreach ($campaigns as $campaign): ?>
                        <?php
                        $campaignId = htmlspecialchars((string) $campaign['id'], ENT_QUOTES, 'UTF-8');
                        $campaignName = htmlspecialchars((string) $campaign['name'], ENT_QUOTES, 'UTF-8');
                        $roomCode = htmlspecialchars((string) $campaign['room_code'], ENT_QUOTES, 'UTF-8');
                        $lastPlayedLabel = htmlspecialchars((string) ($campaign['last_played_label'] ?? 'Not played yet'), ENT_QUOTES, 'UTF-8');
                        ?>
                        <li class="campaign-card">
                            <div class="campaign-info">
                                <h3 class="campaign-name">
                                    <a href="/campaigns/<?= $campaignId ?>"><?= $campaignName ?></a>
                                </h3>
                                <p class="campaign-meta">
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                    <span>Last Played: <?= $lastPlayedLabel ?></span>
                                </p>
                            </div>

                            <div class="campaign-actions">
                                <div class="room-code-pill">
                                    <span class="room-code-label">Code</span>
                                    <code class="room-code-value"><?= $roomCode ?></code>
                                    <button
                                        type="button"
                                        class="room-code-copy"
                                        data-copy-code="<?= $roomCode ?>"
                                        aria-label="Copy room code <?= $roomCode ?>"
                                        title="Copy room code"
                                    >
                                        <i class="fa-regular fa-copy" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <a href="/campaigns/<?= $campaignId ?>" class="enter-room-btn">
                                    Enter Room
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <span id="copy-feedback" class="sr-only" aria-live="polite"></span>
        </section>
    </div>
</main>

<script src="/js/campaigns.js" defer></script>
