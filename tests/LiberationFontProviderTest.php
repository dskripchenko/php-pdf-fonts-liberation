<?php

declare(strict_types=1);

namespace Dskripchenko\PhpPdfFontsLiberation\Tests;

use Dskripchenko\PhpPdf\Font\PdfFontResolver;
use Dskripchenko\PhpPdf\Font\Ttf\TtfFile;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LiberationFontProviderTest extends TestCase
{
    #[Test]
    public function resolves_liberation_sans_regular(): void
    {
        $provider = new LiberationFontProvider;
        $ttf = $provider->resolve('LiberationSans-Regular');
        self::assertInstanceOf(TtfFile::class, $ttf);
    }

    #[Test]
    public function resolves_all_12_variants(): void
    {
        $provider = new LiberationFontProvider;
        foreach (array_keys($provider->bundledFonts()) as $name) {
            $ttf = $provider->resolve($name);
            self::assertNotNull($ttf, "Variant $name should resolve");
        }
    }

    #[Test]
    public function arial_aliased_to_liberation_sans(): void
    {
        $provider = new LiberationFontProvider;
        $ttf = $provider->resolve('Arial');
        self::assertNotNull($ttf);
        // Sanity check — PostScript name reflects Liberation
        self::assertStringContainsString('Liberation', $ttf->postScriptName());
    }

    #[Test]
    public function microsoft_aliases_with_variant_suffix(): void
    {
        $provider = new LiberationFontProvider;
        $bold = $provider->resolve('Arial-Bold');
        $italic = $provider->resolve('Times New Roman-Italic');
        $bi = $provider->resolve('Courier New-BoldItalic');
        self::assertNotNull($bold);
        self::assertNotNull($italic);
        self::assertNotNull($bi);
    }

    #[Test]
    public function unknown_family_returns_null(): void
    {
        $provider = new LiberationFontProvider;
        self::assertNull($provider->resolve('NoSuchFont-Regular'));
    }

    #[Test]
    public function metric_alias_helper(): void
    {
        $provider = new LiberationFontProvider;
        self::assertSame('LiberationSans', $provider->metricAlias('Arial'));
        self::assertSame('LiberationSans', $provider->metricAlias('Helvetica'));
        self::assertSame('LiberationSerif', $provider->metricAlias('Times New Roman'));
        self::assertSame('LiberationSerif', $provider->metricAlias('times'));  // case-insensitive
        self::assertSame('LiberationMono', $provider->metricAlias('Courier New'));
        self::assertNull($provider->metricAlias('Comic Sans'));
    }

    #[Test]
    public function resolver_chain_works_with_provider(): void
    {
        $resolver = new PdfFontResolver(new LiberationFontProvider);

        $regular = $resolver->resolve('LiberationSans');
        $bold = $resolver->resolve('LiberationSans', bold: true);
        $italic = $resolver->resolve('LiberationSans', italic: true);
        $bi = $resolver->resolve('LiberationSans', bold: true, italic: true);

        self::assertNotNull($regular);
        self::assertNotNull($bold);
        self::assertNotNull($italic);
        self::assertNotNull($bi);
        self::assertNotSame($regular, $bold);
    }

    #[Test]
    public function resolver_works_with_microsoft_alias(): void
    {
        $resolver = new PdfFontResolver(new LiberationFontProvider);

        // CSS font-family: 'Arial' → LiberationSans
        $font = $resolver->resolve('Arial', bold: true);
        self::assertNotNull($font);
    }
}
