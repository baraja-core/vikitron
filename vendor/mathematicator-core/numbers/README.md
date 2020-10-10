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
[![License: MIT](https://img.shields.io/badge/License-MIT-brightgreen.svg)](./LICENSE)
[![PHPStan Enabled](https://img.shields.io/badge/PHPStan-enabled%20L8-brightgreen.svg?style=flat)](https://phpstan.org/)


Library for numbers representation in PHP.

> Please help improve this documentation by sending a Pull request.

## Installation

Install via Composer:

```
composer require mathematicator-core/numbers
```

## Idea

Imagine you want store lots of number types exactly. For instance integers, fractions and user inputs (automatically normalized!).

Entity `SmartNumber` can storage all your numbers safely.

## Features

- SmartNumber
- Fraction
- MathLatexBuilder

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
