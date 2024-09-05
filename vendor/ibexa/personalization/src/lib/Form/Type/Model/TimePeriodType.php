<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Ibexa\Personalization\Form\DataTransformer\TimePeriodTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TimePeriodType extends AbstractType
{
    public const QUANTIFIER_DAYS = 'D';
    public const QUANTIFIER_HOURS = 'H';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantifier', ChoiceType::class, [
                'choices' => [
                    $this->translator->trans(
                        /** @Desc("Day") */
                        'model.quantifier.day',
                        [],
                        'ibexa_personalization'
                    ) => self::QUANTIFIER_DAYS,
                    $this->translator->trans(
                        /** @Desc("Hour") */
                        'model.quantifier.hour',
                        [],
                        'ibexa_personalization'
                    ) => self::QUANTIFIER_HOURS,
                ],
            ])
            ->add('period', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual(1),
                    new Assert\LessThanOrEqual(365),
                ],
            ]);

        $builder->addModelTransformer(new TimePeriodTransformer());
    }
}

class_alias(TimePeriodType::class, 'Ibexa\Platform\Personalization\Form\Type\Model\RelevantHistoryType');
