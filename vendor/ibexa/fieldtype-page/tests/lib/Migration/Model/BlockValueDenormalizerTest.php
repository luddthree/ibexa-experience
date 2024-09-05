<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Migration\Model;

use DateTime;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Tests\FieldTypePage\Migration\BaseMigrationDenormalizerTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class BlockValueDenormalizerTest extends BaseMigrationDenormalizerTestCase
{
    protected function buildDenormalizer(): DenormalizerInterface
    {
        $denormalizerFactory = new BlockValueDenormalizerFactory($this->createMock(DenormalizerInterface::class));

        return $denormalizerFactory->buildBlockValueDenormalizer();
    }

    public function testSupportsDenormalization(): void
    {
        self::assertTrue($this->denormalizer->supportsDenormalization([], BlockValue::class));
        self::assertFalse($this->denormalizer->supportsDenormalization([], Attribute::class));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue}>
     */
    public function getDataForTestDenormalize(): iterable
    {
        yield 'full block value' => [
            [
                'id' => 'b-c5ec21f9-77b0-419d-a4b8-04a1c8f4f6a5',
                'type' => 'my_block',
                'name' => 'My block',
                'view' => 'my_view',
                'class' => 'my-css-class',
                'style' => 'text-align: center;',
                'compiled' => '[data-ibx-block-id="123"] { text-align: center; }',
                'since' => '2023-12-12',
                'till' => '2023-12-31',
                'attributes' => [
                    [
                        'id' => 'a-4a2f4f0a-17ad-4bee-ae88-3168823a3387',
                        'name' => 'my_attribute_1',
                        'value' => 'my value 1',
                    ],
                    [
                        'id' => 'a-5f59dfe2-11a0-4abd-b128-3157e451d654',
                        'name' => 'my_attribute_2',
                        'value' => 'my value 2',
                    ],
                ],
            ],
            new BlockValue(
                'b-c5ec21f9-77b0-419d-a4b8-04a1c8f4f6a5',
                'my_block',
                'My block',
                'my_view',
                'my-css-class',
                'text-align: center;',
                '[data-ibx-block-id="123"] { text-align: center; }',
                new DateTime('2023-12-12'),
                new DateTime('2023-12-31'),
                [
                    new Attribute(
                        'a-4a2f4f0a-17ad-4bee-ae88-3168823a3387',
                        'my_attribute_1',
                        'my value 1'
                    ),
                    new Attribute(
                        'a-5f59dfe2-11a0-4abd-b128-3157e451d654',
                        'my_attribute_2',
                        'my value 2'
                    ),
                ]
            ),
        ];

        yield 'minimal block value' => [
            [
                'id' => 'b-c5ec21f9-77b0-419d-a4b8-04a1c8f4f6a5',
                'type' => 'my_block',
                'name' => 'My block',
            ],
            new BlockValue(
                'b-c5ec21f9-77b0-419d-a4b8-04a1c8f4f6a5',
                'my_block',
                'My block',
                'default',
                null,
                null,
                null,
                null,
                null,
                []
            ),
        ];
    }

    /**
     * @dataProvider getDataForTestDenormalize
     *
     * @param array<string, mixed> $inputToDenormalize
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalize(array $inputToDenormalize, BlockValue $expectedBlockValue): void
    {
        self::assertEquals(
            $expectedBlockValue,
            $this->denormalizer->denormalize(
                $inputToDenormalize,
                BlockValue::class
            )
        );
    }
}
