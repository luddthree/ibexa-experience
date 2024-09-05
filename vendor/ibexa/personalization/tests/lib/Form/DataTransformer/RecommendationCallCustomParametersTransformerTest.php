<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Form\DataTransformer\RecommendationCallCustomParametersTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\DataTransformerInterface;

final class RecommendationCallCustomParametersTransformerTest extends TestCase
{
    /** @var \Ibexa\Personalization\Form\DataTransformer\RecommendationCallCustomParametersTransformer */
    private $customParametersTransformer;

    public function setUp(): void
    {
        $this->customParametersTransformer = new RecommendationCallCustomParametersTransformer();
    }

    public function testCreateInstanceRecommendationCallCustomParametersTransformer(): void
    {
        self::assertInstanceOf(
            DataTransformerInterface::class,
            $this->customParametersTransformer
        );
    }

    /**
     * @dataProvider provideTestTransformData
     */
    public function testTransform($valueToTransform, ?string $expectedValue = null): void
    {
        self::assertSame(
            $expectedValue,
            $this->customParametersTransformer->transform($valueToTransform)
        );
    }

    public function provideTestTransformData(): iterable
    {
        yield [
            '',
            null,
        ];

        yield [
            12345,
            null,
        ];

        yield [
            12345.12345,
            null,
        ];

        yield [
            [],
            null,
        ];

        yield [
            'param',
            null,
        ];

        yield [
            ['param'],
            null,
        ];

        yield [
            ['param' => 'value'],
            'param=value',
        ];

        yield [
            [
                'param' => 'value',
                'foo' => 'bar',
            ],
            'param=value',
        ];

        yield [
            ['foo' => 'bar=baz'],
            'foo=bar=baz',
        ];
    }

    /**
     * @dataProvider provideTestReverseTransformData
     */
    public function testReverseTransform($valueToTransform, ?array $expectedValue = null): void
    {
        self::assertSame(
            $expectedValue,
            $this->customParametersTransformer->reverseTransform($valueToTransform)
        );
    }

    public function provideTestReverseTransformData(): iterable
    {
        yield [
            'param=value',
            ['param' => 'value'],
        ];

        yield [
            'param=12345',
            ['param' => '12345'],
        ];

        yield [
            'param=somevalue=bar',
            ['param' => 'somevalue=bar'],
        ];

        yield [
            'someparam',
            null,
        ];

        yield [
            1234512,
            null,
        ];

        yield [
            '123456=somevalue',
            null,
        ];

        yield [
            '123456.1234=somevalue',
            null,
        ];
    }
}

class_alias(RecommendationCallCustomParametersTransformerTest::class, 'Ibexa\Platform\Tests\Personalization\Form\DataTransformer\RecommendationCallCustomParametersTransformerTest');
