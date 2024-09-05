<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Workflow\WorkflowInterface;

final class CatalogPlacesChoiceType extends AbstractType
{
    private WorkflowInterface $workflow;

    public function __construct(
        WorkflowInterface $ibexaCatalogStateMachine
    ) {
        $this->workflow = $ibexaCatalogStateMachine;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => ChoiceList::lazy($this, function (): array {
                return $this->workflow->getDefinition()->getPlaces();
            }),
            'choice_label' => static function ($choice, string $key, ?string $value): ?string {
                if ($value !== null) {
                    return 'catalog.place.' . $value;
                }

                return null;
            },
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
