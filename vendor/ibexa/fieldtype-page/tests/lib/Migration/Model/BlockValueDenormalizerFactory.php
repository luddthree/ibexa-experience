<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Migration\Model;

use DateTime;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\FieldTypePage\Migration\Model\AttributeDenormalizer;
use Ibexa\FieldTypePage\Migration\Model\BlockValueDenormalizer;
use PHPUnit\Framework\MockObject\RuntimeException;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal for tests only
 */
final class BlockValueDenormalizerFactory
{
    /** @var \Symfony\Component\Serializer\Normalizer\DenormalizerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private DenormalizerInterface $delegatingDenormalizerMock;

    /**
     * @param \Symfony\Component\Serializer\Normalizer\DenormalizerInterface&\PHPUnit\Framework\MockObject\MockObject $delegatingDenormalizerMock
     */
    public function __construct(DenormalizerInterface $delegatingDenormalizerMock)
    {
        $this->delegatingDenormalizerMock = $delegatingDenormalizerMock;
    }

    public function buildBlockValueDenormalizer(): DenormalizerInterface
    {
        $denormalizer = new BlockValueDenormalizer();

        $this->delegatingDenormalizerMock->method('supportsDenormalization')->willReturn(true);
        $this->delegatingDenormalizerMock->method('denormalize')->willReturnCallback(
            static function ($data, string $type) {
                if ($type === Attribute::class . '[]') {
                    $attributeListDenormalizer = new ArrayDenormalizer();
                    $attributeListDenormalizer->setDenormalizer(new AttributeDenormalizer());

                    return $attributeListDenormalizer->denormalize($data, $type);
                }
                if ($type === DateTime::class) {
                    return (new DateTimeNormalizer())->denormalize($data, $type);
                }

                throw new RuntimeException("Denormalizer for $type is not configured");
            }
        );

        $denormalizer->setDenormalizer($this->delegatingDenormalizerMock);

        return $denormalizer;
    }
}
