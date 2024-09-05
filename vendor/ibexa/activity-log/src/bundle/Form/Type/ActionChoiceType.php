<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Form\Type;

use Ibexa\Bundle\ActivityLog\Form\ChoiceList\Loader\ActionListLoader;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ActionChoiceType extends AbstractType
{
    private ActivityLogServiceInterface $activityLogService;

    public function __construct(ActivityLogServiceInterface $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => ChoiceList::loader($this, new ActionListLoader($this->activityLogService)),
            'choice_label' => ChoiceList::label(
                $this,
                static fn (ActionInterface $action): string => sprintf(
                    'ibexa.activity_log.search_form.action.%s',
                    $action->getName(),
                ),
            ),
            'choice_value' => ChoiceList::value($this, static fn (ActionInterface $action): int => $action->getId()),
            'translation_domain' => 'ibexa_activity_log',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'activity_log_action_choice';
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
