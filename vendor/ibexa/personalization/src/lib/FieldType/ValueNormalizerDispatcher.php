<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Personalization\Exception\ValueNormalizerNotFoundException;

final class ValueNormalizerDispatcher implements ValueNormalizerDispatcherInterface
{
    /** @var iterable<\Ibexa\Contracts\Personalization\Serializer\Normalizer\ValueNormalizerInterface> */
    private iterable $normalizers;

    /**
     * @param iterable<\Ibexa\Contracts\Personalization\Serializer\Normalizer\ValueNormalizerInterface> $normalizers
     */
    public function __construct(iterable $normalizers)
    {
        $this->normalizers = $normalizers;
    }

    public function dispatch(Value $value)
    {
        foreach ($this->normalizers as $parser) {
            if ($parser->supportsValue($value)) {
                return $parser->normalize($value);
            }
        }

        throw new ValueNormalizerNotFoundException($value);
    }

    public function supportsNormalizer(Value $value): bool
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supportsValue($value)) {
                return true;
            }
        }

        return false;
    }
}
