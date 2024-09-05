<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseAddressType extends AbstractType
{
    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['type', 'country']);
        $resolver->setAllowedTypes('type', ['string']);
        $resolver->setAllowedTypes('country', ['null', 'string']);
        $resolver->setDefault('translation_domain', 'ibexa_fieldtype_address');
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ): void {
        $view->vars['block_prefixes'][] = $this->getCountryBlockPrefix(
            $options['type'],
            $options['country'],
        );
    }

    protected function getCountryBlockPrefix(string $type, ?string $country): string
    {
        $parts = [
            $this->getBlockPrefix(),
            $type,
        ];

        if (null !== $country) {
            $parts[] = $country;
        }

        return implode('_', $parts);
    }
}
