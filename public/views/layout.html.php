<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($pageTitle ?? 'Aplikacja', ENT_QUOTES, 'UTF-8') ?>
    </title>

    <script>
    (function () {
        const storageKey = 'theme';
        let savedTheme = null;

        try {
            savedTheme = localStorage.getItem(storageKey);
        } catch (error) {
            savedTheme = null;
        }

        const hasSavedTheme = savedTheme === 'light' || savedTheme === 'dark';
        const systemPrefersDark = window.matchMedia(
            '(prefers-color-scheme: dark)'
        ).matches;

        document.documentElement.dataset.theme = hasSavedTheme
            ? savedTheme
            : (systemPrefersDark ? 'dark' : 'light');
    })();
    </script>

    <link rel="stylesheet" href="/css/style.css">
    <?php foreach ($stylesheets ?? [] as $stylesheet): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($stylesheet, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="/js/theme.js" defer></script>
</head>
<body>
    <header class="tavern-topbar">
        <a href="/" class="brand-group" aria-label="HexTavern - strona główna">
            <div class="brand-emblem" aria-hidden="true">
                <i class="fa-solid fa-dice"></i>
            </div>
            <div class="brand-info">
                <span class="brand-title">HexTavern</span>
                <span class="brand-tagline">ONLINE TAVERN TABLETOP</span>
            </div>
        </a>

        <?= $navbarContent ?? '' ?>

        <div class="utility-group">
            <?php if ($isLoggedIn ?? false): ?>
                <?php $isGuest = ($userRole ?? null) === 'guest'; ?>
                <div class="user-menu" id="user-menu">
                    <button type="button"
                            id="user-menu-toggle"
                            class="user-badge <?= $isGuest ? 'guest' : 'logged-in' ?>"
                            aria-haspopup="menu"
                            aria-expanded="false"
                            aria-controls="user-dropdown"
                            title="Menu użytkownika"
                            aria-label="Otwórz menu użytkownika">
                        <span class="user-avatar-wrap">
                            <i class="fa-solid fa-user user-avatar-icon" aria-hidden="true"></i>
                            <span class="user-status-dot" aria-hidden="true"></span>
                        </span>
                        <span class="user-name"><?= htmlspecialchars((string) ($userName ?? 'Adventurer'), ENT_QUOTES, 'UTF-8') ?><?= $isGuest ? ' (Guest)' : '' ?></span>
                        <i class="fa-solid fa-chevron-down user-chevron" aria-hidden="true"></i>
                    </button>

                    <div id="user-dropdown" class="user-dropdown" role="menu" hidden>
                        <?php if ($isGuest): ?>
                            <a href="/campaigns/<?= htmlspecialchars((string) ($guestCampaignId ?? 1), ENT_QUOTES, 'UTF-8') ?>" class="user-dropdown-item" role="menuitem">
                                <i class="fa-solid fa-dice-d20" aria-hidden="true"></i>
                                <span>Return to Table</span>
                            </a>
                            <div class="user-dropdown-divider" role="separator"></div>
                            <form method="post" action="/logout" class="user-dropdown-form">
                                <button type="submit" class="user-dropdown-item user-dropdown-logout" role="menuitem">
                                    <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                                    <span>Leave Session</span>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="/campaigns" class="user-dropdown-item" role="menuitem">
                                <i class="fa-solid fa-book-open" aria-hidden="true"></i>
                                <span>My Campaigns</span>
                            </a>
                            <div class="user-dropdown-divider" role="separator"></div>
                            <form method="post" action="/logout" class="user-dropdown-form">
                                <button type="submit" class="user-dropdown-item user-dropdown-logout" role="menuitem">
                                    <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login"
                   class="user-badge logged-out"
                   title="Zaloguj się"
                   aria-label="Zaloguj się">
                    <span class="user-avatar-wrap">
                        <i class="fa-solid fa-user user-avatar-icon" aria-hidden="true"></i>
                        <span class="user-status-dot" aria-hidden="true"></span>
                    </span>
                    <span class="user-name"><?= htmlspecialchars((string) ($userName ?? 'User not logged in'), ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            <?php endif; ?>

            <button type="button" id="sound-toggle" class="util-btn" title="Dźwięk atmosfery" aria-label="Ambience sound">
                <i class="fa-solid fa-volume-high"></i>
            </button>
            <button type="button" id="theme-toggle" class="util-btn" title="Zmień motyw" aria-label="Toggle theme">
                <i class="fa-regular fa-moon"></i>
            </button>
            <button type="button" id="lang-switch" class="lang-pill" aria-label="Wybierz język (PL / EN)">
                <span class="lang-option active">PL</span>
                <span class="lang-divider" aria-hidden="true">/</span>
                <span class="lang-option">EN</span>
            </button>
        </div>
    </header>

    <?= $content ?>

    <footer class="app-footer">
        <div class="footer-col-left">
            <span>HexTavern · Tabletop Simulator</span>
        </div>
        <div class="footer-col-center">
            <a href="#">Tavern Rules</a>
            <a href="#">Tavern Policy</a>
            <a href="#">Patrons</a>
        </div>
        <div class="footer-col-right">
            <span>© 2026 HexTavern</span>
        </div>
    </footer>

    <script>
        const langSwitchBtn = document.getElementById('lang-switch');
        if (langSwitchBtn) {
            langSwitchBtn.addEventListener('click', () => {
                const options = langSwitchBtn.querySelectorAll('.lang-option');
                options.forEach(opt => opt.classList.toggle('active'));
            });
        }

        const soundToggleBtn = document.getElementById('sound-toggle');
        if (soundToggleBtn) {
            soundToggleBtn.addEventListener('click', () => {
                const icon = soundToggleBtn.querySelector('i');
                if (icon) {
                    const isMuted = icon.classList.contains('fa-volume-xmark');
                    icon.className = isMuted ? 'fa-solid fa-volume-high' : 'fa-solid fa-volume-xmark';
                }
            });
        }

        const userMenu = document.getElementById('user-menu');
        const userMenuToggle = document.getElementById('user-menu-toggle');
        const userDropdown = document.getElementById('user-dropdown');

        if (userMenu && userMenuToggle && userDropdown) {
            const setMenuOpen = (isOpen) => {
                userDropdown.hidden = !isOpen;
                userMenuToggle.setAttribute('aria-expanded', String(isOpen));
                userMenu.classList.toggle('open', isOpen);
            };

            userMenuToggle.addEventListener('click', (event) => {
                event.stopPropagation();
                setMenuOpen(userDropdown.hidden);
            });

            document.addEventListener('click', (event) => {
                if (!userMenu.contains(event.target)) {
                    setMenuOpen(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !userDropdown.hidden) {
                    setMenuOpen(false);
                    userMenuToggle.focus();
                }
            });
        }
    </script>
</body>
</html>
