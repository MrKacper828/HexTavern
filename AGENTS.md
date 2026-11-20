# AGENTS.md

Ten plik jest przewodnikiem dla agentów AI pracujących w tym repozytorium. Przed wprowadzeniem zmian przeczytaj go razem z `README.md` i sprawdź aktualny stan kodu. Nie zakładaj, że opisana funkcja istnieje, dopóki nie potwierdzisz tego w plikach projektu.

## Cel projektu

HexTavern — aplikacja webowa w czystym PHP 8.3 do obsługi sesji gier TTRPG online (tworzenie lobby, rzuty kośćmi, wgląd w karty postaci oraz notatki Mistrza Gry). Korzysta z lekkiej, autorskiej architektury MVC bez zewnętrznego frameworka PHP. Aplikacja działa w kontenerach Docker (Nginx, PHP-FPM, PostgreSQL 16, Mailpit, pgAdmin).

Aktualnie aplikacja obsługuje:

- stronę główną pod `GET /` (`DefaultController::index`),
- logowanie Mistrza Gry pod `GET` / `POST /login` (`SecurityController::login`, `loginPost`),
- wylogowanie DM-a lub opuszczenie sesji gościa pod `POST /logout` (`SecurityController::logoutPost`),
- rejestrację konta DM-a pod `GET` / `POST /register` (`SecurityController::register`, `registerPost`),
- odzyskiwanie hasła pod `GET` / `POST /forgot-password` (`SecurityController::forgotPassword`, `forgotPasswordPost`),
- ustawienie nowego hasła pod `GET` / `POST /reset-password` (`SecurityController::resetPassword`, `resetPasswordPost`),
- listę kampanii DM-a pod `GET /campaigns` (`CampaignController::index`),
- dołączanie gracza do kampanii przez kod pod `GET` / `POST /campaigns/join` (`CampaignController::join`, `joinPost`),
- pokój rozgrywki danej kampanii pod `GET /campaigns/{id}` (`CampaignController::show`),
- podgląd kart postaci kampanii (dla DM-a) pod `GET /campaigns/{id}/characters` (`CharacterController::index`),
- odpowiedź 404 dla nieznanych tras (`DefaultController::notFound`).

## Stack technologiczny

- PHP 8.3 z `declare(strict_types=1)`
- Composer i autoloading PSR-4 (`App\` -> `src/`, `App\Controller\` -> `src/controllers/`)
- Nginx jako serwer HTTP
- PHP-FPM do wykonywania kodu PHP
- PostgreSQL 16 jako baza danych
- Mailpit (serwer testowy SMTP i interfejs webowy poczty)
- pgAdmin (panel zarządzania bazą danych)
- Docker oraz Docker Compose
- Czysty HTML, CSS oraz Vanilla JS bez zewnętrznych frameworków frontendowych (ikony Font Awesome z CDN)

## Struktura katalogów

```text
.
├── docker/
│   ├── db/                         # obraz i inicjalizacja PostgreSQL (initdb.d)
│   ├── nginx/                      # obraz i konfiguracja Nginx
│   └── php/                        # obraz PHP-FPM i rozszerzenia PHP
├── public/                         # jedyny publicznie dostępny katalog (document root)
│   ├── css/
│   │   ├── style.css               # globalne style, zmienne motywów (dark/light), navbar, dropdown
│   │   ├── home.css                # style strony głównej
│   │   └── login.css               # dedykowane style widoków auth/join i kart
│   ├── js/
│   │   └── theme.js                # przełącznik motywu jasny/ciemny (localStorage)
│   ├── index.php                   # Front Controller i rejestracja tras
│   └── views/                      # szablony widoków renderowane przez kontrolery
│       ├── layout.html.php         # bazowy układ ($content, $stylesheets, $navbarContent, navbar i menu użytkownika)
│       ├── home.html.php           # strona główna
│       ├── login.html.php          # widok logowania DM-a
│       ├── register.html.php       # widok rejestracji konta DM-a
│       ├── forgot-password.html.php# widok odzyskiwania hasła
│       ├── reset-password.html.php # widok ustawienia nowego hasła
│       ├── campaigns.html.php      # lista kampanii
│       ├── game_room.html.php      # stół / pokój rozgrywki
│       ├── characters.html.php     # karty postaci
│       ├── join.html.php           # dołączanie gracza do lobby przez kod
│       └── not-found.html.php      # strona błędu 404
├── src/
│   ├── controllers/                # kontrolery aplikacji (namespace App\Controller)
│   │   ├── AppController.php       # kontroler bazowy (render(), redirect(), sesja i guardy ról)
│   │   ├── DefaultController.php
│   │   ├── SecurityController.php
│   │   ├── CampaignController.php
│   │   └── CharacterController.php
│   └── Routing.php                 # autorski router aplikacji
├── composer.json                   # konfiguracja autoloadingu PSR-4
└── docker-compose.yaml             # definicje usług kontenerowych
```

Nie umieszczaj logiki aplikacji ani danych wrażliwych w `public/`. Ten katalog jest document rootem Nginx i służy tylko jako punkt wejścia oraz miejsce na widoki i zasoby publiczne.

## Architektura MVC

Projekt stosuje autorski, lekki przepływ MVC:

```text
przeglądarka
    -> Nginx
    -> public/index.php (Front Controller)
    -> Routing
    -> Controller (App\Controller\*)
    -> Model / Repository / Service (gdy funkcja wymaga danych)
    -> View (public/views/layout.html.php + szablon dedykowany)
    -> odpowiedź HTML
```

### Front Controller

`public/index.php` jest jedynym punktem wejścia aplikacji. Ładuje autoloader Composera, rejestruje trasy, przekazuje metodę HTTP i URI do routera (`$routing->dispatch(...)`) oraz uruchamia widok 404 (`DefaultController::notFound()`), gdy żadna trasa nie pasuje.

Nowe trasy dodawaj w tym pliku w postaci:

```php
$routing->get('/example', ExampleController::class, 'index');
$routing->post('/example', ExampleController::class, 'store');
```

### Router

`src/Routing.php` łączy metodę HTTP i ścieżkę URL z publiczną akcją kontrolera dziedziczącego po `AppController`. Parametry zapisane w klamrach (np. `{id}`) są automatycznie przekształcane w nazwane grupy regex dopasowujące cyfry (`(?P<id>\d+)`), rzutowane na `int` i przekazywane do akcji kontrolera jako argumenty pozycyjne/nazwane.

Przykład: `/campaigns/{id}` wywołuje `CampaignController::show(int $id)`.

Router obsługuje obecnie metody GET i POST. Nie dodawaj logiki biznesowej do routera.

### Controller

Kontrolery znajdują się w `src/controllers/`, używają przestrzeni nazw `App\Controller` i dziedziczą po `AppController`. Ich zadaniem jest:

- odebranie i walidacja danych wejściowych,
- wywołanie modelu, repozytorium lub serwisu,
- przygotowanie danych dla widoku,
- wybranie szablonu przez `$this->render(string $template, array $variables = [])` albo przekierowanie po sukcesie żądania `POST` metodą `$this->redirect(string $path): never` (kod `303 See Other`, wzorzec PRG).

Trasy formularzy są rozdzielane na akcję `GET` (wyświetlenie) oraz `POST` z sufiksem `*Post()` (walidacja i przekierowanie). Na obecnym etapie (przed podpięciem bazy PostgreSQL i repozytoriów) walidacja formularzy logowania, rejestracji, resetu hasła i dołączania ma charakter tymczasowy. Kontroler powinien być cienki — nie umieszczaj w nim rozbudowanych zapytań SQL, kodu HTML ani reguł CSS.

### Sesje, role i ochrona tras

`AppController` zarządza sesją PHP (`ensureSession()`) i w `$_SESSION['user']` rozróżnia dwie role aktywnej sesji:

- `'dm'` — zalogowany Mistrz Gry (`name`, `email`, `role`),
- `'guest'` — gracz dołączony kodem pokoju (`name`, `role`, `room_code`, `campaign_id`).

Metoda `AppController::render()` automatycznie przekazuje do wszystkich widoków zmienne globalne `$isLoggedIn`, `$userRole`, `$userName` oraz `$guestCampaignId` (wykorzystywane m.in. w pasku górnym i rozwijanym menu w `layout.html.php`).

Ochrona tras opiera się na metodach pomocniczych w `AppController`:

- `redirectIfAuthenticated()` — blokuje dostęp do tras auth (`/login`, `/register`, `/forgot-password`, `/reset-password` w `GET` i `POST`) oraz `/campaigns/join` dla użytkowników z aktywną sesją (`dm` → `/campaigns`, `guest` → `/campaigns/{campaign_id}`),
- `forbidGuest()` — blokuje rolę `'guest'` na panelach DM-a (`/campaigns` oraz `/campaigns/{id}/characters`),
- w `CampaignController::show()` gość może wejść wyłącznie do pokoju o ID zgodnym z `getGuestCampaignId()`.

### Model

Gdy funkcja wymaga trwałego przechowywania danych (PostgreSQL), wydziel dedykowane klasy modeli lub repozytoriów w `src/` zamiast dodawać bezpośrednie zapytania SQL do kontrolera.

Dla nowych klas zgodnych z mapowaniem `App\` w Composerze preferuj katalogi i nazwy klas o zgodnej wielkości liter, np.:

```text
src/Model/Campaign.php                -> App\Model\Campaign
src/Repository/CampaignRepository.php -> App\Repository\CampaignRepository
```

Po dodaniu lub przeniesieniu klas odśwież autoloader poleceniem `composer dump-autoload` (w kontenerze: `docker compose exec php composer dump-autoload`).

### View

Widoki znajdują się w `public/views/` i mają rozszerzenie `*.html.php`. Metoda `AppController::render()` buforuje wskazany szablon widoku do zmiennej `$content`, rozpakowuje przekazane zmienne przez `extract($variables, EXTR_SKIP)` i dołącza bazowy `public/views/layout.html.php`. W widokach można opcjonalnie przekazać tablicę `$stylesheets` (dodatkowe arkusze CSS) oraz `$navbarContent` (zakładki nawigacji w środku górnego paska). Błędy walidacji formularzy renderowane są wspólnymi klasami `.form-errors` / `.form-error-item`.

Widoki odpowiadają wyłącznie za prezentację. Dozwolone są w nich proste instrukcje warunkowe i pętle, ale nie logika biznesowa, operacje na bazie danych ani obsługa routingu.

Każda wartość pochodząca od użytkownika lub z bazy danych musi być escapowana w kontekście HTML:

```php
<?= htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8') ?>
```

## Konwencje implementacji

- Zachowuj `declare(strict_types=1)` we wszystkich nowych i modyfikowanych plikach PHP.
- Używaj ścisłych deklaracji typów parametrów, zwracanych wartości oraz właściwości klas.
- Nazwy klas zapisuj w `PascalCase`, a metod i zmiennych w `camelCase`.
- Nazwy techniczne w kodzie zapisuj po angielsku; warstwa tekstowa interfejsu może być po polsku.
- Jeden kontroler powinien obsługiwać jeden spójny obszar aplikacji.
- Kontroler przygotowuje dane, a widok odpowiada za prezentację (renderowanie przez `AppController::render()`).
- Dane przekazuj do widoku jawnie w tablicy drugiego argumentu `render()`.
- Nie twórz globalnego stanu i nie używaj `$_GET`, `$_POST` ani `$_SERVER` poza warstwą wejścia/kontrolera, jeżeli dane można przekazać jako argument.
- **Style CSS i responsywność**:
  - W stylach CSS stosuj responsywne jednostki względne, takie jak `rem` (do typografii, paddingów, marginesów, promieni zaokrągleń `border-radius`, obramowań, cieni) oraz `vh` / `vw` / `%` (do wymiarów widoku i kontenerów).
  - Unikaj sztywnych jednostek `px` (również w regułach `@media` – stosuj np. `30rem` zamiast `480px`).
  - Korzystaj ze zmiennych motywu zdefiniowanych w `:root` (`style.css`), takich jak `--color-brand`, `--color-surface`, `--color-background`, `--space-1`, `--radius`, `--font1` itp.
- Nie dodawaj frameworków ani zewnętrznych zależności bez wyraźnej zgody użytkownika.
- Wprowadzaj małe, przemyślane zmiany i nie modyfikuj plików niezwiązanych z zadaniem.
- Nie edytuj ręcznie plików w katalogu `vendor/`.

## Dodawanie nowej funkcji

Typowy porządek prac:

1. Dodaj trasę w `public/index.php`.
2. Dodaj albo rozszerz kontroler w `src/controllers/`.
3. Dodaj model, repozytorium lub serwis, jeśli funkcja zawiera logikę biznesową albo dostęp do danych.
4. Dodaj widok w `public/views/` i przekaż do niego tylko potrzebne dane (opcjonalnie wskazując arkusze w `$stylesheets`).
5. Dodaj lub zmień style w `public/css/` z zachowaniem jednostek względnych (`rem`, `%`, `vh`, `vw`).
6. Sprawdź poprawną trasę, dane wejściowe, zabezpieczenie HTML (`htmlspecialchars`) oraz zachowanie dla błędów i 404.

## Uruchamianie projektu

Pierwsze uruchomienie lub po zmianach w `Dockerfile` / `docker/nginx/nginx.conf`:

```bash
docker compose up --build -d
```

Codzienne uruchamianie i zatrzymywanie bez przebudowy obrazów:

```bash
docker compose up -d
docker compose stop
```

Aplikacja jest domyślnie dostępna pod `http://localhost:8080`. Port można zmienić lokalnie przez zmienną `APP_PORT` w ignorowanym przez Git pliku `.env`.

Usługi pomocnicze:
- Mailpit: `http://localhost:8025` (`MAILPIT_PORT`)
- pgAdmin: `http://localhost:5050` (`PGADMIN_PORT`)
- PostgreSQL: port `5433` (`POSTGRES_PORT`)

Przydatne polecenia:

```bash
docker compose ps
docker compose logs -f
docker compose exec php php -l ścieżka/do/pliku.php
docker compose exec php composer dump-autoload
docker compose down
```

Jeśli katalog `vendor/` nie istnieje lub uległ zmianie:

```bash
docker compose run --rm php composer install
```

## Weryfikacja zmian

W projekcie nie ma obecnie skonfigurowanego zestawu testów automatycznych. Po każdej zmianie:

- uruchom sprawdzenie składni: `docker compose exec -T php php -l ścieżka/do/pliku.php` dla każdego zmienionego pliku PHP,
- sprawdź `composer dump-autoload`, jeżeli zmieniałeś klasy, repozytoria lub przestrzenie nazw,
- po zmianie tras sprawdź poprawny adres URL oraz odpowiedź 404 dla nieistniejących tras,
- sprawdź przypadek poprawny, błędne dane wejściowe oraz logi w `docker compose logs`,
- upewnij się, że w plikach CSS nie wprowadzono sztywnych jednostek `px`,
- przed zakończeniem pokaż `git diff` i podsumuj zmienione pliki.

## Bezpieczeństwo

- Nie odczytuj ani nie commituj sekretów z `.env`.
- Nie wykonuj destrukcyjnych poleceń bez potwierdzenia.
- Nie zmieniaj wygenerowanych plików w `vendor/`.
- Wszystkie dane wyjściowe renderowane w szablonach HTML zabezpieczaj za pomocą `htmlspecialchars()`.

## Zasady dla agentów AI

- Najpierw przeczytaj pliki bezpośrednio związane z zadaniem i potwierdź aktualną implementację.
- Przed edycją sprawdź `git status`; zachowaj zmiany użytkownika i nie cofaj ich.
- Wprowadzaj najmniejszą spójną zmianę realizującą wymaganie.
- Utrzymuj ścisły rozdział odpowiedzialności MVC.
- Nie rozszerzaj zakresu zadania o refaktoryzacje, migracje lub nowe zależności bez uzasadnienia.
- Jeśli wymaganie jest niejednoznaczne, wybierz rozwiązanie zgodne z istniejącymi wzorcami w projekcie; pytaj tylko wtedy, gdy wybór istotnie zmieni zachowanie aplikacji.
- Po zakończeniu pokaż `git diff`, opisz zmienione pliki oraz wykonaną weryfikację.
