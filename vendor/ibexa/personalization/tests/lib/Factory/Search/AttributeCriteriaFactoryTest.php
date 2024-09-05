<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Factory\Search;

use Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface;
use Ibexa\Personalization\Factory\Search\AttributeCriteriaFactory;
use Ibexa\Personalization\Factory\Search\AttributeCriteriaFactoryInterface;
use Ibexa\Personalization\Value\Search\AttributeCriteria;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Factory\Search\AttributeCriteriaFactory
 */
final class AttributeCriteriaFactoryTest extends TestCase
{
    private AttributeCriteriaFactoryInterface $attributeCriteriaFactory;

    /** @var \Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private OutputTypeAttributesResolverInterface $outputTypeAttributesResolver;

    protected function setUp(): void
    {
        $this->outputTypeAttributesResolver = $this->createMock(OutputTypeAttributesResolverInterface::class);
        $this->attributeCriteriaFactory = new AttributeCriteriaFactory($this->outputTypeAttributesResolver);
    }

    /**
     * @dataProvider provideDataForTestGetAttributesCriteria
     *
     * @phpstan-param array<int, array{
     *  'title': string
     *  }> $configuredAttributes
     *
     * @param array<\Ibexa\Personalization\Value\Search\AttributeCriteria> $expectedAttributesCriteria
     */
    public function testGetAttributesCriteria(
        int $siteAccess,
        string $phrase,
        array $configuredAttributes,
        array $expectedAttributesCriteria
    ): void {
        $this->configureOutputTypeAttributesResolverToReturnConfiguredAttributes($siteAccess, $configuredAttributes);

        self::assertEquals(
            $expectedAttributesCriteria,
            $this->attributeCriteriaFactory->getAttributesCriteria($siteAccess, $phrase)
        );
    }

    /**
     * @phpstan-return iterable<array{
     *  int,
     *  string,
     *  array<int, array{
     *      'title': string
     *  }>,
     *  array<\Ibexa\Personalization\Value\Search\AttributeCriteria>
     * }>
     */
    public function provideDataForTestGetAttributesCriteria(): iterable
    {
        $configuredAttributes = $this->getConfiguredAttributes();
        $expectedAttributesCriteria = $this->getExpectedAttributesCriteria();

        yield [
            12345,
            'foo',
            $configuredAttributes,
            $expectedAttributesCriteria,
        ];

        yield [
            12345,
            'foo',
            array_merge(
                $configuredAttributes,
                [
                    4 => [
                        'title' => 'name',
                    ],
                    5 => [
                        'title' => 'short_name',
                    ],
                ]
            ),
            $expectedAttributesCriteria,
        ];

        yield [
            12345,
            'foo',
            [],
            [
                new AttributeCriteria('contentId', 'foo'),
            ],
        ];
    }

    /**
     * @phpstan-return array<int, array{
     *  'title': string
     * }>
     */
    private function getConfiguredAttributes(): array
    {
        return [
            1 => [
                'title' => 'name',
            ],
            2 => [
                'title' => 'short_name',
            ],
            3 => [
                'title' => 'title',
            ],
        ];
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Search\AttributeCriteria>
     */
    private function getExpectedAttributesCriteria(): array
    {
        $criteria[] = new AttributeCriteria('name', 'foo');
        $criteria[] = new AttributeCriteria('short_name', 'foo');
        $criteria[] = new AttributeCriteria('title', 'foo');
        $criteria[] = new AttributeCriteria('contentId', 'foo');

        return $criteria;
    }

    /**
     * @phpstan-param array<int, array{
     *  'title': string
     * }> $configuredAttributes
     */
    private function configureOutputTypeAttributesResolverToReturnConfiguredAttributes(
        int $customerId,
        array $configuredAttributes
    ): void {
        $this->outputTypeAttributesResolver
            ->expects(self::once())
            ->method('resolve')
            ->with($customerId)
            ->willReturn($configuredAttributes);
    }
}
