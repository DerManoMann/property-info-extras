<?php

namespace Radebatz\PropertyInfoExtras\Tests;

use PHPUnit\Framework\TestCase;
use Radebatz\PropertyInfoExtras\PropertyInfoExtraExtractorAdapter;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class PropertyInfoExtraExtractorAdapterTest extends TestCase
{
    protected $propertyInfoExtraExtractor;

    protected function setUp(): void
    {
        parent::setUp();

        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();

        $listExtractors = [
            $reflectionExtractor,
        ];

        $typeExtractors = [
            $phpDocExtractor,
            $reflectionExtractor,
        ];

        $accessExtractors = [
            $reflectionExtractor,
        ];

        $this->propertyInfoExtraExtractor = new PropertyInfoExtraExtractorAdapter(
            new PropertyInfoExtractor(
                $listExtractors,
                $typeExtractors,
                [],
                $accessExtractors
            )
        );
    }

    /** @test */
    public function allProperties()
    {
        $this->assertEquals(
            $this->propertyInfoExtraExtractor->getProperties(Models\SimplePopo::class),
            $this->propertyInfoExtraExtractor->getAllProperties(Models\SimplePopo::class)
        );
    }

    /** @test */
    public function allTypes()
    {
        $this->assertEquals(
            $this->propertyInfoExtraExtractor->getTypes(Models\SimplePopo::class, 'name'),
            $this->propertyInfoExtraExtractor->getAllTypes(Models\SimplePopo::class, 'name')
        );
    }

    /** @test */
    public function isAllReadable()
    {
        $this->assertEquals(
            $this->propertyInfoExtraExtractor->isReadable(Models\SimplePopo::class, 'name'),
            $this->propertyInfoExtraExtractor->isAllReadable(Models\SimplePopo::class, 'name')
        );
    }

    /** @test */
    public function isAllWritable()
    {
        $this->assertEquals(
            $this->propertyInfoExtraExtractor->isWritable(Models\SimplePopo::class, 'name'),
            $this->propertyInfoExtraExtractor->isAllWritable(Models\SimplePopo::class, 'name')
        );
    }
}
