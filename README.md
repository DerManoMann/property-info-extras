# Property Info Extras #
Extensions for the Symfony property-info component.
* Magic class method extractor [DocBlockMagicExtractor](src/Extractor/DocBlockMagicExtractor.php)
* Support for merging results of multiple extractors where possible

[![Build Status](https://travis-ci.org/DerManoMann/property-info-extras.png)](https://travis-ci.org/DerManoMann/property-info-extras)
[![Coverage Status](https://coveralls.io/repos/github/DerManoMann/property-info-extras/badge.svg)](https://coveralls.io/github/DerManoMann/property-info-extras)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Requirements ##
* [PHP 7.1 or higher](http://www.php.net/)

## Installation ##

You can use **Composer** or simply **Download the Release**

### Composer ###

The preferred method is via [composer](https://getcomposer.org). Follow the
[installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have
composer installed.

Once composer is installed, execute the following command in your project root to install this library:

```sh
composer require radebatz/property-info-extras
```

## Usage ##
### `Radebatz\PropertyInfoExtras\Extractor\DocBlockMagicExtractor` ###
```php
<?php
use Radebatz\PropertyInfoExtras\Extractor\DocBlockCache;
use Radebatz\PropertyInfoExtras\Extractor\DocBlockMagicExtractor;

/**
 * @method string getProString()
 * @method void setProString(?string $proString)
 */
class MagicPopo {
    protected $properties = [];

    public function __call($method, $args)
    {
        $name = lcfirst(substr($method, 3));

        if (0 == count($args)) {
            if (0 === strpos($method, 'get')) {
                return array_key_exists($name, $this->properties) ? $this->properties[$name] : null;
            }
        } elseif (1 == count($args)) {
            if (0 === strpos($method, 'set')) {
                $this->properties[$name] = $args[0];

                return;
            }
        }

        throw new \RuntimeException(sprintf('Invalid method on: %s: method: "%s"', get_class($this), $method));
    }
}

$phpDocMagicExtractor = new DocBlockMagicExtractor(new DocBlockCache());
$properties = $phpDocMagicExtractor->getProperties(MagicPopo::class);
// ['proString']
```

### `Radebatz\PropertyInfoExtras\PropertyInfoExtraExtractor` ###
Same as documented in [The PropertyInfo Component](https://symfony.com/doc/current/components/property_info.html)
expect that `Radebatz\PropertyInfoExtras\PropertyInfoExtraExtractor` provides the following additional `xxAllxxx()` methods:
* `getAllProperties()`

  Total of properties reported. Order of extractors is relevant (last one wins).

* `getAllTypes()`

  Total of types reported. Merging is done on property level only in cases where later extractors
  add to the already extracted info (first one wins).
* `isAllReadable()`

  `true` if at least one extractor returns `true`.
* `isAllWritable()`

  `true` if at least one extractor returns `true`.
