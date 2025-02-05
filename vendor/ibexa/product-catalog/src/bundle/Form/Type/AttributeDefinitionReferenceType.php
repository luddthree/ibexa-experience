<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\AttributeDefinitionTransformer;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class AttributeDefinitionReferenceType extends AbstractType
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(AttributeDefinitionServiceInterface $attributeDefinitionService)
    {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new AttributeDefinitionTransformer($this->attributeDefinitionService));
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }
}
