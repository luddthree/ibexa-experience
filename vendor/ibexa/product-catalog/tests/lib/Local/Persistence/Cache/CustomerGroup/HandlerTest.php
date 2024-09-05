<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Cache\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Cache\CustomerGroup\Handler as CacheHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup;
use Ibexa\Tests\ProductCatalog\Local\Persistence\Cache\AbstractCacheHandlerTest;
use InvalidArgumentException;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Handler
 */
final class HandlerTest extends AbstractCacheHandlerTest
{
    private const CUSTOMER_GROUP_ID = 1;
    private const CUSTOMER_GROUP_IDENTIFIER = 'foo';
    private const CUSTOMER_GROUP_DISCOUNT = '0';

    public function getInnerHandler(): HandlerInterface
    {
        return $this->createMock(HandlerInterface::class);
    }

    /**
     * @param object|\Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface $innerHandler
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
            $this->cacheMock,
            $this->cacheIdentifierGeneratorMock,
            $this->loggerMock,
        );
    }

    public function providerForUnCachedMethods(): iterable
    {
        $customerGroup = new CustomerGroup();
        $customerGroup->id = self::CUSTOMER_GROUP_ID;

        $createStruct = new CustomerGroupCreateStruct(
            self::CUSTOMER_GROUP_IDENTIFIER,
            [],
            [],
            self::CUSTOMER_GROUP_DISCOUNT
        );

        $updateStruct = new CustomerGroupUpdateStruct(self::CUSTOMER_GROUP_ID);

        yield [
            'create',
            [
                $createStruct,
            ],
        ];

        yield [
            'findBy',
            [
                [],
            ],
            null,
            null,
            null,
            null,
            [],
        ];

        yield [
            'exists',
            [
                self::CUSTOMER_GROUP_ID,
            ],
            null,
            null,
            null,
            null,
            false,
        ];

        yield [
            'findAll',
            [
                1, 0,
            ],
            null,
            null,
            null,
            null,
            [],
        ];

        yield [
            'update',
            [
                $updateStruct,
            ],
            [
                ['customer_group', [self::CUSTOMER_GROUP_ID], false],
            ],
            null,
            ['cg-1'],
        ];

        yield [
            'delete',
            [
                self::CUSTOMER_GROUP_ID,
            ],
            [
                ['customer_group', [self::CUSTOMER_GROUP_ID], false],
            ],
            null,
            ['cg-1'],
        ];
    }

    public function providerForCachedLoadMethodsHit(): iterable
    {
        $customerGroup = new CustomerGroup();
        $customerGroup->id = self::CUSTOMER_GROUP_ID;

        yield [
            'find',
            [
                self::CUSTOMER_GROUP_ID,
            ],
            'ibx-cg-' . self::CUSTOMER_GROUP_ID,
            null,
            null,
            [['customer_group', [self::CUSTOMER_GROUP_ID], true]],
            ['ibx-cg-' . self::CUSTOMER_GROUP_ID],
            $customerGroup,
        ];
    }

    public function providerForCachedLoadMethodsMiss(): iterable
    {
        $customerGroup = new CustomerGroup();
        $customerGroup->id = self::CUSTOMER_GROUP_ID;

        yield [
            'find',
            [
                self::CUSTOMER_GROUP_ID,
            ],
            'ibx-cg-' . self::CUSTOMER_GROUP_ID,
            [
                ['customer_group', [self::CUSTOMER_GROUP_ID], false],
            ],
            ['cg-1'],
            [['customer_group', [self::CUSTOMER_GROUP_ID], true]],
            ['ibx-cg-' . self::CUSTOMER_GROUP_ID],
            $customerGroup,
        ];
    }
}
