<?php

namespace Radebatz\PropertyInfoExtras;

use Symfony\Component\PropertyInfo\Type;

/**
 * Adds to the {@see PropertyInfoExtractorInterface} implementation, adding methods to merge results of all
 * extractors.
 */
interface PropertyInfoExtraExtractorInterface
{
    /**
     * Gets the merged list of properties available for the given class.
     *
     * @return string[]|null
     */
    public function getAllProperties($class, array $context = []);

    /**
     * Gets merged types of a property.
     *
     * @return Type[]|null
     */
    public function getAllTypes($class, $property, array $context = []);

    /**
     * Is the property readable?
     *
     * @return bool|null
     */
    public function isAllReadable($class, $property, array $context = []);

    /**
     * Is the property writable?
     *
     * @return bool|null
     */
    public function isAllWritable($class, $property, array $context = []);
}
