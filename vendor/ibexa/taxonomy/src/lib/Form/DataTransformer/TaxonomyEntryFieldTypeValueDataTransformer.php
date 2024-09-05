<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\DataTransformer;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<\Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value, int>
 */
final class TaxonomyEntryFieldTypeValueDataTransformer implements DataTransformerInterface
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function reverseTransform($value): Value
    {
        if (null === $value) {
            return new Value();
        }

        return new Value(
            $this->taxonomyService->loadEntryById((int) $value)
        );
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Value) {
            throw new TransformationFailedException(sprintf(
                'Value of type %s expected',
                Value::class
            ));
        }

        return $value->getTaxonomyEntry()->id ?? null;
    }
}
