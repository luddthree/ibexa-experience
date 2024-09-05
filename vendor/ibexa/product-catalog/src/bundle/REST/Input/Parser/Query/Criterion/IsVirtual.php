<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual as IsVirtualCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;
use Ibexa\Rest\Input\ParserTools;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class IsVirtual extends BaseParser implements ProductCriterionInterface
{
    private ValidatorInterface $validator;

    private ParserTools $parserTools;

    private const IS_VIRTUAL_CRITERION = 'IsVirtualCriterion';

    public function __construct(
        ValidatorInterface $validator,
        ParserTools $parserTools
    ) {
        $this->validator = $validator;
        $this->parserTools = $parserTools;
    }

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): IsVirtualCriterion
    {
        $this->validateInputArray($data);

        return new IsVirtualCriterion(
            $this->parserTools->parseBooleanValue($data[self::IS_VIRTUAL_CRITERION])
        );
    }

    public function getName(): string
    {
        return self::IS_VIRTUAL_CRITERION;
    }

    /**
     * Combined constraints Assert\Type and Assert\Choice are used to validate data input
     * due to differences between data type of boolean values passed by XML and JSON.
     * In XML format values true and false are visible as a string, so Assert\Type('boolean') returns validation error.
     * This not occurs in JSON format where each passed boolean values is a bool.
     *
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    private function validateInputArray(array $data): void
    {
        $errors = $this->validator
            ->validate(
                $data,
                new Assert\Collection(
                    ['fields' => [self::IS_VIRTUAL_CRITERION => new Assert\AtLeastOneOf([
                        'constraints' => [
                            new Assert\Type('boolean'),
                            new Assert\Choice(
                                [
                                    'true', 'false',
                                ]
                            ),
                        ],
                    ])]]
                )
            );
        if ($errors->count() > 0) {
            throw new Exceptions\Parser('Invalid <' . self::IS_VIRTUAL_CRITERION . '> format');
        }
    }
}
