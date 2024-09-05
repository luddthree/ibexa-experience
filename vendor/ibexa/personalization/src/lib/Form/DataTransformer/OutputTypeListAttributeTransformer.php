<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Value\Content\AbstractItemType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<string, AbstractItemType>
 */
final class OutputTypeListAttributeTransformer implements DataTransformerInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @phpstan-param null|scalar|array<mixed>|object $value
     */
    public function transform($value): ?AbstractItemType
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be type of: string. %s given.',
                    get_debug_type($value)
                )
            );
        }

        return $this->serializer->deserialize($value, AbstractItemType::class, 'json');
    }

    /**
     * @phpstan-param null|scalar|array<mixed>|object $value
     */
    public function reverseTransform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof AbstractItemType) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be type of: %s. %s given.',
                    AbstractItemType::class,
                    get_debug_type($value)
                )
            );
        }

        return $this->serializer->serialize($value, 'json');
    }
}
