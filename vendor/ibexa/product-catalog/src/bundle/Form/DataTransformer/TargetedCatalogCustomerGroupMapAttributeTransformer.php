<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<string, array{
 *      matches?: array<
 *          array-key, array{
 *              customer_group: \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
 *              catalog: int
 *          }
 *      >
 * }>
 */
final class TargetedCatalogCustomerGroupMapAttributeTransformer implements DataTransformerInterface
{
    /**
     * @throws \JsonException
     */
    public function transform($value): ?array
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Invalid data, string value expected');
        }

        $matches = [];
        $map = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        if (empty($map)) {
            return [];
        }

        foreach ($map as $key => $matchedItems) {
            if (!isset($matchedItems['customer_group'], $matchedItems['catalog'])) {
                throw new TransformationFailedException(
                    'Invalid data. Missing "customer_group" or "catalog" keys'
                );
            }

            $matches += [
                $key => [
                    'customer_group' => new Value($matchedItems['customer_group']),
                    'catalog' => $matchedItems['catalog'],
                ],
            ];
        }

        return ['matches' => $matches];
    }

    /**
     * @throws \JsonException
     */
    public function reverseTransform($value): string
    {
        if (empty($value)) {
            return json_encode([], JSON_THROW_ON_ERROR);
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Invalid data, array value expected');
        }

        if (!isset($value['matches'])) {
            throw new TransformationFailedException('Invalid data. Missing "matches" root key');
        }

        $map = [];

        foreach ($value['matches'] as $key => $matchedItems) {
            if (!isset($matchedItems['customer_group'], $matchedItems['catalog'])) {
                continue;
            }

            $map[$key] = [
                'customer_group' => $matchedItems['customer_group']->getId(),
                'catalog' => $matchedItems['catalog'],
            ];
        }

        return json_encode($map, JSON_THROW_ON_ERROR);
    }
}
