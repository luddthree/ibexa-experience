<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\FieldTypePage\Form\Type\UniversalDiscoveryWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class AttributeEmbedType extends AbstractType
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        NotificationHandlerInterface $notificationHandler
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->notificationHandler = $notificationHandler;
    }

    public function getParent(): string
    {
        return UniversalDiscoveryWidgetType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'block_configuration_attribute_embed';
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $attributeValue = $form->getData();

        if (empty($attributeValue)) {
            $view->vars += [
                'content' => null,
                'content_type' => null,
            ];

            return;
        }

        try {
            $content = $this->contentService->loadContent($attributeValue);
            $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        } catch (NotFoundException $e) {
            $this->notificationHandler->error(sprintf(
                "The embedded content could not be found with contentId '%s'.",
                $attributeValue
            ));

            $content = null;
            $contentType = null;
        }

        $view->vars += [
            'content' => $content,
            'content_type' => $contentType,
        ];
    }
}

class_alias(AttributeEmbedType::class, 'EzSystems\EzPlatformPageFieldType\Form\Type\BlockAttribute\AttributeEmbedType');
