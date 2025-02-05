<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser;

use Ibexa\CorporateAccount\REST\Input\Parser\BaseContentParser;
use Ibexa\CorporateAccount\REST\Input\Parser\BaseMemberInputParser;
use Ibexa\CorporateAccount\REST\Validation\Constraint as IbexaAssert;
use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
final class MemberUpdateValidatorBuilder extends BaseInputParserCollectionValidatorBuilder
{
    private RequestParser $requestParser;

    public function __construct(ValidatorInterface $validator, RequestParser $requestParser)
    {
        parent::__construct($validator);

        $this->requestParser = $requestParser;
    }

    protected function getCollectionConstraints(): array
    {
        return [
            BaseMemberInputParser::EMAIL_KEY => new Assert\Optional([new Assert\Email()]),
            // more strict password validation is performed by PHP API
            BaseMemberInputParser::PASSWORD_KEY => new Assert\Optional(
                [new Assert\NotBlank()]
            ),
            BaseMemberInputParser::ENABLED_KEY => new Assert\Optional([
                new Assert\Choice(
                    ['message' => 'Expected either "true" or "false"'],
                    ['true', 'false']
                ),
            ]),
            BaseMemberInputParser::MAX_LOGIN_KEY => new Assert\Optional(
                [new Assert\Type('numeric')]
            ),
            BaseMemberInputParser::ROLE_KEY => new Assert\Optional([
                new Assert\Type('array'),
                new Assert\Collection(
                    [
                        '_href' => new IbexaAssert\Resource($this->requestParser),
                        '_media-type' => new IbexaAssert\MediaType('Role'),
                    ]
                ),
            ]),
            BaseContentParser::FIELDS_KEY => new Assert\Optional(),
            BaseContentParser::INTERNAL_URL_KEY => new IbexaAssert\Resource(
                $this->requestParser
            ),
        ];
    }
}
