<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\ContentFormSubscriber;
use Ibexa\ContentForms\Data\Content\ContentCreateData;
use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\ContentForms\Event\ContentFormEvents;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentFormSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                ContentFormEvents::CONTENT_PUBLISH,
                ContentFormEvents::CONTENT_CANCEL,
            ],
            array_keys(ContentFormSubscriber::getSubscribedEvents())
        );
    }

    /**
     * @param mixed $data
     *
     * @dataProvider dataProviderForTestOnContentFormEventRedirectsToProductCatalog
     */
    public function testOnContentFormEventRedirectsToProductCatalog($data): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->with('ibexa.product_catalog.product.list')->willReturn('/product-catalog');

        $event = $this->createFormActionEvent($data);

        $subscriber = new ContentFormSubscriber($urlGenerator);
        $subscriber->onContentFormEvent($event);

        self::assertInstanceOf(RedirectResponse::class, $event->getResponse());
        self::assertEquals('/product-catalog', $event->getResponse()->getTargetUrl());
    }

    /**
     * @phpstan-return iterable<
     *     string,
     *     array{
     *         0: \Ibexa\ContentForms\Data\Content\ContentCreateData|\Ibexa\ContentForms\Data\Content\ContentUpdateData
     *     }
     * >
     */
    public function dataProviderForTestOnContentFormEventRedirectsToProductCatalog(): iterable
    {
        $contentCreateData = new ContentCreateData();
        $contentCreateData->contentType = $this->createContentType(true);

        $contentUpdateData = new ContentUpdateData([
            'contentDraft' => $this->createContent(true),
        ]);

        yield ContentCreateData::class => [$contentCreateData];
        yield ContentUpdateData::class => [$contentUpdateData];
    }

    public function testOnContentFormEventIgnoresNonProducts(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects(self::never())->method('generate');

        $data = new ContentCreateData();
        $data->contentType = $this->createContentType(false);

        $response = $this->createMock(Response::class);

        $event = $this->createFormActionEvent($data);
        $event->setResponse($response);

        $subscriber = new ContentFormSubscriber($urlGenerator);
        $subscriber->onContentFormEvent($event);

        self::assertSame($response, $event->getResponse());
    }

    private function createContent(bool $withProductSpecification): Content
    {
        $content = $this->createMock(Content::class);
        $content->method('getContentType')->willReturn(
            $this->createContentType($withProductSpecification)
        );

        return $content;
    }

    private function createContentType(bool $withProductSpecification): ContentType
    {
        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->method('hasFieldDefinitionOfType')
            ->with(ProductSpecificationType::FIELD_TYPE_IDENTIFIER)
            ->willReturn($withProductSpecification);

        return $contentType;
    }

    /**
     * @param mixed $data
     */
    private function createFormActionEvent($data): FormActionEvent
    {
        return new FormActionEvent(
            $this->createMock(FormInterface::class),
            $data,
            'publish'
        );
    }
}
