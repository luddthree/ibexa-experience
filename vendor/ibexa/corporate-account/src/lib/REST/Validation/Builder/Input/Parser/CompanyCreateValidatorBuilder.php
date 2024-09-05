<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser;

use Ibexa\CorporateAccount\REST\Input\Parser\BaseContentParser;
use Ibexa\CorporateAccount\REST\Input\Parser\CompanyCreate;
use Ibexa\CorporateAccount\REST\Validation\Constraint as IbexaAssert;
use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
final class CompanyCreateValidatorBuilder extends BaseInputParserCollectionValidatorBuilder
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
            CompanyCreate::REMOTE_ID_KEY => new Assert\Optional(
                [
                    new Assert\Type('string'),
                    new Assert\NotBlank(),
                ]
            ),
            CompanyCreate::MODIFICATION_DATE_KEY => new Assert\Optional(
                [
                    new Assert\Type('string'),
                    new Assert\NotBlank(),
                ]
            ),
            CompanyCreate::USER_KEY => new Assert\Optional([
                new Assert\Type('array'),
                new Assert\Collection(
                    [
                        '_href' => new IbexaAssert\Resource($this->requestParser),
                        '_media-type' => new IbexaAssert\MediaType('User'),
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
