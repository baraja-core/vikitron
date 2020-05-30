Robust PHP math calculator
==========================

![Integrity check](https://github.com/mathematicator-core/calculator/workflows/Integrity%20check/badge.svg)

Simple to use robust math library for symbolic-work with numbers, operations and patterns.

> Please help improve this documentation by sending a Pull request.

Install by Composer:

```
composer require mathematicator-core/calculator
```

Idea
----

Imagine you want compute some math problem, for instance:

```
(5 + 3) * (2 / (7 + 3))
```

How to compute it? Very simply:

```php
$calculator = new Calculator(/* some dependencies */);

echo $calculator->calculateString('(5 + 3) * (2 / (7 + 3))');
```

Method `calculateString()` return entity `CalculatorResult` what implements `__toString()` method.

Advance use is by array of tokens created by `Tokenizer`:

```php
$tokenizer = new Tokenizer(/* some dependencies */);

// Convert math formule to array of tokens:
$tokens = $tokenizer->tokenize('(5+3)*(2/(7+3))');

// Now you can convert tokens to more useful format:
$objectTokens = $tokenizer->tokensToObject($tokens);

$calculator->calculate($objectTokens);
```