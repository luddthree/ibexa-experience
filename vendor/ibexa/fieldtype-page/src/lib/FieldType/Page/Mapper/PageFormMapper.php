<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Mapper;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\FieldTypePage\Form\Type\PageFieldType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezlandingpage FieldType.
 */
class PageFormMapper implements FieldValueFormMapperInterface
{
    /**
     * @param \Symfony\Component\Form\FormInterface $fieldForm
     * @param \Ibexa\Contracts\ContentForms\Data\Content\FieldData $data
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                           ->create(
                               'value',
                               PageFieldType::class,
                               [
                                   'required' => $fieldDefinition->isRequired,
                                   'label' => $label,
                               ]
                           )
                           ->setAutoInitialize(false)
                           ->getForm()
            );
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'ezlandingpage_fieldtype',
                ]
            );
    }
}

class_alias(PageFormMapper::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Mapper\PageFormMapper');
