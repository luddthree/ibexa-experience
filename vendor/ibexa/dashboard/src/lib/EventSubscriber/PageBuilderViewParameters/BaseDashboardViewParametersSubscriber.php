<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber\PageBuilderViewParameters;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Core\MVC\Symfony\View\ViewEvents;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
abstract class BaseDashboardViewParametersSubscriber implements EventSubscriberInterface
{
    private ConfigResolverInterface $configResolver;

    /** @var array<string, mixed> */
    private array $pageBuilderConfig;

    abstract protected function getContentTypeFromView(View $view): ?ContentType;

    /**
     * @param array<string, mixed> $pageBuilderConfig
     */
    public function __construct(ConfigResolverInterface $configResolver, array $pageBuilderConfig)
    {
        $this->configResolver = $configResolver;
        $this->pageBuilderConfig = $pageBuilderConfig;
    }

    /**
     * @return array<string, array{string, int}>
     */
    final public static function getSubscribedEvents(): array
    {
        return [
            ViewEvents::FILTER_VIEW_PARAMETERS => ['onFilterViewParameters', -100],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    final public function onFilterViewParameters(FilterViewParametersEvent $event): void
    {
        $contentType = $this->getContentTypeFromView($event->getView());
        if (null === $contentType) {
            return;
        }

        if ((new IsDashboardContentType($this->configResolver))->isSatisfiedBy($contentType)) {
            $parameterBag = $event->getParameterBag();
            $pageBuilderConfig = $parameterBag->get('page_builder_config', []);
            // merge any pre-existing configuration, making it a priority over dashboard one
            $parameterBag->add(
                [
                    'page_builder_config' => array_merge($this->pageBuilderConfig, $pageBuilderConfig),
                ]
            );
        }
    }
}
