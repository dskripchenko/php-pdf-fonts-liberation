<?php

declare(strict_types=1);

namespace Dskripchenko\PhpPdfFontsLiberation;

use Dskripchenko\PhpPdf\Font\FontProvider;
use Dskripchenko\PhpPdf\Font\Ttf\TtfFile;

/**
 * A FontProvider implementation bundling the Liberation font family for use in
 * dskripchenko/php-pdf.
 *
 * It ships 12 TTF files (3 families × 4 variants):
 *   LiberationSans-{Regular,Bold,Italic,BoldItalic}.ttf
 *   LiberationSerif-{Regular,Bold,Italic,BoldItalic}.ttf
 *   LiberationMono-{Regular,Bold,Italic,BoldItalic}.ttf
 *
 * Metric-compatible with the Microsoft proprietary fonts:
 *   LiberationSans  ↔ Arial / Helvetica
 *   LiberationSerif ↔ Times New Roman / Times
 *   LiberationMono  ↔ Courier New / Courier
 *
 * The resolver arrives at Liberation for a proprietary name:
 *   $provider->resolve('Arial')        → LiberationSans-Regular
 *   $provider->resolve('Times-Bold')   → LiberationSerif-Bold (when the suffix
 *                                        is part of the name)
 *
 * resolve() does expect a full PostScript name (LiberationSans-Bold), though;
 * for a high-level family+variant lookup use
 * {@see \Dskripchenko\PhpPdf\Font\PdfFontResolver}, which wraps the provider and
 * tries the variant suffix chain.
 *
 * Lazy loading: a TTF is read from disk only on the first resolve() and is
 * cached in $cache between the calls.
 *
 * @see https://github.com/liberationfonts/liberation-fonts (upstream)
 * @see ../OFL.txt (font license)
 * @see ../LICENSE (MIT, for this code)
 */
final class LiberationFontProvider implements FontProvider
{
    /** @var array<string, TtfFile|false> */
    private array $cache = [];

    private string $assetsDir;

    public function __construct(?string $assetsDir = null)
    {
        $this->assetsDir = $assetsDir ?? __DIR__.'/../assets/fonts';
    }

    public function resolve(string $fontName): ?TtfFile
    {
        // Try the exact resolved name first; if there is no file, retry with the
        // '-Regular' suffix (for bare family names such as 'Arial' →
        // LiberationSans-Regular).
        $primary = $this->aliasToLiberation($fontName);
        $candidates = [$primary];
        if (! str_contains($primary, '-')) {
            $candidates[] = $primary.'-Regular';
        }

        foreach ($candidates as $name) {
            if (array_key_exists($name, $this->cache)) {
                if ($this->cache[$name] !== false) {
                    return $this->cache[$name];
                }

                continue;
            }
            $path = $this->assetsDir.'/'.$name.'.ttf';
            if (is_readable($path)) {
                return $this->cache[$name] = TtfFile::fromFile($path);
            }
            $this->cache[$name] = false;
        }

        return null;
    }

    /**
     * It supports the Microsoft names with a variant suffix:
     *   Arial            → LiberationSans
     *   Arial-Bold       → LiberationSans-Bold
     *   Times New Roman-Italic → LiberationSerif-Italic
     *   Courier New      → LiberationMono
     */
    private function aliasToLiberation(string $name): string
    {
        // Split into the base and the variant suffix.
        $suffixes = ['-BoldItalic', '-Bold', '-Italic', '-Regular'];
        $suffix = '';
        $base = $name;
        foreach ($suffixes as $s) {
            if (str_ends_with($name, $s)) {
                $suffix = $s;
                $base = substr($name, 0, -strlen($s));
                break;
            }
        }

        $alias = $this->metricAlias($base);
        $libBase = $alias ?? $base;

        return $libBase.$suffix;
    }

    /**
     * Resolving the Microsoft proprietary names into the metric-compatible
     * Liberation ones:
     *   Arial             → LiberationSans
     *   Times New Roman   → LiberationSerif
     *   Courier New       → LiberationMono
     *
     * Returns null when the name is not a Microsoft alias (it is then used as
     * is).
     */
    public function metricAlias(string $msName): ?string
    {
        return match (strtolower(trim($msName))) {
            'arial', 'helvetica', 'liberationsans' => 'LiberationSans',
            'times new roman', 'times', 'liberationserif' => 'LiberationSerif',
            'courier new', 'courier', 'liberationmono' => 'LiberationMono',
            default => $this->genericAlias($msName),
        };
    }

    /**
     * The generic CSS families (`serif`, `sans-serif`, `monospace`) and their
     * system synonyms.
     *
     * This is not a metric correspondence but an answer to "give me a font of
     * that kind": the set of three families covers exactly those three kinds.
     * Without it the request `font-family: 'IBM Plex Mono', monospace` found
     * nothing and printed with a proportional font — that is, the last word of a
     * CSS list, the one that must always work, never worked at all.
     */
    private function genericAlias(string $name): ?string
    {
        return match (strtolower(trim($name))) {
            'serif', 'ui-serif' => 'LiberationSerif',
            'sans-serif', 'ui-sans-serif', 'system-ui', '-apple-system', 'blinkmacsystemfont' => 'LiberationSans',
            'monospace', 'ui-monospace', 'sfmono-regular', 'menlo', 'consolas' => 'LiberationMono',
            default => null,
        };
    }

    /**
     * A lookup helper: returns a map of alias → absolute path for all 12 bundled
     * TTFs (for debugging and introspection).
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
}
