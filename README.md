# dskripchenko/php-pdf-fonts-liberation

Liberation font family bundle for
[`dskripchenko/php-pdf`](https://github.com/dskripchenko/php-pdf).

## What this is

A separate composer package that bundles the **Liberation** font family
(Sans, Serif, Mono — Regular, Bold, Italic, Bold Italic variants) and
ships them through a `FontProvider` implementation compatible with
`dskripchenko/php-pdf`.

## Why a separate package

`dskripchenko/php-pdf` is MIT-licensed. The Liberation fonts are
licensed under the **SIL Open Font License (OFL) 1.1**, which is
permissive but distinct from MIT (different naming rules for
derivatives, different redistribution clauses). Keeping fonts in a
separate package:

- isolates the OFL license zone from the MIT zone
- lets callers opt-out of the 4.3 MB font bundle when they ship their
  own fonts
- enables future font packages (`php-pdf-fonts-noto`,
  `php-pdf-fonts-roboto`, etc.) without changing core

## Status

🚧 **Skeleton only — fonts will be added in Phase 2** of php-pdf
development (alongside the TTF subset embedding implementation).
This repo is initialised now to reserve the composer name and document
the licensing structure.

## License

This package mixes two licenses:

| Zone | License |
|---|---|
| `src/` (PHP wrapper code) | **MIT** — see [LICENSE](LICENSE) |
| `assets/fonts/` (TTF files) | **SIL OFL 1.1** — see [OFL.txt](OFL.txt) |

Both are permissive, GPL-compatible, and allow commercial use.

## Installation (future)

```bash
composer require dskripchenko/php-pdf-fonts-liberation
```

## Usage (future)

```php
use Dskripchenko\PhpPdf\Build\DocumentBuilder;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$pdf = DocumentBuilder::new()
    ->fontProvider(new LiberationFontProvider)
    ->paragraph('Текст с русскими символами')
    ->toFile('out.pdf');
```

`LiberationFontProvider` will resolve common Microsoft font names
(Arial, Times New Roman, Courier New) to the metric-compatible
Liberation equivalents — important for DOCX-input → PDF workflows.

## Font sources & attribution

Liberation Fonts © Red Hat, Inc., Pavel Hlaváč, and others.
Repository: https://github.com/liberationfonts/liberation-fonts

Bundled version: **2.1.5** (2018, latest stable).
