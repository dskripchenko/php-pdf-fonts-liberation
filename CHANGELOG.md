# Changelog

All notable changes to `dskripchenko/php-pdf-fonts-liberation` are
documented in this file. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versioning
follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.1] — 2026-10-01

### Changed
- README (all languages): php-pdf 1.10's `ExistingFormFiller` uses this
  package automatically for form values the form's own fonts cannot show.
  The font bundle and the provider are unchanged.

## [1.1.0] — 2026-08-05

### Added
- **CSS generic families resolve to the bundled faces**: `serif`,
  `sans-serif`, `monospace` and their system synonyms (`ui-monospace`,
  `system-ui`, `menlo`, `consolas`, …). The last name in a CSS
  `font-family` list is the one that has to work when nothing else does —
  and it was the one that never did: `font-family: 'IBM Plex Mono',
  monospace` resolved to nothing and printed in a proportional face. Three
  bundled families cover exactly the three generic classes, so answering
  them is what this provider is for.

## [1.0.1] — 2026-07-28

### Changed
- CI: tests workflow (PHP 8.2–8.5) and PHPStan configuration; Packagist
  badges in the README. Font bundle itself is unchanged — Liberation
  Fonts 2.1.5 under SIL OFL 1.1.

## [1.0.0] — 2026-05-15

Initial release.

### Added
- Liberation Fonts **2.1.5** bundle — 12 TTFs (Sans / Serif / Mono ×
  Regular / Bold / Italic / BoldItalic) under SIL OFL 1.1 in
  `assets/fonts/`.
- `LiberationFontProvider` (MIT) implementing php-pdf's `FontProvider`:
  lazy per-font loading with caching, bare-family fallback to `-Regular`,
  and metric-compatible aliasing of Microsoft names (Arial →
  LiberationSans, Times New Roman → LiberationSerif, Courier New →
  LiberationMono, including variant suffixes).
- Composes with `ChainedFontProvider` / `PdfFontResolver` from the core
  for brand-font-first stacks and family+bold/italic resolution.
