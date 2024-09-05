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

final class ScenarioCommerceSettingsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(FormEvent $event): void
    {
        /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioCommerceSettingsData $data */
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if (null !== $data->getExcludeMinimalItemPrice() && !$data->getExcludeMinimalItemPrice()->isEnabled()) {
            $data->setExcludeItemsWithoutPrice(false);
        }
    }
}

class_alias(ScenarioCommerceSettingsSubscriber::class, 'Ibexa\Platform\Personalization\Event\Subscriber\Form\ScenarioCommerceSettingsSubscriber');
