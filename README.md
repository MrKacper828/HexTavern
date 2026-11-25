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
| Baza danych | PostgreSQL 16, PDO |
| Konteneryzacja i narzędzia | Docker Compose (Mailpit, pgAdmin) |

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

## Inicjalizacja bazy danych

Compose montuje katalog `docker/db/init/` w trybie tylko do odczytu jako
`/docker-entrypoint-initdb.d` w kontenerze PostgreSQL. Oficjalny entrypoint
wykonuje pliki `*.sql` w kolejności alfabetycznej, ale wyłącznie wtedy, gdy
wolumen danych `pg-data` jest pusty (podczas pierwszej inicjalizacji).

Startowe skrypty inicjalizacyjne:

1. `001_create_users.sql` tworzy tabelę `users`.
2. `002_create_campaigns.sql` tworzy tabelę `campaigns`, klucz obcy `dm_id -> users(id)` oraz indeks `idx_campaigns_dm_id`.
3. `003_seed_demo.sql` dodaje przykładowe konta DM i demonstracyjne kampanie.

Dodanie kolejnego pliku do katalogu nie uruchomi go automatycznie na istniejącej
bazie. W środowisku lokalnym/demonstracyjnym można odtworzyć bazę od zera:

```bash
docker compose down -v
docker compose up --build -d
```

> Polecenie `docker compose down -v` usuwa wszystkie dane z wolumenu `pg-data`, dlatego służy wyłącznie do resetu środowiska lokalnego, a nie do migracji produkcyjnych.

PostgreSQL zamienia niecytowane identyfikatory na małe litery, dlatego projekt używa
standardowych nazw pisanych małymi literami (`users`, `campaigns`).

## Połączenie z bazą

`src/Database.php` (`App\Database`) jest Singletonem: prywatny konstruktor blokuje
`new Database()`, a `Database::getInstance()->getConnection()` udostępnia jedno
współdzielone połączenie PDO na żądanie PHP. Konfiguracja połączenia odczytywana
jest ze zmiennych środowiskowych kontenera (`DB_HOST`, `DB_PORT`, `DB_NAME`,
`DB_USER`, `DB_PASSWORD`).

Bazowa klasa `src/Repository/Repository.php` (`App\Repository\Repository`)
przechowuje `protected PDO $connection` i pozwala opcjonalnie przekazać obiekt
`PDO` w konstruktorze, co ułatwia izolowanie testów jednostkowych.

Aby podejrzeć bazę w pgAdminie (`http://localhost:5050`), dodaj nowy serwer
z parametrami:
- **Host name/address:** `db`
- **Port:** `5432`
- **Maintenance database / Username / Password:** wartości `POSTGRES_DB`, `POSTGRES_USER` i `POSTGRES_PASSWORD` z pliku `.env`.

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
