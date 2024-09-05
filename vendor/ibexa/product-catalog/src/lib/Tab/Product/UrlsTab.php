<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Product;

use Ibexa\AdminUi\Form\Factory\FormFactory;
use Ibexa\AdminUi\Tab\LocationView\UrlsTab as BaseUrlsTab;
use Ibexa\AdminUi\UI\Dataset\DatasetFactory;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Core\Helper\TranslationHelper;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class UrlsTab extends BaseUrlsTab implements ConditionalTabInterface
{
    private ConfigResolverInterface $configResolver;

    private RequestStack $requestStack;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        URLAliasService $urlAliasService,
        FormFactory $formFactory,
        DatasetFactory $datasetFactory,
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        EventDispatcherInterface $eventDispatcher,
        TranslationHelper $translationHelper,
        ConfigResolverInterface $configResolver,
        RequestStack $requestStack
    ) {
        parent::__construct(
            $twig,
            $translator,
            $urlAliasService,
            $formFactory,
            $datasetFactory,
            $locationService,
            $permissionResolver,
            $eventDispatcher,
            $translationHelper
        );

        $this->configResolver = $configResolver;
        $this->requestStack = $requestStack;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['product'] instanceof ContentAwareProductInterface;
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        $request = $this->requestStack->getMainRequest();
        if ($request === null) {
            throw new LogicException('Unable to render URLs tab: $request cannot be null');
        }

        $page = $request->get('page');
        $routeName = $request->get('_route');
        $routeParams = $request->get('_route_params');

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $contentAwareProduct */
        $contentAwareProduct = $contextParameters['product'];

        $contextParameters = [
            'content' => $contentAwareProduct->getContent(),
            'location' => $contentAwareProduct
                ->getContent()
                ->getVersionInfo()
                ->getContentInfo()
                ->getMainLocation(),
            'custom_urls_pagination_params' => [
                'route_name' => $routeName,
                'route_params' => $routeParams,
                'page' => $page['custom_url'] ?? 1,
                'limit' => $this->configResolver->getParameter('pagination.content_custom_url_limit'),
            ],
            'system_urls_pagination_params' => [
                'route_name' => $routeName,
                'route_params' => $routeParams,
                'page' => $page['system_url'] ?? 1,
                'limit' => $this->configResolver->getParameter('pagination.content_system_url_limit'),
            ],
        ];

        return parent::getTemplateParameters($contextParameters);
    }
}
