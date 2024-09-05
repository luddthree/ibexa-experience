<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Gateway\MigrationMetadata;

use Ibexa\Contracts\DoctrineSchema\Event\SchemaBuilderEvent;
use Ibexa\Contracts\DoctrineSchema\SchemaBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Creates a table for migration metadata during platform install.
 *
 * @internal
 */
final class BuildSchemaSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Migration\Gateway\MigrationMetadata\SchemaProvider */
    private $schemaProvider;

    public function __construct(SchemaProvider $schemaProvider)
    {
        $this->schemaProvider = $schemaProvider;
    }

    /**
     * @return array<array{string, int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SchemaBuilderEvents::BUILD_SCHEMA => ['onBuildSchema', 200],
        ];
    }

    public function onBuildSchema(SchemaBuilderEvent $event): void
    {
        $schema = $event->getSchema();

        if ($schema->hasTable($this->schemaProvider->getTableName())) {
            return;
        }

        $this->schemaProvider->buildExpectedTable($schema);
    }
}

class_alias(BuildSchemaSubscriber::class, 'Ibexa\Platform\Migration\Gateway\MigrationMetadata\BuildSchemaSubscriber');
