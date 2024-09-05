<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\LocalAttributeDefinitionViewSubscriber;
use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\EventSubscriber\LocalAttributeDefinitionViewSubscriber
 */
final class LocalAttributeDefinitionViewSubscriberTest extends AbstractLocalViewSubscriberTest
{
    public function testOnPreContentView(): void
    {
        $view = new AttributeDefinitionView(
            'example.html.twig',
            $this->createMock(AttributeDefinitionInterface::class)
        );

        $subscriber = new LocalAttributeDefinitionViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());
        self::assertTrue($view->hasParameter('delete_form'));
        self::assertInstanceOf(FormView::class, $view->getParameter('delete_form'));
    }

    /**
     * @dataProvider dataProviderForTestOnPreContentViewIsNonApplicable
     */
    public function testOnPreContentViewIsNonApplicable(bool $isAdminSiteAccess, bool $isProductCatalogEditable): void
    {
        $view = new AttributeDefinitionView('example.html.twig', $this->createMock(AttributeDefinitionInterface::class));

        $subscriber = new LocalAttributeDefinitionViewSubscriber(
            $this->createSiteAccessServiceMock($isAdminSiteAccess),
            $this->createConfigProvider($isProductCatalogEditable),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertFalse($view->isEditable());
    }

    /**
     * @return iterable<string,array{bool,bool}>
     */
    public function dataProviderForTestOnPreContentViewIsNonApplicable(): iterable
    {
        yield 'non administrative siteaccess' => [false, true];
        yield 'read only product catalog' => [true, false];
    }
}
