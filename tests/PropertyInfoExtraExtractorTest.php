<?php

namespace Radebatz\PropertyInfoExtras\Tests;

use PHPUnit\Framework\TestCase;
use Radebatz\PropertyInfoExtras\Extractor\DocBlockCache;
use Radebatz\PropertyInfoExtras\Extractor\DocBlockMagicExtractor;
use Radebatz\PropertyInfoExtras\PropertyInfoExtraExtractor;
use Radebatz\PropertyInfoExtras\Tests\Models\SimplePopo;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Type;

class PropertyInfoExtraExtractorTest extends TestCase
{
    protected $propertyInfoExtractor;

    protected function setUp(): void
    {
        parent::setUp();

        $phpDocExtractor = new PhpDocExtractor();
        $phpDocMagicExtractor = new DocBlockMagicExtractor(new DocBlockCache());
        $reflectionExtractor = new ReflectionExtractor();

        $listExtractors = [
            $reflectionExtractor,
            $phpDocMagicExtractor,
        ];

        $typeExtractors = [
            $phpDocExtractor,
            $phpDocMagicExtractor,
            $reflectionExtractor,
        ];

        $accessExtractors = [
            $reflectionExtractor,
            $phpDocMagicExtractor,
        ];

        $this->propertyInfoExtraExtractor = new PropertyInfoExtraExtractor(
            $listExtractors,
            $typeExtractors,
            [],
            $accessExtractors
        );
    }

    public function typesData()
    {
        return [
            'pubString' => ['pubString', 'string'],
            'proString' => ['proString', 'string'],
            'priString' => ['priString', 'string'],
            'proInt' => ['proInt', 'int'],
            'proBool' => ['proBool', 'bool'],
            'simplePopo' => ['simplePopo', 'object', SimplePopo::class],
            'proIntArr' => ['proIntArr', 'array', null, Type::BUILTIN_TYPE_INT],
            'simplePopoArr' => ['simplePopoArr', 'array', null, Type::BUILTIN_TYPE_OBJECT, SimplePopo::class],
        ];
    }

    /** @test */
    public function allProperties()
    {
        $properties = $this->propertyInfoExtraExtractor->getAllProperties(Models\MagicPopo::class);

        $this->assertEquals(array_keys($this->typesData()), $properties);
    }

    /**
     * @test
     * @dataProvider typesData
     */
    public function allTypes($property, $buildInType, $class = null, $collectionValueType = null, $collectionValueClass = null)
    {
        $types = $this->propertyInfoExtraExtractor->getAllTypes(Models\MagicPopo::class, $property);

        $this->assertTrue(is_array($types));
        $this->assertCount(1, $types);
        $type = $types[0];
        $this->assertInstanceOf(Type::class, $type);
        $this->assertEquals($buildInType, $type->getBuiltinType());

        if ($class) {
            $this->assertEquals($class, $type->getClassName());
        }

        if ($collectionValueType) {
            $this->assertTrue($type->isCollection());
            $this->assertEquals($collectionValueType, $type->getCollectionValueType()->getBuiltinType());
            if (Type::BUILTIN_TYPE_OBJECT == $collectionValueType) {
                if ($collectionValueClass) {
                    $this->assertEquals($collectionValueClass, $type->getCollectionValueType()->getClassName());
                }
            }
        }
    }

    /**
     * @test
     * @dataProvider typesData
     */
    public function allReadable($property, $buildInType, $class = null, $collectionValueType = null, $collectionValueClass = null)
    {
        $readable = $this->propertyInfoExtraExtractor->isAllReadable(Models\MagicPopo::class, $property);

        $this->assertTrue($readable);
    }

    /**
     * @test
     * @dataProvider typesData
     */
    public function allWritable($property, $buildInType, $class = null, $collectionValueType = null, $collectionValueClass = null)
    {
        $writeable = $this->propertyInfoExtraExtractor->isAllWritable(Models\MagicPopo::class, $property);

        $this->assertTrue($writeable);
    }
}
