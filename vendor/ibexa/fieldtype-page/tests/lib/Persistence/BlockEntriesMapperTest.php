<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Persistence;

use Ibexa\FieldTypePage\Persistence\BlockEntriesMapper;
use Ibexa\FieldTypePage\Persistence\BlockEntry;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class BlockEntriesMapperTest extends TestCase
{
    private const ID = 10;
    private const USER_ID = 20;
    private const CONTENT_ID = 30;
    private const VERSION_NUMBER = 40;
    private const ACTION_TIMESTAMP = 1602670804;
    private const BLOCK_NAME = '__BLOCK_NAME__';
    private const BLOCK_TYPE = '__BLOCK_TYPE__';

    /**
     * @var \Ibexa\FieldTypePage\Persistence\BlockEntriesMapper
     */
    private $blockEntriesMapper;

    protected function setUp(): void
    {
        $this->blockEntriesMapper = new BlockEntriesMapper();
    }

    public function providerForTestMap(): iterable
    {
        yield 'when raw data are valid' => [
            $rawData = [
                [
                    'id' => self::ID,
                    'user_id' => self::USER_ID,
                    'content_id' => self::CONTENT_ID,
                    'version_number' => self::VERSION_NUMBER,
                    'action_timestamp' => self::ACTION_TIMESTAMP,
                    'block_name' => self::BLOCK_NAME,
                    'block_type' => self::BLOCK_TYPE,
                ],
            ],
            $result = [
                new BlockEntry(
                    [
                        'id' => self::ID,
                        'userId' => self::USER_ID,
                        'contentId' => self::CONTENT_ID,
                        'versionNumber' => self::VERSION_NUMBER,
                        'actionTimestamp' => self::ACTION_TIMESTAMP,
                        'blockName' => self::BLOCK_NAME,
                        'blockType' => self::BLOCK_TYPE,
                    ]
                ),
            ],
        ];

        yield 'when raw data are not valid - lack of id' => [
            $rawData = [
                [
                    'user_id' => self::USER_ID,
                    'content_id' => self::CONTENT_ID,
                    'version_number' => self::VERSION_NUMBER,
                    'action_timestamp' => self::ACTION_TIMESTAMP,
                    'block_name' => self::BLOCK_NAME,
                    'block_type' => self::BLOCK_TYPE,
                ],
            ],
            $result = [],
        ];

        yield 'when raw data are not valid - lack of content_id' => [
            $rawData = [
                [
                    'id' => self::ID,
                    'user_id' => self::USER_ID,
                    'version_number' => self::VERSION_NUMBER,
                    'action_timestamp' => self::ACTION_TIMESTAMP,
                    'block_name' => self::BLOCK_NAME,
                    'block_type' => self::BLOCK_TYPE,
                ],
            ],
            $result = [],
        ];

        yield 'when raw data are not valid - lack of version_number' => [
            $rawData = [
                [
                    'id' => self::ID,
                    'user_id' => self::USER_ID,
                    'content_id' => self::CONTENT_ID,
                    'action_timestamp' => self::ACTION_TIMESTAMP,
                    'block_name' => self::BLOCK_NAME,
                    'block_type' => self::BLOCK_TYPE,
                ],
            ],
            $result = [],
        ];

        yield 'when raw data are not valid - lack of action_timestamp' => [
            $rawData = [
                [
                    'id' => self::ID,
                    'user_id' => self::USER_ID,
                    'content_id' => self::CONTENT_ID,
                    'version_number' => self::VERSION_NUMBER,
                    'block_name' => self::BLOCK_NAME,
                    'block_type' => self::BLOCK_TYPE,
                ],
            ],
            $result = [],
        ];

        yield 'when raw data are not valid - lack of block_name' => [
            $rawData = [
                [
                    'id' => self::ID,
                    'user_id' => self::USER_ID,
                    'content_id' => self::CONTENT_ID,
                    'version_number' => self::VERSION_NUMBER,
                    'action_timestamp' => self::ACTION_TIMESTAMP,
                    'block_type' => self::BLOCK_TYPE,
                ],
            ],
            $result = [],
        ];

        yield 'when raw data are not valid - lack of block_type' => [
            $rawData = [
                [
                    'id' => self::ID,
                    'user_id' => self::USER_ID,
                    'content_id' => self::CONTENT_ID,
                    'version_number' => self::VERSION_NUMBER,
                    'action_timestamp' => self::ACTION_TIMESTAMP,
                    'block_name' => self::BLOCK_NAME,
                ],
            ],
            $result = [],
        ];

        yield 'when raw data includes both valid and invalid data' => [
            $rawData = [
                [
                    'id' => self::ID,
                    'user_id' => self::USER_ID,
                    'content_id' => self::CONTENT_ID,
                    'version_number' => self::VERSION_NUMBER,
                    'action_timestamp' => self::ACTION_TIMESTAMP,
                    'block_name' => self::BLOCK_NAME,
                    'block_type' => self::BLOCK_TYPE,
                ],
                [
                    'user_id' => '__USER_ID_2_',
                    'content_id' => '__CONTENT_ID_2__',
                    'block_name' => '__BLOCK_NAME_2__',
                ],
            ],
            $result = [
                new BlockEntry(
                    [
                        'id' => self::ID,
                        'userId' => self::USER_ID,
                        'contentId' => self::CONTENT_ID,
                        'versionNumber' => self::VERSION_NUMBER,
                        'actionTimestamp' => self::ACTION_TIMESTAMP,
                        'blockName' => self::BLOCK_NAME,
                        'blockType' => self::BLOCK_TYPE,
                    ]
                ),
            ],
        ];
    }

    /**
     * @dataProvider providerForTestMap
     */
    public function testMap(array $rawData, array $expectedResult)
    {
        Assert::assertEquals(
            $expectedResult,
            $this->blockEntriesMapper->map($rawData)
        );
    }
}

class_alias(BlockEntriesMapperTest::class, 'EzSystems\EzPlatformPageFieldType\Tests\Persistence\BlockEntriesMapperTest');
