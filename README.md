# dskripchenko/php-pdf-fonts-liberation

Liberation font family bundle for
[`dskripchenko/php-pdf`](https://github.com/dskripchenko/php-pdf) —
ships 12 TTFs (Sans / Serif / Mono × Regular / Bold / Italic /
Bold Italic) and a ready-to-use `FontProvider` implementation.

> 🌐 **English** · [Deutsch](docs/de/README.md) · [Русский](docs/ru/README.md) · [中文](docs/zh/README.md)

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

Glyph coverage: Latin (Western and Eastern European, Turkish,
Vietnamese), Cyrillic, Greek and Hebrew. There is no Arabic, Indic or CJK:
for those, add a font that has them (Noto, for one) with a
`DirectoryFontProvider`.

## Installation

```bash
composer require dskripchenko/php-pdf-fonts-liberation
```

PHP 8.2+. No native extensions required.

## Usage

```php
use Dskripchenko\PhpPdf\Build\DocumentBuilder;
use Dskripchenko\PhpPdf\Layout\Engine;
use Dskripchenko\PhpPdf\Pdf\PdfFont;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new LiberationFontProvider;

$bytes = DocumentBuilder::new()
    ->heading(1, 'Annual Report')
    ->paragraph('Тысяча красивых символов с правильным кернингом.')
    ->toBytes(new Engine(
        defaultFont: new PdfFont($fonts->resolve('LiberationSans')),
        boldFont: new PdfFont($fonts->resolve('LiberationSans-Bold')),
        italicFont: new PdfFont($fonts->resolve('LiberationSans-Italic')),
        boldItalicFont: new PdfFont($fonts->resolve('LiberationSans-BoldItalic')),
        fontProvider: $fonts,   // for runs that name a family: Arial, Times New Roman…
    ));
```

`defaultFont` and its bold / italic faces set the text with no family of
its own; without them the engine falls back to the base-14 Helvetica, which
has no Cyrillic. `fontProvider` answers for runs that name a family —
`font-family: "Times New Roman"` in HTML, `fontFamily` in a `RunStyle`.

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

Use `Dskripchenko\PhpPdf\Font\ChainedFontProvider` to stack providers.
Each font name is looked up in order and the first provider that has it
wins, so your own families come first and Liberation answers for the
rest — `Arial`, `Times New Roman` and the like:

```php
use Dskripchenko\PhpPdf\Font\ChainedFontProvider;
use Dskripchenko\PhpPdf\Font\DirectoryFontProvider;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new ChainedFontProvider(
    new DirectoryFontProvider(__DIR__.'/my-brand-fonts'),  // checked first
    new LiberationFontProvider,                            // then Liberation
);
```

### Filling existing forms

Since php-pdf 1.10, `ExistingFormFiller` picks this package up on its own:
when a value cannot be shown with the form's own fonts (Cyrillic, Greek, …),
it is drawn with the matching Liberation face — Sans, Serif or Mono, regular
or bold, following the field's font. No configuration is needed; installing
the package is enough.

```php
ExistingFormFiller::fromFile('template.pdf')
    ->setValue('full_name', 'Иван Петров')   // drawn with Liberation Sans
    ->toFile('filled.pdf');
```

## The php-pdf family

| Package | What it adds |
|---|---|
| [php-pdf](https://github.com/dskripchenko/php-pdf) | The core: HTML with CSS or a builder to PDF, read, merge, fill forms, sign and validate. |
| [laravel-php-pdf](https://github.com/dskripchenko/laravel-php-pdf) | Laravel bridge: a Blade view straight to PDF, `response()->pdf()`, signing from config. |
| **php-pdf-fonts-liberation** (this package) | Liberation Sans, Serif and Mono, metric-compatible with Arial, Times New Roman and Courier New (OFL, kept out of the MIT core). |
| [php-pdf-cryptopro](https://github.com/dskripchenko/php-pdf-cryptopro) | Signs with a key in CryptoPro CSP on the server: GOST R 34.10-2012, Russian qualified signatures. |
| [php-pdf-pkcs11](https://github.com/dskripchenko/php-pdf-pkcs11) | Signs with a key on an HSM, a smart card or a USB token, through PKCS#11. |
| [php-pdf-cloud-kms](https://github.com/dskripchenko/php-pdf-cloud-kms) | Signs with a key in AWS KMS, Google Cloud KMS or Azure Key Vault. |

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
