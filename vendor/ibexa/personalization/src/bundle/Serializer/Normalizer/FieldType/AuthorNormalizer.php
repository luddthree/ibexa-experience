<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Personalization\Serializer\Normalizer\ValueNormalizerInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\Author\Value as AuthorValue;

final class AuthorNormalizer implements ValueNormalizerInterface
{
    /**
     * @return array<string>
     */
    public function normalize(Value $value): array
    {
        if (!$value instanceof AuthorValue) {
            throw new InvalidArgumentType('$value', AuthorValue::class, $value);
        }

        $authors = [];

        /** @var \Ibexa\Core\FieldType\Author\Author $author */
        foreach ($value->authors as $author) {
            $authors[] = $author->name;
        }

        return $authors;
    }

    public function supportsValue(Value $value): bool
    {
        return $value instanceof AuthorValue;
    }
}
