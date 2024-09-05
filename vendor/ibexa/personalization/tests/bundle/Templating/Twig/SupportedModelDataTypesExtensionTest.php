<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Personalization\Templating\Twig;

use Ibexa\Bundle\Personalization\Templating\Twig\SupportedModelDataTypesExtension;
use Ibexa\Personalization\Value\Model\MetaData;
use Ibexa\Personalization\Value\Model\Model;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Personalization\Templating\Twig\SupportedModelDataTypesExtension
 */
final class SupportedModelDataTypesExtensionTest extends TestCase
{
    private SupportedModelDataTypesExtension $supportedModelDataTypesExtension;

    protected function setUp(): void
    {
        $this->supportedModelDataTypesExtension = new SupportedModelDataTypesExtension();
    }

    /**
     * @dataProvider provideDataForTestGetSupportedModelDataTypes
     *
     * @throws \JsonException
     */
    public function testGetSupportedModelDataTypes(
        Model $model,
        string $expectedSupportedDataTypes
    ): void {
        self::assertEquals(
            $expectedSupportedDataTypes,
            $this->supportedModelDataTypesExtension->getSupportedModelDataTypes($model)
        );
    }

    /**
     * @return iterable<array{\Ibexa\Personalization\Value\Model\Model, string}>
     */
    public function provideDataForTestGetSupportedModelDataTypes(): iterable
    {
        yield [
            $this->getModel(true, true),
            '["default","submodels","segments"]',
        ];

        yield [
            $this->getModel(true, false),
            '["default","submodels"]',
        ];

        yield [
            $this->getModel(false, true),
            '["default","segments"]',
        ];

        yield [
            $this->getModel(false, false),
            '["default"]',
        ];
    }

    private function getModel(bool $submodelsSupported, bool $segmentsSupported): Model
    {
        return new Model(
            'foo_type',
            'foo',
            [],
            [],
            true,
            true,
            $submodelsSupported,
            $segmentsSupported,
            true,
            true,
            true,
            new MetaData(null, null, null, null),
            [],
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
        );
    }
}
