<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Ibexa\Contracts\FormBuilder\FieldType\Model;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

abstract class AbstractActionValidator extends ConstraintValidator implements TranslationContainerInterface
{
    protected function getActionIdentifier(): ?string
    {
        $context = $this->context;
        $form = $context->getRoot();
        if (!$form instanceof FormInterface) {
            throw new UnexpectedTypeException($form, FormInterface::class);
        }

        $data = $form->getData();
        if (!$data instanceof Model\Field) {
            throw new UnexpectedTypeException($data, Model\Field::class);
        }

        $actionAttribute = $data->getAttributeValue('action');
        if (!is_string($actionAttribute)) {
            return null;
        }

        $actionAttribute = json_decode($actionAttribute, true);

        /** @var array<string, string> $actionAttribute */
        return $actionAttribute['action'] ?? null;
    }

    public static function getTranslationMessages(): array
    {
        return [];
    }
}
