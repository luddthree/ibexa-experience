<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\Form\Transformer;

use Ibexa\FieldTypeAddress\FieldType\Value;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<\Ibexa\FieldTypeAddress\FieldType\Value, array<mixed>>
 */
final class FieldTypeModelTransformer implements DataTransformerInterface
{
    public function transform($value): array
    {
        return [
            'name' => $value->name,
            'country' => $value->country,
            'fields' => $value->fields,
        ];
    }

    public function reverseTransform($hash): Value
    {
        $name = $hash['name'] ?? null;
        $country = $hash['country'] ?? null;
        $fields = $hash['fields'] ?? [];

        return new Value($name, $country, $fields);
    }
}
