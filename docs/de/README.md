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

Zeichenabdeckung: Latein (west- und osteuropäisch, Türkisch,
Vietnamesisch), Kyrillisch, Griechisch und Hebräisch. Arabisch, indische Schriften
und CJK fehlen: Dafür eine Schrift, die sie enthält (etwa Noto), über einen
`DirectoryFontProvider` hinzufügen.

## Installation

```bash
composer require dskripchenko/php-pdf-fonts-liberation
```

PHP 8.2+. Es werden keine nativen Erweiterungen benötigt.

## Verwendung

```php
use Dskripchenko\PhpPdf\Build\DocumentBuilder;
use Dskripchenko\PhpPdf\Layout\Engine;
use Dskripchenko\PhpPdf\Pdf\PdfFont;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new LiberationFontProvider;

$bytes = DocumentBuilder::new()
    ->heading(1, 'Jahresbericht')
    ->paragraph('Tausend schöne Zeichen mit sauberem Kerning.')
    ->toBytes(new Engine(
        defaultFont: new PdfFont($fonts->resolve('LiberationSans')),
        boldFont: new PdfFont($fonts->resolve('LiberationSans-Bold')),
        italicFont: new PdfFont($fonts->resolve('LiberationSans-Italic')),
        boldItalicFont: new PdfFont($fonts->resolve('LiberationSans-BoldItalic')),
        fontProvider: $fonts,   // für Runs, die eine Familie nennen: Arial, Times New Roman…
    ));
```

`defaultFont` und seine fetten / kursiven Schnitte setzen Text ohne eigene
Familie; ohne sie fällt die Engine auf Helvetica aus den 14 Standardschriften
zurück, die kein Kyrillisch hat. `fontProvider` ist für Runs zuständig, die eine
Familie nennen — `font-family: "Times New Roman"` in HTML, `fontFamily` in einem `RunStyle`.

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

Mit `Dskripchenko\PhpPdf\Font\ChainedFontProvider` lassen sich Provider stapeln.
Jeder Schriftname wird der Reihe nach gesucht, und der erste Provider, der ihn
kennt, gewinnt — eigene Familien kommen also zuerst, und Liberation übernimmt
den Rest: `Arial`, `Times New Roman` und Ähnliche:

```php
use Dskripchenko\PhpPdf\Font\ChainedFontProvider;
use Dskripchenko\PhpPdf\Font\DirectoryFontProvider;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new ChainedFontProvider(
    new DirectoryFontProvider(__DIR__.'/my-brand-fonts'),  // wird zuerst befragt
    new LiberationFontProvider,                            // dann Liberation
);
```

### Bestehende Formulare ausfüllen

Seit php-pdf 1.10 verwendet `ExistingFormFiller` dieses Paket von selbst:
Lässt sich ein Wert mit den formulareigenen Schriften nicht darstellen
(Kyrillisch, Griechisch, …), wird er mit der passenden Liberation-Schrift
gezeichnet — Sans, Serif oder Mono, normal oder fett, je nach Schrift des
Feldes. Konfiguration ist nicht nötig; die Installation des Pakets genügt.

```php
ExistingFormFiller::fromFile('template.pdf')
    ->setValue('full_name', 'Иван Петров')   // mit Liberation Sans gezeichnet
    ->toFile('filled.pdf');
```

## Die php-pdf-Familie

| Paket | Was es hinzufügt |
|---|---|
| [php-pdf](https://github.com/dskripchenko/php-pdf) | Der Kern: HTML mit CSS oder ein Builder zu PDF, Lesen, Zusammenführen, Formulare ausfüllen, Signieren und Validieren. |
| [laravel-php-pdf](https://github.com/dskripchenko/laravel-php-pdf) | Laravel-Brücke: eine Blade-View direkt als PDF, `response()->pdf()`, Signieren per Konfiguration. |
| **php-pdf-fonts-liberation** (dieses Paket) | Liberation Sans, Serif und Mono, metrisch kompatibel mit Arial, Times New Roman und Courier New (OFL, aus dem MIT-Kern herausgehalten). |
| [php-pdf-cryptopro](https://github.com/dskripchenko/php-pdf-cryptopro) | Signiert mit einem Schlüssel in CryptoPro CSP auf dem Server: GOST R 34.10-2012, russische qualifizierte Signaturen. |
| [php-pdf-pkcs11](https://github.com/dskripchenko/php-pdf-pkcs11) | Signiert mit einem Schlüssel auf einem HSM, einer Smartcard oder einem USB-Token über PKCS#11. |
| [php-pdf-cloud-kms](https://github.com/dskripchenko/php-pdf-cloud-kms) | Signiert mit einem Schlüssel in AWS KMS, Google Cloud KMS oder Azure Key Vault. |

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
