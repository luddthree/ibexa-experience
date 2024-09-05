<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * This DataTransformer is required due to difference in category path handling between Content Repository & Personalization Engine
 * Category paths used in Content Repository contains trailing slashes (e.g.: /1/2/3/4/)
 * Category paths used in Personalization Engine not contains trailing slashes (e.g.: /1/2/3/4).
 *
 * @implements \Symfony\Component\Form\DataTransformerInterface<
 *     \Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData,
 *     \Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData
 * >
 */
final class CategoryPathDataTransformer implements DataTransformerInterface
{
    public function transform($value): ?ScenarioExcludedCategoriesData
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ScenarioExcludedCategoriesData) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be type of: %s. %s given.',
                    ScenarioExcludedCategoriesData::class,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        $paths = $value->getPaths();
        if (empty($paths)) {
            return $value;
        }

        $transformedPaths = [];
        foreach ($paths as $path) {
            $transformedPaths[] = rtrim($path, '/') . '/';
        }

        $value->setPaths($transformedPaths);

        return $value;
    }

    public function reverseTransform($value): ?ScenarioExcludedCategoriesData
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ScenarioExcludedCategoriesData) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be type of: %s. %s given.',
                    ScenarioExcludedCategoriesData::class,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        $paths = $value->getPaths();
        if (empty($paths)) {
            return $value;
        }

        $transformedPaths = [];
        foreach ($paths as $path) {
            if (null !== $path) {
                $transformedPaths[] = rtrim($path, '/');
            }
        }

        $value->setPaths($transformedPaths);

        return $value;
    }
}
