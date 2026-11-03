# CI/CD Lab

![PHP CI](https://github.com/Al-Hav-ic/ci-cd-lab/actions/workflows/php-ci.yml/badge.svg)

Навчальний PHP-проєкт, який розвивається протягом лабораторних робіт з дисципліни «Технології розробки програмного забезпечення». Демонструє повний цикл розробки невеликого застосунку: керування версіями, автоматичні перевірки якості коду та безперервну інтеграцію.

## Функціонал

- сервісний шар із бізнес-логікою на PHP (`App\Services\Calculator`);
- модульні тести на PHPUnit;
- перевірка стилю коду за допомогою Laravel Pint (PSR-12, пресет `laravel`);
- CI-пайплайн на GitHub Actions: встановлення залежностей → лінт → тести → демонстрація Secrets;
- захист головної гілки `main`: PR, обов'язковий статус-чек `build`, мінімум одне схвалення.

## Теорія до лабораторних (HTML)

Матеріали для перегляду в браузері — каталог [`docs/theory/`](docs/theory/README.md):

| Лаб. | Файл |
|------|------|
| 1 | [theory-lab1.html](docs/theory/theory-lab1.html) |
| 2 | [theory-lab2.html](docs/theory/theory-lab2.html) |
| 3 | [theory-lab3.html](docs/theory/theory-lab3.html) |
| 4 | [theory-lab4.html](docs/theory/theory-lab4.html) |
| 5 | [theory-lab5.html](docs/theory/theory-lab5.html) |

Демо скриптів лаб. 3–4 при `php artisan serve`: `/lab3-demo.php`, `/lab3-entity-array.php`, `/lab4-demo.php`.

Traceability Issues ↔ PR: [`docs/labs-traceability.md`](docs/labs-traceability.md). Звірка теми та варіантів лаб. 2–5: [`docs/RECONCILIATION.md`](docs/RECONCILIATION.md).

## Процес роботи (реліз і захист `main`)

Прямий push у гілку **`main` заборонений** (branch protection). Будь-яка зміна проходить так:

1. Оновити локальний `main`: `git pull origin main`.
2. Створити гілку від `main`: `git checkout -b feature/short-description`.
3. Зробити коміт(и) з **Conventional Commits**, наприклад:
   - `feat: add product filter to catalog`
   - `fix: guard division by zero in Calculator`
   - `docs: update architecture for lab 2`
4. Запушити гілку: `git push -u origin feature/short-description`.
5. Відкрити **Pull Request** у `main`, заповнити шаблон PR, додати посилання на Issue (`Fixes #14`).
6. Дочекатися зеленого статус-чеку **`build`** (GitHub Actions: Pint + тести).
7. Отримати **мінімум 1 approval** від reviewer (колаборатор).
8. Переконатися, що гілка **up to date** з `main` і всі коментарі resolved.
9. Злити PR (merge). Issue з ключовим словом `Fixes` / `Resolves` закриється автоматично; картка на Projects перейде в **Done**.

### Branch protection на `main`

| Правило | Значення |
|---|---|
| Pull request before merge | так |
| Required approvals | 1 |
| Required status check | **`build`** |
| Require branches up to date | так |
| Require conversation resolution | так |
| Enforce for administrators | так |
| Force push / delete branch | заборонено |

Workflow **PHP CI Application** (`.github/workflows/php-ci.yml`) на Laravel-етапі також виконує `php artisan test`.

## Структура проєкту (скорочено)

```
.
├── .github/workflows/php-ci.yml
├── app/                    # домен (Models, Services, Http, …)
├── docs/                   # architecture, UML, theory HTML, traceability
├── public/                 # lab3-demo.php, lab4-demo.php, Laravel entry
├── routes/web.php
├── tests/Unit|Feature/
├── composer.json
└── README.md
```

## CI-пайплайн

Workflow `.github/workflows/php-ci.yml` запускається на `push` і `pull_request` у гілку `main` та складається з одного job'а `build`:

| Крок | Що робить |
|---|---|
| `actions/checkout@v4` | клонує репозиторій у раннер |
| `shivammathur/setup-php@v2` | встановлює PHP 8.2 і Composer |
| `Install Dependencies` | `composer install -q --no-ansi --no-interaction --no-progress --prefer-dist` |
| `Lint code with Pint` | `./vendor/bin/pint --test` — перевірка стилю без зміни файлів |
| `Run unit tests` / Laravel | `./vendor/bin/phpunit tests/Unit` або `php artisan test` |
| `Demonstrate GitHub Secrets` | показує, що значення секрету маскується в логах як `***` |

Лінтер запускається **перед** тестами: стильові помилки виявляються за секунди, тому немає сенсу витрачати час на прогін тестів, якщо код усе одно не пройде перевірку.

## Локальний запуск

Потрібні PHP 8.2+ та Composer.

```bash
composer install
./vendor/bin/pint --test
./vendor/bin/phpunit tests/Unit
```

Щоб автоматично виправити стильові порушення локально:

```bash
./vendor/bin/pint
```

## Автор

Al-Hav-ic
