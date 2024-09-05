<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\DataTransformer;

use Ibexa\FieldTypePage\ScheduleBlock\ScheduleBlock;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class ScheduleAttributeDataTransformer implements DataTransformerInterface
{
    /** @var \JMS\Serializer\SerializerInterface */
    private $serializer;

    /** @var string */
    private $attributeIdentifier;

    /**
     * @param \JMS\Serializer\SerializerInterface $serializer
     * @param string $attributeIdentifier
     */
    public function __construct(SerializerInterface $serializer, string $attributeIdentifier)
    {
        $this->serializer = $serializer;
        $this->attributeIdentifier = $attributeIdentifier;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $value
     */
    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        return $this->serializer->serialize($value, 'json');
    }

    /**
     * {@inheritdoc}
     *
     * @param string $value
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        return $this->serializer->deserialize(
            (string) $value,
            ScheduleBlock::SERIALIZATION_MAP[$this->attributeIdentifier],
            'json'
        );
    }
}

class_alias(ScheduleAttributeDataTransformer::class, 'EzSystems\EzPlatformPageFieldType\Form\DataTransformer\ScheduleAttributeDataTransformer');
