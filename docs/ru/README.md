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

Покрытие символов: полная латиница (западная и восточноевропейская), кириллица,
греческий, иврит, арабский и основы деванагари.

## Установка

```bash
composer require dskripchenko/php-pdf-fonts-liberation
```

PHP 8.2+. Расширений не требуется.

## Использование

```php
use Dskripchenko\PhpPdf\Build\DocumentBuilder;
use Dskripchenko\PhpPdf\Layout\Engine;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$bytes = DocumentBuilder::new()
    ->heading(1, 'Годовой отчёт')
    ->paragraph('Тысяча красивых символов с правильным кернингом.')
    ->toBytes(new Engine(fontProvider: new LiberationFontProvider));
```

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

`Dskripchenko\PhpPdf\Font\ChainedFontProvider` выстраивает провайдеры в цепочку,
и Liberation становится запасным вариантом для недостающих знаков:

```php
use Dskripchenko\PhpPdf\Font\ChainedFontProvider;
use Dskripchenko\PhpPdf\Font\DirectoryFontProvider;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new ChainedFontProvider([
    new DirectoryFontProvider(__DIR__.'/my-brand-fonts'),  // смотрим сюда первым делом
    new LiberationFontProvider,                            // запасной вариант
]);
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
