# dskripchenko/php-pdf-fonts-liberation

Liberation font family bundle for
[`dskripchenko/php-pdf`](https://github.com/dskripchenko/php-pdf) —
ships 12 TTFs (Sans / Serif / Mono × Regular / Bold / Italic /
Bold Italic) and a ready-to-use `FontProvider` implementation.

[![Tests](https://img.shields.io/github/actions/workflow/status/dskripchenko/php-pdf-fonts-liberation/tests.yml?branch=main&label=tests&logo=github)](https://github.com/dskripchenko/php-pdf-fonts-liberation/actions/workflows/tests.yml)
[![Packagist](https://img.shields.io/packagist/v/dskripchenko/php-pdf-fonts-liberation.svg)](https://packagist.org/packages/dskripchenko/php-pdf-fonts-liberation)
[![Total Downloads](https://img.shields.io/packagist/dt/dskripchenko/php-pdf-fonts-liberation)](https://packagist.org/packages/dskripchenko/php-pdf-fonts-liberation)
[![License: MIT + OFL-1.1](https://img.shields.io/badge/license-MIT%20%2B%20OFL--1.1-brightgreen.svg)](#license)
[![PHP](https://img.shields.io/badge/php-%5E8.2-blue.svg)](composer.json)

---

## Why a separate package

`dskripchenko/php-pdf` is MIT-licensed. The Liberation fonts ship under
the **SIL Open Font License (OFL) 1.1**, which is permissive and
GPL-compatible but distinct from MIT (different naming rules for
derivatives, different redistribution clauses). Keeping fonts in a
separate package:

- isolates the OFL license zone from the MIT zone;
- lets callers opt out of the ~4 MB font bundle when they ship their
  own fonts;
- enables future sibling packages (`php-pdf-fonts-noto`,
  `php-pdf-fonts-roboto`, etc.) without changing the core.

## What's bundled

Liberation Fonts **2.1.5** (latest stable), 12 files in `assets/fonts/`:

| Family            | Variants                                    |
|-------------------|---------------------------------------------|
| LiberationSans    | Regular, Bold, Italic, BoldItalic           |
| LiberationSerif   | Regular, Bold, Italic, BoldItalic           |
| LiberationMono    | Regular, Bold, Italic, BoldItalic           |

The Liberation family is **metric-compatible** with Microsoft's
proprietary fonts — text laid out for Arial / Times New Roman /
Courier New will render with the same line wrapping using the
Liberation equivalent, so DOCX-input → PDF and HTML-templates → PDF
workflows produce identical pagination.

Glyph coverage: full Latin (Western and Eastern European), Cyrillic,
Greek, Hebrew, Arabic, and Devanagari basics.

## Installation

```bash
composer require dskripchenko/php-pdf-fonts-liberation
```

PHP 8.2+. No native extensions required.

## Usage

```php
use Dskripchenko\PhpPdf\Build\DocumentBuilder;
use Dskripchenko\PhpPdf\Layout\Engine;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$bytes = DocumentBuilder::new()
    ->heading(1, 'Annual Report')
    ->paragraph('Тысяча красивых символов с правильным кернингом.')
    ->toBytes(new Engine(fontProvider: new LiberationFontProvider));
```

### Microsoft name aliasing

`LiberationFontProvider` resolves common proprietary names to their
metric-compatible Liberation equivalent automatically:

| Requested name           | Resolved to                |
|--------------------------|----------------------------|
| `Arial`                  | `LiberationSans`           |
| `Arial-Bold`             | `LiberationSans-Bold`      |
| `Times New Roman`        | `LiberationSerif`          |
| `Times New Roman-Italic` | `LiberationSerif-Italic`   |
| `Courier New`            | `LiberationMono`           |

A template that says `font-family: "Times New Roman"` works out of
the box.

### Combining with system or custom fonts

Use `Dskripchenko\PhpPdf\Font\ChainedFontProvider` to stack providers
so Liberation acts as a fallback for missing glyphs:

```php
use Dskripchenko\PhpPdf\Font\ChainedFontProvider;
use Dskripchenko\PhpPdf\Font\DirectoryFontProvider;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new ChainedFontProvider([
    new DirectoryFontProvider(__DIR__.'/my-brand-fonts'),  // checked first
    new LiberationFontProvider,                            // fallback
]);
```

## License

This package mixes two licenses:

| Zone                 | License                                   |
|----------------------|-------------------------------------------|
| `src/` (PHP wrapper) | **MIT** — see [LICENSE](LICENSE)          |
| `assets/fonts/` TTFs | **SIL OFL 1.1** — see [OFL.txt](OFL.txt)  |

Both are permissive, GPL-compatible, and allow commercial use.

## Attribution

Liberation Fonts © Red Hat, Inc., Pavel Hlaváč, and others.
Upstream: <https://github.com/liberationfonts/liberation-fonts>
