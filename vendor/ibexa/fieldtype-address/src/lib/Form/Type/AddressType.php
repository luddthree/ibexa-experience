<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\Form\Type;

use Ibexa\FieldTypeAddress\FieldType\Value;
use Ibexa\FieldTypeAddress\Form\Transformer\FieldTypeModelTransformer;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddressType extends BaseAddressType
{
    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_address';
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder->add('name', TextType::class, [
            'required' => true,
            'label' => /** @Desc("Name") */ 'ibexa.address.name',
            'type' => $options['type'],
            'country' => $options['country'] ?? null,
        ]);

        $builder->add('country', CountryType::class, [
            'required' => true,
            'label' => /** @Desc("Country") */ 'ibexa.address.country',
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($options) {
            $data = $event->getData();
            $form = $event->getForm();

            $form->remove(Value::FIELDS_IDENTIFIER);
            $form->add('fields', AddressFieldsType::class, [
                'label' => false,
                'type' => $options['type'],
                'country' => $data->country,
            ]);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event) use ($options) {
            $data = $event->getData();
            $form = $event->getForm();

            $form->setData(new Value());
            $form->remove(Value::FIELDS_IDENTIFIER);
            $form->add('fields', AddressFieldsType::class, [
                'label' => false,
                'type' => $options['type'],
                'country' => $data['country'],
            ]);
        });

        $builder->addModelTransformer(new FieldTypeModelTransformer(), true);
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ): void {
        $view->vars['block_prefixes'][] = $this->getCountryBlockPrefix(
            $options['type'],
            $form->getNormData()['country'],
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('allow_extra_fields', true);
        $resolver->setDefault('translation_domain', 'ibexa_fieldtype_address');
    }
}
