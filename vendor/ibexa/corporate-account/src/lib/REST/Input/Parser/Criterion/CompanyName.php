<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Input\Parser\Criterion;

use Ibexa\Contracts\CorporateAccount\Values\Query\Criterion\CompanyName as CompanyNameCriterion;
use Ibexa\Contracts\Rest\Input\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\CorporateAccount\REST\Exception\InputValidationFailedException;
use Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\Criterion\CompanyNameCriterionValidatorBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
class CompanyName extends Parser
{
    public const COMPANY_NAME_CRITERION = 'CompanyNameCriterion';
    public const OPERATOR_KEY = 'Operator';
    public const VALUE_KEY = 'Value';

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array{CompanyNameCriterion: array{Operator: string, Value: string|array<string>}} $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CompanyNameCriterion
    {
        $validatorBuilder = new CompanyNameCriterionValidatorBuilder($this->validator);
        $validatorBuilder->validateInputArray($data);
        $violations = $validatorBuilder->build()->getViolations();
        if ($violations->count() > 0) {
            throw new InputValidationFailedException('Criterion\CompanyName', $violations);
        }

        return new CompanyNameCriterion(
            $data[self::COMPANY_NAME_CRITERION][self::OPERATOR_KEY],
            $data[self::COMPANY_NAME_CRITERION][self::VALUE_KEY]
        );
    }
}
