<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Migration\Generator\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Migration\Generator\Content\CriterionFactory;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;

/**
 * @covers \Ibexa\Migration\Generator\Content\CriterionFactory
 */
final class CriterionFactoryTest extends IbexaKernelTestCase
{
    private const VALUE_STRING = ['__VALUE__'];
    private const VALUE_INT = [1];

    /** @var \Ibexa\Migration\Generator\Content\CriterionFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = self::getServiceByClassName(CriterionFactory::class);
    }

    public function testBuildThrowsExceptionWhenMatchPropertyIsUnknown(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->build('__UNKNOWN_PROPERTY__', []);
    }

    /**
     * @phpstan-param self::VALUE_* $value
     *
     * @dataProvider providerForTestBuildReturnsCriterion
     */
    public function testBuildReturnsCriterion(string $matchProperty, Criterion $expectedCriterion, array $value): void
    {
        $criterion = $this->factory->build($matchProperty, $value);
        Assert::assertEquals($expectedCriterion, $criterion);
    }

    /**
     * @return iterable<array{string, \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion, self::VALUE_*}>
     */
    public function providerForTestBuildReturnsCriterion(): iterable
    {
        yield [
            'content_id',
            $this->prepareExpectedCriterion(new Criterion\ContentId(self::VALUE_INT)),
            self::VALUE_INT,
        ];

        yield [
            'content_type_id',
            $this->prepareExpectedCriterion(new Criterion\ContentTypeId(self::VALUE_INT)),
            self::VALUE_INT,
        ];

        yield [
            'content_type_group_id',
            $this->prepareExpectedCriterion(new Criterion\ContentTypeGroupId(self::VALUE_INT)),
            self::VALUE_INT,
        ];

        yield [
            'content_type_identifier',
            $this->prepareExpectedCriterion(new Criterion\ContentTypeIdentifier(self::VALUE_STRING)),
            self::VALUE_STRING,
        ];

        yield [
            'content_remote_id',
            $this->prepareExpectedCriterion(new Criterion\RemoteId(self::VALUE_STRING)),
            self::VALUE_STRING,
        ];

        yield [
            'location_id',
            $this->prepareExpectedCriterion(new Criterion\LocationId(self::VALUE_INT)),
            self::VALUE_INT,
        ];

        yield [
            'location_remote_id',
            $this->prepareExpectedCriterion(new Criterion\LocationRemoteId(self::VALUE_STRING)),
            self::VALUE_STRING,
        ];

        yield [
            'parent_location_id',
            $this->prepareExpectedCriterion(new Criterion\ParentLocationId(self::VALUE_INT)),
            self::VALUE_INT,
        ];

        yield [
            'user_id',
            $this->prepareExpectedCriterion(new Criterion\UserId(self::VALUE_INT)),
            self::VALUE_INT,
        ];
    }

    private function prepareExpectedCriterion(Criterion $criterion): Criterion
    {
        return new Criterion\LogicalAnd(
            array_merge(
                [new Criterion\Visibility(Criterion\Visibility::VISIBLE)],
                [$criterion]
            )
        );
    }
}
