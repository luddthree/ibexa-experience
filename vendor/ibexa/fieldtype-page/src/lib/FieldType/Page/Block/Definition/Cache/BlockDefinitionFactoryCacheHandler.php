<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Cache;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Core\Persistence\Cache\AbstractInMemoryHandler;
use Ibexa\Core\Persistence\Cache\Adapter\TransactionAwareAdapterInterface;
use Ibexa\Core\Persistence\Cache\CacheIndicesValidatorInterface;
use Ibexa\Core\Persistence\Cache\InMemory\InMemoryCache;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;

/**
 * @internal
 */
final class BlockDefinitionFactoryCacheHandler extends AbstractInMemoryHandler
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    public function __construct(
        TransactionAwareAdapterInterface $cache,
        PersistenceLogger $logger,
        InMemoryCache $inMemory,
        BlockDefinitionFactoryInterface $blockDefinitionFactory,
        ?CacheIndicesValidatorInterface $cacheIndicesValidator = null
    ) {
        parent::__construct($cache, $logger, $inMemory, $cacheIndicesValidator);

        $this->blockDefinitionFactory = $blockDefinitionFactory;
    }

    /**
     * @return string[]
     */
    public function getBlockIdentifiers(): array
    {
        return $this->getCacheValue(
            '',
            'ibx-page-block-identifiers',
            function (): array {
                return $this->blockDefinitionFactory->getBlockIdentifiers();
            },
            static function (): array {
                return [
                    'ibx-p-blkids',
                ];
            },
            static function (): array {
                return [
                    'ibx-page-block-identifiers',
                ];
            }
        );
    }

    public function getBlockDefinition(string $identifier): BlockDefinition
    {
        return $this->getCacheValue(
            $identifier,
            'ibx-page-block-definition-',
            function (string $identifier): BlockDefinition {
                return $this->blockDefinitionFactory->getBlockDefinition($identifier);
            },
            static function (BlockDefinition $blockDefinition): array {
                return [
                    'ibx-p-blkd-' . $blockDefinition->getIdentifier(),
                ];
            },
            static function (BlockDefinition $blockDefinition): array {
                return [
                    'ibx-page-block-definition-' . $blockDefinition->getIdentifier(),
                ];
            }
        );
    }
}
