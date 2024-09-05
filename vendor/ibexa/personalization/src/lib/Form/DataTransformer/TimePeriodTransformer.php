<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use DateInterval;
use Exception;
use Ibexa\Personalization\Form\Data\TimePeriodData;
use Ibexa\Personalization\Form\Type\Model\TimePeriodType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<
 *     \Ibexa\Personalization\Form\Data\TimePeriodData,
 *     \Ibexa\Personalization\Form\Data\TimePeriodData
 * >
 */
final class TimePeriodTransformer implements DataTransformerInterface
{
    private const PERIOD_PATTERN = '/P((\d+)D|T(\d+)H)/';

    public function transform($value): TimePeriodData
    {
        if (!$value instanceof TimePeriodData) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be of type: %s, %s given.',
                    TimePeriodData::class,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        $period = $value->getPeriod();
        if (null === $period) {
            return $value;
        }

        if (false === preg_match_all(self::PERIOD_PATTERN, $period, $matches)) {
            throw new TransformationFailedException('Failed to match pattern');
        }

        $days = $matches[2][0];
        $hours = $matches[3][0];

        if (!empty($days)) {
            $value->setQuantifier(TimePeriodType::QUANTIFIER_DAYS);
            $value->setPeriod($days);
        } elseif (!empty($hours)) {
            $value->setQuantifier(TimePeriodType::QUANTIFIER_HOURS);
            $value->setPeriod($hours);
        }

        return $value;
    }

    /**
     * @param \Ibexa\Personalization\Form\Data\TimePeriodData $value
     */
    public function reverseTransform($value): TimePeriodData
    {
        $period = $value->getPeriod();
        $quantifier = $value->getQuantifier();

        if (null === $period) {
            return $value;
        }

        if (!empty($quantifier)) {
            if (!is_numeric($period) || $period < 1) {
                throw new TransformationFailedException(
                    sprintf(
                        'Invalid data. Value should be positive integer, "%s" given.',
                        $period
                    )
                );
            }

            if ($quantifier === TimePeriodType::QUANTIFIER_DAYS) {
                $value->setPeriod('P' . $period . $quantifier);
            } elseif ($value->getQuantifier() === TimePeriodType::QUANTIFIER_HOURS) {
                $value->setPeriod('PT' . $period . $quantifier);
            }

            $value->setQuantifier();
        }

        try {
            new DateInterval($value->getPeriod());
        } catch (Exception $e) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be compatible with ISO 8601 duration specification. "%s" given.',
                    $period
                )
            );
        }

        return $value;
    }
}

class_alias(TimePeriodTransformer::class, 'Ibexa\Platform\Personalization\Form\DataTransformer\RelevantHistoryTransformer');
