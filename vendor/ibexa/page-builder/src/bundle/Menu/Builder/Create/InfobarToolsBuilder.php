<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Menu\Builder\Create;

use Ibexa\Bundle\PageBuilder\Menu\Event\PageBuilderConfigureMenuEventName;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Knp\Menu\ItemInterface;

/**
 * KnpMenuBundle Menu Builder service implementation.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class InfobarToolsBuilder extends AbstractBuilder
{
    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        return $menu;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return PageBuilderConfigureMenuEventName::PAGE_BUILDER_INFOBAR_CREATE_MODE_TOOLS;
    }
}

class_alias(InfobarToolsBuilder::class, 'EzSystems\EzPlatformPageBuilderBundle\Menu\Builder\Create\InfobarToolsBuilder');
