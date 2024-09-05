<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\LocalAttributeGroupListViewSubscriber;
use Ibexa\Bundle\ProductCatalog\View\AttributeGroupListView;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalAttributeGroupListViewSubscriberTest extends AbstractLocalViewSubscriberTest
{
    /**
     * @dataProvider dataProviderForOnPreContentView
     */
    public function testOnPreContentView(
        bool $missingAtLeastOneAttributeGroupDefinition
    ): void {
        $view = new AttributeGroupListView(
            'example.html.twig',
            [],
            $this->createMock(FormInterface::class)
        );

        $subscriber = new LocalAttributeGroupListViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createAttributeGroupServiceMock($missingAtLeastOneAttributeGroupDefinition),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());
        self::assertTrue($view->hasParameter('bulk_delete_form'));
        self::assertTrue($view->hasParameter('no_attributes_groups'));
        self::assertEquals(
            $missingAtLeastOneAttributeGroupDefinition,
            $view->getParameter('no_attributes_groups')
        );
    }

    /**
     * @return iterable<string,array{bool}>
     */
    public function dataProviderForOnPreContentView(): iterable
    {
        yield 'missing at least one attribute group definition' => [true];
        yield 'at least one attribute group is defined' => [false];
    }

    /**
     * @dataProvider dataProviderForTestOnPreContentViewIsNonApplicable
     */
    public function testOnPreContentViewIsNonApplicable(bool $isAdminSiteAccess, bool $isLocal): void
    {
        $view = new AttributeGroupListView(
            'example.html.twig',
            [],
            $this->createMock(FormInterface::class)
        );

        $subscriber = new LocalAttributeGroupListViewSubscriber(
            $this->createSiteAccessServiceMock($isAdminSiteAccess),
            $this->createConfigProvider($isLocal),
            $this->createAttributeGroupServiceMock(),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertFalse($view->isEditable());
        self::assertFalse($view->hasParameter('bulk_delete_form'));
        self::assertFalse($view->hasParameter('no_attributes_groups'));
    }

    /**
     * @return iterable<string,array{bool,bool}>
     */
    public function dataProviderForTestOnPreContentViewIsNonApplicable(): iterable
    {
        yield 'non administrative siteaccess' => [false, true];
        yield 'read only product catalog' => [true, false];
    }

    private function createAttributeGroupServiceMock(
        bool $withEmptyAttributesGroupsList = false
    ): AttributeGroupServiceInterface {
        $list = $this->createMock(AttributeGroupListInterface::class);
        $list->method('getTotalCount')->willReturn($withEmptyAttributesGroupsList ? 0 : 1);

        $attributeGroupService = $this->createMock(AttributeGroupServiceInterface::class);
        $attributeGroupService->method('findAttributeGroups')->willReturn($list);

        return $attributeGroupService;
    }
}
