Robust PHP math integral solver
===============================

Get instance of `IntegralSolver` and compute:

```php
$solver = new IntegralSolver(/* some dependencies */);

// Process simple input:
$solver->process('1 + x');
```

All results can be renderer as LaTeX or returned as array of tokens for future computation.

All dependencies you can get by [Package manager](https://github.com/baraja-core/package-manager).

Fully compatible with `Nette 3.0` and `PHP 7.1`.