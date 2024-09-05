<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\CorporateAccount\REST\Exception\InputValidationFailedException;
use Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\MemberCreateValidatorBuilder;
use Ibexa\CorporateAccount\REST\Value\MemberCreateStruct;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 *
 * @phpstan-import-type RESTContentFieldValueInputArray from BaseContentParser
 * @phpstan-import-type RESTResourceReferenceInputArray from BaseContentParser
 *
 * @phpstan-type RESTMemberCreateInputArray array{
 *     login: string,
 *     email: string,
 *     password: string,
 *     Role: RESTResourceReferenceInputArray,
 *     fields: array<array{field: RESTContentFieldValueInputArray}>,
 * }
 */
final class MemberCreate extends BaseMemberInputParser
{
    public const LOGIN_KEY = 'login';

    private MemberService $memberService;

    public function __construct(
        RequestParser $requestParser,
        FieldTypeParser $fieldTypeParser,
        MemberService $memberService,
        RoleService $roleService,
        ValidatorInterface $validator
    ) {
        parent::__construct($requestParser, $fieldTypeParser, $roleService, $validator);

        $this->memberService = $memberService;
    }

    /**
     * @phpstan-param RESTMemberCreateInputArray $data
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): MemberCreateStruct
    {
        $this->assertMemberCreateInputIsValid($data);

        $memberCreateStruct = $this->memberService->newMemberCreateStruct(
            $data[self::LOGIN_KEY],
            $data[self::EMAIL_KEY],
            $data[self::PASSWORD_KEY]
        );
        $role = $this->loadRole($data[self::ROLE_KEY]);

        $this->setContentFields(
            $memberCreateStruct,
            $memberCreateStruct->contentType,
            $data['fields']['field'] ?? []
        );

        return new MemberCreateStruct($memberCreateStruct, $role);
    }

    /**
     *  @phpstan-param RESTMemberCreateInputArray $data
     */
    private function assertMemberCreateInputIsValid(array $data): void
    {
        $validatorBuilder = new MemberCreateValidatorBuilder(
            $this->validator,
            $this->requestParser
        );

        $validatorBuilder->validateInputArray($data);
        $violations = $validatorBuilder->build()->getViolations();

        if ($violations->count() > 0) {
            throw new InputValidationFailedException('MemberCreate', $violations);
        }
    }
}
