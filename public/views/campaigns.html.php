<main>
    <header>
        <p>Dane z PostgreSQL</p>
        <h1>Lista kampanii DM-a</h1>
        <p>Każda kampania jest połączona z Mistrzem Gry przez klucz obcy <code>campaigns.dm_id</code>.</p>
    </header>

    <?php if ($campaigns === []): ?>
        <p>Brak kampanii do wyświetlenia.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($campaigns as $campaign): ?>
                <li>
                    <article>
                        <p>
                            <span>#<?= htmlspecialchars((string) $campaign['id'], ENT_QUOTES, 'UTF-8') ?></span>
                            |
                            <span>Status: <?= htmlspecialchars((string) $campaign['status'], ENT_QUOTES, 'UTF-8') ?></span>
                            |
                            <span>Kod pokoju: <code><?= htmlspecialchars((string) $campaign['room_code'], ENT_QUOTES, 'UTF-8') ?></code></span>
                        </p>
                        <h2>
                            <a href="/campaigns/<?= htmlspecialchars((string) $campaign['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars((string) $campaign['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h2>
                        <p><?= htmlspecialchars((string) $campaign['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p>
                            Mistrz Gry:
                            <strong><?= htmlspecialchars((string) ($campaign['dm_username'] ?? $campaign['dm_email']), ENT_QUOTES, 'UTF-8') ?></strong>
                            (<?= htmlspecialchars((string) $campaign['dm_email'], ENT_QUOTES, 'UTF-8') ?>)
                        </p>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="/">Wróć na stronę główną</a></p>
</main>
