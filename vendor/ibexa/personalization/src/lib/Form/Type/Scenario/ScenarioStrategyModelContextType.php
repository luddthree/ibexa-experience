<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ScenarioStrategyModelContextType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => true,
            'choices' => $this->getChoices(),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getChoices(): array
    {
        $choices = [];

        foreach ($this->getTranslatedModelContextList() as $label => $value) {
            $choices[$label] = $value;
        }

        return $choices;
    }

    private function getTranslatedModelContextList(): array
    {
        return [
            $this->translator->trans(/** @Desc("Automatic") **/
                'scenario.strategy_model_context.automatic',
                [],
                'ibexa_personalization'
            ) => 'AUTO',
            $this->translator->trans(/** @Desc("Page Context") **/
                'scenario.strategy_model_context.page_context',
                [],
                'ibexa_personalization'
            ) => 'ITEM',
            $this->translator->trans(/** @Desc("Click History") **/
                'scenario.strategy_model_context.click_history',
                [],
                'ibexa_personalization'
            ) => 'CLICKED',
            $this->translator->trans(/** @Desc("Buy History") **/
                'scenario.strategy_model_context.buy_history',
                [],
                'ibexa_personalization'
            ) => 'OWNS',
            $this->translator->trans(/** @Desc("Consume History") **/
                'scenario.strategy_model_context.consume_history',
                [],
                'ibexa_personalization'
            ) => 'CONSUMED',
            $this->translator->trans(/** @Desc("Basket History") **/
                'scenario.strategy_model_context.basket_history',
                [],
                'ibexa_personalization'
            ) => 'BASKET',
            $this->translator->trans(/** @Desc("Rate History") **/
                'scenario.strategy_model_context.rate_history',
                [],
                'ibexa_personalization'
            ) => 'RATED',
            $this->translator->trans(/** @Desc("No context") **/
                'scenario.strategy_model_context.no_context',
                [],
                'ibexa_personalization'
            ) => 'NONE',
        ];
    }
}

class_alias(ScenarioStrategyModelContextType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioStrategyModelContextType');
