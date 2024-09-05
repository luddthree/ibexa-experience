<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\AdminUi\Form\Data\ContentTypeData;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\AdminUi\Event\FormEvents;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentTypeFormActionSubscriber implements EventSubscriberInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::CONTENT_TYPE_PUBLISH => 'onContentTypePublish',
            FormEvents::CONTENT_TYPE_REMOVE_DRAFT => 'onContentTypeRemoveDraft',
        ];
    }

    public function onContentTypePublish(FormActionEvent $event): void
    {
        $data = $event->getData();

        if ($data instanceof ContentTypeData && $this->isProductType($data->contentTypeDraft)) {
            $event->setResponse($this->createRedirectToViewResponse($data->identifier));
        }
    }

    public function onContentTypeRemoveDraft(FormActionEvent $event): void
    {
        $data = $event->getData();

        if ($data instanceof ContentTypeData && $this->isProductType($data->contentTypeDraft)) {
            if ($data->isNew()) {
                $event->setResponse($this->createRedirectToListResponse());
            } else {
                $event->setResponse($this->createRedirectToViewResponse($data->identifier));
            }
        }
    }

    private function isProductType(ContentTypeDraft $contentTypeDraft): bool
    {
        return $contentTypeDraft->hasFieldDefinitionOfType(Type::FIELD_TYPE_IDENTIFIER);
    }

    private function createRedirectToListResponse(): RedirectResponse
    {
        return new RedirectResponse(
            $this->urlGenerator->generate('ibexa.product_catalog.product_type.list')
        );
    }

    private function createRedirectToViewResponse(string $identifier): RedirectResponse
    {
        return new RedirectResponse(
            $this->urlGenerator->generate(
                'ibexa.product_catalog.product_type.view',
                [
                    'productTypeIdentifier' => $identifier,
                ]
            )
        );
    }
}
