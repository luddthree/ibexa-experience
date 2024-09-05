<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Ibexa\Rest\Input\Handler\Json;
use Ibexa\Rest\Input\Handler\Xml;

abstract class AbstractCriterionParserTestCase extends IbexaKernelTestCase
{
    protected ParsingDispatcher $parsingDispatcher;

    private Xml $xml;

    private Json $json;

    protected function setUp(): void
    {
        $this->parsingDispatcher = $this->getIbexaTestCore()->getServiceByClassName(ParsingDispatcher::class);
        $this->xml = $this->getIbexaTestCore()->getServiceByClassName(Xml::class);
        $this->json = $this->getIbexaTestCore()->getServiceByClassName(Json::class);
    }

    /**
     * @dataProvider provideXml
     */
    final public function testXmlConversion(string $xml, CriterionInterface $expectedCriterion): void
    {
        $data = $this->xml->convert($xml);
        $result = $this->parsingDispatcher->parse(
            $data,
            'application/vnd.ibexa.api.internal.activity_log.criteria'
        );

        self::assertIsArray($result);
        self::assertContainsOnlyInstancesOf(CriterionInterface::class, $result);
        self::assertCount(1, $result);
        [$criterion] = $result;
        self::assertEquals($expectedCriterion, $criterion);
    }

    /**
     * @dataProvider provideJson
     */
    final public function testJsonConversion(string $json, CriterionInterface $expectedCriterion): void
    {
        $data = $this->json->convert($json);
        $result = $this->parsingDispatcher->parse(
            $data,
            'application/vnd.ibexa.api.internal.activity_log.criterion'
        );

        self::assertInstanceOf(CriterionInterface::class, $result);
        self::assertEquals($expectedCriterion, $result);
    }

    /**
     * @return iterable<array{string, \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface}>
     */
    abstract public static function provideXml(): iterable;

    /**
     * @return iterable<array{string, \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface}>
     */
    abstract public static function provideJson(): iterable;
}
