<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\Contracts\DoctrineSchema\Event\SchemaBuilderEvent;
use Ibexa\Contracts\DoctrineSchema\SchemaBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BuildSchemaSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $schemaFilePath;

    /**
     * @param string $schemaFilePath Path to Yaml schema definition supported by SchemaBuilder
     */
    public function __construct(string $schemaFilePath)
    {
        $this->schemaFilePath = $schemaFilePath;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SchemaBuilderEvents::BUILD_SCHEMA => ['onBuildSchema', 300],
        ];
    }

    public function onBuildSchema(SchemaBuilderEvent $event): void
    {
        $event
            ->getSchemaBuilder()
            ->importSchemaFromFile($this->schemaFilePath);
    }
}

class_alias(BuildSchemaSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\BuildSchemaSubscriber');
