<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Type\Currency\CurrencyDeleteType;
use Ibexa\Bundle\ProductCatalog\View\CurrencyListView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce\AdministrateCurrencies;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CurrencyListViewSubscriber implements EventSubscriberInterface
{
    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    private PermissionResolverInterface $permissionResolver;

    public function __construct(
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        PermissionResolverInterface $permissionResolver
    ) {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->permissionResolver = $permissionResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
        ];
    }

    public function onPreContentView(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();
        if (!$view instanceof CurrencyListView) {
            return;
        }

        if ($this->canAdministrateCurrencies()) {
            $view->addParameters([
                'bulk_delete_form' => $this->createBulkDeleteForm()->createView(),
            ]);
        }
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(CurrencyDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->urlGenerator->generate('ibexa.product_catalog.currency.bulk_delete'),
        ]);
    }

    private function canAdministrateCurrencies(): bool
    {
        return $this->permissionResolver->canUser(new AdministrateCurrencies());
    }
}
