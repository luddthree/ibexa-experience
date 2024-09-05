<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\View\ProductCreateView;
use Ibexa\Bundle\ProductCatalog\View\ProductUpdateView;
use Ibexa\Contracts\ContentForms\Content\Form\Provider\GroupedContentFormFieldsProviderInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;

final class ProductFieldGroupsViewSubscriber extends AbstractLocalViewSubscriber
{
    private GroupedContentFormFieldsProviderInterface $fieldsProvider;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider,
        GroupedContentFormFieldsProviderInterface $fieldsProvider
    ) {
        parent::__construct($siteAccessService, $configProvider);

        $this->fieldsProvider = $fieldsProvider;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\ProductCreateView|\Ibexa\Bundle\ProductCatalog\View\ProductUpdateView $view
     */
    protected function configureView(View $view): void
    {
        $parameters = $view->getParameters();
        $parameters['grouped_fields'] = $this->fieldsProvider->getGroupedFields(
            $view->getForm()->get('fieldsData')->all()
        );

        $view->setParameters($parameters);
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ProductCreateView
            || $view instanceof ProductUpdateView;
    }
}
