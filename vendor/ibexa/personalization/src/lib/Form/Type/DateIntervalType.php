<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Factory\DateTime\GranularityFactoryInterface;
use Ibexa\Personalization\Form\Data\DateIntervalData;
use Ibexa\Personalization\Form\DataTransformer\DateIntervalTransformer;
use Ibexa\Personalization\Value\TimePeriod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType as BaseDateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateIntervalType extends AbstractType
{
    /** @var \Ibexa\Personalization\Factory\DateTime\GranularityFactoryInterface */
    private $granularityFactory;

    public function __construct(GranularityFactoryInterface $granularityFactory)
    {
        $this->granularityFactory = $granularityFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_interval', BaseDateIntervalType::class, [
                'attr' => ['hidden' => true],
                'input' => 'string',
                'widget' => 'single_text',
                'required' => false,
                'with_hours' => true,
                'empty_data' => TimePeriod::LAST_24_HOURS,
                'label' => false,
            ])
            ->add('end_date', IntegerType::class, [
                'attr' => ['hidden' => true],
                'required' => false,
                'label' => false,
            ])
            ->addModelTransformer(new DateIntervalTransformer($this->granularityFactory));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DateIntervalData::class,
        ]);
    }
}

class_alias(DateIntervalType::class, 'Ibexa\Platform\Personalization\Form\Type\DateIntervalType');
