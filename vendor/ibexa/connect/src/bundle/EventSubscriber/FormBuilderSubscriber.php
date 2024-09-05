<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\EventSubscriber;

use Ibexa\Bundle\Connect\Message\WebhookRequest;
use Ibexa\Bundle\Connect\MessageHandler\WebhookRequestHandler;
use Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder;
use Ibexa\FormBuilder\Event\FieldDefinitionEvent;
use Ibexa\FormBuilder\Event\FormEvents;
use Ibexa\FormBuilder\Event\FormSubmitEvent;
use Ibexa\FormBuilder\Exception\FormFieldNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FormBuilderSubscriber implements EventSubscriberInterface
{
    private const ATTRIBUTE_IDENTIFIER = 'ibexa_connect_webhook_url';

    private WebhookRequestHandler $notificationHandler;

    public function __construct(WebhookRequestHandler $notificationHandler)
    {
        $this->notificationHandler = $notificationHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::FORM_SUBMIT => 'onFormSubmit',
            FormEvents::getFieldDefinitionEventName('button') => 'onButtonField',
        ];
    }

    public function onFormSubmit(FormSubmitEvent $event): void
    {
        try {
            $button = $event->getForm()->getFieldByIdentifier('button');
        } catch (FormFieldNotFoundException $e) {
            return;
        }

        if (!$button->hasAttribute(self::ATTRIBUTE_IDENTIFIER)) {
            return;
        }

        $url = $button->getAttributeValue(self::ATTRIBUTE_IDENTIFIER);
        if (!is_string($url) || empty($url)) {
            return;
        }

        $fields = $event->getForm()->getFields();
        $data = $event->getData();

        $requestData = [
            'languageCode' => $data['languageCode'],
            'contentId' => $data['contentId'],
            'contentFieldId' => $data['contentFieldId'],
            'fields' => [],
        ];

        foreach ($fields as $field) {
            $requestData['fields'][] = [
                'field_type_identifier' => $field->getIdentifier(),
                'field_name' => $field->getName(),
                'value' => $data['fields'][$field->getId()] ?? null,
            ];
        }

        $notification = new WebhookRequest($url, $requestData);

        $this->notificationHandler->handle($notification);
    }

    public function onButtonField(FieldDefinitionEvent $event): void
    {
        $webhookAttribute = new FieldAttributeDefinitionBuilder();
        $webhookAttribute->setIdentifier(self::ATTRIBUTE_IDENTIFIER);
        $webhookAttribute->setName('Ibexa Connect Webhook URL');
        $webhookAttribute->setType('string');

        $definitionBuilder = $event->getDefinitionBuilder();
        $definitionBuilder->addAttribute($webhookAttribute->buildDefinition());
    }
}
