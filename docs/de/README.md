# dskripchenko/php-pdf-fonts-liberation

Schriftpaket der Liberation-Familie für
[`dskripchenko/php-pdf`](https://github.com/dskripchenko/php-pdf): 12 TTF-Dateien
(Sans / Serif / Mono × Regular / Bold / Italic / Bold Italic) und eine
gebrauchsfertige `FontProvider`-Implementierung.

> 🌐 [English](../../README.md) · **Deutsch** · [Русский](../ru/README.md) · [中文](../zh/README.md)

[![Tests](https://img.shields.io/github/actions/workflow/status/dskripchenko/php-pdf-fonts-liberation/tests.yml?branch=main&label=tests&logo=github)](https://github.com/dskripchenko/php-pdf-fonts-liberation/actions/workflows/tests.yml)
[![Packagist](https://img.shields.io/packagist/v/dskripchenko/php-pdf-fonts-liberation.svg)](https://packagist.org/packages/dskripchenko/php-pdf-fonts-liberation)
[![Total Downloads](https://img.shields.io/packagist/dt/dskripchenko/php-pdf-fonts-liberation)](https://packagist.org/packages/dskripchenko/php-pdf-fonts-liberation)
[![License: MIT + OFL-1.1](https://img.shields.io/badge/license-MIT%20%2B%20OFL--1.1-brightgreen.svg)](#lizenz)
[![PHP](https://img.shields.io/badge/php-%5E8.2-blue.svg)](../../composer.json)

---

## Warum ein eigenes Paket

`dskripchenko/php-pdf` steht unter MIT. Die Liberation-Schriften stehen unter
der **SIL Open Font License (OFL) 1.1** — ebenfalls freizügig und
GPL-verträglich, aber eben nicht MIT: andere Namensregeln für Ableitungen,
andere Weitergabeklauseln. Die Schriften separat zu halten,

- trennt die OFL-Zone von der MIT-Zone;
- lässt Aufrufer die rund 4 MB Schriften weglassen, wenn sie eigene mitbringen;
- macht künftige Geschwisterpakete (`php-pdf-fonts-noto`,
  `php-pdf-fonts-roboto` …) ohne Eingriff in den Kern möglich.

## Was enthalten ist

Liberation Fonts **2.1.5** (aktuelle stabile Version), 12 Dateien in
`assets/fonts/`:

| Familie           | Schnitte                                    |
|-------------------|---------------------------------------------|
| LiberationSans    | Regular, Bold, Italic, BoldItalic           |
| LiberationSerif   | Regular, Bold, Italic, BoldItalic           |
| LiberationMono    | Regular, Bold, Italic, BoldItalic           |

Die Liberation-Familie ist **metrisch kompatibel** zu den proprietären
Microsoft-Schriften: Text, der für Arial, Times New Roman oder Courier New
gesetzt wurde, bricht mit dem jeweiligen Liberation-Pendant an denselben Stellen
um. Deshalb liefern die Wege „DOCX → PDF“ und „HTML-Vorlage → PDF“ denselben
Seitenumbruch.

Zeichenabdeckung: vollständiges Latein (west- und osteuropäisch), Kyrillisch,
Griechisch, Hebräisch, Arabisch sowie Grundzüge von Devanagari.

## Installation

```bash
composer require dskripchenko/php-pdf-fonts-liberation
```

PHP 8.2+. Es werden keine nativen Erweiterungen benötigt.

## Verwendung

```php
use Dskripchenko\PhpPdf\Build\DocumentBuilder;
use Dskripchenko\PhpPdf\Layout\Engine;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$bytes = DocumentBuilder::new()
    ->heading(1, 'Jahresbericht')
    ->paragraph('Tausend schöne Zeichen mit sauberem Kerning.')
    ->toBytes(new Engine(fontProvider: new LiberationFontProvider));
```

### Auflösung von Microsoft-Namen

`LiberationFontProvider` bildet die gängigen proprietären Namen automatisch auf
ihr metrisch kompatibles Liberation-Pendant ab:

| Angefordert              | Aufgelöst zu               |
|--------------------------|----------------------------|
| `Arial`                  | `LiberationSans`           |
| `Arial-Bold`             | `LiberationSans-Bold`      |
| `Times New Roman`        | `LiberationSerif`          |
| `Times New Roman-Italic` | `LiberationSerif-Italic`   |
| `Courier New`            | `LiberationMono`           |

Eine Vorlage mit `font-family: "Times New Roman"` funktioniert sofort.

### Zusammen mit System- oder eigenen Schriften

Mit `Dskripchenko\PhpPdf\Font\ChainedFontProvider` lassen sich Provider stapeln,
sodass Liberation als Rückfallebene für fehlende Glyphen dient:

```php
use Dskripchenko\PhpPdf\Font\ChainedFontProvider;
use Dskripchenko\PhpPdf\Font\DirectoryFontProvider;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new ChainedFontProvider([
    new DirectoryFontProvider(__DIR__.'/my-brand-fonts'),  // wird zuerst befragt
    new LiberationFontProvider,                            // Rückfallebene
]);
```

## Lizenz

Im Paket treffen zwei Lizenzen aufeinander:

| Bereich                    | Lizenz                                        |
|----------------------------|-----------------------------------------------|
| `src/` (PHP-Wrapper)       | **MIT** — siehe [LICENSE](../../LICENSE)      |
| TTFs in `assets/fonts/`    | **SIL OFL 1.1** — siehe [OFL.txt](../../OFL.txt) |

Beide sind freizügig, GPL-verträglich und erlauben kommerzielle Nutzung.

## Namensnennung

Liberation Fonts © Red Hat, Inc., Pavel Hlaváč und weitere.
Ursprung: <https://github.com/liberationfonts/liberation-fonts>
