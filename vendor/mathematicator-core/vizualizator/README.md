Vizualizator
============

![Integrity check](https://github.com/mathematicator-core/vizualizator/workflows/Integrity%20check/badge.svg)

Smart engine for elegant creating images and graphic visualizations.

Render to SVG, PNG and JPG. All output is in base64 format valid in HTML document.

Install
-------

By Composer:

```shell
composer require mathematicator-core/vizualizator
```

Usage
-----

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

Full simple short example
-------------------------

This example use short fluid-syntax. Final image size is `200x100`:

```php
echo (new Renderer)->createRequest(200, 100)
    ->addLine(10, 10, 35, 70, '#aaa')
    ->addLine(35, 70, 70, 35, 'red');
```
