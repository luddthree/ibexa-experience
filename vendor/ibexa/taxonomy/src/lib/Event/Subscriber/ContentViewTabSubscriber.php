<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\AdminUi\Tab\Event\TabEvents;
use Ibexa\AdminUi\Tab\Event\TabGroupEvent;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentViewTabSubscriber implements EventSubscriberInterface
{
    private const UNSUPPORTED_TABS = [
        'locations',
        'relations',
        'versions',
        'ecommerce-tab',
        'sub_items',
    ];

    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TabEvents::TAB_GROUP_INITIALIZE => ['onTabGroupInitialize', -50],
        ];
    }

    public function onTabGroupInitialize(TabGroupEvent $tabGroupEvent): void
    {
        $tabGroup = $tabGroupEvent->getData();
        $parameters = $tabGroupEvent->getParameters();

        if (
            !isset($parameters['contentType'])
            || $tabGroup->getIdentifier() !== 'location-view'
        ) {
            return;
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $parameters['contentType'];

        if (!$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentType)) {
            return;
        }

        foreach (self::UNSUPPORTED_TABS as $tabIdentifier) {
            try {
                $tabGroup->removeTab($tabIdentifier);
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }
    }
}
