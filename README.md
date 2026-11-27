# HexTavern

Strona oferuje darmowe tworzenie lobby do sesji online w gry TTRPG (Tabletop Role-Playing Game). Pozwala na posiadanie lobby gdzie widoczne są rzuty kości wszystkich graczy i dm-a (Dungeon Master), gracze mogą również zobaczyć podstawowe statystyki swoich kompanów, a dm ma możliwość pełnego wglądu w karty postaci bądź swoje notatki.

## Funkcje

| Obszar | Co robi |
|---|---|
| Strona startowa | Wybór ścieżki gracza lub Dungeon Mastera (DM). |
| Dołączanie do lobby | Gracze dołączają kodem pokoju i nickiem bez zakładania konta. |
| Konto DM-a | Logowanie, rejestracja i odzyskiwanie hasła Mistrza Gry. |
| Sesja i interfejs | Rozróżnienie ról (DM / Gość) w menu użytkownika oraz przełącznik motywu (ciemny / jasny). |
| Lista kampanii DM-a | Pobieranie kampanii z PostgreSQL, data ostatniej rozgrywki i kopiowanie kodu pokoju. |
| Stół do rozgrywki | Widok sesji, rzuty kośćmi i podgląd kart postaci. |
| Karty postaci (DM) | Lista kart przypisanych do wybranej kampanii dla DM-a. |

## Tech stack

| Warstwa | Technologie |
|---|---|
| Backend | PHP 8.3 (własne MVC, Composer PSR-4) |
| Frontend | HTML, CSS, Vanilla JS |
| Serwer HTTP | Nginx |
| Baza danych | PostgreSQL 16, PDO |
| Konteneryzacja i narzędzia | Docker Compose (Mailpit, pgAdmin) |

## Architektura

Aplikacja korzysta z lekkiego, autorskiego modelu MVC bez zewnętrznego frameworka PHP — logika żądań jest rozdzielona między router, cienkie kontrolery, warstwę repozytoriów PDO oraz szablony widoków w `public/views/`:

```text
Przeglądarka -> Nginx -> public/index.php -> Routing -> Controller -> Repository / PostgreSQL -> View
```

## Szybki start

1. Sklonuj repozytorium:

```bash
git clone https://github.com/MrKacper828/HexTavern.git
cd HexTavern
```

2. Przed uruchomieniem utwórz lokalny plik `.env` na podstawie szablonu `.env.example` i uzupełnij własne dane dostępowe (plik `.env` jest ignorowany przez Git i nie należy go nigdzie udostępniać):

```bash
cp .env.example .env
```

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

## Baza danych

- **Inicjalizacja:** skrypty startowe w `docker/db/init/` (`001_create_users.sql`, `002_create_campaigns.sql`, `003_seed_demo.sql`) wykonują się automatycznie przy pierwszym starcie, gdy wolumen `pg-data` jest pusty. Lokalny reset bazy od zera (uwaga: usuwa dane wolumenu): `docker compose down -v && docker compose up --build -d`.
- **Połączenie w PHP:** singleton `src/Database.php` (`App\Database`) udostępnia współdzielone połączenie PDO konfigurowane przez zmienne środowiskowe z `.env`, a bazowa klasa `src/Repository/Repository.php` przyjmuje opcjonalne `PDO` w konstruktorze.
- **Podgląd w pgAdmin:** `http://localhost:5050` — serwer z hostem `db`, portem `5432` oraz danymi `POSTGRES_DB` / `POSTGRES_USER` / `POSTGRES_PASSWORD` z `.env`.

## Struktura projektu

- `public/` — punkt wejścia (`index.php`), widoki (`views/`), style (`css/`) oraz skrypty (`js/`).
- `src/` — router (`Routing.php`), połączenie PDO (`Database.php`), warstwa bazowa repozytoriów (`Repository/`) oraz kontrolery (`controllers/`).
- `docker/` — konfiguracja obrazów Nginx, PHP-FPM i PostgreSQL oraz startowe skrypty SQL (`db/init/`).
- `docker-compose.yaml`, `.env.example`, `composer.json` — definicje usług kontenerowych, szablon zmiennych środowiskowych oraz autoloading PSR-4.

## Zasady developmentu

- Branch dla każdej funkcji.
- Małe, logiczne commity.
- Sekrety wyłącznie w .env.

## Autorzy

- Kacper Korpacki
