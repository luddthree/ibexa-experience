<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionEvents;
use Ibexa\FieldTypePage\Exception\BlockDefinitionNotFoundException;
use Ibexa\FieldTypePage\Exception\BlockDefinitionUnsupportedTypeException;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @phpstan-import-type TBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 * @phpstan-import-type TReactBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 */
class BlockDefinitionFactory implements BlockDefinitionFactoryInterface
{
    /** @var array<string, TBlockConfiguration|TReactBlockConfiguration> */
    protected array $configuration = [];

    protected EventDispatcherInterface $eventDispatcher;

    /** @var iterable<\Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Strategy\BlockDefinitionFactoryStrategyInterface> */
    private iterable $blockFactoryStrategies;

    /** @return array<string> */
    public function getBlockIdentifiers(): array
    {
        return array_keys($this->configuration);
    }

    /**
     * @phpstan-param iterable<\Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Strategy\BlockDefinitionFactoryStrategyInterface> $blockFactoryStrategies
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        iterable $blockFactoryStrategies
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->blockFactoryStrategies = $blockFactoryStrategies;
    }

    /** @phpstan-param array<string, TBlockConfiguration|TReactBlockConfiguration> $configuration */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    /** @return array<string, TBlockConfiguration|TReactBlockConfiguration> */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /** @phpstan-param non-empty-string $identifier */
    public function hasBlockDefinition(string $identifier): bool
    {
        return isset($this->configuration[$identifier]);
    }

    /**
     * @param non-empty-string $identifier
     *
     * @throws \Ibexa\FieldTypePage\Exception\BlockDefinitionNotFoundException
     * @throws \Ibexa\FieldTypePage\Exception\BlockDefinitionUnsupportedTypeException
     */
    public function getBlockDefinition(string $identifier): BlockDefinition
    {
        if (!$this->hasBlockDefinition($identifier)) {
            throw new BlockDefinitionNotFoundException(
                $identifier,
                sprintf('%s is unsupported yet.', $identifier)
            );
        }

        $configuration = $this->configuration[$identifier];

        foreach ($this->blockFactoryStrategies as $blockFactoryStrategy) {
            if ($blockFactoryStrategy->supports($configuration['block_type'])) {
                return $this->resolveBlockDefinition(
                    $blockFactoryStrategy->create($identifier, $configuration),
                    $configuration,
                    $identifier
                );
            }
        }

        throw new BlockDefinitionUnsupportedTypeException($identifier);
    }

    /** @phpstan-param array<string, TBlockConfiguration|TReactBlockConfiguration> $blocks*/
    public function addConfiguration(array $blocks): void
    {
        $this->configuration = array_merge($this->configuration, $blocks);
    }

    /**
     * @param non-empty-string $identifier
     */
    private function resolveBlockDefinition(
        BlockDefinition $blockDefinition,
        array $configuration,
        string $identifier
    ): BlockDefinition {
        $blockDefinitionEvent = $this->eventDispatcher->dispatch(
            new BlockDefinitionEvent($blockDefinition, $configuration),
            BlockDefinitionEvents::getBlockDefinitionEventName($identifier)
        );

        return $blockDefinitionEvent->getDefinition();
    }
}

class_alias(BlockDefinitionFactory::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\BlockDefinitionFactory');
