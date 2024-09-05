<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser;

use Ibexa\CorporateAccount\REST\Input\Parser\BaseContentParser;
use Ibexa\CorporateAccount\REST\Validation\Constraint as IbexaAssert;
use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
final class CompanyUpdateValidatorBuilder extends BaseInputParserCollectionValidatorBuilder
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
            // given there's nothing else to update for Company, let's require some fields
            BaseContentParser::FIELDS_KEY => new Assert\Required(
                [
                    'constraints' => [
                        new Assert\Sequentially(
                            [
                                new Assert\Type('array'),
                                new Assert\Count(null, 1),
                            ]
                        ),
                    ],
                ]
            ),
            BaseContentParser::INTERNAL_URL_KEY => new IbexaAssert\Resource(
                $this->requestParser
            ),
        ];
    }
}
