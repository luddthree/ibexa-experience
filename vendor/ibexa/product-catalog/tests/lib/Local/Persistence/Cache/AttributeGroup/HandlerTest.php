<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Cache\AttributeGroup;

use Ibexa\ProductCatalog\Local\Persistence\Cache\AttributeGroup\Handler as CacheHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupUpdateStruct;
use Ibexa\Tests\ProductCatalog\Local\Persistence\Cache\AbstractCacheHandlerTest;
use InvalidArgumentException;

final class HandlerTest extends AbstractCacheHandlerTest
{
    private const ATTRIBUTE_GROUP_ID = 1;
    private const ATTRIBUTE_GROUP_IDENTIFIER = 'foo';

    public function getInnerHandler(): HandlerInterface
    {
        return $this->createMock(HandlerInterface::class);
    }

    /**
     * @param object|\Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\HandlerInterface $innerHandler
     */
    public function getPersistenceCacheHandler(object $innerHandler): CacheHandler
    {
        if (!$innerHandler instanceof HandlerInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s to be passed to %s, received %s',
                    HandlerInterface::class,
                    __METHOD__,
                    get_class($innerHandler),
                )
            );
        }

        return new CacheHandler(
            $innerHandler,
            $this->loggerMock,
            $this->cacheMock,
            $this->cacheIdentifierGeneratorMock,
            $this->cacheIdentifierSanitizer
        );
    }

    public function providerForUnCachedMethods(): iterable
    {
        yield [
            'findMatching',
            [
                'namePrefix', 0, 1,
            ],
            null,
            null,
            null,
            null,
            [],
        ];
        yield [
            'countMatching',
            [
                'namePrefix',
            ],
            null,
            null,
            null,
            null,
            0,
        ];
        yield [
            'create',
            [
                new AttributeGroupCreateStruct(),
            ],
            null,
            null,
            null,
            null,
            null,
        ];
        yield [
            'update',
            [
                new AttributeGroupUpdateStruct(['id' => self::ATTRIBUTE_GROUP_ID]),
            ],
            [
                ['attribute_group', [self::ATTRIBUTE_GROUP_ID], false],
            ],
            null,
            ['ag-1'],
        ];
        yield [
            'deleteByIdentifier',
            [
                self::ATTRIBUTE_GROUP_IDENTIFIER,
            ],
            [
                ['attribute_group', [self::ATTRIBUTE_GROUP_IDENTIFIER], false],
            ],
            null,
            ['ag-1'],
        ];
    }

    public function providerForCachedLoadMethodsHit(): iterable
    {
        $group = new AttributeGroup(['id' => self::ATTRIBUTE_GROUP_ID]);

        yield [
            'load',
            [
                self::ATTRIBUTE_GROUP_ID,
            ],
            'ibx-ag-' . self::ATTRIBUTE_GROUP_ID,
            null,
            null,
            [['attribute_group', [self::ATTRIBUTE_GROUP_ID], true]],
            ['ibx-ag-' . self::ATTRIBUTE_GROUP_ID],
            $group,
        ];
        yield [
            'loadByIdentifier',
            [
                self::ATTRIBUTE_GROUP_IDENTIFIER,
            ],
            'ibx-ag-' . self::ATTRIBUTE_GROUP_IDENTIFIER,
            null,
            null,
            [['attribute_group', [self::ATTRIBUTE_GROUP_IDENTIFIER], true]],
            ['ibx-ag-' . self::ATTRIBUTE_GROUP_IDENTIFIER],
            $group,
        ];
    }

    public function providerForCachedLoadMethodsMiss(): iterable
    {
        $group = new AttributeGroup(
            [
                'id' => self::ATTRIBUTE_GROUP_ID,
                'identifier' => self::ATTRIBUTE_GROUP_IDENTIFIER,
            ]
        );

        yield [
            'load',
            [
                self::ATTRIBUTE_GROUP_ID,
            ],
            'ibx-ag-' . self::ATTRIBUTE_GROUP_ID,
            [
                ['attribute_group', [self::ATTRIBUTE_GROUP_ID], false],
                ['attribute_group', [self::ATTRIBUTE_GROUP_IDENTIFIER], false],
            ],
            ['ag-1', 'ag-foo'],
            [['attribute_group', [self::ATTRIBUTE_GROUP_ID], true]],
            ['ibx-ag-' . self::ATTRIBUTE_GROUP_ID],
            $group,
        ];
        yield [
            'loadByIdentifier',
            [
                self::ATTRIBUTE_GROUP_IDENTIFIER,
            ],
            'ibx-ag-' . self::ATTRIBUTE_GROUP_IDENTIFIER,
            [
                ['attribute_group', [self::ATTRIBUTE_GROUP_ID], false],
                ['attribute_group', [self::ATTRIBUTE_GROUP_IDENTIFIER], false],
            ],
            ['ag-1', 'ag-foo'],
            [['attribute_group', [self::ATTRIBUTE_GROUP_IDENTIFIER], true]],
            ['ibx-ag-' . self::ATTRIBUTE_GROUP_IDENTIFIER],
            $group,
        ];
    }
}
