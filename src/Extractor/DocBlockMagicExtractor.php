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

namespace Radebatz\PropertyInfoExtras\Extractor;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Component\PropertyInfo\PropertyAccessExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Util\PhpDocTypeHelper;

/**
 * Magic method docblock extractor.
 */
class DocBlockMagicExtractor implements PropertyListExtractorInterface, PropertyTypeExtractorInterface, PropertyAccessExtractorInterface
{
    protected $dockBlockCache;

    protected $dockTypeHelper;

    /** @var string[] */
    protected $properties = [];

    public function __construct(?DocBlockCache $dockBlockCache = null)
    {
        $this->dockBlockCache = $dockBlockCache ?: new DocBlockCache();
        $this->dockTypeHelper = new PhpDocTypeHelper();
    }

    /**
     * @inheritdoc
     */
    public function getProperties($class, array $context = [])
    {
        if (array_key_exists($class, $this->properties)) {
            return $this->properties[$class];
        }

        if (!$docBlock = $this->dockBlockCache->getClassDocBlock($class)) {
            return null;
        }

        $properties = [];

        /** @var Method $method */
        foreach ($docBlock->getTagsByName('method') as $method) {
            if (!($method instanceof Method) || $method->isStatic()) {
                continue;
            }

            $propertyName = $this->getPropertyName($method->getMethodName());
            if ($propertyName && !preg_match('/^[A-Z]{2,}/', $propertyName)) {
                $propertyName = lcfirst($propertyName);
                $properties[$propertyName] = $propertyName;
            }
        }

        return $this->properties[$class] = ($properties ? array_values($properties) : null);
    }

    /**
     * @inheritdoc
     */
    public function getTypes($class, $property, array $context = [])
    {
        if (!$docBlock = $this->dockBlockCache->getClassDocBlock($class)) {
            return null;
        }

        $properties = $this->getProperties($class, $context);
        if (null === $properties || !in_array($property, $properties)) {
            return null;
        }

        $type = null;
        $ucProperty = ucfirst($property);

        /** @var DocBlock\Tags\Method $method */
        foreach ($docBlock->getTagsByName('method') as $method) {
            if (!($method instanceof Method)) {
                continue;
            }

            $methodName = $method->getMethodName();

            foreach ($this->dockBlockCache->getMutatorPrefixes() as $mutatorPrefix) {
                if ($mutatorPrefix . $ucProperty == $methodName) {
                    if ($arguments = $method->getArguments()) {
                        $argument = $arguments[0];
                        $magicType = $argument['type'];

                        $type = $this->dockTypeHelper->getTypes($magicType);
                        break;
                    }
                }
            }
        }

        return $type ?: null;
    }

    protected function getPropertyName(string $methodName): ?string
    {
        $pattern = implode('|', array_merge($this->dockBlockCache->getAccessorPrefixes(), $this->dockBlockCache->getMutatorPrefixes()));

        if ('' !== $pattern && preg_match('/^(' . $pattern . ')(.+)$/i', $methodName, $matches)) {
            return $matches[2];
        }

        return null;
    }

    protected function hasPrefixMethod($class, $property, array $prefixes, array $context = [])
    {
        if (!$docBlock = $this->dockBlockCache->getClassDocBlock($class)) {
            return null;
        }

        $properties = $this->getProperties($class, $context);
        if (!in_array($property, $properties)) {
            return null;
        }

        $type = null;
        $ucProperty = ucfirst($property);

        /** @var DocBlock\Tags\Method $method */
        foreach ($docBlock->getTagsByName('method') as $method) {
            $methodName = $method->getMethodName();

            foreach ($prefixes as $prefix) {
                if ($prefix . $ucProperty == $methodName) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function isReadable($class, $property, array $context = [])
    {
        return $this->hasPrefixMethod($class, $property, $this->dockBlockCache->getAccessorPrefixes(), $context);
    }

    /**
     * @inheritdoc
     */
    public function isWritable($class, $property, array $context = [])
    {
        return $this->hasPrefixMethod($class, $property, $this->dockBlockCache->getMutatorPrefixes(), $context);
    }
}
