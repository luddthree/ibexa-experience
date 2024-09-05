<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\AssetTransformer;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer;
use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AssetReferenceListType extends AbstractType
{
    private AssetServiceInterface $assetService;

    public function __construct(AssetServiceInterface $assetService)
    {
        $this->assetService = $assetService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new StringToArrayTransformer(
                new AssetTransformer(
                    $this->assetService,
                    $options['product']
                )
            )
        );
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['product']);
        $resolver->setAllowedTypes('product', ProductInterface::class);
    }
}
