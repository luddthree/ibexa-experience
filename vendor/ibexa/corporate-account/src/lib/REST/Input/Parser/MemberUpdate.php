<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\CorporateAccount\REST\Exception\InputValidationFailedException;
use Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\MemberUpdateValidatorBuilder;
use Ibexa\CorporateAccount\REST\Value\MemberUpdateStruct;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 *
 * @phpstan-import-type RESTContentFieldValueInputArray from BaseContentParser
 * @phpstan-import-type RESTResourceReferenceInputArray from BaseContentParser
 *
 * @phpstan-type RESTMemberUpdateInputArray array{
 *     __url: string,
 *     email: string,
 *     password: string,
 *     enabled: 'true'|'false',
 *     maxLogin: int,
 *     Role: RESTResourceReferenceInputArray,
 *     fields: array<array{field: RESTContentFieldValueInputArray}>,
 * }
 */
final class MemberUpdate extends BaseMemberInputParser
{
    private MemberService $memberService;

    private CompanyService $companyService;

    public function __construct(
        RequestParser $requestParser,
        FieldTypeParser $fieldTypeParser,
        MemberService $memberService,
        CompanyService $companyService,
        RoleService $roleService,
        ValidatorInterface $validator
    ) {
        parent::__construct($requestParser, $fieldTypeParser, $roleService, $validator);

        $this->memberService = $memberService;
        $this->companyService = $companyService;
    }

    /**
     * @phpstan-param RESTMemberUpdateInputArray $data
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \JsonException
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): MemberUpdateStruct
    {
        $this->assertMemberUpdateInputIsValid($data);

        $member = $this->loadMemberFromData($data);

        $memberUpdateStruct = $this->memberService->newMemberUpdateStruct();

        // intentionally not using null coalescing to distinguish between set to null and not set
        if (array_key_exists(self::EMAIL_KEY, $data)) {
            $memberUpdateStruct->email = $data[self::EMAIL_KEY];
        }
        if (array_key_exists(self::PASSWORD_KEY, $data)) {
            $memberUpdateStruct->password = $data[self::PASSWORD_KEY];
        }
        if (array_key_exists(self::ENABLED_KEY, $data)) {
            $memberUpdateStruct->enabled = $this->parseBoolean($data[self::ENABLED_KEY]);
        }
        if (array_key_exists(self::MAX_LOGIN_KEY, $data)) {
            $memberUpdateStruct->maxLogin = (int)$data[self::MAX_LOGIN_KEY];
        }

        $this->setContentFields(
            $memberUpdateStruct->contentUpdateStruct,
            $member->getContentType(),
            $data['fields']['field'] ?? []
        );

        return new MemberUpdateStruct(
            $memberUpdateStruct,
            array_key_exists(self::ROLE_KEY, $data) ? $this->loadRole($data[self::ROLE_KEY]) : null,
        );
    }

    /**
     * @param array{__url: string} $data
     */
    private function loadMemberFromData(array $data): Member
    {
        $parsedResult = $this->requestParser->parse($data[self::INTERNAL_URL_KEY]);
        $companyId = (int)$parsedResult['companyId'];
        $memberId = (int)$parsedResult['memberId'];

        return $this->memberService->getMember(
            $memberId,
            $this->companyService->getCompany($companyId)
        );
    }

    /**
     * @throws \JsonException
     */
    private function parseBoolean(string $value): bool
    {
        // json_decode properly decodes "true" and "false" strings into boolean strict type
        return json_decode(
            $value,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     *  @phpstan-param RESTMemberUpdateInputArray $data
     */
    private function assertMemberUpdateInputIsValid(array $data): void
    {
        $validatorBuilder = new MemberUpdateValidatorBuilder(
            $this->validator,
            $this->requestParser
        );

        $validatorBuilder->validateInputArray($data);
        $violations = $validatorBuilder->build()->getViolations();

        if ($violations->count() > 0) {
            throw new InputValidationFailedException('MemberUpdate', $violations);
        }
    }
}
