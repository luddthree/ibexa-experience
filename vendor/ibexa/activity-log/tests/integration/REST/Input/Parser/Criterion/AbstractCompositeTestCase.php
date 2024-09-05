<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\Rest\Input\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Ibexa\Rest\Input\Handler\Json;
use Ibexa\Rest\Input\Handler\Xml;

abstract class AbstractCompositeTestCase extends IbexaKernelTestCase
{
    private ParsingDispatcher $parsingDispatcher;

    private Xml $xml;

    private Json $json;

    protected function setUp(): void
    {
        $this->parsingDispatcher = $this->getIbexaTestCore()->getServiceByClassName(ParsingDispatcher::class);
        $this->xml = $this->getIbexaTestCore()->getServiceByClassName(Xml::class);
        $this->json = $this->getIbexaTestCore()->getServiceByClassName(Json::class);

        $this->configureParsingDispatcher();
    }

    abstract protected function configureParsingDispatcher(): void;

    /**
     * @param array<mixed> $value
     */
    protected function addParser(string $mediaType, array $value = []): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->expects(self::once())
            ->method('parse')
            ->with(
                self::identicalTo($value),
                self::identicalTo($this->parsingDispatcher)
            )
            ->willReturn($this->createMock(CriterionInterface::class));

        $this->parsingDispatcher->addParser($mediaType, $parser);
    }

    abstract protected function getXml(): string;

    abstract protected function getJson(): string;

    abstract protected function getExpectedCriterion(): CriterionInterface;

    final public function testXmlConversion(): void
    {
        $xml = $this->getXml();
        $expectedCriterion = $this->getExpectedCriterion();

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

    final public function testJsonConversion(): void
    {
        $json = $this->getJson();
        $expectedCriterion = $this->getExpectedCriterion();

        $data = $this->json->convert($json);
        $result = $this->parsingDispatcher->parse(
            $data,
            'application/vnd.ibexa.api.internal.activity_log.criterion'
        );

        self::assertInstanceOf(CriterionInterface::class, $result);
        self::assertEquals($expectedCriterion, $result);
    }
}
