<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\CorporateAccount\REST\Exception\InputValidationFailedException;
use Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\CompanyUpdateValidatorBuilder;
use Ibexa\CorporateAccount\REST\Value\CompanyUpdateStruct;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 *
 * @phpstan-import-type RESTContentFieldValueInputArray from BaseContentParser
 *
 * @phpstan-type RESTCompanyUpdateInputArray array{
 *      __url: string,
 *      fields: array<array{field: RESTContentFieldValueInputArray}>,
 * }
 */
final class CompanyUpdate extends BaseContentParser
{
    protected CompanyService $companyService;

    public function __construct(
        RequestParser $requestParser,
        CompanyService $companyService,
        FieldTypeParser $fieldTypeParser,
        ValidatorInterface $validator
    ) {
        parent::__construct($requestParser, $fieldTypeParser, $validator);

        $this->companyService = $companyService;
    }

    /**
     * @phpstan-param RESTCompanyUpdateInputArray $data
     *
     * @throws \Exception
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CompanyUpdateStruct
    {
        $this->assertCompanyUpdateInputIsValid($data);

        $company = $this->companyService->getCompany($this->getCompanyIdFromData($data));

        $companyUpdateStruct = $this->companyService->newCompanyUpdateStruct();

        $this->setContentFields(
            $companyUpdateStruct,
            $company->getContent()->getContentType(),
            $data['fields']['field'] ?? []
        );

        return new CompanyUpdateStruct($companyUpdateStruct);
    }

    /**
     * @param array{__url: string} $data
     */
    private function getCompanyIdFromData(array $data): int
    {
        return (int)$this->requestParser->parseHref(
            $data['__url'],
            'companyId'
        );
    }

    /**
     * @phpstan-param RESTCompanyUpdateInputArray $data
     */
    private function assertCompanyUpdateInputIsValid(array $data): void
    {
        $validatorBuilder = new CompanyUpdateValidatorBuilder(
            $this->validator,
            $this->requestParser
        );

        $validatorBuilder->validateInputArray($data);
        $violations = $validatorBuilder->build()->getViolations();

        if ($violations->count() > 0) {
            throw new InputValidationFailedException('CompanyUpdate', $violations);
        }
    }
}
