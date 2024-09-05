<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Form\Data\TimePeriodChoiceData;
use Ibexa\Personalization\Value\TimePeriod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TimePeriodChoiceType extends AbstractType
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('period', ChoiceType::class, [
            'choices' => $this->getTimePeriodChoices($builder->getOption('custom_range')),
            'required' => true,
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TimePeriodChoiceData::class,
        ]);
        $resolver->setDefined('custom_range');
        $resolver->setAllowedTypes('custom_range', 'bool');
    }

    private function getTimePeriodChoices(bool $enableCustomRange): array
    {
        $choices = [];

        foreach ($this->getTimePeriodField($enableCustomRange) as $label => $value) {
            $choices[$label] = $value;
        }

        return $choices;
    }

    private function getTimePeriodField(bool $enableCustomRange): array
    {
        $translations = [
            $this->translator->trans(/** @Desc("Last 24h") */
                'ibexa_personalization.date_time_range.last_24h',
                [],
                'ibexa_personalization'
            ) => TimePeriod::LAST_24_HOURS,
            $this->translator->trans(/** @Desc("Last 7 days") */
                'ibexa_personalization.date_time_range.last_7_days',
                [],
                'ibexa_personalization'
            ) => TimePeriod::LAST_7_DAYS,
            $this->translator->trans(/** @Desc("Last 30 days") */
                'ibexa_personalization.date_time_range.last_30_days',
                [],
                'ibexa_personalization'
            ) => TimePeriod::LAST_30_DAYS,
            $this->translator->trans(/** @Desc("Last month") */
                'ibexa_personalization.date_time_range.last_month',
                [],
                'ibexa_personalization'
            ) => TimePeriod::LAST_MONTH,
        ];

        if ($enableCustomRange) {
            $translations[
                $this->translator->trans(/** @Desc("Custom range") */
                    'ibexa_personalization.date_time_range.custom_range',
                    [],
                    'ibexa_personalization'
                )
            ] = 'custom_range';
        }

        return $translations;
    }
}

class_alias(TimePeriodChoiceType::class, 'Ibexa\Platform\Personalization\Form\Type\TimePeriodChoiceType');
