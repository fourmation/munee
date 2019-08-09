# Munee: Standalone PHP Asset Optimisation & Manipulation

| Branch | Build | Coverage |
| ------ | ----- | -------- |
| [master][master] | [![Build Status][master-build-travis-badge]][travis] | Not Installed |
| [develop][develop] | [![Build Status][develop-build-travis-badge]][travis] | Not Installed |

<!-- Links -->
[travis]: https://travis-ci.com/fourmation/munee
[master]: https://github.com/fourmation/munee/tree/master
[master-build-travis-badge]: https://travis-ci.com/fourmation/munee.svg?branch=master
[develop]: https://github.com/fourmation/munee/tree/develop
[develop-build-travis-badge]: https://travis-ci.com/fourmation/munee.svg?branch=develop

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
You'll need to have XDebug installed, as it is required to test response headers.
