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
use Ibexa\Core\FieldType\RelationList\Value as RelationListValue;
use Ibexa\Personalization\FieldType\DestinationContentNormalizerDispatcherInterface;

final class RelationListNormalizer implements ValueNormalizerInterface
{
    private DestinationContentNormalizerDispatcherInterface $destinationContentNormalizerDispatcher;

    public function __construct(DestinationContentNormalizerDispatcherInterface $destinationContentNormalizerDispatcher)
    {
        $this->destinationContentNormalizerDispatcher = $destinationContentNormalizerDispatcher;
    }

    /**
     * @return array<array<scalar|null>|scalar|null>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function normalize(Value $value): array
    {
        if (!$value instanceof RelationListValue) {
            throw new InvalidArgumentType('$value', RelationListValue::class);
        }

        $values = [];
        foreach ($value->destinationContentIds as $destinationContentId) {
            $normalizedValue = $this->destinationContentNormalizerDispatcher->dispatch($destinationContentId);
            if (null !== $normalizedValue) {
                $values[] = $normalizedValue;
            }
        }

        return $values;
    }

    public function supportsValue(Value $value): bool
    {
        return $value instanceof RelationListValue;
    }
}
