<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL\Utils;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Utils\DateFormatter;
use PHPUnit\Framework\TestCase;

final class DateFormatterTest extends TestCase
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Utils\DateFormatter */
    private $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DateFormatter();
    }

    public function dataProviderForToElasticSearchDateTime(): iterable
    {
        $timestamp = time();

        yield 'null' => [null, null];
        yield 'timestamp' => [$timestamp, date('Y-m-d\TH:i:sO', $timestamp)];
        yield 'string' => [
            '2020-01-01 00:00:00',
            '2020-01-01T00:00:00+0000',
        ];
    }

    /**
     * @dataProvider dataProviderForToElasticSearchDateTime
     */
    public function testToElasticSearchDateTime($value, ?string $expectedOutput): void
    {
        $this->assertEquals(
            $expectedOutput,
            $this->formatter->toElasticSearchDateTime($value),
        );
    }

    public function testToElasticSearchDateTimeInvalidArgumentException(): void
    {
        $value = 'invalid date';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date provided: ' . $value);

        $this->formatter->toElasticSearchDateTime($value);
    }
}

class_alias(DateFormatterTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\Utils\DateFormatterTest');
