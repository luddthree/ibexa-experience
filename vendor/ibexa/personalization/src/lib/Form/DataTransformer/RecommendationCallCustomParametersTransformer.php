<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<array<string, string>, string>
 */
final class RecommendationCallCustomParametersTransformer implements DataTransformerInterface
{
    public function transform($value): ?string
    {
        if (
            empty($value)
            || !is_array($value)
            || is_numeric(array_key_first($value))
        ) {
            return null;
        }

        return sprintf(
            '%s=%s',
            array_key_first($value),
            current($value)
        );
    }

    public function reverseTransform($value): ?array
    {
        if (
            !is_string($value)
            || !strpos($value, '=')
        ) {
            return null;
        }

        $splitValue = explode('=', $value, 2);

        if (is_numeric($splitValue[0])) {
            return null;
        }

        return [
            $splitValue[0] => $splitValue[1],
        ];
    }
}

class_alias(RecommendationCallCustomParametersTransformer::class, 'Ibexa\Platform\Personalization\Form\DataTransformer\RecommendationCallCustomParametersTransformer');
