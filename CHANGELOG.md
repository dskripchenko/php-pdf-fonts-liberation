# Changelog

All notable changes to `dskripchenko/php-pdf-fonts-liberation` are
documented in this file. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versioning
follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
