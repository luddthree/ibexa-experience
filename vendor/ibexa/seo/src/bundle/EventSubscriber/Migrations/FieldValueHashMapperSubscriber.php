<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Seo\EventSubscriber\Migrations;

use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\HashFromFieldValueEvent;
use Ibexa\Migration\Event\MigrationEvents;
use Ibexa\Seo\FieldType\SeoType;
use Ibexa\Seo\FieldType\SeoValue;
use Ibexa\Seo\Value\SeoTypesValue;
use Ibexa\Seo\Value\SeoTypeValue;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class FieldValueHashMapperSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                'convertScalarHashIntoObjects',
                -100,
            ],
            MigrationEvents::AFTER_HASH_FROM_FIELD_VALUE => [
                'convertObjectHashIntoScalarHash',
                -100,
            ],
        ];
    }

    public function convertScalarHashIntoObjects(FieldValueFromHashEvent $event): void
    {
        /** @var array<mixed> $hash */
        $hash = $event->getHash();
        $fieldTypeIdentifier = $event->getFieldTypeIdentifier();

        if ($fieldTypeIdentifier !== SeoType::IDENTIFIER || empty($hash['types'])) {
            return;
        }

        Assert::isArray($hash['types']);

        $fieldValueHash = ['value' => new SeoTypesValue()];
        foreach ($hash['types'] as $type) {
            $fieldValueHash['value']->setType($type['type'], new SeoTypeValue($type['type'], $type['fields']));
        }

        $event->setHash($fieldValueHash);
    }

    public function convertObjectHashIntoScalarHash(HashFromFieldValueEvent $event): void
    {
        /** @var array{value: \Ibexa\Seo\FieldType\SeoValue} $hash */
        $hash = $event->getHash();

        if (empty($hash['value']) || !$hash['value'] instanceof SeoValue) {
            return;
        }

        $seoTypesValue = $hash['value']->getSeoTypesValue();
        Assert::notNull($seoTypesValue);
        $scalarHash = ['types' => []];
        foreach ($seoTypesValue->getSeoTypesValues() as $seoTypeValue) {
            $type = $seoTypeValue->getType();
            $scalarHash['types'][$type] = $seoTypeValue->jsonSerialize();
        }
        $event->setHash($scalarHash);
    }
}
