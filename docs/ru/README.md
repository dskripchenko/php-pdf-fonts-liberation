# dskripchenko/php-pdf-fonts-liberation

Набор шрифтов Liberation для
[`dskripchenko/php-pdf`](https://github.com/dskripchenko/php-pdf): 12 файлов TTF
(Sans / Serif / Mono × Regular / Bold / Italic / Bold Italic) и готовая
реализация `FontProvider`.

> 🌐 [English](../../README.md) · [Deutsch](../de/README.md) · **Русский** · [中文](../zh/README.md)

[![Tests](https://img.shields.io/github/actions/workflow/status/dskripchenko/php-pdf-fonts-liberation/tests.yml?branch=main&label=tests&logo=github)](https://github.com/dskripchenko/php-pdf-fonts-liberation/actions/workflows/tests.yml)
[![Packagist](https://img.shields.io/packagist/v/dskripchenko/php-pdf-fonts-liberation.svg)](https://packagist.org/packages/dskripchenko/php-pdf-fonts-liberation)
[![Total Downloads](https://img.shields.io/packagist/dt/dskripchenko/php-pdf-fonts-liberation)](https://packagist.org/packages/dskripchenko/php-pdf-fonts-liberation)
[![License: MIT + OFL-1.1](https://img.shields.io/badge/license-MIT%20%2B%20OFL--1.1-brightgreen.svg)](#лицензия)
[![PHP](https://img.shields.io/badge/php-%5E8.2-blue.svg)](../../composer.json)

---

## Зачем отдельный пакет

`dskripchenko/php-pdf` выпущен под MIT. Шрифты Liberation поставляются под
**SIL Open Font License (OFL) 1.1** — тоже разрешительной и совместимой с GPL,
но всё-таки другой: там свои правила именования производных и свои условия
распространения. Держать шрифты отдельным пакетом значит:

- не смешивать зону OFL с зоной MIT;
- дать возможность отказаться от ~4 МБ шрифтов тем, кто везёт свои;
- открыть дорогу соседним пакетам (`php-pdf-fonts-noto`,
  `php-pdf-fonts-roboto` и прочим) без правок в ядре.

## Что внутри

Liberation Fonts **2.1.5** (последняя стабильная), 12 файлов в `assets/fonts/`:

| Семейство         | Начертания                                  |
|-------------------|---------------------------------------------|
| LiberationSans    | Regular, Bold, Italic, BoldItalic           |
| LiberationSerif   | Regular, Bold, Italic, BoldItalic           |
| LiberationMono    | Regular, Bold, Italic, BoldItalic           |

Семейство Liberation **метрически совместимо** с проприетарными шрифтами
Microsoft: текст, свёрстанный под Arial, Times New Roman или Courier New, ляжет
с теми же переносами строк на соответствующем шрифте Liberation. Поэтому
сценарии «DOCX → PDF» и «HTML-шаблон → PDF» дают одинаковую разбивку на
страницы.

Покрытие символов: латиница (западно- и восточноевропейская, турецкая,
вьетнамская), кириллица, греческий и иврит. Арабского, индийских письменностей
и CJK нет: для них добавьте шрифт, где они есть (например, Noto), через
`DirectoryFontProvider`.

## Установка

```bash
composer require dskripchenko/php-pdf-fonts-liberation
```

PHP 8.2+. Расширений не требуется.

## Использование

```php
use Dskripchenko\PhpPdf\Build\DocumentBuilder;
use Dskripchenko\PhpPdf\Layout\Engine;
use Dskripchenko\PhpPdf\Pdf\PdfFont;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new LiberationFontProvider;

$bytes = DocumentBuilder::new()
    ->heading(1, 'Годовой отчёт')
    ->paragraph('Тысяча красивых символов с правильным кернингом.')
    ->toBytes(new Engine(
        defaultFont: new PdfFont($fonts->resolve('LiberationSans')),
        boldFont: new PdfFont($fonts->resolve('LiberationSans-Bold')),
        italicFont: new PdfFont($fonts->resolve('LiberationSans-Italic')),
        boldItalicFont: new PdfFont($fonts->resolve('LiberationSans-BoldItalic')),
        fontProvider: $fonts,   // для фрагментов с явным семейством: Arial, Times New Roman…
    ));
```

`defaultFont` и его жирное / курсивное начертания задают шрифт текста без
собственного семейства; без них движок откатывается на Helvetica из базовых 14,
в которой нет кириллицы. `fontProvider` отвечает за фрагменты с явно указанным
семейством — `font-family: "Times New Roman"` в HTML, `fontFamily` в `RunStyle`.

### Подстановка имён Microsoft

`LiberationFontProvider` сам сопоставляет привычные проприетарные имена их
метрическим эквивалентам:

| Запрошено                | Подставляется              |
|--------------------------|----------------------------|
| `Arial`                  | `LiberationSans`           |
| `Arial-Bold`             | `LiberationSans-Bold`      |
| `Times New Roman`        | `LiberationSerif`          |
| `Times New Roman-Italic` | `LiberationSerif-Italic`   |
| `Courier New`            | `LiberationMono`           |

Шаблон с `font-family: "Times New Roman"` работает без правок.

### Вместе с системными и собственными шрифтами

`Dskripchenko\PhpPdf\Font\ChainedFontProvider` выстраивает провайдеры в цепочку.
Каждое имя шрифта ищется по порядку, и побеждает первый провайдер, у которого
оно есть, — поэтому ваши семейства идут первыми, а Liberation отвечает за
остальное: `Arial`, `Times New Roman` и подобные:

```php
use Dskripchenko\PhpPdf\Font\ChainedFontProvider;
use Dskripchenko\PhpPdf\Font\DirectoryFontProvider;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new ChainedFontProvider(
    new DirectoryFontProvider(__DIR__.'/my-brand-fonts'),  // смотрим сюда первым делом
    new LiberationFontProvider,                            // затем Liberation
);
```

### Заполнение готовых форм

Начиная с php-pdf 1.10 `ExistingFormFiller` подхватывает этот пакет сам:
если значение нельзя показать собственными шрифтами формы (кириллица,
греческий…), оно рисуется подходящей гарнитурой Liberation — Sans, Serif
или Mono, обычной или жирной, по шрифту поля. Настраивать ничего не нужно —
достаточно установить пакет.

```php
ExistingFormFiller::fromFile('template.pdf')
    ->setValue('full_name', 'Иван Петров')   // рисуется Liberation Sans
    ->toFile('filled.pdf');
```

## Семейство php-pdf

| Пакет | Что добавляет |
|---|---|
| [php-pdf](https://github.com/dskripchenko/php-pdf) | Ядро: HTML с CSS или билдер → PDF, чтение, слияние, заполнение форм, подпись и проверка. |
| [laravel-php-pdf](https://github.com/dskripchenko/laravel-php-pdf) | Мост для Laravel: Blade-шаблон сразу в PDF, `response()->pdf()`, подпись по конфигу. |
| **php-pdf-fonts-liberation** (этот пакет) | Liberation Sans, Serif и Mono, метрически совместимые с Arial, Times New Roman и Courier New (OFL, вынесены из MIT-ядра). |
| [php-pdf-cryptopro](https://github.com/dskripchenko/php-pdf-cryptopro) | Подпись ключом в КриптоПро CSP на сервере: ГОСТ Р 34.10-2012, российская КЭП. |
| [php-pdf-pkcs11](https://github.com/dskripchenko/php-pdf-pkcs11) | Подпись ключом на HSM, смарт-карте или USB-токене через PKCS#11. |
| [php-pdf-cloud-kms](https://github.com/dskripchenko/php-pdf-cloud-kms) | Подпись ключом в AWS KMS, Google Cloud KMS или Azure Key Vault. |

## Лицензия

В пакете сходятся две лицензии:

| Зона                     | Лицензия                                    |
|--------------------------|---------------------------------------------|
| `src/` (обёртка на PHP)  | **MIT** — см. [LICENSE](../../LICENSE)      |
| TTF в `assets/fonts/`    | **SIL OFL 1.1** — см. [OFL.txt](../../OFL.txt) |

Обе разрешительные, совместимы с GPL и допускают коммерческое использование.

## Атрибуция

Liberation Fonts © Red Hat, Inc., Pavel Hlaváč и другие.
Первоисточник: <https://github.com/liberationfonts/liberation-fonts>
