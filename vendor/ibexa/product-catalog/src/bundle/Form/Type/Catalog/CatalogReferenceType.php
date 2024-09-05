<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CatalogTransformer;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class CatalogReferenceType extends AbstractType
{
    private CatalogServiceInterface $catalogService;

    public function __construct(CatalogServiceInterface $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CatalogTransformer($this->catalogService));
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }
}
