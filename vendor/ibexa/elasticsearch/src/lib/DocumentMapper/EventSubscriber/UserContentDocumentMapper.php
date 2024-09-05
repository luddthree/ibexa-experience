<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\DocumentMapper\EventSubscriber;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\IdentifierField;
use Ibexa\Contracts\Elasticsearch\Mapping\Event\ContentIndexCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserContentDocumentMapper implements EventSubscriberInterface
{
    /** @internal */
    public const HASHING_ALGORITHM = 'sha256';

    public static function getSubscribedEvents(): array
    {
        return [
            ContentIndexCreateEvent::class => [
                'onContentIndexCreate',
                -100,
            ],
        ];
    }

    public function onContentIndexCreate(ContentIndexCreateEvent $event): void
    {
        $content = $event->getContent();

        $field = $this->getUserField($content);
        if ($field === null) {
            return;
        }

        $document = $event->getDocument();

        if (isset($field->value->externalData['login'])) {
            $document->fields[] = new Field(
                'user_login',
                hash(self::HASHING_ALGORITHM, $field->value->externalData['login']),
                new IdentifierField()
            );
        }

        if (isset($field->value->externalData['email'])) {
            $document->fields[] = new Field(
                'user_email',
                hash(self::HASHING_ALGORITHM, $field->value->externalData['email']),
                new IdentifierField()
            );
        }
    }

    private function getUserField(SPIContent $content): ?SPIContent\Field
    {
        foreach ($content->fields as $field) {
            if ($field->type === 'ezuser') {
                return $field;
            }
        }

        return null;
    }
}
