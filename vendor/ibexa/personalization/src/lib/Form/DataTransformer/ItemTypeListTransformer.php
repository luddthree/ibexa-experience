<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Value\Content\ItemTypeList;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<
 *     ?array<\Ibexa\Personalization\Value\Content\ItemType>,
 *     ?\Ibexa\Personalization\Value\Content\ItemTypeList
 * >
 */
final class ItemTypeListTransformer implements DataTransformerInterface
{
    /**
     * @return array<\Ibexa\Personalization\Value\Content\ItemType>|null
     */
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ItemTypeList) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be type of: %s. %s given.',
                    ItemTypeList::class,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        return $value->getItemTypes();
    }

    public function reverseTransform($value): ?ItemTypeList
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be type of: array. %s given.',
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        return new ItemTypeList($value);
    }
}

class_alias(ItemTypeListTransformer::class, 'Ibexa\Platform\Personalization\Form\DataTransformer\ItemTypeListTransformer');
