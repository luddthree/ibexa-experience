<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\LocalCatalogListViewSubscriber;
use Ibexa\Bundle\ProductCatalog\View\CatalogListView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;

final class LocalCatalogListViewSubscriberTest extends AbstractLocalViewSubscriberTest
{
    /**
     * @dataProvider dataProviderForTestOnPreContentView
     */
    public function testOnPreContentView(
        bool $canCreate,
        bool $canEdit
    ): void {
        $view = new CatalogListView(
            'example.html.twig',
            [],
            $this->createMock(FormInterface::class)
        );
        $subscriber = new LocalCatalogListViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createPermissionResolverMock($canCreate, $canEdit),
            $this->createFormFactoryMock(),
            $this->createMock(RouterInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue(
            $view->hasParameter('bulk_delete_form'),
        );
        self::assertTrue(
            $view->hasParameter('can_create')
        );
        self::assertTrue(
            $view->hasParameter('can_edit')
        );
        self::assertSame(
            $canCreate,
            $view->getParameter('can_create'),
        );
        self::assertSame(
            $canEdit,
            $view->getParameter('can_edit'),
        );
    }

    /**
     * @return iterable<string,array{bool,bool}>
     */
    public function dataProviderForTestOnPreContentView(): iterable
    {
        yield 'can create and edit' => [true, true];
        yield 'can create, can`t edit' => [true, false];
        yield 'can`t create, can`t edit' => [false, false];
        yield 'can`t create, can edit' => [false, true];
    }

    private function createPermissionResolverMock(
        bool $canCreate,
        bool $canEdit
    ): PermissionResolverInterface {
        $permissionResolver = $this->createMock(PermissionResolverInterface::class);
        $permissionResolver
            ->method('canUser')
            ->willReturnCallback(
                static function (PolicyInterface $policy) use ($canCreate, $canEdit): bool {
                    if ($policy instanceof Create) {
                        return $canCreate;
                    }
                    if ($policy instanceof Edit) {
                        return $canEdit;
                    }

                    return false;
                }
            );

        return $permissionResolver;
    }
}
