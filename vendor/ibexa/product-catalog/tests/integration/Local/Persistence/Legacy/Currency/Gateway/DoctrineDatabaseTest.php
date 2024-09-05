<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\Currency\Gateway;

use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\Gateway\DoctrineDatabase;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyCreateStruct;
use Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\AbstractDoctrineDatabaseTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\Gateway\DoctrineDatabase
 *
 * @phpstan-import-type Data from \Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\Gateway\DoctrineDatabase
 *
 * @phpstan-extends \Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\AbstractDoctrineDatabaseTest<Data>
 */
final class DoctrineDatabaseTest extends AbstractDoctrineDatabaseTest
{
    public function testCreate(): void
    {
        $struct = new CurrencyCreateStruct();
        $struct->code = 'FOO';
        $struct->subunits = 2;
        $struct->enabled = true;

        $database = $this->getDoctrineDatabaseService();
        $data = $database->create($struct);

        self::assertIsInt($data['id']);
        self::assertSame('FOO', $data['code']);
        self::assertSame(2, $data['subunits']);
        self::assertTrue($data['enabled']);

        $struct = new CurrencyCreateStruct();
        $struct->code = 'BAR';
        $struct->subunits = 3;
        $struct->enabled = false;

        $database = $this->getDoctrineDatabaseService();
        $data = $database->create($struct);

        self::assertIsInt($data['id']);
        self::assertSame('BAR', $data['code']);
        self::assertSame(3, $data['subunits']);
        self::assertFalse($data['enabled']);
    }

    protected function getDoctrineDatabaseService(): DoctrineDatabase
    {
        return self::getServiceByClassName(DoctrineDatabase::class);
    }

    public function provideForFindAllTest(): iterable
    {
        yield 'Simple "findAll"' => [
            static function (array $results, int $count): void {
                self::assertCount($count, $results);
                self::assertSame([
                    [
                        'id' => 1,
                        'code' => 'EUR',
                        'subunits' => 2,
                        'enabled' => true,
                    ], [
                        'id' => 2,
                        'code' => 'USD',
                        'subunits' => 3,
                        'enabled' => true,
                    ], [
                        'id' => 3,
                        'code' => 'BTC',
                        'subunits' => 4,
                        'enabled' => true,
                    ],
                ], $results);
            },
        ];
    }

    public function provideForFindByTest(): iterable
    {
        yield 'Simple "findBy"' => [
            static function (array $results, int $count): void {
                self::assertCount($count, $results);
                self::assertSame([
                    [
                        'id' => 1,
                        'code' => 'EUR',
                        'subunits' => 2,
                        'enabled' => true,
                    ], [
                        'id' => 2,
                        'code' => 'USD',
                        'subunits' => 3,
                        'enabled' => true,
                    ], [
                        'id' => 3,
                        'code' => 'BTC',
                        'subunits' => 4,
                        'enabled' => true,
                    ],
                ], $results);
            },
            [],
        ];

        yield 'Find by code (EUR)' => [
            static function (array $results, int $count): void {
                self::assertCount($count, $results);
                self::assertSame([
                    [
                        'id' => 1,
                        'code' => 'EUR',
                        'subunits' => 2,
                        'enabled' => true,
                    ],
                ], $results);
            },
            [
                'code' => 'EUR',
            ],
        ];

        yield 'Find by codes (EUR, BTC)' => [
            static function (array $results, int $count): void {
                self::assertCount($count, $results);
                self::assertEqualsCanonicalizing([
                    [
                        'id' => 3,
                        'code' => 'BTC',
                        'subunits' => 4,
                        'enabled' => true,
                    ], [
                        'id' => 1,
                        'code' => 'EUR',
                        'subunits' => 2,
                        'enabled' => true,
                    ],
                ], $results);
            },
            [
                'code' => ['BTC', 'EUR'],
            ],
        ];
    }

    public function provideForFindByIdTest(): iterable
    {
        yield 'Find existing (1) ID' => [
            static function (?array $result): void {
                self::assertSame([
                    'id' => 1,
                    'code' => 'EUR',
                    'subunits' => 2,
                    'enabled' => true,
                ], $result);
            },
            1,
        ];

        yield 'Find non existing (999) ID' => [
            static function (?array $result): void {
                self::assertNull($result);
            },
            999,
        ];
    }
}
