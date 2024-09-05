<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\FieldType\Mapper;

use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\FieldTypeAddress\Form\Type\AddressType;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

final class AddressFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface, TranslationContainerInterface
{
    private array $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function mapFieldDefinitionForm(
        FormInterface $fieldDefinitionForm,
        FieldDefinitionData $data
    ): void {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('type', ChoiceType::class, [
                'required' => true,
                'property_path' => 'fieldSettings[type]',
                'label' => /** @Desc("Address Type") */ 'field_definition.ibexa_address.type',
                'translation_domain' => 'ibexa_fieldtype_address',
                'disabled' => $isTranslation,
                'choices' => $this->getTypeChoices(),
                'expanded' => false,
                'multiple' => false,
            ]);
    }

    public function mapFieldValueForm(
        FormInterface $fieldForm,
        FieldData $data
    ) {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        AddressType::class,
                        [
                            'label' => $fieldDefinition->getName(),
                            'required' => $fieldDefinition->isRequired,
                            'type' => $fieldDefinition->fieldSettings['type'],
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    private function getTypeChoices(): array
    {
        $types = array_keys($this->configuration);
        $choices = [];
        foreach ($types as $type) {
            $choices[sprintf('field_definition.ibexa_address.type.%s', $type)] = $type;
        }

        return $choices;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('field_definition.ibexa_address.type.personal', 'ibexa_fieldtype_address'))->setDesc('Personal'),
        ];
    }
}
