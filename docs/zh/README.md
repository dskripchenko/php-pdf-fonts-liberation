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

字形覆盖：完整拉丁字母（西欧与东欧）、西里尔、希腊、希伯来、阿拉伯，以及天城文基础字符。

## 安装

```bash
composer require dskripchenko/php-pdf-fonts-liberation
```

PHP 8.2+，不需要任何原生扩展。

## 用法

```php
use Dskripchenko\PhpPdf\Build\DocumentBuilder;
use Dskripchenko\PhpPdf\Layout\Engine;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$bytes = DocumentBuilder::new()
    ->heading(1, '年度报告')
    ->paragraph('Тысяча красивых символов с правильным кернингом.')
    ->toBytes(new Engine(fontProvider: new LiberationFontProvider));
```

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

用 `Dskripchenko\PhpPdf\Font\ChainedFontProvider` 把多个 provider 串起来，
让 Liberation 作为缺字时的兜底：

```php
use Dskripchenko\PhpPdf\Font\ChainedFontProvider;
use Dskripchenko\PhpPdf\Font\DirectoryFontProvider;
use Dskripchenko\PhpPdfFontsLiberation\LiberationFontProvider;

$fonts = new ChainedFontProvider([
    new DirectoryFontProvider(__DIR__.'/my-brand-fonts'),  // 先查这里
    new LiberationFontProvider,                            // 兜底
]);
```

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
