<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;
use Ibexa\Contracts\Rest\Input\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Ibexa\Rest\Input\Handler\Json;
use Ibexa\Rest\Input\Handler\Xml;
use LogicException;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;

/**
 * @covers \Ibexa\ActivityLog\REST\Input\Parser\ActivityLogGroupListInput
 *
 * @phpstan-type ParserMap array<
 *     string,
 *     class-string|array{
 *         data?: array<mixed>,
 *         class: class-string,
 *         invocation_rule?: \PHPUnit\Framework\MockObject\Rule\InvokedCount,
 *     },
 * >
 */
final class ActivityLogGroupListInputTest extends IbexaKernelTestCase
{
    private ParsingDispatcher $parsingDispatcher;

    private Xml $xml;

    private Json $json;

    protected function setUp(): void
    {
        $this->parsingDispatcher = $this->getIbexaTestCore()->getServiceByClassName(ParsingDispatcher::class);
        $this->xml = $this->getIbexaTestCore()->getServiceByClassName(Xml::class);
        $this->json = $this->getIbexaTestCore()->getServiceByClassName(Json::class);
    }

    /**
     * @dataProvider provideJson
     *
     * @phpstan-param ParserMap $parserMap
     */
    public function testJsonConversion(
        string $json,
        array $parserMap,
        int $limit = 25,
        int $offset = 0,
        int $criteriaCount = 0,
        int $sortClausesCount = 1 // LoggedAsSortClause is default, if empty
    ): void {
        $this->addParsers($parserMap);

        $data = $this->json->convert($json);

        $query = $this->parsingDispatcher->parse($data, 'application/vnd.ibexa.api.ActivityLogGroupListInput');

        self::assertInstanceOf(Query::class, $query);
        self::assertSame($limit, $query->limit);
        self::assertSame($offset, $query->offset);
        self::assertCount($criteriaCount, $query->criteria);
        self::assertCount($sortClausesCount, $query->sortClauses);
    }

    /**
     * @dataProvider provideXml
     *
     * @phpstan-param ParserMap $parserMap
     */
    public function testXmlConversion(
        string $json,
        array $parserMap,
        int $limit = 25,
        int $offset = 0,
        int $criteriaCount = 0,
        int $sortClausesCount = 1 // LoggedAtSortClause is default, if empty
    ): void {
        $this->addParsers($parserMap);

        $data = $this->xml->convert($json);

        self::assertCount(1, $data, 'XML Handler returned more than 1 element in root');
        self::assertArrayHasKey('ActivityLogListInput', $data);

        // TODO: Fix dispatcher unable to work with empty XML tags in ibexa/rest package
        $input = $data['ActivityLogListInput'] ?? [];

        $query = $this->parsingDispatcher->parse($input, 'application/vnd.ibexa.api.ActivityLogGroupListInput');

        self::assertInstanceOf(Query::class, $query);
        self::assertSame($limit, $query->limit);
        self::assertSame($offset, $query->offset);
        self::assertCount($criteriaCount, $query->criteria);
        self::assertCount($sortClausesCount, $query->sortClauses);
    }

    /**
     * @phpstan-return iterable<array{
     *     string,
     *     ParserMap,
     *     2?: int,
     *     3?: int,
     *     4?: int,
     *     5?: int,
     * }>
     */
    public static function provideJson(): iterable
    {
        yield [
            '{}',
            [
                'application/vnd.ibexa.api.internal.activity_log.sort_clause' => [
                    'class' => SortClauseInterface::class,
                    'invocation_rule' => self::never(),
                ],
                'application/vnd.ibexa.api.internal.activity_log.criterion' => [
                    'class' => CriterionInterface::class,
                    'invocation_rule' => self::never(),
                ],
            ],
        ];

        yield [
            <<<JSON
            {
              "limit": 10,
              "offset": 42
            }
            JSON,
            [
                'application/vnd.ibexa.api.internal.activity_log.sort_clause' => [
                    'class' => SortClauseInterface::class,
                    'invocation_rule' => self::never(),
                ],
                'application/vnd.ibexa.api.internal.activity_log.criterion' => [
                    'class' => CriterionInterface::class,
                    'invocation_rule' => self::never(),
                ],
            ],
            10,
            42,
            0,
            1,
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *     string,
     *     ParserMap,
     *     2?: int,
     *     3?: int,
     *     4?: int,
     *     5?: int,
     * }>
     */
    public static function provideXml(): iterable
    {
        yield [
            '<ActivityLogListInput />',
            [
                'application/vnd.ibexa.api.internal.activity_log.sort_clause' => [
                    'class' => SortClauseInterface::class,
                    'invocation_rule' => self::never(),
                ],
                'application/vnd.ibexa.api.internal.activity_log.criterion' => [
                    'class' => CriterionInterface::class,
                    'invocation_rule' => self::never(),
                ],
            ],
        ];

        yield [
            <<<XML
            <ActivityLogListInput>
                <limit>10</limit>
                <offset>42</offset>
            </ActivityLogListInput>
            XML,
            [
                'application/vnd.ibexa.api.internal.activity_log.sort_clause' => [
                    'class' => SortClauseInterface::class,
                    'invocation_rule' => self::never(),
                ],
                'application/vnd.ibexa.api.internal.activity_log.criterion' => [
                    'class' => CriterionInterface::class,
                    'invocation_rule' => self::never(),
                ],
            ],
            10,
            42,
            0,
            1,
        ];
    }

    /**
     * @param array<mixed> $value
     * @param class-string $className
     */
    private function addParser(string $mediaType, string $className, array $value = [], ?InvokedCount $rule = null): void
    {
        $rule ??= self::once();
        $parser = $this->createMock(Parser::class);
        $parser->expects($rule)
            ->method('parse')
            ->with(
                self::identicalTo($value),
                self::identicalTo($this->parsingDispatcher)
            )
            ->willReturn($this->createMock($className));

        $this->parsingDispatcher->addParser($mediaType, $parser);
    }

    /**
     * @phpstan-param ParserMap $parserMap
     */
    private function addParsers(array $parserMap): void
    {
        foreach ($parserMap as $key => $parserData) {
            if (is_string($parserData)) {
                $this->addParser($key, $parserData);
            } elseif (is_array($parserData)) {
                $this->addParser(
                    $key,
                    $parserData['class'],
                    $parserData['data'] ?? [],
                    $parserData['invocation_rule'] ?? self::once(),
                );
            } else {
                throw new LogicException(sprintf(
                    'Invalid data provided from %s provider. Expected one of: "%s". Received "%s"',
                    'provideJson',
                    implode('","', ['string', 'array']),
                    get_debug_type($parserData),
                ));
            }
        }
    }
}
