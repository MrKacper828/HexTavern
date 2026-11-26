INSERT INTO users (username, email, password_hash, role)
VALUES
    ('dm.eldric', 'eldric@example.com', '$2y$10$demo', 'dm'),
    ('dm.lyra', 'lyra@example.com', '$2y$10$demo', 'dm');

INSERT INTO campaigns (dm_id, name, description, room_code, last_played_at)
VALUES
    (
        (SELECT id FROM users WHERE email = 'eldric@example.com'),
        'Klątwa Mglistej Twierdzy',
        'Kampania dark fantasy skupiona na eksploracji ruin i intrygach w przygranicznym księstwie.',
        'HEX-101',
        '2026-11-24 20:00:00+01'
    ),
    (
        (SELECT id FROM users WHERE email = 'lyra@example.com'),
        'Kroniki Żelaznego Wybrzeża',
        'Morska wyprawa drużyny najemników w poszukiwaniu zaginionych artefaktów.',
        'HEX-202',
        '2026-11-20 19:30:00+01'
    ),
    (
        (SELECT id FROM users WHERE email = 'eldric@example.com'),
        'Cień nad Karczmą',
        'Krótka kampania wprowadzająca dla początkującej drużyny poszukiwaczy przygód.',
        'HEX-303',
        NULL
    );
