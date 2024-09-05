<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Form;

use Ibexa\ContentForms\Form\Type\Content\ContentEditType;
use Ibexa\Scheduler\Form\Type\DateBasedPublisherType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extends Content Edit form with additional fields.
 */
class ContentEditTypeExtension extends AbstractTypeExtension
{
    public const EXTENSION_NAME = 'date_based_publisher';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(/** @Desc("Date Based Publisher") */
            'schedule_publish',
            SubmitType::class,
            [
            'label' => 'schedule_publish',
            'attr' => ['hidden' => true],
            'translation_domain' => 'ibexa_scheduler',
        ]
        );

        $builder->add(/** @Desc("Discard scheduled publish") */
            'discard_schedule_publish',
            SubmitType::class,
            [
            'label' => 'discard_schedule_publish',
            'attr' => ['hidden' => true],
            'translation_domain' => 'ibexa_scheduler',
        ]
        );

        $builder->add(self::EXTENSION_NAME, DateBasedPublisherType::class, [
            'mapped' => false,
            'label' => false,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ContentEditType::class];
    }
}

class_alias(ContentEditTypeExtension::class, 'EzSystems\DateBasedPublisher\Core\Form\ContentEditTypeExtension');
