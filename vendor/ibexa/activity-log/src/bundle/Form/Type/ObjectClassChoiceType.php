<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Form\Type;

use Ibexa\Bundle\ActivityLog\Form\ChoiceList\Loader\ObjectClassListLoader;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ObjectClassChoiceType extends AbstractType
{
    private ActivityLogServiceInterface $activityLogService;

    public function __construct(ActivityLogServiceInterface $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => ChoiceList::loader($this, new ObjectClassListLoader($this->activityLogService)),
            'choice_value' => ChoiceList::value($this, static fn (ObjectClassInterface $object): int => $object->getId()),
            'choice_label' => ChoiceList::label(
                $this,
                static function (ObjectClassInterface $object): string {
                    if ($object->getShortName() === null) {
                        return $object->getObjectClass();
                    }

                    return sprintf(
                        'ibexa.activity_log.search_form.object_class.%s',
                        $object->getShortName(),
                    );
                },
            ),
            'translation_domain' => 'ibexa_activity_log',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'activity_log_object_class_choice';
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
