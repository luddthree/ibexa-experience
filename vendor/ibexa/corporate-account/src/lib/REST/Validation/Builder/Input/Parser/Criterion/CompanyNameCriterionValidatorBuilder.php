<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\CorporateAccount\REST\Input\Parser\Criterion\CompanyName;
use Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\BaseInputParserCollectionValidatorBuilder;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @internal
 */
final class CompanyNameCriterionValidatorBuilder extends BaseInputParserCollectionValidatorBuilder
{
    public const ALLOWED_OPERATORS = [
        Operator::IN,
        Operator::EQ,
        Operator::LIKE,
        Operator::CONTAINS,
    ];

    protected function getCollectionConstraints(): array
    {
        return [
            CompanyName::COMPANY_NAME_CRITERION => new Assert\Collection([
                CompanyName::OPERATOR_KEY => new Assert\Choice(self::ALLOWED_OPERATORS),
                CompanyName::VALUE_KEY => new Assert\AtLeastOneOf([
                    new Assert\Type('string'),
                    new Assert\Type('array'),
                ]),
            ]),
        ];
    }
}
