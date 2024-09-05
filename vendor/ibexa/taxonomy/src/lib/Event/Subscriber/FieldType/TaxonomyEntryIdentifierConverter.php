<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber\FieldType;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\MigrationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TaxonomyEntryIdentifierConverter implements EventSubscriberInterface
{
    private string $fieldTypeIdentifier;

    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(string $fieldTypeIdentifier, TaxonomyServiceInterface $taxonomyService)
    {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->taxonomyService = $taxonomyService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                'convertIdentifiersIntoIds',
                -1,
            ],
        ];
    }

    public function convertIdentifiersIntoIds(FieldValueFromHashEvent $event): void
    {
        if ($event->getFieldTypeIdentifier() !== $this->fieldTypeIdentifier) {
            return;
        }

        $hash = $event->getHash();
        if (
            !is_array($hash)
            || isset($hash['taxonomy_entry'])
            || !isset($hash['taxonomy_entry_identifier'])) {
            return;
        }

        $identifier = $hash['taxonomy_entry_identifier'];
        if (isset($hash['taxonomy'])) {
            $entry = $this->taxonomyService->loadEntryByIdentifier((string)$identifier, $hash['taxonomy']);
        } else {
            $entry = $this->taxonomyService->loadEntryByIdentifier((string)$identifier);
        }

        $hash['taxonomy_entry'] = $entry;
        unset($hash['taxonomy_entry_identifier']);

        $event->setHash($hash);
    }
}
