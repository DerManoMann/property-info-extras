<?php

declare(strict_types=1);

/*
* This file is part of the PropertyInfoExtras library.
*
* (c) Martin Rademacher <mano@radebatz.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Radebatz\PropertyInfoExtras\Tests\Models;

/**
 * @method string       getProString()
 * @method void         setProString(?string $proString)
 * @method string       getPriString()
 * @method void         setPriString(?string $priString)
 * @method int          getProInt()
 * @method void         setProInt(int $proInt)
 * @method bool         getProBool()
 * @method void         setProBool(bool $proBool)
 * @method SimplePopo   getSimplePopo()
 * @method void         setSimplePopo(SimplePopo $simplePopo)
 * @method int[]        getProIntArr()
 * @method void         setProIntArr(integer[] $proIntArr)
 * @method SimplePopo[] getSimplePopoArr()
 * @method void         setSimplePopoArr(SimplePopo[] $simplePopoArr)
 */
class MagicPopo implements \JsonSerializable
{
    /** @var string */
    public $pubString = null;
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

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'pubString' => $this->pubString,
            'proString' => $this->getProString(),
            'priString' => $this->getPriString(),
            'proInt' => $this->getProInt(),
            'proBool' => $this->getProBool(),
            'simplePopo' => $this->getSimplePopo(),
            'proIntArr' => $this->getProIntArr(),
            'simplePopoArr' => $this->getSimplePopoArr(),
        ];
    }
}
