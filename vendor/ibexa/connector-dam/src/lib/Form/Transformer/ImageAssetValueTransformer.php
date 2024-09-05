<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Form\Transformer;

use Ibexa\Connector\Dam\FieldType\ImageAsset\Value;
use Ibexa\ContentForms\FieldType\DataTransformer\AbstractBinaryBaseTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ImageAssetValueTransformer extends AbstractBinaryBaseTransformer implements DataTransformerInterface
{
    /**
     * @param \Ibexa\Connector\Dam\FieldType\ImageAsset\Value|null $value
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Value) {
            throw new TransformationFailedException(sprintf('Received %s instead of %s', \gettype($value), Value::class));
        }

        return array_merge(
            $this->getDefaultProperties(),
            [
                'destinationContentId' => $value->destinationContentId,
                'alternativeText' => $value->alternativeText,
                'source' => $value->source,
            ]
        );
    }

    /**
     * @param array|null $value
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value): ?Value
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException(sprintf('Received %s instead of an array', \gettype($value)));
        }

        return new Value(
            $value['destinationContentId'],
            $value['alternativeText'],
            $value['source'],
        );
    }
}

class_alias(ImageAssetValueTransformer::class, 'Ibexa\Platform\Connector\Dam\Form\Transformer\ImageAssetValueTransformer');
