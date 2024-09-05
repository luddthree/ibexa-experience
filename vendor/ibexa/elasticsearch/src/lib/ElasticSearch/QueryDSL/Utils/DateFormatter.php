<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Utils;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

/**
 * @internal
 */
final class DateFormatter
{
    /**
     * @param string|int|null $value
     *
     * @throw \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function toElasticSearchDateTime($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            $date = new DateTimeImmutable("@{$value}");
        } else {
            try {
                $date = new DateTimeImmutable($value);
            } catch (Exception $e) {
                throw new InvalidArgumentException('$value', 'Invalid date provided: ' . $value);
            }
        }

        return $date->format(DateTimeInterface::ISO8601);
    }
}

class_alias(DateFormatter::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\Utils\DateFormatter');
