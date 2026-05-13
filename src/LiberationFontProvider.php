<?php

declare(strict_types=1);

namespace Dskripchenko\PhpPdfFontsLiberation;

use Dskripchenko\PhpPdf\Font\FontProvider;
use Dskripchenko\PhpPdf\Font\Ttf\TtfFile;

/**
 * FontProvider implementation, bundling Liberation font family для
 * использования в dskripchenko/php-pdf.
 *
 * Поставляет 12 TTF файлов (3 family × 4 variant):
 *   LiberationSans-{Regular,Bold,Italic,BoldItalic}.ttf
 *   LiberationSerif-{Regular,Bold,Italic,BoldItalic}.ttf
 *   LiberationMono-{Regular,Bold,Italic,BoldItalic}.ttf
 *
 * Metric-compatible с Microsoft proprietary fonts:
 *   LiberationSans  ↔ Arial / Helvetica
 *   LiberationSerif ↔ Times New Roman / Times
 *   LiberationMono  ↔ Courier New / Courier
 *
 * Resolver приходит к Liberation чем для proprietary имени:
 *   $provider->resolve('Arial')        → LiberationSans-Regular
 *   $provider->resolve('Times-Bold')   → LiberationSerif-Bold (если
 *                                        suffix включён в имя)
 *
 * Однако resolve() ожидает full PostScript name (LiberationSans-Bold);
 * для high-level family+variant lookup используй
 * {@see \Dskripchenko\PhpPdf\Font\PdfFontResolver} который wraps
 * provider и пробует variant suffix chain.
 *
 * Lazy load: TTF читается с диска только при первом resolve();
 * кешируется в $cache между вызовами.
 *
 * @see https://github.com/liberationfonts/liberation-fonts (upstream)
 * @see ../OFL.txt (font license)
 * @see ../LICENSE (MIT для этого кода)
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
        // Try exact resolved name first; if no file, retry with '-Regular'
        // suffix (для bare family names типа 'Arial' → LiberationSans-Regular).
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
     * Поддерживает Microsoft-имена с variant suffix'ом:
     *   Arial            → LiberationSans
     *   Arial-Bold       → LiberationSans-Bold
     *   Times New Roman-Italic → LiberationSerif-Italic
     *   Courier New      → LiberationMono
     */
    private function aliasToLiberation(string $name): string
    {
        // Split на base + variant suffix.
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
     * Резолв Microsoft-проприетарных имён в metric-compatible Liberation:
     *   Arial             → LiberationSans
     *   Times New Roman   → LiberationSerif
     *   Courier New       → LiberationMono
     *
     * Возвращает null если имя — не Microsoft-alias (используется как
     * есть).
     */
    public function metricAlias(string $msName): ?string
    {
        return match (strtolower(trim($msName))) {
            'arial', 'helvetica', 'liberationsans' => 'LiberationSans',
            'times new roman', 'times', 'liberationserif' => 'LiberationSerif',
            'courier new', 'courier', 'liberationmono' => 'LiberationMono',
            default => null,
        };
    }

    /**
     * Lookup-helper: возвращает map alias → absolute path для всех
     * 12 bundled TTF (для debug/introspection).
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
