<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute;

use Ibexa\FormBuilder\Form\Type\FieldAttribute\Action\ActionType;
use Ibexa\FormBuilder\Form\Type\FieldAttribute\Action\LocationType;
use Ibexa\FormBuilder\Form\Validator\Constraints\IsNotBlankMessageAction;
use Ibexa\FormBuilder\Form\Validator\Constraints\IsNotEmptyLocationRedirectAction;
use Ibexa\FormBuilder\Form\Validator\Constraints\IsNotEmptyUrlRedirectAction;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class AttributeActionType extends BaseType
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('compound', true);
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_attribute_action';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('action', ActionType::class);
        $builder->add('location_id', LocationType::class, [
            'constraints' => [
                new IsNotEmptyLocationRedirectAction(),
            ],
        ]);
        $builder->add('url', TextType::class, [
            'label' => $this->translator->trans(
                /** @Desc("Redirection URL") */
                'form_builder.attribute.action.url.label',
                [],
                'ibexa_form_builder',
            ),
            'constraints' => [
                new IsNotEmptyUrlRedirectAction(),
            ],
        ]);
        $builder->add('message', TextareaType::class, [
            'label' => $this->translator->trans(
                /** @Desc("Message to display") */
                'form_builder.attribute.action.message.label',
                [],
                'ibexa_form_builder',
            ),
            'constraints' => [
                new IsNotBlankMessageAction(),
            ],
        ]);

        $transformer = new CallbackTransformer(
            static function ($input) {
                if (empty($input)) {
                    return [];
                }

                return json_decode($input, true, 2, JSON_OBJECT_AS_ARRAY);
            },
            static function ($input) {
                return json_encode($input);
            }
        );

        $builder->addModelTransformer($transformer);
    }
}

class_alias(AttributeActionType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeActionType');
