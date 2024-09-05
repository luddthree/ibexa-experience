<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ActivityLog\Values\ActivityLog\SortClause;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\AbstractSortClause;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\AbstractSortClause
 */
final class AbstractSortClauseTest extends TestCase
{
    /**
     * @phpstan-param 'ASC'|'DESC' $order
     *
     * @dataProvider provideValidOrder
     */
    public function testValidConstruct(string $order): void
    {
        $sortClause = $this->getSortClause($order);
        self::assertSame($order, $sortClause->getOrder());
    }

    /**
     * @dataProvider provideInvalidOrder
     */
    public function testInvalidConstruct(string $order): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument '\$order' is invalid: Expected one of: \"ASC\", \"DESC\". Received \"$order\".");
        // @phpstan-ignore-next-line
        $this->getSortClause($order);
    }

    /**
     * @phpstan-param 'ASC'|'DESC' $order
     *
     * @dataProvider provideValidOrder
     */
    public function testSetValidOrder(string $order): void
    {
        $sortClause = $this->getSortClause('ASC');
        $sortClause->setOrder($order);
        self::assertSame($order, $sortClause->getOrder());
    }

    /**
     * @dataProvider provideInvalidOrder
     */
    public function testSetInvalidOrder(string $order): void
    {
        $sortClause = $this->getSortClause('ASC');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument '\$order' is invalid: Expected one of: \"ASC\", \"DESC\". Received \"$order\".");
        // @phpstan-ignore-next-line
        $sortClause->setOrder($order);
    }

    /**
     * @return iterable<array{'ASC'|'DESC'}>
     */
    public function provideValidOrder(): iterable
    {
        yield ['ASC'];
        yield ['DESC'];
    }

    /**
     * @return iterable<array{string}>
     */
    public function provideInvalidOrder(): iterable
    {
        yield ['foo'];
        yield ['asc'];
    }

    /**
     * @phpstan-param 'ASC'|'DESC' $order
     */
    private function getSortClause(string $order): AbstractSortClause
    {
        return new class ($order) extends AbstractSortClause {
        };
    }
}
