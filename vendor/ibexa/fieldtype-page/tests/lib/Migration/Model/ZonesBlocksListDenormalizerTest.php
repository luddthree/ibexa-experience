<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Migration\Model;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\Migration\Data\ZonesBlocksList;
use Ibexa\FieldTypePage\Migration\Data\ZonesBlocksListDenormalizer;
use Ibexa\Tests\FieldTypePage\Migration\BaseMigrationDenormalizerTestCase;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ZonesBlocksListDenormalizerTest extends BaseMigrationDenormalizerTestCase
{
    protected function buildDenormalizer(): DenormalizerInterface
    {
        $denormalizer = new ZonesBlocksListDenormalizer();

        $denormalizerFactory = new BlockValueDenormalizerFactory($this->createMock(DenormalizerInterface::class));
        $blockValueDenormalizer = $denormalizerFactory->buildBlockValueDenormalizer();

        $blockValueListDenormalizer = new ArrayDenormalizer();
        $blockValueListDenormalizer->setDenormalizer($blockValueDenormalizer);

        $denormalizer->setDenormalizer($blockValueListDenormalizer);

        return $denormalizer;
    }

    public function testSupportsDenormalization(): void
    {
        self::assertTrue($this->denormalizer->supportsDenormalization([], ZonesBlocksList::class));
        self::assertFalse($this->denormalizer->supportsDenormalization([], Attribute::class));
        self::assertFalse($this->denormalizer->supportsDenormalization([], BlockValue::class));
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalize(): void
    {
        $denormalizationResult = $this->denormalizer->denormalize(
            [
                [
                    'name' => 'my-zone',
                    'blocks' => [
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
                    ],
                ],
            ],
            ZonesBlocksList::class
        );
        self::assertCount(1, $denormalizationResult);
        foreach ($denormalizationResult as $zoneName => $blockList) {
            self::assertSame('my-zone', $zoneName);
            self::assertCount(1, $blockList);
            [$block] = $blockList;
            self::assertSame('my_block', $block->getType());
            self::assertCount(2, $block->getAttributes());
        }
    }
}
