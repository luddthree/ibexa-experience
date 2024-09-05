<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Client\Consumer\Performance\PopularityDataFetcher;
use Ibexa\Personalization\Form\Data\PopularityDurationChoiceData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PopularityDurationType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface<\Symfony\Component\Form\FormBuilder> $builder
     * @param array<mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('duration', ChoiceType::class, [
            'choices' => $this->getDurationChoices(),
            'required' => true,
            'label' => false,
            'attr' => ['class' => 'dashboard_popularity_duration'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PopularityDurationChoiceData::class,
        ]);
    }

    /**
     * @return array<string, PopularityDataFetcher::DURATION_*>
     */
    private function getDurationChoices(): array
    {
        $choices = [];

        foreach ($this->getTranslatedChoices() as $label => $value) {
            $choices[$label] = $value;
        }

        return $choices;
    }

    /**
     * @return array<string, PopularityDataFetcher::DURATION_*>
     */
    private function getTranslatedChoices(): array
    {
        return [
            $this->translator->trans(/** @Desc("Last 24h") */
                'ibexa_personalization.date_time_range.last_24h',
                [],
                'ibexa_personalization'
            ) => PopularityDataFetcher::DURATION_24H,
            $this->translator->trans(/** @Desc("Last 7 days") */
                'ibexa_personalization.date_time_range.last_7_days',
                [],
                'ibexa_personalization'
            ) => PopularityDataFetcher::DURATION_WEEK,
            $this->translator->trans(/** @Desc("Last 30 days") */
                'ibexa_personalization.date_time_range.last_30_days',
                [],
                'ibexa_personalization'
            ) => PopularityDataFetcher::DURATION_30DAYS,
            $this->translator->trans(/** @Desc("Last month") */
                'ibexa_personalization.date_time_range.last_month',
                [],
                'ibexa_personalization'
            ) => PopularityDataFetcher::DURATION_PREV_MONTH,
        ];
    }
}
