<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\AdminUi\Form\Data\ContentTypeData;
use Ibexa\Bundle\ProductCatalog\EventSubscriber\ContentTypeFormActionSubscriber;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\AdminUi\Event\FormEvents;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationType;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentTypeFormActionSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                FormEvents::CONTENT_TYPE_PUBLISH => 'onContentTypePublish',
                FormEvents::CONTENT_TYPE_REMOVE_DRAFT => 'onContentTypeRemoveDraft',
            ],
            ContentTypeFormActionSubscriber::getSubscribedEvents()
        );
    }

    public function testOnContentTypePublishSkipNonContentTypeData(): void
    {
        $event = $this->createMock(FormActionEvent::class);
        $event->method('getData')->willReturn(new stdClass());
        $event->expects(self::never())->method('setResponse');

        $subscriber = new ContentTypeFormActionSubscriber($this->createMock(UrlGeneratorInterface::class));
        $subscriber->onContentTypePublish($event);
    }

    public function testOnContentTypePublishSkipContentTypeDataWithoutProductSpecification(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $event = $this->createMock(FormActionEvent::class);
        $event->method('getData')->willReturn($this->createContentTypeData(false));
        $event->expects(self::never())->method('setResponse');

        $subscriber = new ContentTypeFormActionSubscriber($urlGenerator);
        $subscriber->onContentTypePublish($event);
    }

    public function testOnContentTypePublish1RedirectToProductTypeView(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator
            ->method('generate')
            ->with('ibexa.product_catalog.product_type.view')
            ->willReturn('/product-type-view');

        $event = $this->createMock(FormActionEvent::class);
        $event->method('getData')->willReturn($this->createContentTypeData(true));
        $event->expects(self::once())->method('setResponse')->with(self::isInstanceOf(RedirectResponse::class));

        $subscriber = new ContentTypeFormActionSubscriber($urlGenerator);
        $subscriber->onContentTypePublish($event);
    }

    public function testOnContentTypeRemoveDraftRedirectToProductTypeList(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator
            ->method('generate')
            ->with('ibexa.product_catalog.product_type.list')
            ->willReturn('/product-types');

        $event = $this->createMock(FormActionEvent::class);
        $event->method('getData')->willReturn($this->createContentTypeData(true, true));
        $event->expects(self::once())->method('setResponse')->with(self::isInstanceOf(RedirectResponse::class));

        $subscriber = new ContentTypeFormActionSubscriber($urlGenerator);
        $subscriber->onContentTypeRemoveDraft($event);
    }

    public function testOnContentTypeRemoveDraftRedirectToProductTypeView(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator
            ->method('generate')
            ->with('ibexa.product_catalog.product_type.view')
            ->willReturn('/product-type-view');

        $event = $this->createMock(FormActionEvent::class);
        $event->method('getData')->willReturn($this->createContentTypeData(true, false));
        $event->expects(self::once())->method('setResponse')->with(self::isInstanceOf(RedirectResponse::class));

        $subscriber = new ContentTypeFormActionSubscriber($urlGenerator);
        $subscriber->onContentTypeRemoveDraft($event);
    }

    private function createContentTypeData(bool $withProductSpecification, bool $isNew = false): ContentTypeData
    {
        return new ContentTypeData([
            'identifier' => $isNew ? '__new__' : 'example',
            'contentTypeDraft' => $this->createContentTypeDraft($withProductSpecification, $isNew),
        ]);
    }

    private function createContentTypeDraft(bool $withProductSpecification, bool $isNew = false): ContentTypeDraft
    {
        $contentTypeDraft = $this->createMock(ContentTypeDraft::class);
        $contentTypeDraft->method('__get')->with('identifier')->willReturn($isNew ? '__new__' : 'example');
        $contentTypeDraft
            ->method('hasFieldDefinitionOfType')
            ->with(ProductSpecificationType::FIELD_TYPE_IDENTIFIER)
            ->willReturn($withProductSpecification);

        return $contentTypeDraft;
    }
}
