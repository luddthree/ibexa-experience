<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Factory\DateTime\GranularityFactoryInterface;
use Ibexa\Personalization\Form\Data\DateIntervalData;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class DateIntervalTransformer implements DataTransformerInterface
{
    /** @var \Ibexa\Personalization\Factory\DateTime\GranularityFactoryInterface */
    private $granularityFactory;

    public function __construct(GranularityFactoryInterface $granularityFactory)
    {
        $this->granularityFactory = $granularityFactory;
    }

    public function transform($value)
    {
        return null;
    }

    public function reverseTransform($value): GranularityDateTimeRange
    {
        if (false === $value instanceof DateIntervalData) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be type of: %s. %s given.',
                    DateIntervalData::class,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        if (null !== $value->getEndDate()) {
            return $this->granularityFactory->createFromEndDateTimestampAndInterval(
                $value->getDateInterval(),
                $value->getEndDate()
            );
        }

        return $this->granularityFactory->createFromInterval(
            $value->getDateInterval()
        );
    }
}

class_alias(DateIntervalTransformer::class, 'Ibexa\Platform\Personalization\Form\DataTransformer\DateIntervalTransformer');
