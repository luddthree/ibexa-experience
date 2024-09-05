<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ApplicationConfig\Providers;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\FieldTypePage\Event\AvailableLayoutsConfigEvent;
use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\LayoutDefinitionConverter;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @phpstan-import-type TLayoutDefinition from \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\LayoutDefinitionConverter
 */
class LayoutDefinitions implements LayoutDefinitionsInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\LayoutDefinitionConverter */
    private $layoutDefinitionConverter;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        LayoutDefinitionConverter $layoutDefinitionConverter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->layoutDefinitionConverter = $layoutDefinitionConverter;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getConfig(?ContentType $filterBy = null): array
    {
        $layouts = $this->layoutDefinitionConverter->toHash();

        if ($filterBy !== null && $filterBy->hasFieldDefinitionOfType('ezlandingpage')) {
            $fieldDefinition = $filterBy->getFirstFieldDefinitionOfType('ezlandingpage');
            $settings = $fieldDefinition->fieldSettings;

            $layouts = isset($settings['availableLayouts'])
                ? $this->filterByAvailability($layouts, $settings['availableLayouts'])
                : $layouts;
        }

        $availableLayoutsConfigEvent = $this->eventDispatcher->dispatch(
            new AvailableLayoutsConfigEvent($layouts, $filterBy)
        );

        return array_values($availableLayoutsConfigEvent->getLayouts());
    }

    /**
     * @param array<string> $availableLayouts
     *
     * @phpstan-param array<string, TLayoutDefinition> $layouts
     *
     * @return array<string, TLayoutDefinition>
     */
    private function filterByAvailability(array $layouts, array $availableLayouts): array
    {
        return array_map(
            static function (array $layout) use ($availableLayouts): array {
                if (!in_array($layout['id'], $availableLayouts, true)) {
                    $layout['visible'] = false;
                }

                return $layout;
            },
            $layouts,
        );
    }
}

class_alias(LayoutDefinitions::class, 'EzSystems\EzPlatformPageFieldType\ApplicationConfig\Providers\LayoutDefinitions');
