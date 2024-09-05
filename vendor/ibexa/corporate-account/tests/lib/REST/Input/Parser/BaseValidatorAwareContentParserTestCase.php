<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\Rest\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\CorporateAccount\REST\Exception\InputValidationFailedException;
use Ibexa\CorporateAccount\REST\Input\Parser\BaseContentParser;
use Ibexa\Rest\RequestParser;
use Ibexa\Tests\CorporateAccount\PHPUnit\Constraint\ViolationListMatchesExpectedViolations;
use Ibexa\Tests\CorporateAccount\PHPUnit\Value\SymfonyConstraintData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;

abstract class BaseValidatorAwareContentParserTestCase extends TestCase
{
    public const URL_ROUTE_MATCHER_MAP = [
        '/corporate/companies/1/members' => 'ibexa.rest.corporate_account.members.create',
        '/corporate/companies' => 'ibexa.rest.corporate_account.companies.create',
        '/corporate/companies/1' => 'ibexa.rest.corporate_account.companies.update',
    ];

    protected BaseContentParser $parser;

    /** @var \Ibexa\Contracts\Rest\Input\ParsingDispatcher&\PHPUnit\Framework\MockObject\MockObject */
    protected ParsingDispatcher $parsingDispatcherMock;

    /** @var \Ibexa\Rest\RequestParser&\PHPUnit\Framework\MockObject\MockObject */
    protected RequestParser $requestParserMock;

    /**
     * @return iterable<string, array{array<mixed>, array<SymfonyConstraintData>}>
     */
    abstract public function getDataForTestParseValidatesInputArray(): iterable;

    protected function setUp(): void
    {
        $this->parsingDispatcherMock = $this->createMock(ParsingDispatcher::class);

        $this->requestParserMock = $this->createMock(RequestParser::class);
        $this->requestParserMock->method('parseHref')
            ->with('/corporate/companies/1')
            ->willReturn('1');

        $this->requestParserMock->method('parse')
            ->withAnyParameters()
            ->willReturnCallback(static function (string $url): array {
                if (isset(self::URL_ROUTE_MATCHER_MAP[$url])) {
                    return ['_route' => self::URL_ROUTE_MATCHER_MAP[$url]];
                }

                throw new InvalidArgumentException("No route matched '$url'");
            });
    }

    /**
     * @dataProvider getDataForTestParseValidatesInputArray
     *
     * @param array<mixed> $data
     * @param array<\Ibexa\Tests\CorporateAccount\PHPUnit\Value\SymfonyConstraintData> $expectedViolationsData
     */
    public function testParseValidatesInputArray(array $data, array $expectedViolationsData): void
    {
        try {
            $this->parser->parse($data, $this->parsingDispatcherMock);
            self::fail(
                sprintf(
                    '%s has not been thrown for "%s" data set',
                    InputValidationFailedException::class,
                    $this->getDataSetAsString(false)
                )
            );
        } catch (InputValidationFailedException $e) {
            self::assertThat(
                $e->getErrors(),
                new ViolationListMatchesExpectedViolations($expectedViolationsData),
                sprintf('Violation list for %s is incorrect', __METHOD__)
            );
        }
    }

    protected function buildSymfonyValidator(): ValidatorInterface
    {
        return (new ValidatorBuilder())->getValidator();
    }

    protected function buildExpectedConstraintDataForMissingField(
        string $field,
        ?string $propertyPath = null
    ): SymfonyConstraintData {
        if (null === $propertyPath) {
            $propertyPath = "[$field]";
        }

        return new SymfonyConstraintData(
            'This field is missing.',
            $propertyPath,
            ['{{ field }}' => "\"$field\""]
        );
    }
}
