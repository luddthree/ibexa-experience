<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CatalogValueTransformer;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogQuery;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\Query\Criterion\CatalogStatus;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogChoiceType extends AbstractType
{
    private CatalogServiceInterface $catalogService;

    public function __construct(CatalogServiceInterface $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CatalogValueTransformer($this->catalogService));
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(
                [
                    'choice_loader' => ChoiceList::lazy(
                        $this,
                        $this->getChoiceList()
                    ),
                    'choice_value' => 'id',
                    'choice_label' => 'name',
                ]
            );
    }

    private function getChoiceList(): callable
    {
        return function (): iterable {
            $query = $this->getCatalogQuery();

            return $this->catalogService->findCatalogs($query);
        };
    }

    private function getCatalogQuery(): CatalogQuery
    {
        return new CatalogQuery(
            new LogicalAnd(
                new CatalogStatus(Status::PUBLISHED_PLACE)
            ),
            null,
            null
        );
    }
}
