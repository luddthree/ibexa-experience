<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ScenarioUserSettingsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(FormEvent $event): void
    {
        /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioUserProfileSettingsData $data */
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if (null !== $data->getBoostItem() && !$data->getBoostItem()->isEnabled()) {
            $data->getBoostItem()->setAttribute(null)->setPosition(null);
            $data->getUserAttributeName()->setEnabled(false)->setValue(null);
        }
    }
}

class_alias(ScenarioUserSettingsSubscriber::class, 'Ibexa\Platform\Personalization\Event\Subscriber\Form\ScenarioUserSettingsSubscriber');
