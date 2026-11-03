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

## Структура проєкту

```
.
├── .github/
│   ├── workflows/
│   │   └── php-ci.yml
│   └── PULL_REQUEST_TEMPLATE.md
├── app/
│   └── Services/
│       └── Calculator.php
├── tests/
│   └── Unit/
│       └── CalculatorTest.php
├── .gitignore
├── composer.json
├── phpunit.xml
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
| `Run unit tests` | `./vendor/bin/phpunit tests/Unit` |
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
