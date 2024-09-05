<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\LocalProductTypeViewSubscriber;
use Ibexa\Bundle\ProductCatalog\View\ProductTypeView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Delete;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalProductTypeViewSubscriberTest extends AbstractLocalViewSubscriberTest
{
    public function testOnPreContentView(): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);

        $view = new ProductTypeView('example.html.twig', $productType);

        $subscriber = new LocalProductTypeViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createPermissionResolverMock($productType),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());
        self::assertTrue($view->hasParameter('delete_form'));
    }

    /**
     * @dataProvider dataProviderForTestOnPreContentViewIsNonApplicable
     */
    public function testOnPreContentViewIsNonApplicable(bool $isAdminSiteAccess, bool $isLocal): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);

        $view = new ProductTypeView('example.html.twig', $productType);

        $subscriber = new LocalProductTypeViewSubscriber(
            $this->createSiteAccessServiceMock($isAdminSiteAccess),
            $this->createConfigProvider($isLocal),
            $this->createPermissionResolverMock($productType),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertFalse($view->isEditable());
        self::assertFalse($view->hasParameter('delete_form'));
    }

    /**
     * @return iterable<string,array{bool,bool}>
     */
    public function dataProviderForTestOnPreContentViewIsNonApplicable(): iterable
    {
        yield 'non administrative siteaccess' => [false, true];
        yield 'read only product catalog' => [true, false];
    }

    private function createPermissionResolverMock(
        ProductTypeInterface $productType,
        bool $canUser = true
    ): PermissionResolverInterface {
        $permissionResolver = $this->createMock(PermissionResolverInterface::class);
        $permissionResolver
            ->method('canUser')
            ->with(new Delete($productType))
            ->willReturn($canUser);

        return $permissionResolver;
    }
}
