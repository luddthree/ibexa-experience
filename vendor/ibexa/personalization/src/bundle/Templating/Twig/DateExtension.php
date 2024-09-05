<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Templating\Twig;

use DateInterval;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class DateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_personalization_transform_interval_to_date_range',
                [$this, 'endDateAndIntervalTransform']
            ),
        ];
    }

    /**
     * @throws \Exception
     */
    public function endDateAndIntervalTransform(int $timestamp, string $interval): array
    {
        return [
            'start_date' => $this->createStartDateTime($timestamp, $interval),
            'end_date' => $this->createEndDateTime($timestamp),
        ];
    }

    /**
     * @throws \Exception
     */
    public function createStartDateTime(int $timestamp, string $interval): DateTime
    {
        $startDate = new DateTime();

        if ($timestamp > 0) {
            $startDate->setTimestamp($timestamp);
        }

        $startDate->sub(
            new DateInterval($interval)
        );

        return $startDate;
    }

    public function createEndDateTime(int $timestamp): DateTime
    {
        $endDate = new DateTime();

        return $endDate->setTimestamp($timestamp);
    }
}

class_alias(DateExtension::class, 'Ibexa\Platform\Bundle\Personalization\Templating\Twig\DateExtension');
