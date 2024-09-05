<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ApplicationConfig\Providers;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\FieldTypePage\Event\AvailableBlocksConfigEvent;
use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\BlockDefinitionConverter;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @phpstan-import-type TBlockDefinition from \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\BlockDefinitionConverter
 */
class BlockDefinitions implements BlockDefinitionsInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\BlockDefinitionConverter */
    private $blockDefinitionConverter;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        BlockDefinitionConverter $blockDefinitionConverter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->blockDefinitionConverter = $blockDefinitionConverter;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws \Exception
     */
    public function getConfig(?ContentType $filterBy = null): array
    {
        $blocks = $this->blockDefinitionConverter->toHash();

        if (null !== $filterBy && $filterBy->hasFieldDefinitionOfType('ezlandingpage')) {
            $fieldDefinition = $filterBy->getFirstFieldDefinitionOfType('ezlandingpage');
            $settings = $fieldDefinition->fieldSettings;

            $blocks = isset($settings['availableBlocks'])
                ? $this->filterByAvailability($blocks, $settings['availableBlocks'])
                : $blocks;
        }

        $availableBlocksConfigEvent = $this->eventDispatcher->dispatch(
            new AvailableBlocksConfigEvent($blocks, $filterBy)
        );

        return array_values($availableBlocksConfigEvent->getBlocks());
    }

    /**
     * @param array<string> $availableBlocksTypes
     *
     * @phpstan-param array<string, TBlockDefinition> $blocks
     *
     * @return array<string, TBlockDefinition>
     */
    private function filterByAvailability(array $blocks, array $availableBlocksTypes): array
    {
        return array_map(
            static function (array $blockDefinition) use ($availableBlocksTypes): array {
                if (!in_array($blockDefinition['type'], $availableBlocksTypes, true)) {
                    $blockDefinition['visible'] = false;
                }

                return $blockDefinition;
            },
            $blocks,
        );
    }
}

class_alias(BlockDefinitions::class, 'EzSystems\EzPlatformPageFieldType\ApplicationConfig\Providers\BlockDefinitions');
