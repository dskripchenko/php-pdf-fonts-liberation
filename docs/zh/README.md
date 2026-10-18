# dskripchenko/php-pdf-fonts-liberation

为 [`dskripchenko/php-pdf`](https://github.com/dskripchenko/php-pdf) 准备的 Liberation 字体包：
12 个 TTF 文件（Sans / Serif / Mono × Regular / Bold / Italic / Bold Italic），
外加一个开箱即用的 `FontProvider` 实现。

> 🌐 [English](../../README.md) · [Deutsch](../de/README.md) · [Русский](../ru/README.md) · **中文**

[![Tests](https://img.shields.io/github/actions/workflow/status/dskripchenko/php-pdf-fonts-liberation/tests.yml?branch=main&label=tests&logo=github)](https://github.com/dskripchenko/php-pdf-fonts-liberation/actions/workflows/tests.yml)
[![Packagist](https://img.shields.io/packagist/v/dskripchenko/php-pdf-fonts-liberation.svg)](https://packagist.org/packages/dskripchenko/php-pdf-fonts-liberation)
[![Total Downloads](https://img.shields.io/packagist/dt/dskripchenko/php-pdf-fonts-liberation)](https://packagist.org/packages/dskripchenko/php-pdf-fonts-liberation)
[![License: MIT + OFL-1.1](https://img.shields.io/badge/license-MIT%20%2B%20OFL--1.1-brightgreen.svg)](#许可证)
[![PHP](https://img.shields.io/badge/php-%5E8.2-blue.svg)](../../composer.json)

---

## 为什么单独成包

`dskripchenko/php-pdf` 使用 MIT 许可，而 Liberation 字体使用
**SIL 开放字体许可证（OFL）1.1**——同样宽松、同样与 GPL 兼容，但毕竟不是 MIT：
衍生作品的命名规则不同，再分发条款也不同。把字体拆成独立包，可以：

- 把 OFL 区域与 MIT 区域隔开；
- 让自带字体的使用者跳过这约 4 MB 的字体；
- 为将来的同类包（`php-pdf-fonts-noto`、`php-pdf-fonts-roboto` 等）留出空间，
  而无需改动核心。

## 包含哪些内容

Liberation Fonts **2.1.5**（当前稳定版），`assets/fonts/` 下共 12 个文件：

| 字族              | 字重／字形                                  |
|-------------------|---------------------------------------------|
| LiberationSans    | Regular、Bold、Italic、BoldItalic           |
| LiberationSerif   | Regular、Bold、Italic、BoldItalic           |
| LiberationMono    | Regular、Bold、Italic、BoldItalic           |

Liberation 字族与微软的专有字体**度量兼容**：按 Arial、Times New Roman 或
Courier New 排版的文字，换成对应的 Liberation 字体后换行位置保持一致。
因此「DOCX → PDF」和「HTML 模板 → PDF」两条路径都能得到相同的分页结果。

字形覆盖：拉丁字母（西欧与东欧、土耳其语、越南语）、西里尔、希腊与希伯来字母。
不含阿拉伯文、印度系文字和 CJK：如需这些文字，请通过 `DirectoryFontProvider`
添加包含它们的字体（例如 Noto）。

## 安装

```bash
composer require dskripchenko/php-pdf-fonts-liberation
```

PHP 8.2+，不需要任何原生扩展。

## 用法

```php
use Dskripchenko\PhpPdf\Build\DocumentBuilder;
use Dskripchenko\PhpPdf\Layout\Engine;
use Dskripchenko\PhpPdf\Pdf\PdfFont;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new LiberationFontProvider;

$bytes = DocumentBuilder::new()
    ->heading(1, '年度报告')
    ->paragraph('Тысяча красивых символов с правильным кернингом.')
    ->toBytes(new Engine(
        defaultFont: new PdfFont($fonts->resolve('LiberationSans')),
        boldFont: new PdfFont($fonts->resolve('LiberationSans-Bold')),
        italicFont: new PdfFont($fonts->resolve('LiberationSans-Italic')),
        boldItalicFont: new PdfFont($fonts->resolve('LiberationSans-BoldItalic')),
        fontProvider: $fonts,   // 用于指定了字族的文本：Arial、Times New Roman……
    ));
```

`defaultFont` 及其粗体／斜体字形用于没有自身字族的文本；缺少它们时，引擎会回退到
标准 14 字体中的 Helvetica，而它不含西里尔字母。`fontProvider` 负责指定了字族的文本——
HTML 中的 `font-family: "Times New Roman"`、`RunStyle` 中的 `fontFamily`。

### 微软字体名的自动映射

`LiberationFontProvider` 会把常见的专有字体名自动解析到度量兼容的 Liberation 版本：

| 请求的名称               | 解析为                     |
|--------------------------|----------------------------|
| `Arial`                  | `LiberationSans`           |
| `Arial-Bold`             | `LiberationSans-Bold`      |
| `Times New Roman`        | `LiberationSerif`          |
| `Times New Roman-Italic` | `LiberationSerif-Italic`   |
| `Courier New`            | `LiberationMono`           |

写着 `font-family: "Times New Roman"` 的模板可以直接使用。

### 与系统字体或自有字体搭配

用 `Dskripchenko\PhpPdf\Font\ChainedFontProvider` 把多个 provider 串起来。
每个字体名按顺序查找，第一个拥有该字体的 provider 胜出——因此你自己的字族排在前面，
其余的（`Arial`、`Times New Roman` 等）交给 Liberation：

```php
use Dskripchenko\PhpPdf\Font\ChainedFontProvider;
use Dskripchenko\PhpPdf\Font\DirectoryFontProvider;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new ChainedFontProvider(
    new DirectoryFontProvider(__DIR__.'/my-brand-fonts'),  // 先查这里
    new LiberationFontProvider,                            // 然后是 Liberation
);
```

### 填写已有表单

自 php-pdf 1.10 起，`ExistingFormFiller` 会自动使用本包：当表单自带字体无法显示某个值（西里尔文、希腊文等）时，会根据字段字体选用相应的 Liberation 字体（Sans、Serif 或 Mono，常规或粗体）进行绘制。无需任何配置，安装本包即可。

```php
ExistingFormFiller::fromFile('template.pdf')
    ->setValue('full_name', 'Иван Петров')   // 使用 Liberation Sans 绘制
    ->toFile('filled.pdf');
```

## php-pdf 家族

| 包 | 新增能力 |
|---|---|
| [php-pdf](https://github.com/dskripchenko/php-pdf) | 核心：HTML（含 CSS）或构建器生成 PDF，读取、合并、填写表单、签名与验证。 |
| [laravel-php-pdf](https://github.com/dskripchenko/laravel-php-pdf) | Laravel 桥接：Blade 视图直接生成 PDF、`response()->pdf()`、按配置签名。 |
| **php-pdf-fonts-liberation**（本包） | Liberation Sans、Serif 与 Mono，度量上与 Arial、Times New Roman 和 Courier New 兼容（OFL 许可，独立于 MIT 核心之外）。 |
| [php-pdf-cryptopro](https://github.com/dskripchenko/php-pdf-cryptopro) | 使用服务器上 CryptoPro CSP 中的密钥签名：GOST R 34.10-2012，俄罗斯合格电子签名。 |
| [php-pdf-pkcs11](https://github.com/dskripchenko/php-pdf-pkcs11) | 通过 PKCS#11 使用 HSM、智能卡或 USB 令牌上的密钥签名。 |
| [php-pdf-cloud-kms](https://github.com/dskripchenko/php-pdf-cloud-kms) | 使用 AWS KMS、Google Cloud KMS 或 Azure Key Vault 中的密钥签名。 |

## 许可证

本包中并存两种许可证：

| 范围                     | 许可证                                          |
|--------------------------|-------------------------------------------------|
| `src/`（PHP 封装）       | **MIT** —— 见 [LICENSE](../../LICENSE)          |
| `assets/fonts/` 中的 TTF | **SIL OFL 1.1** —— 见 [OFL.txt](../../OFL.txt)  |

两者都很宽松、与 GPL 兼容，并且允许商业使用。

## 署名

Liberation Fonts © Red Hat, Inc.、Pavel Hlaváč 等。
上游：<https://github.com/liberationfonts/liberation-fonts>
