<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Input\Parser;

use DateTime;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\CorporateAccount\REST\Exception\InputValidationFailedException;
use Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\CompanyCreateValidatorBuilder;
use Ibexa\CorporateAccount\REST\Value\CompanyCreateStruct;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 *
 * @phpstan-import-type RESTContentFieldValueInputArray from BaseContentParser
 *
 * @phpstan-type RESTCompanyCreateInputArray array{
 *     remoteId: string,
 *     modificationDate: string,
 *     User: array<mixed>,
 *     fields: array<array{field: RESTContentFieldValueInputArray}>,
 * }
 */
final class CompanyCreate extends BaseContentParser
{
    public const REMOTE_ID_KEY = 'remoteId';
    public const MODIFICATION_DATE_KEY = 'modificationDate';
    public const USER_KEY = 'User';

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
     * @phpstan-param RESTCompanyCreateInputArray $data
     *
     * @throws \Exception
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CompanyCreateStruct
    {
        $this->assertCompanyCreateInputIsValid($data);

        $companyCreateStruct = $this->companyService->newCompanyCreateStruct();

        if (array_key_exists(self::REMOTE_ID_KEY, $data)) {
            $companyCreateStruct->remoteId = $data[self::REMOTE_ID_KEY];
        }

        if (array_key_exists(self::MODIFICATION_DATE_KEY, $data)) {
            $companyCreateStruct->modificationDate = new DateTime(
                $data[self::MODIFICATION_DATE_KEY]
            );
        }

        if (array_key_exists(self::USER_KEY, $data) && is_array($data[self::USER_KEY])) {
            $companyCreateStruct->ownerId = $this->requestParser->parseHref(
                $data[self::USER_KEY]['_href'],
                'userId'
            );
        }

        $this->setContentFields(
            $companyCreateStruct,
            $companyCreateStruct->contentType,
            $data['fields']['field'] ?? []
        );

        return new CompanyCreateStruct($companyCreateStruct);
    }

    /**
     * @phpstan-param RESTCompanyCreateInputArray $data
     */
    private function assertCompanyCreateInputIsValid(array $data): void
    {
        $validatorBuilder = new CompanyCreateValidatorBuilder(
            $this->validator,
            $this->requestParser
        );

        $validatorBuilder->validateInputArray($data);
        $violations = $validatorBuilder->build()->getViolations();

        if ($violations->count() > 0) {
            throw new InputValidationFailedException('CompanyCreate', $violations);
        }
    }
}
