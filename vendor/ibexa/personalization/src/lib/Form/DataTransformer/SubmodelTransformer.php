<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Form\Data\SubmodelData;
use Ibexa\Personalization\Value\Model\Submodel;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<
 *     \Ibexa\Personalization\Form\Data\SubmodelData,
 *     \Ibexa\Personalization\Form\Data\SubmodelData
 * >
 */
final class SubmodelTransformer implements DataTransformerInterface
{
    /**
     * @param \Ibexa\Personalization\Form\Data\SubmodelData $value
     */
    public function reverseTransform($value): SubmodelData
    {
        $attributeValues = (array)$value->getAttributeValues();
        $filteredValues = [];

        if ($value->getType() === Submodel::TYPE_NOMINAL) {
            foreach ($attributeValues as $group => $groupValues) {
                $groupValues = array_filter($groupValues);
                if (count($groupValues)) {
                    $filteredValues[$group] = $groupValues;
                }
            }
        } elseif ($value->getType() === Submodel::TYPE_NUMERIC) {
            $filteredValues = array_values(
                array_filter(
                    $attributeValues,
                    static function (array $item): bool {
                        return $item['leftValue'] !== null && $item['rightValue'] !== null;
                    }
                )
            );
        }

        $value->setSource(count($attributeValues) ? null : $value->getSource());
        $value->setAttributeValues($filteredValues);

        return $value;
    }

    /**
     * @param \Ibexa\Personalization\Form\Data\SubmodelData $value
     */
    public function transform($value): SubmodelData
    {
        return $value;
    }
}

class_alias(SubmodelTransformer::class, 'Ibexa\Platform\Personalization\Form\DataTransformer\SubmodelTransformer');
