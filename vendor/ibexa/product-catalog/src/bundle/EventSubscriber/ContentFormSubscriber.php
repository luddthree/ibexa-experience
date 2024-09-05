<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\ContentForms\Data\Content\ContentCreateData;
use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\ContentForms\Event\ContentFormEvents;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentFormSubscriber implements EventSubscriberInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentFormEvents::CONTENT_PUBLISH => ['onContentFormEvent', -255],
            ContentFormEvents::CONTENT_CANCEL => ['onContentFormEvent', -255],
        ];
    }

    public function onContentFormEvent(FormActionEvent $event): void
    {
        $contentType = $this->resolveContentType($event->getData());

        if ($contentType !== null) {
            $isProduct = $contentType->hasFieldDefinitionOfType(Type::FIELD_TYPE_IDENTIFIER);
            if ($isProduct) {
                $event->setResponse($this->createRedirectToProductListResponse());
            }
        }
    }

    private function createRedirectToProductListResponse(): RedirectResponse
    {
        $redirectionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.product.list'
        );

        return new RedirectResponse($redirectionUrl);
    }

    /**
     * @param mixed $data
     */
    private function resolveContentType($data): ?ContentType
    {
        if ($data instanceof ContentCreateData) {
            return $data->contentType;
        } elseif ($data instanceof ContentUpdateData) {
            return $data->contentDraft->getContentType();
        } else {
            return null;
        }
    }
}
