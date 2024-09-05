<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber\Form;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioCategoryPathData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ScenarioStrategySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(FormEvent $event): void
    {
        /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData $data */
        $data = $event->getData();

        if (
            null === $data->getModels()->getFirstModelStrategy()->getReferenceCode()
            && null === $data->getModels()->getSecondModelStrategy()->getReferenceCode()
        ) {
            $scenarioCategoryPathData = new ScenarioCategoryPathData();
            $scenarioCategoryPathData->setWholeSite(true);

            $data->setCategoryPath($scenarioCategoryPathData);
        }

        $event->setData($data);
    }
}

class_alias(ScenarioStrategySubscriber::class, 'Ibexa\Platform\Personalization\Event\Subscriber\Form\ScenarioStrategySubscriber');
