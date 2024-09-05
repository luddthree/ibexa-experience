<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\VariantFormMapperRegistryInterface;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeDefinitionAssignmentType extends AbstractType
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private VariantFormMapperRegistryInterface $formMapperRegistry;

    public function __construct(
        AttributeDefinitionServiceInterface $attributeDefinitionService,
        VariantFormMapperRegistryInterface $formMapperRegistry
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->formMapperRegistry = $formMapperRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('attributeDefinition', HiddenType::class);

        $builder->add('discriminator', CheckboxType::class, [
            'required' => false,
        ]);

        $builder->add('required', CheckboxType::class, [
            'required' => false,
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                $data = $event->getData();
                if (!empty($data) && isset($data['attributeDefinition'])) {
                    $definition = $this->attributeDefinitionService->getAttributeDefinition($data['attributeDefinition']);
                    if (!$this->formMapperRegistry->hasMapper($definition->getType()->getIdentifier())) {
                        $form = $event->getForm();

                        $form->remove('discriminator');
                        $form->add('discriminator', CheckboxType::class, [
                            'required' => false,
                            'disabled' => true,
                            'data' => false,
                        ]);
                    }
                }
            }
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attribute_definition'] = null;

        if (($data = $form->getData()) !== null) {
            $definition = $this->attributeDefinitionService->getAttributeDefinition(
                $data['attributeDefinition']
            );

            $view->vars['attribute_definition'] = $definition;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
