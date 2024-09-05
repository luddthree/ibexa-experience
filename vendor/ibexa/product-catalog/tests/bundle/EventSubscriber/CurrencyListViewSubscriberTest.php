<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\CurrencyListViewSubscriber;
use Ibexa\Bundle\ProductCatalog\View\CurrencyListView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce\AdministrateCurrencies;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CurrencyListViewSubscriberTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestOnPreContentView
     */
    public function testOnPreContentView(
        bool $canAdministrateCurrencies
    ): void {
        $currencies = [
            $this->createMock(CurrencyInterface::class),
            $this->createMock(CurrencyInterface::class),
            $this->createMock(CurrencyInterface::class),
        ];
        $searchForm = $this->createMock(FormInterface::class);

        $view = new CurrencyListView('example.html.twig', $currencies, $searchForm);

        $subscriber = new CurrencyListViewSubscriber(
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createPermissionResolverMock($canAdministrateCurrencies),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertSame($currencies, $view->getCurrencies());
        self::assertSame($canAdministrateCurrencies, $view->hasParameter('bulk_delete_form'));
    }

    /**
     * @return iterable<string,array{bool}>
     */
    public function dataProviderForTestOnPreContentView(): iterable
    {
        yield 'can administrate currencies' => [true];
        yield 'cannot administrate currencies' => [false];
    }

    private function createFormFactoryMock(): FormFactoryInterface
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($this->createMock(FormView::class));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->method('create')->willReturn($form);

        return $formFactory;
    }

    private function createPermissionResolverMock(bool $canAdministrateCurrencies): PermissionResolverInterface
    {
        $permissionResolver = $this->createMock(PermissionResolverInterface::class);
        $permissionResolver
            ->method('canUser')
            ->with(new AdministrateCurrencies())
            ->willReturn($canAdministrateCurrencies);

        return $permissionResolver;
    }
}
