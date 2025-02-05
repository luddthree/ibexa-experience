<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\FieldType;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\MapLocation;

class MapLocationTest extends FieldTypeTest
{
    /**
     * Returns the field type under test.
     *
     * This method is used by all test cases to retrieve the field type under
     * test. Just create the FieldType instance using mocks from the provided
     * get*Mock() methods and/or custom get*Mock() implementations. You MUST
     * NOT take care for test case wide caching of the field type, just return
     * a new instance from this method!
     *
     * @return \Ibexa\Core\FieldType\FieldType
     */
    protected function createFieldTypeUnderTest()
    {
        $fieldType = new MapLocation\Type();
        $fieldType->setTransformationProcessor($this->getTransformationProcessorMock());

        return $fieldType;
    }

    /**
     * Returns the validator configuration schema expected from the field type.
     *
     * @return array
     */
    protected function getValidatorConfigurationSchemaExpectation()
    {
        return [];
    }

    /**
     * Returns the settings schema expected from the field type.
     *
     * @return array
     */
    protected function getSettingsSchemaExpectation()
    {
        return [];
    }

    /**
     * Returns the empty value expected from the field type.
     */
    protected function getEmptyValueExpectation()
    {
        return new MapLocation\Value();
    }

    public function provideInvalidInputForAcceptValue()
    {
        return [
            [
                'some string',
                InvalidArgumentException::class,
            ],
            [
                new MapLocation\Value(
                    [
                        'latitude' => 'foo',
                    ]
                ),
                InvalidArgumentException::class,
            ],
            [
                new MapLocation\Value(
                    [
                        'latitude' => 23.42,
                        'longitude' => 'bar',
                    ]
                ),
                InvalidArgumentException::class,
            ],
            [
                new MapLocation\Value(
                    [
                        'latitude' => 23.42,
                        'longitude' => 42.23,
                        'address' => [],
                    ]
                ),
                InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * Data provider for valid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to acceptValue(), 2. The expected return value from acceptValue().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          __FILE__,
     *          new BinaryFileValue( array(
     *              'path' => __FILE__,
     *              'fileName' => basename( __FILE__ ),
     *              'fileSize' => filesize( __FILE__ ),
     *              'downloadCount' => 0,
     *              'mimeType' => 'text/plain',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidInputForAcceptValue()
    {
        return [
            [
                null,
                new MapLocation\Value(),
            ],
            [
                [],
                new MapLocation\Value(),
            ],
            [
                new MapLocation\Value(),
                new MapLocation\Value(),
            ],
            [
                [
                    'latitude' => 23.42,
                    'longitude' => 42.23,
                    'address' => 'Nowhere',
                ],
                new MapLocation\Value(
                    [
                        'latitude' => 23.42,
                        'longitude' => 42.23,
                        'address' => 'Nowhere',
                    ]
                ),
            ],
            [
                [
                    'latitude' => 23,
                    'longitude' => 42,
                    'address' => 'Somewhere',
                ],
                new MapLocation\Value(
                    [
                        'latitude' => 23,
                        'longitude' => 42,
                        'address' => 'Somewhere',
                    ]
                ),
            ],
            [
                new MapLocation\Value(
                    [
                        'latitude' => 23.42,
                        'longitude' => 42.23,
                        'address' => 'Nowhere',
                    ]
                ),
                new MapLocation\Value(
                    [
                        'latitude' => 23.42,
                        'longitude' => 42.23,
                        'address' => 'Nowhere',
                    ]
                ),
            ],
        ];
    }

    /**
     * Provide input for the toHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to toHash(), 2. The expected return value from toHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) ),
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForToHash()
    {
        return [
            [
                new MapLocation\Value(),
                null,
            ],
            [
                new MapLocation\Value(
                    [
                        'latitude' => 23.42,
                        'longitude' => 42.23,
                        'address' => 'Nowhere',
                    ]
                ),
                [
                    'latitude' => 23.42,
                    'longitude' => 42.23,
                    'address' => 'Nowhere',
                ],
            ],
        ];
    }

    /**
     * Provide input to fromHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to fromHash(), 2. The expected return value from fromHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ),
     *          new BinaryFileValue(
     *              array(
     *                  'path' => 'some/file/here',
     *                  'fileName' => 'sindelfingen.jpg',
     *                  'fileSize' => 2342,
     *                  'downloadCount' => 0,
     *                  'mimeType' => 'image/jpeg',
     *              )
     *          )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForFromHash()
    {
        return [
            [
                null,
                new MapLocation\Value(),
            ],
            [
                [
                    'latitude' => 23.42,
                    'longitude' => 42.23,
                    'address' => 'Nowhere',
                ],
                new MapLocation\Value(
                    [
                        'latitude' => 23.42,
                        'longitude' => 42.23,
                        'address' => 'Nowhere',
                    ]
                ),
            ],
        ];
    }

    protected function provideFieldTypeIdentifier()
    {
        return 'ezgmaplocation';
    }

    public function provideDataForGetName(): array
    {
        return [
            [$this->getEmptyValueExpectation(), '', [], 'en_GB'],
            [new MapLocation\Value(['address' => 'Bag End, The Shire']), 'Bag End, The Shire', [], 'en_GB'],
        ];
    }
}

class_alias(MapLocationTest::class, 'eZ\Publish\Core\FieldType\Tests\MapLocationTest');
