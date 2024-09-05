<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute\Action;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActionType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [
            $this->translator->trans(
                /** @Desc("Select what happens after the form is submitted") */
                'form_builder.attribute.button.action.empty',
                [],
                'ibexa_form_builder'
            ) => '',
            $this->translator->trans(
                /** @Desc("Redirect to a Content item") */
                'form_builder.attribute.button.action.location_id',
                [],
                'ibexa_form_builder'
            ) => 'location_id',
            $this->translator->trans(
                /** @Desc("Redirect to a URL") */
                'form_builder.attribute.button.action.url',
                [],
                'ibexa_form_builder'
            ) => 'url',
            $this->translator->trans(
                /** @Desc("Show a message") */
                'form_builder.attribute.button.action.message',
                [],
                'ibexa_form_builder'
            ) => 'message',
        ];

        $resolver->setDefaults([
            'choices' => $choices,
            'translation_domain' => 'ibexa_form_builder',
        ]);
    }
}

class_alias(ActionType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\Action\ActionType');
