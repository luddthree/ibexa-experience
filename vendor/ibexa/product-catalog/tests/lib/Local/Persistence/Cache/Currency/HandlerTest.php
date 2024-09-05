<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Cache\Currency;

use Ibexa\ProductCatalog\Local\Persistence\Cache\Currency\Handler as CacheHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyUpdateStruct;
use Ibexa\Tests\ProductCatalog\Local\Persistence\Cache\AbstractCacheHandlerTest;
use InvalidArgumentException;

final class HandlerTest extends AbstractCacheHandlerTest
{
    private const CURRENCY_ID = 1;
    private const CURRENCY_CODE = 'foo';

    public function getInnerHandler(): HandlerInterface
    {
        return $this->createMock(HandlerInterface::class);
    }

    /**
     * @param object|\Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\HandlerInterface $innerHandler
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
            $this->cacheIdentifierGeneratorMock
        );
    }

    public function providerForUnCachedMethods(): iterable
    {
        $currency = new Currency(self::CURRENCY_ID, self::CURRENCY_CODE, 2, true);

        $createStruct = new CurrencyCreateStruct();
        $createStruct->code = self::CURRENCY_CODE;
        $createStruct->subunits = 2;
        $createStruct->enabled = true;

        $updateStruct = new CurrencyUpdateStruct();
        $updateStruct->id = self::CURRENCY_ID;

        yield [
            'create',
            [
                $createStruct,
            ],
            null,
            null,
            null,
            null,
            $currency,
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
                self::CURRENCY_ID,
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
                ['currency', [self::CURRENCY_ID], false],
            ],
            null,
            ['cu-1'],
            null,
            $currency,
        ];

        yield [
            'delete',
            [
                self::CURRENCY_ID,
            ],
            [
                ['currency', [self::CURRENCY_ID], false],
            ],
            null,
            ['cu-1'],
        ];
    }

    public function providerForCachedLoadMethodsHit(): iterable
    {
        $currency = new Currency(self::CURRENCY_ID, self::CURRENCY_CODE, 2, true);

        yield [
            'find',
            [
                self::CURRENCY_ID,
            ],
            'ibx-cu-' . self::CURRENCY_ID,
            null,
            null,
            [['currency', [self::CURRENCY_ID], true]],
            ['ibx-cu-' . self::CURRENCY_ID],
            $currency,
        ];
    }

    public function providerForCachedLoadMethodsMiss(): iterable
    {
        $currency = new Currency(self::CURRENCY_ID, self::CURRENCY_CODE, 2, true);

        yield [
            'find',
            [
                self::CURRENCY_ID,
            ],
            'ibx-cu-' . self::CURRENCY_ID,
            [
                ['currency', [self::CURRENCY_ID], false],
            ],
            ['cu-1'],
            [['currency', [self::CURRENCY_ID], true]],
            ['ibx-cu-' . self::CURRENCY_ID],
            $currency,
        ];
    }
}
