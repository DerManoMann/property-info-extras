<?php

namespace Radebatz\PropertyInfoExtras;

use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyInitializableExtractorInterface;

/**
 * Adapter to allow to use a {@see PropertyInfoExtractor} as {@see PropertyInfoExtraExtractorInterface}.
 */
class PropertyInfoExtraExtractorAdapter implements PropertyInfoExtraExtractorInterface, PropertyInfoExtractorInterface, PropertyInitializableExtractorInterface
{
    /** @var PropertyInfoExtractor */
    protected $adaptee;

    public function __construct(PropertyInfoExtractor $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    /**
     * @inheritdoc
     */
    public function getAllProperties($class, array $context = [])
    {
        return $this->getProperties($class, $context);
    }

    /**
     * @inheritdoc
     */
    public function getAllTypes($class, $property, array $context = [])
    {
        return $this->getTypes($class, $property, $context);
    }

    /**
     * @inheritdoc
     */
    public function isAllReadable($class, $property, array $context = [])
    {
        return $this->isReadable($class, $property, $context);
    }

    /**
     * @inheritdoc
     */
    public function isAllWritable($class, $property, array $context = [])
    {
        return $this->isWritable($class, $property, $context);
    }

    /**
     * @inheritdoc
     */
    public function isReadable($class, $property, array $context = [])
    {
        return $this->adaptee->isReadable($class, $property, $context);
    }

    /**
     * @inheritdoc
     */
    public function isWritable($class, $property, array $context = [])
    {
        return $this->adaptee->isWritable($class, $property, $context);
    }

    /**
     * @inheritdoc
     */
    public function getShortDescription($class, $property, array $context = [])
    {
        return $this->adaptee->getShortDescription($class, $property, $context);
    }

    /**
     * @inheritdoc
     */
    public function getLongDescription($class, $property, array $context = [])
    {
        return $this->adaptee->getLongDescription($class, $property, $context);
    }

    /**
     * @inheritdoc
     */
    public function isInitializable(string $class, string $property, array $context = []): ?bool
    {
        return $this->adaptee->isInitializable($class, $property, $context);
    }

    /**
     * @inheritdoc
     */
    public function getProperties($class, array $context = [])
    {
        return $this->adaptee->getProperties($class, $context);
    }

    /**
     * @inheritdoc
     */
    public function getTypes($class, $property, array $context = [])
    {
        return $this->adaptee->getTypes($class, $property, $context);
    }
}
