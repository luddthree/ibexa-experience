<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData;
use Ibexa\Personalization\Form\DataTransformer\CategoryPathDataTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @covers \Ibexa\Personalization\Form\DataTransformer\CategoryPathDataTransformer
 */
final class CategoryPathDataTransformerTest extends TestCase
{
    private CategoryPathDataTransformer $categoryPathDataTransformer;

    protected function setUp(): void
    {
        $this->categoryPathDataTransformer = new CategoryPathDataTransformer();
    }

    public function testTransformReturnsNullWhenValueIsNull(): void
    {
        self::assertNull($this->categoryPathDataTransformer->transform(null));
    }

    public function testTransformThrowsTransformationFailedException(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid data. Value should be type of: %s. array given.',
            ScenarioExcludedCategoriesData::class
        ));

        /** @phpstan-ignore-next-line */
        $this->categoryPathDataTransformer->transform([]);
    }

    /**
     * @dataProvider provideDataForTestTransform
     *
     * @phpstan-param array{
     *  array<string>,
     *  \Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData
     * } $expectedPaths
     */
    public function testTransform(
        array $expectedPaths,
        ScenarioExcludedCategoriesData $scenarioExcludedCategoriesData
    ): void {
        $data = $this->categoryPathDataTransformer->transform($scenarioExcludedCategoriesData);

        self::assertNotNull($data);
        self::assertEquals($expectedPaths, $data->getPaths());
    }

    public function testReverseTransformThrowsTransformationFailedException(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid data. Value should be type of: %s. array given.',
            ScenarioExcludedCategoriesData::class
        ));

        /** @phpstan-ignore-next-line */
        $this->categoryPathDataTransformer->reverseTransform([]);
    }

    /**
     * @dataProvider provideDataForTestReverseTransform
     *
     * @phpstan-param array{
     *  array<string>,
     *  \Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData
     * } $expectedPaths
     */
    public function testReverseTransform(
        array $expectedPaths,
        ScenarioExcludedCategoriesData $scenarioExcludedCategoriesData
    ): void {
        $data = $this->categoryPathDataTransformer->reverseTransform($scenarioExcludedCategoriesData);

        self::assertNotNull($data);
        self::assertEquals(
            $expectedPaths,
            $data->getPaths()
        );
    }

    /**
     * @return iterable<array{
     *  array<string>,
     *  \Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData
     * }>
     */
    public function provideDataForTestTransform(): iterable
    {
        yield [
            [],
            new ScenarioExcludedCategoriesData(
                true,
                []
            ),
        ];

        yield [
            ['/1/2/3/4/'],
            new ScenarioExcludedCategoriesData(
                true,
                ['/1/2/3/4']
            ),
        ];

        yield [
            [
                '/1/2/',
                '/3/4/5/',
                '/6/7/8/9/',
            ],
            new ScenarioExcludedCategoriesData(
                true,
                [
                    '/1/2',
                    '/3/4/5/',
                    '/6/7/8/9',
                ]
            ),
        ];
    }

    /**
     * @return iterable<array{
     *  array<string>,
     *  \Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData
     * }>
     */
    public function provideDataForTestReverseTransform(): iterable
    {
        yield [
            [],
            new ScenarioExcludedCategoriesData(
                true,
                []
            ),
        ];

        yield [
            ['/1/2/3/4'],
            new ScenarioExcludedCategoriesData(
                true,
                ['/1/2/3/4/']
            ),
        ];

        yield [
            [
                '/1/2',
                '/3/4/5',
                '/6/7/8/9',
            ],
            new ScenarioExcludedCategoriesData(
                true,
                [
                    '/1/2/',
                    '/3/4/5/',
                    '/6/7/8/9',
                ]
            ),
        ];
    }
}
