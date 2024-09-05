<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\LocalAttributeGroupViewSubscriber;
use Ibexa\Bundle\ProductCatalog\View\AttributeGroupView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalAttributeGroupViewSubscriberTest extends AbstractLocalViewSubscriberTest
{
    public function testOnPreContentView(): void
    {
        $view = new AttributeGroupView(
            'example.html.twig',
            $this->createMock(AttributeGroupInterface::class),
        );

        $subscriber = new LocalAttributeGroupViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());
        self::assertTrue($view->hasParameter('group_delete_form'));
    }

    /**
     * @dataProvider dataProviderForTestOnPreContentViewIsNonApplicable
     */
    public function testOnPreContentViewIsNonApplicable(bool $isAdminSiteAccess, bool $isLocal): void
    {
        $view = new AttributeGroupView(
            'example.html.twig',
            $this->createMock(AttributeGroupInterface::class),
        );

        $subscriber = new LocalAttributeGroupViewSubscriber(
            $this->createSiteAccessServiceMock($isAdminSiteAccess),
            $this->createConfigProvider($isLocal),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertFalse($view->isEditable());
        self::assertFalse($view->hasParameter('group_delete_form'));
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
