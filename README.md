# HexTavern

Strona oferuje darmowe tworzenie lobby do sesji online w gry TTRPG (Tabletop Role-Playing Game). Pozwala na posiadanie lobby gdzie widoczne są rzuty kości wszystkich graczy i dm-a (Dungeon Master), gracze mogą również zobaczyć podstawowe statystyki swoich kompanów, a dm ma możliwość pełnego wglądu w karty postaci bądź swoje notatki. 

## Funkcje

| Obszar | Co robi |
|---|---|
| Strona startowa | Wybór ścieżki gracza lub Dungeon Mastera (DM). |
| Dołączanie do lobby | Gracze dołączają kodem pokoju i nickiem bez zakładania konta. |
| Konto DM-a | Logowanie, rejestracja i odzyskiwanie hasła Mistrza Gry. |
| Sesja i interfejs | Rozróżnienie ról (DM / Gość) w menu użytkownika oraz przełącznik motywu (ciemny / jasny). |
| Lista kampanii DM-a | Zarządzanie prowadzonymi kampaniami. |
| Stół do rozgrywki | Widok sesji, rzuty kośćmi i podgląd kart postaci. |
| Karty postaci (DM) | Pełny wgląd DM-a w karty postaci graczy oraz karty customowe. |

## Tech stack

| Warstwa | Technologie |
|---|---|
| Backend | PHP 8.3 (własne MVC, Composer PSR-4) |
| Frontend | HTML, CSS, Vanilla JS |
| Serwer HTTP | Nginx |
| Baza danych | PostgreSQL 16 |
| Konteneryzacja i narzędzia | Docker Compose (Mailpit, pgAdmin) |

## Szybki start

1. Sklonuj repozytorium:

```bash
git clone https://github.com/MrKacper828/HexTavern.git
cd HexTavern
```

2. Przed uruchomieniem utwórz lokalny plik `.env` ze zmiennymi środowiskowymi wymaganymi przez `docker-compose.yaml` (plik `.env` jest ignorowany przez Git i nie należy go nigdzie udostępniać).

3. Pierwsze uruchomienie (budowanie kontenerów):

```bash
docker compose up --build -d
docker compose exec php composer dump-autoload
```

Codzienne uruchamianie i zatrzymywanie bez ponownego budowania obrazów:

```bash
docker compose up -d
docker compose stop
```

Adresy lokalne (domyślne porty):
- Aplikacja: http://localhost:8080
- Mailpit: http://localhost:8025
- pgAdmin: http://localhost:5050

## Struktura projektu

- `public/` — punkt wejścia (`index.php`), widoki (`views/`), style (`css/`) oraz skrypty (`js/`).
- `src/` — router (`Routing.php`) i kontrolery aplikacji (`controllers/`).
- `docker/` — konfiguracja obrazów Nginx, PHP-FPM i PostgreSQL.
- `docker-compose.yaml`, `composer.json` — definicje usług kontenerowych oraz autoloading PSR-4.

## Zasady developmentu

- Branch dla każdej funkcji.
- Małe, logiczne commity.
- Sekrety wyłącznie w .env.

## Autorzy

- Kacper Korpacki
