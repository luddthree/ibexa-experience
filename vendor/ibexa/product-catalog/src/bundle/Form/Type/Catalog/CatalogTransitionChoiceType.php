<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

final class CatalogTransitionChoiceType extends AbstractType implements TranslationContainerInterface
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
            'choice_loader' => function (Options $options) {
                return ChoiceList::lazy(
                    $this,
                    function () use ($options): array {
                        return array_map(static function (Transition $transition): string {
                            return $transition->getName();
                        }, $this->workflow->getEnabledTransitions($options['catalog']));
                    },
                    [$options['catalog']]
                );
            },
            'choice_label' => static function ($choice, string $key, ?string $value): ?string {
                if ($value !== null) {
                    return 'catalog.transition.' . $value;
                }

                return null;
            },
            'translation_domain' => 'ibexa_product_catalog',
        ]);

        $resolver->setRequired(['catalog']);
        $resolver->setAllowedTypes('catalog', CatalogInterface::class);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                'catalog.transition.publish',
                'ibexa_product_catalog'
            )->setDesc('Publish'),
            Message::create(
                'catalog.transition.archive',
                'ibexa_product_catalog'
            )->setDesc('Archive'),
        ];
    }
}
