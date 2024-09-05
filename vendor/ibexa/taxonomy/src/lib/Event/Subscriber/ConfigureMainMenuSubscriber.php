<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Taxonomy\Exception\TaxonomyNotFoundException;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Ibexa\Taxonomy\Tree\TaxonomyTreeServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigureMainMenuSubscriber implements EventSubscriberInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    private TaxonomyTreeServiceInterface $taxonomyTreeService;

    private ContentService $contentService;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration,
        TaxonomyTreeServiceInterface $taxonomyTreeService,
        ContentService $contentService
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyTreeService = $taxonomyTreeService;
        $this->contentService = $contentService;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::MAIN_MENU => 'onMainMenuConfigure'];
    }

    public function onMainMenuConfigure(ConfigureMenuEvent $event): void
    {
        $mainMenu = $event->getMenu();
        $contentMenu = $mainMenu->getChild(MainMenuBuilder::ITEM_CONTENT);

        if (null === $contentMenu) {
            return;
        }

        foreach ($this->taxonomyConfiguration->getTaxonomies() as $taxonomy) {
            $register = $this->taxonomyConfiguration->getConfigForTaxonomy(
                $taxonomy,
                TaxonomyConfiguration::CONFIG_REGISTER_MAIN_MENU
            );
            if ($register === false) {
                continue;
            }

            try {
                $treeRoot = $this->taxonomyTreeService->loadTreeRoot($taxonomy);
                $treeRootNode = reset($treeRoot);
                $rootContent = $this->contentService->loadContentInfo($treeRootNode['contentId']);
            } catch (UnauthorizedException | TaxonomyNotFoundException $exception) {
                continue;
            }

            $contentMenu->addChild(
                sprintf('taxonomy.%s', $taxonomy),
                [
                    'route' => 'ibexa.content.view',
                    'routeParameters' => [
                        'contentId' => $rootContent->id,
                        'locationId' => $rootContent->mainLocationId,
                    ],
                    'extras' => [
                        'translation_domain' => 'ibexa_taxonomy',
                        'orderNumber' => 65,
                        'taxonomy' => $taxonomy,
                    ],
                ]
            );
        }
    }
}
