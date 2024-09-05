<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\QueryType;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\QueryTypeInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\QueryType\QueryTypeRegistry;
use PHPUnit\Framework\TestCase;

final class QueryTypeRegistryTest extends TestCase
{
    private const QUERY_TYPE_NAME_A = 'A';

    private const QUERY_TYPE_NAME_B = 'B';

    private const QUERY_TYPE_NAME_NON_EXISTING = 'C';

    /** @var iterable<\Ibexa\Contracts\ProductCatalog\QueryTypeInterface> */
    private iterable $queryTypes;

    private QueryTypeInterface $queryTypeA;

    private QueryTypeInterface $queryTypeB;

    public function setUp(): void
    {
        $this->queryTypeA = $this->getQueryType(self::QUERY_TYPE_NAME_A);
        $this->queryTypeB = $this->getQueryType(self::QUERY_TYPE_NAME_B);
        $this->queryTypes = new ArrayIterator(
            [
                self::QUERY_TYPE_NAME_A => $this->queryTypeA,
                self::QUERY_TYPE_NAME_B => $this->queryTypeB,
            ]
        );
    }

    public function testGetQueryType(): void
    {
        $queryTypeRegistry = new QueryTypeRegistry($this->queryTypes);

        self::assertSame(
            $this->queryTypeA,
            $queryTypeRegistry->getQueryType(self::QUERY_TYPE_NAME_A)
        );

        self::assertSame(
            $this->queryTypeB,
            $queryTypeRegistry->getQueryType(self::QUERY_TYPE_NAME_B)
        );
    }

    public function testGetQueryTypeWillThrowInvalidArgumentException(): void
    {
        $queryTypeRegistry = new QueryTypeRegistry($this->queryTypes);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                "Argument 'QueryType name' is invalid: No QueryType found with name: %s",
                self::QUERY_TYPE_NAME_NON_EXISTING
            )
        );

        $queryTypeRegistry->getQueryType('C');
    }

    public function testHasQueryType(): void
    {
        $queryTypeRegistry = new QueryTypeRegistry($this->queryTypes);

        self::assertTrue(
            $queryTypeRegistry->hasQueryType(self::QUERY_TYPE_NAME_A)
        );

        self::assertFalse(
            $queryTypeRegistry->hasQueryType(self::QUERY_TYPE_NAME_NON_EXISTING)
        );
    }

    private function getQueryType(string $name): QueryTypeInterface
    {
        $queryType = $this->createMock(QueryTypeInterface::class);
        $queryType
            ->method('getName')
            ->willReturn($name);

        return $queryType;
    }
}
