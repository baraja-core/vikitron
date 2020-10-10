<h1 align="center">
    Smart PHP Number Utilities
</h1>

<p align="center">
    <a href="https://mathematicator.com" target="_blank">
        <img src="https://avatars3.githubusercontent.com/u/44620375?s=100&v=4">
    </a>
</p>

[![Integrity check](https://github.com/mathematicator-core/numbers/workflows/Integrity%20check/badge.svg)](https://github.com/mathematicator-core/numbers/actions?query=workflow%3A%22Integrity+check%22)
[![codecov](https://codecov.io/gh/mathematicator-core/numbers/branch/master/graph/badge.svg)](https://codecov.io/gh/mathematicator-core/numbers)
[![Latest Stable Version](https://poser.pugx.org/mathematicator-core/numbers/v/stable)](https://packagist.org/packages/mathematicator-core/numbers)
[![Latest Unstable Version](https://poser.pugx.org/mathematicator-core/numbers/v/unstable)](https://packagist.org/packages/mathematicator-core/numbers)
[![License: MIT](https://img.shields.io/badge/License-MIT-brightgreen.svg)](./LICENSE)
[![PHPStan Enabled](https://img.shields.io/badge/PHPStan-enabled%20L8-brightgreen.svg?style=flat)](https://phpstan.org/)

**A PHP library to safely store and represent numbers and its equivalents in PHP.**

Store lots of number types exactly (**integers, decimals, fractions**) and convert
them to each other. Expressions can be outputted as a **human string** (e.g. `1/2`)
or **LaTeX** (e.g. `\frac{1}{2}`).

It is highly recommended to make sure you have enabled [BCMath](https://www.php.net/manual/en/book.bc.php)
or [GMP](https://www.php.net/manual/en/book.gmp.php) extension on your PHP server for much
faster calculations.

## Installation

```bash
composer require mathematicator-core/numbers
```

## Features

- **SmartNumber** - Unified safe storage for all number types with
    an arbitrary precision. It supports comparisons.
    - **Entity\Number** Less magic universal arbitrary precision
    number storage with basic features.
- **Fractions:**
    - **Entity\Fraction** - Storage for simple or compound fraction that
    can consist from numbers and other expressions.
    - **Entity\FractionNumbersOnly** - Storage for simple or compound fraction
    that consists only from numbers and is directly computable.
- **LaTeX support:** ([What is LaTeX?](https://en.wikipedia.org/wiki/LaTeX))
    - **MathLatexBuilder** - Create valid LaTeX for mathematical expressions
    in simple way with a fluent interface.
    - **MathLatexToolkit** - Statical library for LaTeX expressions
    (includes constants, functions, operators etc.)
    - **MathLatexSnippet** - Storage for LaTeX syntax.
- **Human string support:**
    - **MathHumanStringBuilder** - same interface as MathLatexBuilder,
    but produces human strings
    - **MathHumanStringToolkit** - same interface as MathLatexToolkit,
    but produces human strings (e.g. `1/2*(3+1)`)
- **Set generators**
    - Primary numbers
    - Even numbers
    - Odd numbers
- **Converters:**
    - Array to fraction and back
    - Decimal to fraction
    - Fraction to human string
    - Fraction to LaTeX
    - Int to Roman and back
- **Calculation** - simple arithmetic operations ([brick/math](https://github.com/brick/math) decorator)

💡 **TIP:** You can use [mathematicator-core/tokenizer](https://github.com/mathematicator-core/tokenizer)
for advance user input string **tokenization** or [mathematicator-core/calculator](https://github.com/mathematicator-core/calculator)
for advance **calculations**.

## Usage

```php
use Brick\Math\RoundingMode;
use Mathematicator\Numbers\SmartNumber;

$smartNumber = SmartNumber::of('80.500');
echo $smartNumber->toBigDecimal(); // 80.500
echo $smartNumber->toFraction()->getNumerator(); // 161
echo $smartNumber->toFraction()->getDenominator(); // 2
echo Calculation::of($smartNumber)->multipliedBy(-4); // -322.000
echo Calculation::of($smartNumber)->multipliedBy(-4)->abs()->getResult()->toInt(); // 322
echo $smartNumber->toBigDecimal()->toScale(0, RoundingMode::HALF_UP); // 81

$smartNumber2 = SmartNumber::of('161/2');
echo $smartNumber2->toHumanString(); // 161/2
echo $smartNumber2->toHumanString()->plus(5)->equals('90.5'); // 161/2+10=90.5
echo $smartNumber2->toLatex(); // \frac{161}{2}
echo $smartNumber2->toBigDecimal();  // 80.5
```

## Recommended libraries

For safe operations with arbitrary length numbers we recommend to use:

- [brick/math](https://github.com/brick/math) - Arbitrary precision
arithmetic library for PHP with a simple interface.
- [PHP BC Math extension](https://www.php.net/manual/en/ref.bc.php) - Native PHP extension for
arbitrary precision computing.

### Working with money

Use one of these libraries if you work with money in your application.

- [brick/money](https://github.com/brick/money) - A money and currency library
with an interface like brick/math.
- [moneyphp/money](https://github.com/moneyphp/money) - Widely adopted PHP
implementation of Fowler's Money pattern.

## Why float is not safe?

**Float stores your number as an approximation with limited precision.**

You should never trust float to the last digit. Do not use floats
directly for checking equity if you rely on precision
(not only monetary calculations).

**Example:**
```php
$result = 0.1 + 0.2;
echo $result; // output: 0.3

echo ($result == 0.3) ? 'true' : 'false'; // output: false
```

[How is float stored in memory?](https://softwareengineering.stackexchange.com/a/215126/354697)

[See in PHP manual](https://www.php.net/manual/en/language.types.float.php)

[Read more about float on Wikipedia](https://en.wikipedia.org/wiki/Floating-point_arithmetic)

## Mathematicator Framework tools structure

The biggest advantage is that you can choose which layer best fits
your needs and start build on the top of it, immediately, without the need
to create everything by yourself. Our tools are tested for bugs
and tuned for performance, so you can save a significant amount
of your time, money, and effort.

Framework tend to be modular as much as possible, so you should be able
to create an extension on each layer and its sublayers.

**Mathematicator framework layers** ordered from the most concrete
one to the most abstract one:

<table>
    <tr>
        <td>
            <b>
            <a href="https://github.com/mathematicator-core/search">
                Search
            </a>
            </b>
        </td>
        <td>
            Modular search engine layer that calls its sublayers
            and creates user interface.
        </td>
    </tr>
    <tr>
        <td>
            <b>
            <a href="https://github.com/mathematicator-core/vizualizator">
                Vizualizator
            </a>
            </b>
        </td>
        <td>
            Elegant graphic visualizer that can render to
            SVG, PNG, JPG and Base64.<br />
            <u>Extensions:</u>
            <b>
            <a href="https://github.com/mathematicator-core/mandelbrot-set">
                Mandelbrot set generator
            </a>
            </b>
        </td>
    </tr>
    <tr>
        <td>
            <b>
            <a href="https://github.com/mathematicator-core/calculator">
                Calculator
            </a>
            </b>
        </td>
        <td>
            Modular advance calculations layer.
            <br />
            <u>Extensions:</u>
            <b>
            <a href="https://github.com/mathematicator-core/integral-solver">
                Integral Solver
            </a>,
            <a href="https://github.com/mathematicator-core/statistic">
                Statistics
            </a>
            </b>
        </td>
    </tr>
    <tr>
        <td>
            <b>
            <a href="https://github.com/mathematicator-core/engine">
                Engine
            </a>
            </b>
        </td>
        <td>
            Core logic layer that maintains basic controllers,
            DAOs, translator, common exceptions, routing etc.
        </td>
    </tr>
    <tr>
        <td>
            <b>
            <a href="https://github.com/mathematicator-core/tokenizer">
                Tokenizer
            </a>
            </b>
        </td>
        <td>
            Tokenizer that can convert string (user input / LaTeX) to numbers
            and operators.
        </td>
    </tr>
    <tr>
        <td>
            <b>
            <a href="https://github.com/mathematicator-core/numbers">
                Numbers
            </a>
            </b>
        </td>
        <td>
            Fast & secure storage for numbers with arbitrary precision.
            It supports Human string and LaTeX output and basic conversions.
        </td>
    </tr>
</table>

**Third-party packages:**

⚠️ Not guaranteed!

<table>
    <tr>
        <td>
            <b>
            <a href="https://github.com/cothema/math-php-api">
                REST API
            </a>
            </b>
        </td>
        <td>
            Install the whole pack as a REST API service
            on your server (Docker ready) or
            access it via public cloud REST API.
        </td>
    </tr>
</table>


## Contribution

> Please help to improve this documentation by sending a Pull request.

### Tests

All new contributions should have its unit tests in `/tests` directory.

Before you send a PR, please, check all tests pass.

This package uses [Nette Tester](https://tester.nette.org/).
You can run tests via command:
```bash
composer test
````

For benchmarking, we use [phpbench](https://github.com/phpbench/phpbench).
You can run benchmarks this way:
```bash
composer global require phpbench/phpbench @dev # only the first time
phpbench run
````

Before PR, please run complete code check via command:
```bash
composer cs:install # only the first time
composer fix # otherwise pre-commit hook can fail
````
