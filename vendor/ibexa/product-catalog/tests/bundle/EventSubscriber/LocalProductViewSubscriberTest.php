<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\LocalProductViewSubscriber;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\CompletenessFactoryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\CompletenessInterface;
use Ibexa\Bundle\ProductCatalog\UI\Language\PreviewLanguageResolverInterface;
use Ibexa\Bundle\ProductCatalog\View\ProductView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalProductViewSubscriberTest extends AbstractLocalViewSubscriberTest
{
    public function testOnPreContentView(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $view = new ProductView('example.html.twig', $product);

        $subscriber = new LocalProductViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createPermissionResolverMock(true),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(CompletenessFactoryInterface::class),
            $this->createMock(PreviewLanguageResolverInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());

        self::assertTrue($view->hasParameter('delete_form'));
        self::assertInstanceOf(FormView::class, $view->getParameter('delete_form'));

        self::assertTrue($view->hasParameter('completeness'));
        self::assertInstanceOf(CompletenessInterface::class, $view->getParameter('completeness'));
    }

    public function testOnPreContentViewWithoutDeletePermissions(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $view = new ProductView('example.html.twig', $product);

        $subscriber = new LocalProductViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createPermissionResolverMock(false),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(CompletenessFactoryInterface::class),
            $this->createMock(PreviewLanguageResolverInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());
        self::assertFalse($view->hasParameter('delete_form'));

        self::assertTrue($view->hasParameter('completeness'));
        self::assertInstanceOf(CompletenessInterface::class, $view->getParameter('completeness'));
    }

    private function createPermissionResolverMock(bool $canUser): PermissionResolverInterface
    {
        $permissionResolver = $this->createMock(PermissionResolverInterface::class);
        $permissionResolver
            ->method('canUser')
            ->with(new Delete())
            ->willReturn($canUser);

        return $permissionResolver;
    }
}
