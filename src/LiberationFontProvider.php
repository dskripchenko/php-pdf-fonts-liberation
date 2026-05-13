<?php

declare(strict_types=1);

namespace Dskripchenko\PhpPdfFontsLiberation;

/**
 * FontProvider implementation that bundles the Liberation font family.
 *
 * **STUB — implementation lives in Phase 2 of dskripchenko/php-pdf**, when
 * the host library has the `FontProvider` interface and TTF subset
 * embedding logic to consume font bytes. Until then this class only
 * exposes the metadata layer:
 *
 *  - `bundledFonts(): array<string, string>` — map alias → absolute TTF path
 *  - `metricAlias(string): ?string` — resolve "Arial" → "LiberationSans", etc.
 *
 * Bundled files (will be added in Phase 2 alongside the implementation):
 *
 *   assets/fonts/LiberationSans-Regular.ttf
 *   assets/fonts/LiberationSans-Bold.ttf
 *   assets/fonts/LiberationSans-Italic.ttf
 *   assets/fonts/LiberationSans-BoldItalic.ttf
 *   assets/fonts/LiberationSerif-Regular.ttf
 *   assets/fonts/LiberationSerif-Bold.ttf
 *   assets/fonts/LiberationSerif-Italic.ttf
 *   assets/fonts/LiberationSerif-BoldItalic.ttf
 *   assets/fonts/LiberationMono-Regular.ttf
 *   assets/fonts/LiberationMono-Bold.ttf
 *   assets/fonts/LiberationMono-Italic.ttf
 *   assets/fonts/LiberationMono-BoldItalic.ttf
 *
 * @see https://github.com/liberationfonts/liberation-fonts (upstream)
 * @see ../OFL.txt (font license)
 * @see ../LICENSE (MIT for this code)
 */
final class LiberationFontProvider
{
    private string $assetsDir;

    public function __construct(?string $assetsDir = null)
    {
        $this->assetsDir = $assetsDir ?? __DIR__.'/../assets/fonts';
    }

    /**
     * Carta доступных TTF в bundle'е. Имя → absolute path.
     *
     * Существование файлов на диске НЕ проверяется здесь; вызов идёт
     * в Phase 2 когда host-библиотека реально пытается прочитать font.
     *
     * @return array<string, string>
     */
    public function bundledFonts(): array
    {
        $variants = [
            'LiberationSans-Regular',
            'LiberationSans-Bold',
            'LiberationSans-Italic',
            'LiberationSans-BoldItalic',
            'LiberationSerif-Regular',
            'LiberationSerif-Bold',
            'LiberationSerif-Italic',
            'LiberationSerif-BoldItalic',
            'LiberationMono-Regular',
            'LiberationMono-Bold',
            'LiberationMono-Italic',
            'LiberationMono-BoldItalic',
        ];
        $out = [];
        foreach ($variants as $name) {
            $out[$name] = $this->assetsDir.'/'.$name.'.ttf';
        }

        return $out;
    }

    /**
     * Резолв Microsoft-проприетарных имён в metric-compatible Liberation:
     *
     *   Arial             → LiberationSans
     *   Times New Roman   → LiberationSerif
     *   Courier New       → LiberationMono
     *
     * Используется PDF-builder'ом при HTML-input'е где font-family указан
     * как Arial / Times New Roman / Courier New — мы рендерим в Liberation
     * с теми же метриками, layout совпадает с Word-документом.
     *
     * Возвращает null если имя не Microsoft-alias.
     */
    public function metricAlias(string $msName): ?string
    {
        return match (strtolower(trim($msName))) {
            'arial', 'helvetica' => 'LiberationSans',
            'times new roman', 'times' => 'LiberationSerif',
            'courier new', 'courier' => 'LiberationMono',
            default => null,
        };
    }
}
