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

namespace Radebatz\PropertyInfoExtras\Tests\Extractor;

use PHPUnit\Framework\TestCase;
use Radebatz\PropertyInfoExtras\Extractor\DocBlockCache;
use Radebatz\PropertyInfoExtras\Extractor\DocBlockMagicExtractor;
use Radebatz\PropertyInfoExtras\Tests\Models;
use Radebatz\PropertyInfoExtras\Tests\Models\SimplePopo;
use Symfony\Component\PropertyInfo\Type;

class DocBlockMagicExtractorTest extends TestCase
{
    /** @test */
    public function properties()
    {
        $phpDocMagicExtractor = new DocBlockMagicExtractor(new DocBlockCache());
        $properties = $phpDocMagicExtractor->getProperties(Models\MagicPopo::class);

        $this->assertEquals(array_keys($this->typesData()), $properties);
    }

    public function typesData()
    {
        return [
            'proString' => ['proString', 'string'],
            'priString' => ['priString', 'string'],
            'proInt' => ['proInt', 'int'],
            'proBool' => ['proBool', 'bool'],
            'simplePopo' => ['simplePopo', 'object', SimplePopo::class],
            'proIntArr' => ['proIntArr', 'array', null, Type::BUILTIN_TYPE_INT],
            'simplePopoArr' => ['simplePopoArr', 'array', null, Type::BUILTIN_TYPE_OBJECT, SimplePopo::class],
        ];
    }

    /**
     * @test
     * @dataProvider typesData
     */
    public function types($property, $buildInType, $class = null, $collectionValueType = null, $collectionValueClass = null)
    {
        $phpDocMagicExtractor = new DocBlockMagicExtractor(new DocBlockCache());
        $types = $phpDocMagicExtractor->getTypes(Models\MagicPopo::class, $property);

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
    public function readable($property, $buildInType, $class = null, $collectionValueType = null, $collectionValueClass = null)
    {
        $phpDocMagicExtractor = new DocBlockMagicExtractor(new DocBlockCache());

        $this->assertTrue($phpDocMagicExtractor->isReadable(Models\MagicPopo::class, $property));
    }

    /**
     * @test
     * @dataProvider typesData
     */
    public function writable($property, $buildInType, $class = null, $collectionValueType = null, $collectionValueClass = null)
    {
        $phpDocMagicExtractor = new DocBlockMagicExtractor(new DocBlockCache());

        $this->assertTrue($phpDocMagicExtractor->isWritable(Models\MagicPopo::class, $property));
    }

    /** @test */
    public function access()
    {
        $phpDocMagicExtractor = new DocBlockMagicExtractor(new DocBlockCache());

        $this->assertNull($phpDocMagicExtractor->isWritable(Models\MagicPopo::class, 'foo'));
        $this->assertNull($phpDocMagicExtractor->isWritable(Models\MagicPopo::class, 'pubString'));
    }
}
