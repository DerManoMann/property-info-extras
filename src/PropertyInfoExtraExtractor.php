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

namespace Radebatz\PropertyInfoExtras;

use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyInitializableExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Adds to the {@see PropertyInfoExtractorInterface} implementation, adding methods to merge results of all
 * extractors.
 */
class PropertyInfoExtraExtractor extends PropertyInfoExtractor
{
    protected $listExtractors;
    protected $typeExtractors;
    protected $descriptionExtractors;
    protected $accessExtractors;
    protected $initializableExtractors;

    /**
     * @param iterable|PropertyListExtractorInterface[]          $listExtractors
     * @param iterable|PropertyTypeExtractorInterface[]          $typeExtractors
     * @param iterable|PropertyDescriptionExtractorInterface[]   $descriptionExtractors
     * @param iterable|PropertyAccessExtractorInterface[]        $accessExtractors
     * @param iterable|PropertyInitializableExtractorInterface[] $initializableExtractors
     */
    public function __construct(iterable $listExtractors = [], iterable $typeExtractors = [], iterable $descriptionExtractors = [], iterable $accessExtractors = [], iterable $initializableExtractors = [])
    {
        parent::__construct($listExtractors, $typeExtractors, $descriptionExtractors, $accessExtractors, $initializableExtractors);

        $this->listExtractors = $listExtractors;
        $this->typeExtractors = $typeExtractors;
        $this->descriptionExtractors = $descriptionExtractors;
        $this->accessExtractors = $accessExtractors;
        $this->initializableExtractors = $initializableExtractors;
    }

    /**
     * Gets the merged list of properties available for the given class.
     */
    public function getAllProperties($class, array $context = [])
    {
        $values = [];

        foreach ($this->listExtractors as $extractor) {
            if (null !== $value = $extractor->getProperties($class, $context)) {
                $values = array_merge($values, $value);
            }
        }

        return $values ?: null;
    }

    /**
     * Gets merged types of a property.
     */
    public function getAllTypes($class, $property, array $context = [])
    {
        $values = [];

        foreach ($this->typeExtractors as $extractor) {
            if (null !== $value = $extractor->getTypes($class, $property, $context)) {
                if (!$values) {
                    $values = $value;
                } else {
                    // simple only
                    if (1 === count($values) && 1 === count($value)) {
                        // merge where possible...
                        $current = $values[0];
                        $next = $value[0];

                        if ($current->getBuiltinType() === $next->getBuiltinType()) {
                            $values[0] = new Type(
                                $this->refine($current, $next, 'getBuiltinType'),
                                $current->isNullable() || $next->isNullable(),
                                $this->refine($current, $next, 'getClassName'),
                                $current->isCollection() || $next->isCollection(),
                                $this->refine($current, $next, 'getCollectionKeyType'),
                                $this->refine($current, $next, 'getCollectionValueType')
                            );
                        }
                    }
                }
            }
        }

        return $values ?: null;
    }

    protected function refine(Type $current, Type $next, string $method)
    {
        $cval = $current->{$method}();
        $nval = $next->{$method}();

        if (null === $cval) {
            return $nval;
        }

        if (null === $nval) {
            return $cval;
        }

        // both set...
        if ($cval !== $nval) {
            throw new \InvalidArgumentException(sprintf('Type mismatch: %s - %s/%s', $method, $cval, $nval));
        }

        return $cval;
    }

    /**
     * Is the property readable?
     * 
     * @return bool|null `true` if at least one extractor reports `true`.
     */
    public function isAllReadable($class, $property, array $context = [])
    {
        $readable = null;

        foreach ($this->accessExtractors as $extractor) {
            if (null !== $value = $extractor->isReadable($class, $property, $context)) {
                $readable = $readable || $value;
            }
        }

        return $readable;
    }

    /**
     * Is the property writable?
     * 
     * @return bool|null `true` if at least one extractor reports `true`.
     */
    public function isAllWritable($class, $property, array $context = [])
    {
        $writable = null;

        foreach ($this->accessExtractors as $extractor) {
            if (null !== $value = $extractor->isWritable($class, $property, $context)) {
                $writable = $writable || $value;
            }
        }

        return $writable;
    }
}
