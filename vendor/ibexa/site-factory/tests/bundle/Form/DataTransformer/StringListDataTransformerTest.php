<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteFactory\Form\DataTransformer;

use Ibexa\Bundle\SiteFactory\Form\DataTransformer\StringListDataTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringListDataTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, $expected)
    {
        $transformer = new StringListDataTransformer();

        $result = $transformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider transformWithInvalidInputDataProvider
     */
    public function testTransformWithInvalidInput($value)
    {
        $transformer = new StringListDataTransformer();

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(sprintf('Expected a numeric, got %s instead', \gettype($value)));

        $transformer->transform($value);
    }

    /**
     * @dataProvider reverseTransformDataProvider
     */
    public function testReverseTransform($value, $expected)
    {
        $transformer = new StringListDataTransformer();

        $result = $transformer->reverseTransform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider reverseTransformWithInvalidInputDataProvider
     */
    public function testReverseTransformWithInvalidInput($value)
    {
        $transformer = new StringListDataTransformer();

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(sprintf('Expected a string, got %s instead', \gettype($value)));

        $transformer->reverseTransform($value);
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            'language_codes' => [['pol_PL', 'eng_GB'], 'pol_PL, eng_GB'],
            'empty_array' => [[], ''],
            'an_empty_string' => ['', null],
            '0_as_an_integer' => [0, null],
            '0_as_an_float' => [0.0, null],
            '0_as_a_string' => ['0', null],
            'null' => [null, null],
            'an_empty_array' => [[], null],
        ];
    }

    /**
     * @return array
     */
    public function transformWithInvalidInputDataProvider(): array
    {
        return [
            'string' => ['string'],
            'integer' => [1],
            'bool' => [true],
            'float' => [12.34],
            'object' => [new \stdClass()],
        ];
    }

    /**
     * @return array
     */
    public function reverseTransformDataProvider(): array
    {
        return [
            'language_codes' => ['pol_PL, eng_GB', ['pol_PL', 'eng_GB']],
            'empty_string' => ['', null],
            '0_as_an_integer' => [0, null],
            '0_as_an_float' => [0.0, null],
            '0_as_a_string' => ['0', null],
            'null' => [null, null],
            'an_empty_array' => [[], null],
        ];
    }

    /**
     * @return array
     */
    public function reverseTransformWithInvalidInputDataProvider(): array
    {
        return [
            'integer' => [1],
            'bool' => [true],
            'float' => [12.34],
            'object' => [new \stdClass()],
        ];
    }
}

class_alias(StringListDataTransformerTest::class, 'EzSystems\EzPlatformSiteFactoryBundle\Tests\Form\DataTransformer\StringListDataTransformerTest');
