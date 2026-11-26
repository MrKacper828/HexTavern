<aside>
    <p>boczny navbar</p>
</aside>
<main>
    <nav>
        <p>
            górny navbar |
            nr kampanii: #<?= htmlspecialchars((string) $campaign['id'], ENT_QUOTES, 'UTF-8') ?> |
            <?= htmlspecialchars((string) $campaign['name'], ENT_QUOTES, 'UTF-8') ?> |
            kod pokoju: <code><?= htmlspecialchars((string) $campaign['room_code'], ENT_QUOTES, 'UTF-8') ?></code>
        </p>
        <p><a href="/campaigns">← Wróć do listy kampanii</a></p>
        <p><a href="/campaigns/<?= htmlspecialchars((string) $campaign['id'], ENT_QUOTES, 'UTF-8') ?>/characters">Zobacz karty postaci tej kampanii</a></p>
    </nav>
    <section>
        <h1><?= htmlspecialchars((string) $campaign['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars((string) $campaign['description'], ENT_QUOTES, 'UTF-8') ?></p>
        <dl>
            <div>
                <dt>Last Played</dt>
                <dd><?= htmlspecialchars((string) ($campaign['last_played_label'] ?? 'Not played yet'), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt>Mistrz Gry</dt>
                <dd><?= htmlspecialchars((string) ($campaign['dm_username'] ?? $campaign['dm_email']), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt>E-mail DM-a</dt>
                <dd><?= htmlspecialchars((string) $campaign['dm_email'], ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
        </dl>
    </section>
    <section>
        <p>dm i lista graczy</p>
    </section>
    <section>
        <p>kości menu</p>
    </section>
    <section>
        <p>logi</p>
    </section>
    <p><a href="/">Wróć na stronę główną</a></p>
</main>
