<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\AbstractProductVariantData;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductVariantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('code', TextType::class, [
            'required' => true,
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\AbstractProductVariantData $data */
            $data = $event->getData();
            $product = $data->getProduct();
            $form = $event->getForm();

            $form->add('attributes', AttributeCollectionType::class, [
                'product_type' => $product->getProductType(),
                'attribute_filter' => $this->getAttributeFilter(),
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AbstractProductVariantData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }

    /**
     * @return callable(\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface): bool
     */
    private function getAttributeFilter(): callable
    {
        return static function (AttributeDefinitionAssignmentInterface $attribute): bool {
            return $attribute->isDiscriminator();
        };
    }
}
