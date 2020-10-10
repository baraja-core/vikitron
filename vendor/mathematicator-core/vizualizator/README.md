# Vizualizator

[![Integrity check](https://github.com/mathematicator-core/vizualizator/workflows/Integrity%20check/badge.svg)](https://github.com/mathematicator-core/vizualizator/actions?query=workflow%3A%22Integrity+check%22)
[![codecov](https://codecov.io/gh/mathematicator-core/vizualizator/branch/master/graph/badge.svg)](https://codecov.io/gh/mathematicator-core/vizualizator)
[![Latest Stable Version](https://poser.pugx.org/mathematicator-core/vizualizator/v/stable)](https://packagist.org/packages/mathematicator-core/vizualizator)
[![Latest Unstable Version](https://poser.pugx.org/mathematicator-core/vizualizator/v/unstable)](https://packagist.org/packages/mathematicator-core/vizualizator)
[![License: MIT](https://img.shields.io/badge/License-MIT-brightgreen.svg)](./LICENSE)
[![PHPStan Enabled](https://img.shields.io/badge/PHPStan-enabled%20L8-brightgreen.svg?style=flat)](https://phpstan.org/)

Smart engine for creating elegant images and graphic visualizations.

Render to SVG, PNG and JPG. All output is in base64 format valid in HTML document.

## Installation

```shell
composer require mathematicator-core/vizualizator
```

## Usage

Imagine you can render all your images by simple objective request.

First inject `Renderer` to your script and create request:

```php
$renderer = new Renderer;
$request = $renderer->createRequest();
```

Now you can add some lines and more:

```php
$request->addLine(10, 10, 35, 70);
$request->addLine(35, 70, 70, 35);
```

And render to page (output is valid HTML code, `base64` or `svg` tag):

```php
// Render specific format:

echo $request->render(Renderer::FORMAT_PNG);
echo $request->render(Renderer::FORMAT_JPG);
echo $request->render(Renderer::FORMAT_SVG);

// Or use default renderer and __toString() method

echp $request;
```

## Full simple short example

This example use short fluid-syntax. Final image size is `200x100`:

```php
echo (new Renderer)->createRequest(200, 100)
    ->addLine(10, 10, 35, 70, '#aaa')
    ->addLine(35, 70, 70, 35, 'red');
```

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

### Tests

All new contributions should have its unit tests in `/tests` directory.

Before you send a PR, please, check all tests pass.

This package uses [Nette Tester](https://tester.nette.org/). You can run tests via command:
```bash
composer test
````

Before PR, please run complete code check via command:
```bash
composer cs:install # only first time
composer fix # otherwise pre-commit hook can fail
````
