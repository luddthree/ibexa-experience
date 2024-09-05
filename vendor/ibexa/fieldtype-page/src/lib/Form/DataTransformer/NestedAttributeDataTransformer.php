<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

final class NestedAttributeDataTransformer implements DataTransformerInterface
{
    /** @var array<string, array<string, string|array>> */
    private $attributes;

    /**
     * @param array<string, array<string, string|array>> $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @phpstan-return array{
     *     'attributes': array<array<string, array<string, mixed>>>
     * }
     *
     * @throws \JsonException
     */
    public function transform($value): array
    {
        if (null === $value) {
            $emptyData = [];
            foreach ($this->attributes as $identifier => $attr) {
                $emptyData[$identifier] = ['value' => $attr['value'] ?? null];
            }

            return ['attributes' => [$emptyData]];
        }

        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    public function reverseTransform($value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
