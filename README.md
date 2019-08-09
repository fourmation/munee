# Munee: Standalone PHP Asset Optimisation & Manipulation

On-The-Fly Image Resizing, On-the-fly LESS, SCSS, CoffeeScript Compiling, CSS & JavaScript Combining/Minifying, and Smart Client Side and Server Side Caching

This is a fork of https://github.com/meenie/munee and is not available on Packagist.

## Sample Usage

```php
require 'vendor/autoload.php';

use \Fourmation\Munee\Dispatcher;
use \Fourmation\Munee\Request;

echo Dispatcher::run(new Request([
    'css' => [ 'lessifyAllCss' => true ],
    'image' => [
        'checkReferrer' => false,
        'placeholders' => [ '*' => WEBROOT . DS . 'images' . DS . 'placeholder-image.jpg' ],
    ]
]));
```

## Unit Tests

Tests are located in `/tests` and can be run with `./vendor/bin/phpunit`.
