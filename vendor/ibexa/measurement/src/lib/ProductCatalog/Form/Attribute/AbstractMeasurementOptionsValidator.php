<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\Core\Options\OptionsBag;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
abstract class AbstractMeasurementOptionsValidator implements OptionsValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return array<\Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError>
     */
    final protected function convertToOptionErrors(ConstraintViolationListInterface $errors): array
    {
        $optionErrors = [];
        foreach ($errors as $error) {
            $optionErrors[] = new OptionsValidatorError($error->getPropertyPath(), (string)$error->getMessage());
        }

        return $optionErrors;
    }

    final public function validateOptions(OptionsBag $options): array
    {
        $validator = $this->validator;

        if (!$options instanceof AttributeDefinitionOptions) {
            throw new InvalidArgumentException(sprintf(
                'Expected instance of %s. Received %s.',
                AttributeDefinitionOptions::class,
                get_class($options),
            ));
        }

        $errors = $validator->validate($options, [
            ...$this->getClassConstraints(),
            new OptionsBagConstraint($this->getConstraints($options)),
        ]);

        return $this->convertToOptionErrors($errors);
    }

    /**
     * @return array<string, \Symfony\Component\Validator\Constraint|array<\Symfony\Component\Validator\Constraint>>
     */
    protected function getConstraints(OptionsBag $options): array
    {
        return [
            '[type]' => [
                new Assert\NotBlank(),
            ],
            '[unit]' => [
                new Assert\NotBlank(),
            ],
            '[measurementRange][minimum]' => new Assert\Optional([
                new Assert\Type('numeric'),
                new Assert\LessThanOrEqual(null, '[measurementRange][maximum]'),
            ]),
            '[measurementRange][maximum]' => new Assert\Optional([
                new Assert\Type('numeric'),
                new Assert\GreaterThanOrEqual(null, '[measurementRange][minimum]'),
            ]),
        ];
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getClassConstraints(): array
    {
        return [
            new AttributeUnitConstraint(),
        ];
    }
}
